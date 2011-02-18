<?
include('source/YaFotki.php');

$fotki = new YaFotki(array('login' => 'vitaly.orloff', 'sizes' => array('XXS')));

var_dump($fotki->get_albums());

?>