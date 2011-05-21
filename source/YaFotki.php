<?
/**
 * Class for Yandex.fotki
 *
 * @author     orloff.v@gmail.com
 * @copyright  (c) 2011 Orloffv
 */
Class YaFotki {

    public $login;
    public $url = 'http://api-fotki.yandex.ru/api/users/';
    public $sizes = array();
    public $cache = FALSE;
    public $cache_path = 'cache/';
    public $cache_lifetime = 360000;
    public $categor_title_separator = ' / ';

    function __construct($settings)
    {
        if (isset($settings['sizes']))
        {
            $this->sizes = $settings['sizes'];
        }

        if (isset($settings['login']))
        {
            $this->login = $settings['login'];
        }

        if (isset($settings['cache']))
        {
            $this->cache = $settings['cache'];
        }
    }
    
    /**
     * Возвращает массив с данными после запроса к api или в кэш
     * 
     * @param string $url
     * @param int $limit
     * @return array
     */
    protected function query($url, $limit='')
    {
        $url .= '?format=json';

        if (!empty($limit))
        {
            $url.= '&limit=' . $limit;
        }

        if (($this->cache AND ($result_json = $this->get_cache($url)) === FALSE) OR ! $this->cache)
        {   
            $result_json = json_decode(file_get_contents($url));

            if ($this->cache)
            {
                $this->set_cache($url, $result_json);
            }
        }

        return $result_json;
    }
    
    /**
     * Возвращаем массив с фотками
     * 
     * пример вызова:
     * get_photos(array('XXS', 'XXL'))
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
    protected function get_photos($sizes, $what = 'photos/', $limit = '')
    {
        $result = $this->query($this->url . $this->login . '/' . $what, $limit);

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
        return $this->get_photos($this->sizes);
    }

    /**
     * Возвращает фотки из альбома
     * 
     * @param int $album
     * @return array 
     */
    public function get_album_photos($album)
    {
        return $this->get_photos($this->sizes, 'album/' . $album . '/photos/');
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
    public function get_albums()
    {
        $result = $this->query($this->url . $this->login . '/albums/');

        $return_items = array();
            
        $albums_parents = array(); 
        
        $items = $result->entries;

        foreach ($items as $item)
        {
            $id_album = $this->get_album_id($item->links->alternate);
            
            $parent_id = 0;
            
            if (isset($item->links->album))
            {
                $parent_id = $this->get_album_id($item->links->album);
            }
            
            $albums_parents[$id_album] = array(
                'title' => $item->title,
                'parent_id' => $parent_id,
            );
            
            if ( ! isset($item->protected) AND $item->imageCount)
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
            $album['parents'] = $this->get_parents($album['id'], $albums_parents);
            
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
    protected function get_parents($album_id, $albums_parents)
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
           
            $parents = array_merge($parents, $this->get_parents($albums_parents[$album_id]['parent_id'], $albums_parents));
        }
        
        return $parents;
    }

    /**
     * Возвращает id альбома из url
     * 
     * пример вызова:
     * get_album_id('http://api-fotki.yandex.ru/api/users/username/album/123131/');
     *
     * @param string $url
     * @return int 
     */
    protected function get_album_id($url)
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
    public function get_albums_with_preview($preview_size)
    {
        $albums = $this->get_albums();

        foreach ($albums as &$album)
        {
            $result_photo = $this->get_photos(array($preview_size), 'album/' . $album['id'] . '/photos/', 1);

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
    protected function get_cache($key)
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
    protected function set_cache($key, $value)
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

}