<?
include('../source/YaFotki.php');

$fotki = new YaFotki(array('login' => 'vitaly.orloff', 'cache' => TRUE));

$albums = $fotki->get_albums();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="content-type" content="text/html;charset=utf-8" />
        
        <script src="http://yandex.st/jquery/1.4.2/jquery.min.js"></script>
        <script src="http://yandex.st/jquery-ui/1.8.0/jquery.ui.widget.min.js" type="text/javascript"></script>
        
        <script src="vendor/smoothDivScroll/js/jquery.smoothDivScroll-1.1-min.js" type="text/javascript"></script>
        <link rel="Stylesheet" type="text/css" href="vendor/smoothDivScroll/css/smoothDivScroll.css" /> 
        
        <script src="vendor/galleria/galleria.js"></script>
        
        <link rel="Stylesheet" type="text/css" href="css/style.css" />
        <script src="js/script.js"></script>

        <title>Orloff Vitaly Gallery</title>
    </head>

    <body>
        <a href="https://github.com/orloffv/phpYaFotki" target="_blank">
            <img src="img/ribbon.gif" width="150" height="150" style="position:absolute; right:0px; top:0px; margin:0px; padding:0px">
        </a>
        <div id="outline">
            <div id="header">
                <a href="index.php"><div id="logo"></div></a>
                <div id="menu">
                    <ul>
                        <div id="makeMeScrollable"> 
                            <div class="scrollingHotSpotLeft"></div> 
                            <div class="scrollingHotSpotRight"></div> 
                            <div class="scrollWrapper"> 
                                <div class="scrollableArea"> 		
                                    <?php foreach ($albums as $album): ?>
                                        <li><a album_id="<?= $album['id'] ?>" id="menu_<?= $album['id'] ?>" href=#><?= $album['title_path'] ?></a></li>
                                    <?php endforeach; ?>
                                </div> 
                            </div> 
                        </div> 
                    </ul>	
                </div>
                <div class="line"></div>
            </div>
            <div id="content">
                <div id="js_gallery">
                    <div id="galleria"></div>
                    <p id="nav">
                        <a id="g_prev" href="#">Назад</a> 
                        <a id="g_next" href="#">Вперед</a> 
                        <a id="g_play" href="#">Play</a> 
                        <a id="g_pause" href="#">Пауза</a> 
                        <a id="g_fullscreen" href="#">На весь экран</a>
                    </p>
                </div>
                <div id="albums_grid">
                </div>
            </div>
            <div id="footer">
                <span class="copy">&copy; <a href="mailto:orloff.v@gmail.com">orloff.v@gmail.com</a></span>
            </div>
        </div>

    </body>

</html>

<!-- Yandex.Metrika counter -->
<div style="display:none;"><script type="text/javascript">
    (function(w, c) {
        (w[c] = w[c] || []).push(function() {
            try {
                w.yaCounter4553848 = new Ya.Metrika(4553848);
                yaCounter4553848.clickmap(true);
                yaCounter4553848.trackLinks(true);
        
            } catch(e) { }
        });
    })(window, 'yandex_metrika_callbacks');
    </script></div>
<script src="//mc.yandex.ru/metrika/watch.js" type="text/javascript" defer="defer"></script>
<noscript><div style="position:absolute"><img src="//mc.yandex.ru/watch/4553848" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->