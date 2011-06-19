<?

include('../source/YaFotki.php');

$fotki = new YaFotki(array('login' => 'vitaly.orloff', 'sizes' => array('XXS', 'XL', 'XXXL'), 'cache' => TRUE));

$json = array();

if (!empty($_GET['id'])) 
{
    $photos = $fotki->get_album_photos($_GET['id']);

    foreach ($photos as $key => $photo) 
    {
        $json[$key]->image = $photo['images']['XL'];
        $json[$key]->thumb = $photo['images']['XXS'];
        $json[$key]->big = $photo['images']['XXXL'];
        $json[$key]->title = $photo['title'];
    }
} 
else 
{
    $json = $fotki->get_albums_with_preview('S', 'name_path');
}

echo json_encode($json);