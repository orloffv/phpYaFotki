<?
include('source/YaFotki.php');

var_dump(YaFotki::instance(array('login' => 'vitaly.orloff', 'password' => 'vampir', 'sizes' => array('XXS')))->get_albums('name'));
?>