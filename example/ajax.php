<?
include('../source/Yfotki.php');

$fotki = new yFotki(array('login' => 'vitaly.orloff', 'sizes' => array('XXS', 'XL'), 'cache' => TRUE));

$photos = $fotki->get_albumPhotos($_GET['id']);

$json = array();

foreach ($photos as $key => $photo)
{
    $json[$key]->image = $photo['images']['XL'];
    $json[$key]->thumb = $photo['images']['XXS'];
    $json[$key]->title = $photo['title'];
}
echo json_encode($json);