jQuery(document).ready(function(){ 
	"use strict";
	
	jQuery('#horizontal_gallery_wrapper tbody tr td .gallery_image_wrapper').children('.horizontal_gallery_img').each(function(index, value)
	{
	   	var calScreenHeight = jQuery(window).height();
	   	var calGalleryHeight = 500;
	   	
	   	if(calScreenHeight <= 768 && calScreenHeight > 480)
		{
			calGalleryHeight = 400;
		}
		else if(calScreenHeight <= 480)
		{
			calGalleryHeight = 300;
		}
	   	
	   	jQuery(this).css('height', calGalleryHeight+'px');
	    jQuery(this).parent().addClass('visible');
	});
	
	var calScreenWidth = jQuery(window).width();
	
	jQuery('#horizontal_gallery').imagesLoaded(function(){
		if(calScreenWidth >= 480)
		{
			jQuery(this).addClass('visible');
		}
	});
	
	jQuery(window).resize(function() {
		jQuery('#horizontal_gallery_wrapper tbody tr td .gallery_image_wrapper').children('.horizontal_gallery_img').each(function(index, value)
		{
		   	var calScreenHeight = jQuery(window).height();
		   	var calGalleryHeight = 500;
		   	
		   	if(calScreenHeight <= 768 && calScreenHeight > 480)
			{
				calGalleryHeight = 400;
			}
			else if(calScreenHeight <= 480)
			{
				calGalleryHeight = 300;
			}
		   	
		   	jQuery(this).css('height', calGalleryHeight+'px');
		    jQuery(this).parent().addClass('visible');
		});
	});
	
});