/*!
 * Galleria Fotki Plugin v 0.0.1-dev
 * http://turgumbaev.com
 * http://fotki.yandex.ru
 * http://api.yandex.ru/fotki/
 *
 * Copyright 2010 - *, 2BJ
 * Licensed under the MIT license.
 */

/**
 * TODO:
 * - paging
 * - albums
 * - original link
 */
(function() {
   
var G = window.Galleria; 
if (typeof G == 'undefined') {
    return;
}

var F = G.Fotki = function(user) {
    if (!user) {
        G.raise('Pls, set user name');
    }
    
	this.user = user;
	this.api_url = 'http://api-fotki.yandex.ru/api/users/' + user + '/';
	this.params = {
        format : 'json',
        callback : '?'
	};
};

F.prototype = {
    request: function(uri, callback) {
	    var url = this.api_url + uri + '/?';
		var params = [];
		
		jQuery.each(this.params, function(key, value) {
			params.push(key + '=' +value);
		});
		
		url += params.join('&');
		
		jQuery.getJSON(url, callback);
	},
	getPhotos: function(what, callback) {		
		this.request(what, function(res){
		    var sizes = ['XL', 'L', 'M', 'S', 'XS', 'XXS', 'XXXS'];
		    var obj = [];
		    if (res.entries) {
		        var len = res.entries.length;
		        
		        for (var i=0; i<len; i++) {
		            var photo = res.entries[i];
		            
                    var image = '';
                    
	                for (var j=0; j<sizes.length; j++ ) {
                        if (photo.img[sizes[j]]) {
                            image = photo.img[sizes[j]].href;
                            break;
                        }	                    
	                }
		            
		            var item = {
        				thumb: photo.img.XXXS.href,
        				image: image,
        				title: photo.title
        			};
        			
        			Array.prototype.push.call(obj, item);
		        }
		    }
	        callback.call(this, obj);
		});
	},
	getAllPhotos: function(callback) {
		this.getPhotos('photos', callback);
	},
	getAlbumPhotos: function(album, callback) {
		this.getPhotos('album/'+album+'/photos/', callback);
	},
	getAllAlbums:function(callback) {
		this.request('albums', function(res){
			var obj = [];
			if (res.entries) {
				for(i in res.entries)
				{
					if ( ! res.entries[i].protected && res.entries[i].imageCount){
						var link = res.entries[i].links.photos;
						link = link.split('/');
						var uri = link[7];
						var item = {
        					link: uri,
        					title: res.entries[i].title,
        					updated: res.entries[i].updated
 			       		};
 			       		
 			       		Array.prototype.push.call(obj, item);
					}
					
				}
			}
			
			callback.call(this, obj);
			
		});
	}
}

})();
