<?
Class yFotki {

    public $login;
    public $url = 'http://api-fotki.yandex.ru/api/users/';
    public $sizes = array();

    function __construct($settings)
    {
        $this->login = $settings['login'];
        $this->sizes = $settings['sizes'];
    }

    protected function query($url, $limit='')
    {
        $url .= '?format=json';

        if (!empty($limit))
        {
            $url.= '&limit=' . $limit;
        }

        $result_json = file_get_contents($url);
        $result = json_decode($result_json);
        return $result;
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

}