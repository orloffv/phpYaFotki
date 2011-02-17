<?
include('Yfotki.php');

$fotki = new yFotki(array('login' => 'vitaly.orloff', 'sizes' => array('XXS')));

var_dump($fotki->get_albums());

?>