<?

include('../source/YaFotki.php');

$fotki = new YaFotki(array('login' => 'vitaly.orloff', 'sizes' => array('XXS', 'XL'), 'cache' => TRUE));

$json = array();

if (!empty($_GET['id'])) 
{
    $photos = $fotki->get_album_photos($_GET['id']);

    foreach ($photos as $key => $photo) 
    {
        $json[$key]->image = $photo['images']['XL'];
        $json[$key]->thumb = $photo['images']['XXS'];
        $json[$key]->title = $photo['title'];
    }
} 
else 
{
    $json = $fotki->get_albums_with_preview('S');
}

echo json_encode($json);