<?
include('../source/Yfotki.php');

$fotki = new yFotki(array('login' => 'vitaly.orloff', 'sizes' => array('XXS', 'XL'), 'cache' => TRUE));

$json = array();

if ( ! empty($_GET['id']))
{
    $photos = $fotki->get_albumPhotos($_GET['id']);    
    
    foreach ($photos as $key => $photo)
    {
        $json[$key]->image = $photo['images']['XL'];
        $json[$key]->thumb = $photo['images']['XXS'];
        $json[$key]->title = $photo['title'];
    }
}
else
{
    $json = $fotki->get_albumsWithPreview('S');
}

echo json_encode($json);