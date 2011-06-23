<?
/**
 * Class for Yandex.fotki
 *
 * @author     orloff.v@gmail.com
 * @copyright  (c) 2011 Orloffv
 */
Class YaFotki {

    public $login;
    protected $password;
    protected $token;
    protected $protected = FALSE;
    public $url = 'http://api-fotki.yandex.ru/api/users/';
    public $sizes = array();
    public $cache = FALSE;
    public $cache_path = 'cache/';
    public $cache_lifetime = 360000;
    public $categor_title_separator = ' / ';

    function __construct(array $settings)
    {
        if (isset($settings['sizes']))
        {
            $this->sizes = $settings['sizes'];
        }
        
        if (isset($settings['protected']))
        {
            $this->protected = $settings['protected'];
        }

        if (isset($settings['login']))
        {
            $this->login = $settings['login'];
        }
        
        if (isset($settings['password']))
        {
            $this->password = $settings['password'];
        }

        if (isset($settings['cache']))
        {
            $this->cache = $settings['cache'];
        }
    }
    
    public static function instance(array $settings)
    {
        static $instance = NULL;
        
        if ($instance == NULL)
        {
            $instance = new self($settings);
        }

        return $instance;
    }
    
    protected function _auth()
    {
        $xml = $this->_query('http://auth.mobile.yandex.ru/yamrsa/key/', NULL, FALSE, 'xml', NULL, FALSE);
        $pattern = '#<response>[\s]*?<key>(.*)</key>[\s]*?<request_id>(.*)</request_id>[\s]*?</response>#';
        preg_match_all($pattern, $xml, $matches);
        
        $rsa_key = $matches[1][0];
        $request_id = $matches[2][0];
        
        if ($rsa_key AND $request_id)
        {
            $credentials = "<credentials login=\"{$this->login}\" password=\"{$this->password}\"/>";
            
            $credentials = $this->_base64_rsa($rsa_key, $credentials);
            
            $post = "request_id={$request_id}&credentials={$credentials}";
            
            $token = $this->_query("http://auth.mobile.yandex.ru/yamrsa/token/", NULL, FALSE, 'xml', $post, FALSE);
            
            $pattern = '#<response>[\s]*?<token>(.*)</token>[\s]*?</response>#';
            preg_match($pattern, $token, $match);
            
            $token = $match[1];
            
            if ($token)
            {
                $this->token = $token;
            }
        }
        
    }


    /**
     * Возвращает массив с данными после запроса к api или в кэш
     * 
     * @param string $url
     * @param int $limit
     * @return array
     */
    protected function _query($url, $limit = NULL, $cache = NULL, $format = 'json', $post = NULL, $protected = NULL)
    {
        if ($format == 'json')
        {
            $url .= '?format=json';
        }

        if ( ! is_null($limit))
        {
            $url .= '&limit=' . $limit;
        }

        if (is_null($cache))
        {
            $cache = $this->cache;
        }
        
        if (($cache AND ($result = $this->_get_cache($url)) === FALSE) OR ! $cache)
        {   
            if (is_null($protected))
            {
                $protected = $this->protected;
            }
            
            if ( ! is_null($post))
            {
                $context = stream_context_create(array(
                    'http' => array(
                        'method' => "POST",
                        'header' => "Content-Type: application/x-www-form-urlencoded" . PHP_EOL,
                        'content' => $post,
                    ),
                ));
            }
            elseif ($protected)
            {
                if ( ! $this->token)
                {
                    $this->_auth();
                }
                
                $context = stream_context_create(array(
                    'http' => array(
                        'method' => "GET",
                        'header' => "Authorization: FimpToken realm=\"fotki.yandex.ru\", token=\"{$this->token}\"",
                    ),
                ));
            }
            else
            {
                $context = NULL;
            }
            
            $result = file_get_contents($url, NULL, $context);
            
            if ($format == 'json')
            {
                $result = json_decode($result);
            }
            else if ($format == 'xml')
            {
                
            }

            if ($cache)
            {
                $this->_set_cache($url, $result);
            }
        }

        return $result;
    }
    
    /**
     * Возвращаем массив с фотками
     * 
     * пример вызова:
     * _get_photos(array('XXS', 'XXL'))
     * 
     * пример результата:
     * array(
     *  'title' => 'blahblah',
     *      'images' => array(
     *          'XXS' => 'ur',
     *          'XXL' => 'url',
     *      )
     * );
     * 
     * @param array $sizes
     * @param stirng $what путь к нужным данным в api после login
     * пример: http://api-fotki.yandex.ru/api/users/username/{album/12345/photos/}
     * @param int $limit
     * @return array 
     */
    protected function _get_photos($sizes, $what = 'photos/', $limit = NULL)
    {
        $result = $this->_query($this->url . $this->login . '/' . $what, $limit);

        $return_items = array();

        $items = $result->entries;
        foreach ($items as $item)
        {
            $url_sizes = array();

            foreach ($sizes as $size)
            {
                $url_sizes[$size] = $item->img->{$size}->href;
            }

            $return_items[] = array(
                'images' => $url_sizes,
                'title' => $item->title,
            );
        }

        return $return_items;
    }
    
    
    /**
     *  Возвращает последние фотки
     *
     * @return array 
     */
    public function get_all_photos()
    {
        return $this->_get_photos($this->sizes);
    }

    /**
     * Возвращает фотки из альбома
     * 
     * @param int $album
     * @return array 
     */
    public function get_album_photos($album)
    {
        return $this->_get_photos($this->sizes, 'album/' . $album . '/photos/');
    }
    
    /**
     * Возвращает альбомы
     *
     * пример результата:
     * array(
     *  'id' => 12345,
     *  'title' => 'blahblah',
     *  'title_path' => 'category_title / blahblah',
     *  'update' => 2009-01-27T11:57:32Z,
     *  'parents' => array(
     *          0 => array(
     *              'title' => 'album_name',
     *              'album_id' => id,
     *          ),
     *      ),
     * );
     * 
     * @return array
     */
    public function get_albums($sort = NULL)
    {
        $result = $this->_query($this->url . $this->login . '/albums/');

        $return_items = array();
            
        $albums_parents = array(); 
        
        $items = $result->entries;

        foreach ($items as $item)
        {
            $id_album = $this->_get_album_id($item->links->alternate);
            
            $parent_id = 0;
            
            if (isset($item->links->album))
            {
                $parent_id = $this->_get_album_id($item->links->album);
            }
            
            $albums_parents[$id_album] = array(
                'title' => $item->title,
                'parent_id' => $parent_id,
            );
            
            if (( ! $this->protected AND ! isset($item->protected) OR $this->protected) AND $item->imageCount)
            {

                $return_items[] = array(
                    'id' => $id_album,
                    'title' => $item->title,
                    'update' => $item->updated
                );
            }
        }
        
        foreach($return_items as &$album)
        {
            $album['parents'] = $this->_get_parents($album['id'], $albums_parents);
            
            $album['title_path'] = '';

            if (count($album['parents']))
            {       
                foreach ($album['parents'] as $parent)
                {
                    $album['title_path'] = $parent['title'].$this->categor_title_separator.$album['title_path'];
                }
			}

            $album['title_path'] .= $album['title'];
        }
        
        $return_items = $this->_sort_albums($return_items, $sort);
        
        return $return_items;
    }
    
    /**
     * Возвращает массив родителей альбомов,
     * Рекурсивная функция.
     * 
     * @param int $album_id
     * @param array $albums_parents
     * @return array 
     */
    protected function _get_parents($album_id, $albums_parents)
    {
        $parents = array();
        
        if ($albums_parents[$album_id]['parent_id'] == 0)
        {
            
            return $parents;
        }
        else
        {            
            $parents[] = array(
                            'title' => $albums_parents[$albums_parents[$album_id]['parent_id']]['title'],
                            'album_id' => $albums_parents[$album_id]['parent_id'],
                            );
           
            $parents = array_merge($parents, $this->_get_parents($albums_parents[$album_id]['parent_id'], $albums_parents));
        }
        
        return $parents;
    }

    /**
     * Возвращает id альбома из url
     * 
     * пример вызова:
     * _get_album_id('http://api-fotki.yandex.ru/api/users/username/album/123131/');
     *
     * @param string $url
     * @return int 
     */
    protected function _get_album_id($url)
    {
        $id_album = NULL;

        $url_temp = explode('/', $url);

        $id_album = $url_temp[count($url_temp) - 2];
        
        return $id_album;
    }


    /**
     * Возвращает альбомы с обложками
     *
     * пример результата:
     * array(
     *  'id' => 12345,
     *  'title' => 'blahblah',
     *  'update' => 2009-01-27T11:57:32Z,
     *  'image' => url,
     * );
     * 
     * @param string $preview_size
     * @return array 
     */
    public function get_albums_with_preview($preview_size, $sort = NULL)
    {
        $albums = $this->get_albums($sort);

        foreach ($albums as &$album)
        {
            $result_photo = $this->_get_photos(array($preview_size), 'album/' . $album['id'] . '/photos/', 1);

            $album['image'] = $result_photo[0]['images'][$preview_size];
        }

        return $albums;
    }

    /**
     * Возвращает данные из кэш
     * если данных нет или время кэша истекло возвращает FALSE
     * 
     * @param string $key
     * @return mixed bool or array 
     */
    protected function _get_cache($key)
    {
        $file = $this->cache_path . md5($key) . '.json';

        if (is_file($file))
        {
            $result_cache_json = file_get_contents($file);
            $result_cache_array = json_decode($result_cache_json);

            if ($result_cache_array->expiry < time())
            {
                unlink($file);
                return FALSE;
            }
            else
            {
                return $result_cache_array->data;
            }
        }
        else
        {
            return FALSE;
        }
    }
    
    /**
     * Записывает данные в кэш
     *
     * @param string $key
     * @param mixed $value 
     */
    protected function _set_cache($key, $value)
    {
        $file = $this->cache_path . md5($key) . '.json';

        if (!is_dir($this->cache_path))
        {
            $mkdir = mkdir(realpath('./') . '/' . $this->cache_path, 0755, TRUE);
        }

        $save_cache_array = array(
            'expiry' => time() + $this->cache_lifetime,
            'data' => $value,
        );

        file_put_contents($file, json_encode($save_cache_array));
    }
    
    /**
     * Сортирует массив альбомов
     *
     * @param string $key
     * @param mixed $value 
     */
    protected function _sort_albums($albums, $sort = NULL)
    {
    	if (is_null($sort))
    	{
    		$sort = 'date';
    	}
    	
    	switch ($sort) {
		    case 'date':
		    	$result_albums = $albums;
 			break;
 			case 'name':
				$result_albums = array();
    	
		    	foreach ($albums as $key => $album)
    			{
    				$temp_name[$key] = $album['title'];	
		    	}
 				
 				asort($temp_name);
 				
 				foreach($temp_name as $key => $dev_null)
 				{
 					$result_albums[] = $albums[$key];
 				}
 				
 			break;
 			case 'name_path':
				$result_albums = array();
    	
		    	foreach ($albums as $key => $album)
    			{
    				$temp_name[$key] = $album['title_path'];	
		    	}
 				
 				asort($temp_name);
 				
 				foreach($temp_name as $key => $dev_null)
 				{
 					$result_albums[] = $albums[$key];
 				}
 				
 			break;
 			default:
 				$result_albums = $albums;
 			break;
 		}

    	return $result_albums;
    }
    
    /**
     * tnx https://github.com/SilentImp/API_Yandex.Fotki_PHP_LIB/blob/SilentImp/YFSecurity.php 
     * RSA шифрование со вкусом Яндекса
     * Использует BCMath библиотеку
     *
     * @param string $key ключ шифрования
     * @param string $data данные, которые будут зашифрованы
     * @return string
     * @access private
     */	
    protected function _base64_rsa($key, $data)
    {
            $buffer = array();
            list($nstr, $estr) = explode('#', $key);

            $nv = $this->_bchexdec($nstr);
            $ev = $this->_bchexdec($estr);

            $stepSize = strlen($nstr)/2 - 1;
            $prev_crypted = array();
            $prev_crypted = array_fill(0, $stepSize, 0);
            $hex_out = '';
            for($i=0; $i<strlen($data); $i++){
                $buffer[] = ord($data{$i});
            }
            for($i=0; $i<(int)(((count($buffer)-1)/$stepSize)+1); $i++){
                $tmp = array_slice($buffer, $i * $stepSize, ($i + 1) * $stepSize);
                for ($j=0;$j<count($tmp); $j++){
                    $tmp[$j] = ($tmp[$j] ^ $prev_crypted[$j]);
                }				
                $tmp = array_reverse($tmp);
                $plain = "0";
                $pn="0";
                for($x = 0; $x < count($tmp); ++$x){
                    $pow = bcpowmod(256,$x,$nv);
                    $pow_mult = bcmul($pow,$tmp[$x]);
                    $plain = bcadd($plain,$pow_mult);
                }
                $plain_pow = bcpowmod($plain, $ev, $nv);
                $plain_pow_str = strtoupper($this->_dec2hex($plain_pow));
                $hex_result = array();

                for($k=0;$k<(strlen($nstr)-strlen($plain_pow))+ 1;$k++){
                    $hex_result[]="";
                }

                $hex_result = implode("0",$hex_result).$plain_pow_str;
                $min_x = min(strlen($hex_result), count($prev_crypted) * 2);

                for($x=0;$x<$min_x;$x=$x+2){
                    $prev_crypted[$x/2] = hexdec('0x'.substr($hex_result,$x,2));
                }
                if(count($tmp) < 16){
                    $hex_out.= '00';
                }
                $hex_out.= strtoupper(dechex(count($tmp)).'00');
                $ks = strlen($nstr) / 2;
                if($ks<16){
                    $hex_out.='0';
                }
                $hex_out.= dechex($ks).'00';
                $hex_out.= $hex_result;
            }
            
            return urlencode(base64_encode(pack("H*" , $hex_out)));
    }
    
    /**
     * tnx https://github.com/SilentImp/API_Yandex.Fotki_PHP_LIB/blob/SilentImp/YFSecurity.php 
     * Этот метод переводит большое шестнадцатиричное число в десятичное, использует BCMath
     * 
     * @param string $hex очень большое шестнадцатеричное число в виде строки
     * @return string
     * @access private
     */
    protected function _bchexdec($hex)
    {
        $dec = 0;
        $len = strlen($hex);
        for ($i = 1; $i <= $len; $i++) 
        {
            $dec = bcadd($dec, bcmul(strval(hexdec($hex[$i - 1])), bcpow('16', strval($len - $i))));
        }
        
        return $dec;
    }
    
    /**
     * tnx https://github.com/SilentImp/API_Yandex.Fotki_PHP_LIB/blob/SilentImp/YFSecurity.php
     * Этот метод переводит большое десятичное число в шестнадцатиричное, использует BCMath
     *
     * @param string $number очень большое десятичное число в виде строки
     * @return string
     * @access private
     */	
    protected function _dec2hex($number)
    {
        $hexvalues = array('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F');
        $hexval = '';
        
        while($number != '0') 
        {
            $hexval = $hexvalues[bcmod($number,'16')].$hexval;
            $number = bcdiv($number,'16',0);
        }
        
        return $hexval;
    }
}