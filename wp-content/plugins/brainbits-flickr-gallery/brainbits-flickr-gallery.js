var flickrKey = 'bb15f0686a6ee0f73865e854366a92c0';
var flickrSecret = 'bd369c18494f3980';

var brainbitsFlickrGallery = {
	/* save the last photo displayed */
	lastphoto: null,

	/* save the last photoset displayed */
	lastphotoset: null,

	/* photosets cache */
	photosets: [],

	/* photos cache */
	photos: [],

	/* load photo sets into photosets list */
	photoSetsLoad: function(userID){
		var req = "http://api.flickr.com/services/rest/?method=flickr.photosets.getList&format=json&api_key="+flickrKey+"&user_id="+userID+"&jsoncallback=?";
		jQuery.getJSON(req, function(data){
			if (data.stat == "ok"){
				jQuery.each(data.photosets.photoset, function(i, photoset){
					brainbitsFlickrGallery.photosets[i] = photoset;

					var img = new Image();
					jQuery('.brainbits-flickrgallery-photosets ul').append(jQuery(img));
					jQuery(img).wrap('<li />');
					jQuery(img).attr('src', 'http://farm'+photoset.farm+'.static.flickr.com/'+photoset.server+'/'+photoset.primary+'_'+photoset.secret+'_s.jpg');
					jQuery(img).addClass('brainbits-flickrgallery-photoset-'+i);
					jQuery(img).attr('title', photoset.title._content);
					jQuery(img).attr('alt', photoset.title._content);
				});
				jQuery('.brainbits-flickrgallery-photosets ul').fadeIn('fast');
			}
		});
	},

	/* load photo stream into photos list and populate photostream thumbnail */
	photosLoadFromPublicPhotos: function(userID){
		if (brainbitsFlickrGallery.lastphotoset != userID){
			jQuery('.brainbits-flickrgallery-photoset-'+brainbitsFlickrGallery.lastphotoset).parent().removeClass('selected');
			jQuery('.brainbits-flickrgallery-publicphotos').parent().addClass('selected');
			brainbitsFlickrGallery.lastphotoset = userID;
			brainbitsFlickrGallery.lastphoto = null;
			var req = "http://api.flickr.com/services/rest/?method=flickr.people.getPublicPhotos&format=json&api_key="+flickrKey+"&user_id="+userID+"&extras=description&jsoncallback=?";
			var photoDisplayFired = false;
			jQuery.getJSON(req, function(data){
				if (data.stat == "ok"){
					jQuery('.brainbits-flickrgallery-photos ul').html("");
					jQuery('.brainbits-flickrgallery-photos h3').html("Photo Stream");
					jQuery.each(data.photos.photo, function(i, photo){
				
						if (i == 0){
							jQuery('.brainbits-flickrgallery-publicphotos').attr('src', 'http://farm'+photo.farm+'.static.flickr.com/'+photo.server+'/'+photo.id+'_'+photo.secret+'_s.jpg');
						}
				
						brainbitsFlickrGallery.photos[i] = photo;
						var img = new Image();
						jQuery('.brainbits-flickrgallery-photos ul').append(img);
						jQuery(img).wrap('<li />');
						jQuery(img).attr('src', 'http://farm'+photo.farm+'.static.flickr.com/'+photo.server+'/'+photo.id+'_'+photo.secret+'_s.jpg');
						jQuery(img).addClass('brainbits-flickrgallery-photo-'+i);
						jQuery(img).attr('title', photo.title._content);
						jQuery(img).attr('alt', photo.title._content);
						if (!photoDisplayFired){
							brainbitsFlickrGallery.photoDisplay(i);
							photoDisplayFired = 1;
						}
					});
				}
			});
		}
	},

	/* load photos into photos list */
	photosLoadFromPhotoSet: function(photoSetID){
		/* present selected photo with 1 opacity */
		if (photoSetID != brainbitsFlickrGallery.lastphotoset){
			jQuery('.brainbits-flickrgallery-publicphotos').parent().removeClass('selected');
			jQuery('.brainbits-flickrgallery-photoset-'+brainbitsFlickrGallery.lastphotoset).parent().removeClass('selected');
			jQuery('.brainbits-flickrgallery-photoset-'+photoSetID).parent().addClass('selected');
			brainbitsFlickrGallery.lastphotoset = photoSetID;
			brainbitsFlickrGallery.lastphoto = null;
			photoset = brainbitsFlickrGallery.photosets[photoSetID];
			var req = "http://api.flickr.com/services/rest/?method=flickr.photosets.getPhotos&format=json&api_key="+flickrKey+"&photoset_id="+photoset.id+"&extras=description&jsoncallback=?";
			var photoDisplayFired = 0;
			jQuery.getJSON(req, function(data){
				if (data.stat == "ok"){
					jQuery('.brainbits-flickrgallery-photos ul').html("");
					jQuery('.brainbits-flickrgallery-photos h3').html(brainbitsFlickrGallery.photosets[photoSetID].title._content);
					brainbitsFlickrGallery.photos = [];
					jQuery.each(data.photoset.photo, function(i, photo){
						brainbitsFlickrGallery.photos[i] = photo;
						var img = new Image();
						jQuery('.brainbits-flickrgallery-photos ul').append(img);
						jQuery(img).wrap('<li />');
						jQuery(img).attr('src', 'http://farm'+photo.farm+'.static.flickr.com/'+photo.server+'/'+photo.id+'_'+photo.secret+'_s.jpg');
						jQuery(img).addClass('brainbits-flickrgallery-photo-'+i);
						jQuery(img).attr('title', photoset.title._content);
						jQuery(img).attr('alt', photoset.title._content);
						if (!photoDisplayFired){
							brainbitsFlickrGallery.photoDisplay(i);
							photoDisplayFired = 1;
						}
					});
				}
			});
		}
	},

	/* display photo in main photo display */
	photoDisplay: function(photoID){
		if (brainbitsFlickrGallery.lastphoto != photoID){
			var photo = brainbitsFlickrGallery.photos[photoID];
			jQuery('.brainbits-flickrgallery-description p').hide().html(photo.description._content).fadeIn('fast');
			jQuery('.brainbits-flickrgallery-display img').css('opacity', 0);
			jQuery('.brainbits-flickrgallery-display img').attr('src', 'http://farm'+photo.farm+'.static.flickr.com/'+photo.server+'/'+photo.id+'_'+photo.secret+'.jpg');
			jQuery('.brainbits-flickrgallery-display img').removeClass(jQuery('.brainbits-flickrgallery-display img').className).addClass('display-'+photoID);
//			jQuery('.brainbits-flickrgallery-display img').removeClass('display-'+brainbitsFlickrGallery.lastphoto).addClass('display-'+photoID);
			jQuery('.brainbits-flickrgallery-display img').attr('title', photo.title._content);
			jQuery('.brainbits-flickrgallery-display img').attr('alt', photo.title._content);
			/* present selected photo with 1 opacity */
			jQuery('.brainbits-flickrgallery-photo-'+brainbitsFlickrGallery.lastphoto).parent().removeClass('selected');
			jQuery('.brainbits-flickrgallery-photo-'+photoID).parent().addClass('selected');
			brainbitsFlickrGallery.lastphoto = photoID;

			/* animated slider */
			var imagesize = jQuery('.brainbits-flickrgallery-photos img').outerWidth(true);
			var contentwidth = jQuery('.brainbits-flickrgallery-photos').find('img').length * imagesize;
			var viewportwidth = jQuery('.brainbits-flickrgallery-photos').width();
			var scroll = parseInt(jQuery('.brainbits-flickrgallery-photos ul').css('left'));
			var clickedpos = jQuery('.brainbits-flickrgallery-photo-'+photoID).position().left;
			var scrollto = Math.round(-(clickedpos - (viewportwidth / 2)));
//			alert(imagesize + ' ' + contentwidth + ' ' + (contentwidth + scrollto) + ' ' + viewportwidth);
			if (contentwidth > viewportwidth){
				jQuery('.brainbits-flickrgallery-photos ul').css('width', contentwidth);
			}
			if (contentwidth + scrollto < viewportwidth){
				scrollto = viewportwidth - contentwidth;
			}
			if (scrollto > 0){
				scrollto = 0;
			}
			jQuery('.brainbits-flickrgallery-photos ul').animate({'left': scrollto}, 500);
		}
	}
}

jQuery(document).ready(function(){
	/* try and number gallerys shown so we can show more than one gallery at a time */
/*	jQuery.each(jQuery.find('.brainbits-flickrgallery'), function(i, gallery){
		brainbitsFlickrGalleryID = 'brainbits-flickrgallery-'+i;
		jQuery(gallery).attr('id', brainbitsFlickrGalleryID);
	});
*/
	/* get the flickrid from the magic userid html attribute of the master ul */
	var flickrUserId = jQuery('.brainbits-flickrgallery').attr('userid');

	/* load photos from the user's public photoset */
	brainbitsFlickrGallery.photosLoadFromPublicPhotos(flickrUserId);
	/* load the user's photosets into the photoset thumbnails */
	brainbitsFlickrGallery.photoSetsLoad(flickrUserId);

	/* display the next photo in the set if the big display photo is clicked */
	jQuery('.brainbits-flickrgallery-display img').live('click', function(e){
		current = parseInt(e.target.className.slice(8));
		if (typeof(brainbitsFlickrGallery.photos[current+1]) != "undefined")
			brainbitsFlickrGallery.photoDisplay(current+1);
		else
			brainbitsFlickrGallery.photoDisplay(0);
	});
	jQuery('.brainbits-flickrgallery-display img').load(function(e){
		jQuery('.brainbits-flickrgallery-display img').animate({'opacity': 1}, 250);
	});
	
	/* load photos from a photoset if its thumbnail is clicked */
	jQuery('.brainbits-flickrgallery-photosets').find('img:not(.brainbits-flickrgallery-publicphotos)').live('click',function(e){
		/* animated slider */
		var imagesize = jQuery('.brainbits-flickrgallery-photosets img').outerWidth(true);
		var contentwidth = jQuery('.brainbits-flickrgallery-photosets').find('img').length * imagesize;
		var viewportwidth = jQuery('.brainbits-flickrgallery-photosets').width();
		var scroll = parseFloat(jQuery('.brainbits-flickrgallery-photosets ul').css('left'));
		var clickedpos = jQuery(e.target).position().left;
		var scrollto = Math.round(-(clickedpos - (viewportwidth / 2)));

		if (contentwidth > viewportwidth){
			jQuery('.brainbits-flickrgallery-photosets ul').css('width', contentwidth);
		}
		if (contentwidth + scrollto < viewportwidth){
			scrollto = viewportwidth - contentwidth;
		}
		if (scrollto > 0){
			scrollto = 0;
		}
		jQuery('.brainbits-flickrgallery-photosets ul').animate({'left': scrollto}, 500);

		/* reset the photos slider */
		jQuery('.brainbits-flickrgallery-photos ul').css('left', 0);

		brainbitsFlickrGallery.photosLoadFromPhotoSet(e.target.className.slice(33));
	});

	/* display the users public photostream if the photostream thumbnail is clicked */
	jQuery('.brainbits-flickrgallery-publicphotos').live('click', function(e){
		brainbitsFlickrGallery.photosLoadFromPublicPhotos(flickrUserId);
	});

	/* display the photo in the loaded set that is clicked */
	jQuery('.brainbits-flickrgallery-photos').live('click',function(e){
		brainbitsFlickrGallery.photoDisplay(e.target.className.slice(30));
	});
});
