<?
include('../source/YaFotki.php');

$fotki = new YaFotki(array('login' => 'vitaly.orloff', 'cache' => TRUE));

$albums = $fotki->get_albums();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<meta http-equiv="content-type" content="text/html;charset=utf-8" />
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
		<link rel="Stylesheet" type="text/css" href="http://www.smoothdivscroll.com/css/smoothDivScroll.css" /> 
		<script src="http://www.smoothdivscroll.com/js/jquery.ui.widget.js" type="text/javascript"></script> 
	    <script src="http://www.smoothdivscroll.com/js/jquery.smoothDivScroll-1.1-min.js" type="text/javascript"></script>
		<script src="src/galleria.js"></script>
		
		<title>Orloff Vitaly Gallery</title>
		<style type="text/css" media="screen"><!--
			*{margin:0;padding:0}
			body{font:12px/1.3  arial,sans-serif; color:#000; background:#f7f7f7;}   
		    .out{background:#fff;}
			a img,fieldset{border:none}
			.pda, .print, legend{display:none}
			a{color:#0172c3}
			a:visited{color:#2787cc;}
			a:hover{color:#025189}
			.clr{clear:both; height:1px; font-size:1px; line-height:1px;}
	
			 html, body {
          width: 100%;
          height: 100%;
          padding: 0;
          margin: 0;
        }
        
            #galleria{height:550px;}
			
			body{background-image: url(img/stripe.png); min-width: 1000px}
			
			#outline {position: relative; height: 100%; width: 1000px; margin: 0px auto 0; }
			
			#header {height:154px; width:100%; position: relative; padding-top: 10px;}
			
			#logo {cursor:pointer; background-image: url(img/logo.png); width:410px; height:115px; background-position: -51px -50px; background-repeat: no-repeat;}
			
			#title { width: 800px; top: 100px; position: absolute; visibility: visible; }
			
			#menu {width: 100%; height: 37px; overflow: hidden;}
			
			.line{height: 8px; background-color: #CFCFFC;}
			
			p { color: #666; font-size: 16px; font-family: "Lucida Grande", Arial, sans-serif; font-weight: normal; margin-top: 0; }
			h1 { color: #778fbd; font-size: 20px; font-family: "Lucida Grande", Arial, sans-serif; font-weight: 500; line-height: 32px; margin-top: 4px; }
			h2 { color: #778fbd; font-size: 18px; font-family: "Lucida Grande", Arial, sans-serif; font-weight: normal; margin: 0.83em 0 0; }
			h3 { color: #666; font-size: 60px; font-family: "Lucida Grande", Arial, sans-serif; font-weight: bold; text-align: center; letter-spacing: -1px; width: auto; }
			h4 { font-weight: bold; text-align: center; margin: 1.33em 0; }
			a { color: #666; text-decoration: underline; }
			
			#menu ul {display: block; list-style: none; height: 37px; overflow: hidden;}
			#menu li {float: left;}
			#menu a {background:#EDF2D5; display: block;float: left;font: bold 15px Arial, Helvetica, sans-serif;color: #95969A;text-decoration: none;padding: 10px;margin: 0 8px 0 0;}
			#menu a.active {color: #EDF2D5; background: #FDCB08;}
			#menu a:hover { color: #edf2d5; background: #95969a; }
			
			#nav {display:none; padding:15px;}
			#nav a{text-decoration: none; padding-right:10px;}
			
			#content{position: absolute;
          overflow: auto;
          width: 100%;
          top: 170px;
          left: 0;
          bottom: 30px;
          background: white;}
			
			#footer{height: 30px; width:100%; background:#EDF2D5; position: absolute;
          width: 100%;
          left: 0;
          bottom: 0;}
			#footer .copy {text-align: right; float: right; padding: 7px;}
			
		#makeMeScrollable
		{
			width:100%;
			height: 37px;
			position: relative;
		}	
		
		#js_gallery{display: none;}
		#albums_grid{display: none;}
		
		
		#albums_grid .album {cursor:pointer; background: #EDF2D5; padding:10px; float: left; margin: 10px; width:150px; height:170px; position: relative; text-align: center;}
		#albums_grid .album div {width:100%; position: relative;}
		#albums_grid .album img {}
		#albums_grid .album:hover{background: #95969a;}
			
		--></style>
	</head>

	<body>
	    <a href="https://github.com/orloffv/phpYaFotki" target="_blank"><img src="http://quickribbon.com/ribbon/2011/02/4fa2603264f9864b5b328e3e25067ea0.gif" border="0" width="150" height="150" style="position:absolute; right:0px; top:0px; margin:0px; padding:0px"></a>
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
            					<?php foreach ($albums as $album):?>
                					<li><a album_id="<?=$album['id']?>" id="menu_<?=$album['id']?>" href=#><?=$album['title']?></a></li>
            					<?php endforeach;?>
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

<script>    
    $(document).ready(function() {
        Galleria.loadTheme('src/themes/classic/galleria.classic.js');
        hash = document.location.hash;
        temp_hash = hash.split('=');
        
        $("div#makeMeScrollable").smoothDivScroll();
        
        if (temp_hash[0] == '#id')
        {
            show_gallery(temp_hash[1]);
            $("#menu a[album_id='"+temp_hash[1]+"']").addClass('active');
            
            number = $("#menu a[album_id='"+temp_hash[1]+"']").index("#menu li a")+1;
            
            $("div#makeMeScrollable").smoothDivScroll("moveToElement", "number", number);
        }
        else
        {
            draw_albums_grid();
        }
    });
    
    function draw_albums_grid()
    {
        $.get('ajax.php', function(data) {
            var $div = $("#albums_grid");
            $div.show();
            $.each(data, function(index, value) {
                $div.append('<div album_id="'+value.id+'" class="album"><div>'+value.title+'</div><img src="'+value.image+'"></div>');
            });
            
        }, "json");
    }
    
    
    function show_gallery(id)
    {
        document.location.hash = "id="+id;
        $("#js_gallery").show();
        $("#albums_grid").hide();
        
        $("#menu a").removeClass('active');
        $("#menu_"+id).addClass('active');
        
        $.get('ajax.php?id='+id, function(data) {
                $('#galleria').galleria({
                    data_source: data, // add the fotki.yandex data
                    preload:1,
                    extend: function() {
                        var gallery = this; // save the scope
                        $('#nav a').click(function(e) {
                            e.preventDefault(); // prevent default actions on the links
                        })
                        // attach gallery methods to links:
                        $('#g_prev').click(function() {
                            gallery.prev();
                        });
                        $('#g_next').click(function() {
                            gallery.next();
                        });
                        $('#g_play').click(function() {
                            gallery.play();
                        });
                        $('#g_pause').click(function() {
                            gallery.pause();
                        });
                        $('#g_fullscreen').click(function() {
                            gallery.enterFullscreen();
                        });
                    }
                });
        }, "json");
        
        $("#nav").show();
    }
    
    $("#menu a").click(function(){
        var id= $(this).attr('album_id');
        show_gallery(id);
                
        return false;
    });
    
    $("#albums_grid .album").live('click', function() {
        var id= $(this).attr('album_id');
        show_gallery(id);
                
        return false;
    });
    
</script>

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