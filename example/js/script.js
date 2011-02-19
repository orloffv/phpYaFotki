$(document).ready(function() {
    Galleria.loadTheme('vendor/galleria/themes/classic/galleria.classic.js');
    hash = document.location.hash;
    temp_hash = hash.split('=');
        
    $("div#makeMeScrollable").smoothDivScroll();
        
    if (temp_hash[0] == '#id')
    {
        show_gallery(temp_hash[1], true);
    }
    else
    {
        draw_albums_grid();
    }
    
    $("#menu a").click(function(){
        var id= $(this).attr('album_id');
        show_gallery(id);
                
        return false;
    });
    
    $("#albums_grid .album").live('click', function() {
        var id= $(this).attr('album_id');
        show_gallery(id, true);
                
        return false;
    });
});
    
function draw_albums_grid()
{
    $.get('ajax.php', function(data) {
        var $div = $("#albums_grid");
        $div.show();
        $.each(data, function(index, value) {
            $div.append('<div album_id="'+value.id+'" class="album"><div>'+value.title_path+'</div><img src="'+value.image+'"></div>');
        });
                
        $div.append('<div class="clr"></div>');
            
    }, "json");
}
    
    
function show_gallery(id, scroll)
{
    scroll = scroll || false;
        
    document.location.hash = "id="+id;
    $("#js_gallery").show();
    $("#albums_grid").hide();
        
    $("#menu a").removeClass('active');
    $("#menu_"+id).addClass('active');
        
    if (scroll)
    {
        number = $("#menu a[album_id='"+id+"']").index("#menu li a")+1;
            
        $("div#makeMeScrollable").smoothDivScroll("moveToElement", "number", number);
    }
        
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
    
