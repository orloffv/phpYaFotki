<?
Class YaFotki {

    public $login;
    public $url = 'http://api-fotki.yandex.ru/api/users/';
    public $sizes = array();
    public $cache = FALSE;
    public $cache_path = 'cache/';
    public $cache_lifetime = 360000;

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

    protected function query($url, $limit='')
    {
        $url .= '?format=json';

        if (!empty($limit))
        {
            $url.= '&limit=' . $limit;
        }
        
        if ($this->cache AND ($result_json = $this->get_cache($url)) === FALSE)
        {
        	$result_json = json_decode(file_get_contents($url));
        
        	if ($this->cache)
        	{
        		$this->set_cache($url, $result_json);
        	}
        }
        
        return $result_json;
    }

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

    public function get_allPhotos()
    {
        return $this->get_photos($this->sizes);
    }

    public function get_albumPhotos($album)
    {
        return $this->get_photos($this->sizes, 'album/' . $album . '/photos/');
    }

    public function get_albums()
    {
        $result = $this->query($this->url . $this->login . '/albums/');

        $return_items = array();

        $items = $result->entries;

        foreach ($items as $item)
        {
            if ((isset($item->protected) AND $item->protected) OR !$item->imageCount)
            {
                continue;
            }

            $id_album = NULL;

            $link = $item->links->alternate;

            $link = explode('/', $link);

            $id_album = $link[count($link) - 2];

            $return_items[] = array(
                'id' => $id_album,
                'title' => $item->title,
                'update' => $item->updated
            );
        }

        return $return_items;
    }

    public function get_albumsWithPreview($preview_size)
    {
        $albums = $this->get_albums();

        foreach ($albums as &$album)
        {
            $result_photo = $this->get_photos(array($preview_size), 'album/' . $album['id'] . '/photos/', 1);

            $album['image'] = $result_photo[0]['images'][$preview_size];
        }

        return $albums;
    }
	
	protected function get_cache($key)
	{
		$file = $this->cache_path.md5($key).'.json';
		
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
	
	protected function set_cache($key, $value)
	{
		$file = $this->cache_path.md5($key).'.json';
		
		if ( ! is_dir($this->cache_path))
		{
			$mkdir = mkdir(realpath('./').'/'.$this->cache_path, 0755, TRUE);
		}
		
		$save_cache_array = array(
		                  'expiry' => time() + $this->cache_lifetime,
		                  'data' => $value,
		);
		
		file_put_contents($file, json_encode($save_cache_array));
	}
}