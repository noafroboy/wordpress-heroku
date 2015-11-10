function removeSortRecord(thisParentLi, targetObj)
{
	jQuery('li#'+thisParentLi+'_sort').remove();
	var order = jQuery('#'+targetObj).sortable('toArray');
    jQuery('#'+targetObj+'_data').val(order);
}

jQuery(document).ready(function(){

    jQuery('#current_sidebar li a.sidebar_del').on( 'click', function(){
    	if(confirm('Are you sure you want to delete this sidebar? (this can not be undone)'))
    	{
    		sTarget = jQuery(this).attr('href');
    		sSidebar = jQuery(this).attr('rel');
    		objTarget = jQuery(this).parent('li');
    		
    		jQuery.ajax({
        		type: 'POST',
        		url: sTarget,
        		data: 'sidebar_id='+sSidebar,
        		success: function(msg){ 
        			objTarget.fadeOut();
        			setTimeout(function() {
                      location.reload();
                    }, 1000);
        		}
        	});
    	}
    	
    	return false;
    });
    
    jQuery('a.image_del').on( 'click', function(){
    	if(confirm('Are you sure you want to delete this image? (this can not be undone)'))
    	{
    		sTarget = jQuery(this).attr('href');
    		sFieldId = jQuery(this).attr('rel');
    		objTarget = jQuery('#'+sFieldId+'_wrapper');
    		
    		jQuery.ajax({
        		type: 'POST',
        		url: sTarget,
        		data: 'field_id='+sFieldId,
        		success: function(msg){ 
        			objTarget.fadeOut();
        			jQuery('#'+sFieldId).val('');
        		}
        	});
    	}
    	
    	return false;
    });
    
    jQuery('#pp_advance_clear_cache').on( 'click', function(){
    	if(confirm('Are you sure you want to clear all cache'))
    	{
    		sTarget = jQuery(this).attr('href');
    		
    		jQuery.ajax({
        		type: 'POST',
        		url: sTarget,
        		data: 'method=clear_cache',
        		success: function(msg){ 
        			jQuery('#pp_advance_clear_cache').html('Cache files were successfully cleared.');
        			jQuery('#pp_advance_clear_cache').attr("disabled", "disabled");
        		}
        	});
    	}
    	
    	return false;
    });
    
    jQuery('#pp_panel a').on( 'click', function(){
    	jQuery('#pp_panel a').removeClass('nav-tab-active');
    	jQuery(this).addClass('nav-tab-active');
    	
    	jQuery('.rm_section').css('display', 'none');
    	jQuery(jQuery(this).attr('href')).fadeIn();
    	jQuery('#current_tab').val(jQuery(this).attr('href'));
    	
    	return false;
    });
    
    jQuery('.iphone_checkboxes').iCheck({
		    checkboxClass: 'icheckbox_flat-green',
		    radioClass: 'iradio_flat-green'
		  });
		
	jQuery('.rm_section').css('display', 'none');
    
    if(self.document.location.hash != '')
	{
	    jQuery('html, body').animate({scrollTop:0}, 'fast');
	    jQuery('.nav-tab').removeClass('nav-tab-active');
	    jQuery('a'+self.document.location.hash+'_a').addClass('nav-tab-active');
	    jQuery('div'+self.document.location.hash).css('display', 'block');
	    jQuery('#current_tab').val(self.document.location.hash);
	}
	else
	{
	    jQuery('div#pp_panel_general').css('display', 'block');
	}
    
    jQuery( ".pp_sortable" ).sortable({
	    placeholder: "ui-state-highlight",
	    create: function(event, ui) { 
	    	myCheckRel = jQuery(this).attr('rel');
	    
	    	var order = jQuery(this).sortable('toArray');
        	jQuery('#'+myCheckRel).val(order);
	    },
	    update: function(event, ui) {
	    	myCheckRel = jQuery(this).attr('rel');
	    
	    	var order = jQuery(this).sortable('toArray');
        	jQuery('#'+myCheckRel).val(order);
	    }
	});
	jQuery( ".pp_sortable" ).disableSelection();
	
	jQuery(".pp_checkbox input").change(function(){
	    myCheckId = jQuery(this).val();
	    myCheckRel = jQuery(this).attr('rel');
	    myCheckTitle = jQuery(this).attr('alt');
	    
	    if (jQuery(this).is(':checked')) { 
	    	jQuery('#'+myCheckRel).append('<li id="'+myCheckId+'_sort" class="ui-state-default">'+myCheckTitle+'</li>');
	    } 
	    else
	    {
	    	jQuery('#'+myCheckId+'_sort').remove();
	    }

	    var order = jQuery('#'+myCheckRel).sortable('toArray');

        jQuery('#'+myCheckRel+'_data').val(order);
	});
	
	jQuery(".pp_sortable_button").on( 'click', function(){
	    var targetSelect = jQuery('#'+jQuery(this).attr('data-rel'));
	    
	    myCheckId = targetSelect.find("option:selected").val();
	    myCheckRel = targetSelect.find("option:selected").attr('data-rel');
	    myCheckTitle = targetSelect.find("option:selected").attr('title');

	    if (jQuery('#'+myCheckRel).children('li#'+myCheckId+'_sort').length == 0)
	    {
	    	jQuery('#'+myCheckRel).append('<li id="'+myCheckId+'_sort" class="ui-state-default"><div class="title">'+myCheckTitle+'</div><a data-rel="'+myCheckRel+'" href="javascript:removeSortRecord(\''+myCheckId+'\', \''+myCheckRel+'\');" class="remove">x</a><br style="clear:both"/></li>');
	    	//jQuery('#'+myCheckId+'_sort').remove();
	    	
	    	var order = jQuery('#'+myCheckRel).sortable('toArray');
        	jQuery('#'+myCheckRel+'_data').val(order);
        }
        else
        {
        	alert('You have already added "'+myCheckTitle+'"');
        }
	});
	
	jQuery(".pp_sortable li a.remove").on( 'click', function(){
	    jQuery(this).parent('li').remove();
	    var order = jQuery('#'+jQuery(this).attr('data-rel')).sortable('toArray');
        jQuery('#'+jQuery(this).attr('data-rel')+'_data').val(order);
	});
    
    jQuery(".pp_font").change(function(){
    	var valueElement = jQuery(this).data('value');
    	var sampleElement = jQuery(this).data('sample');
    	jQuery("#"+valueElement).attr('value', jQuery(this).children("option:selected").attr('data-family'));
    
    	var ppGGFont = 'http://fonts.googleapis.com/css?family='+jQuery(this).val();
    	jQuery('head').append('<link rel="stylesheet" id="google_fonts_'+valueElement+'" href="'+ppGGFont+'" type="text/css" media="all">');
    	
    	if(jQuery(this).children("option:selected").attr('data-family') != '')
    	{
    		jQuery('#'+sampleElement).css('font-family', '"'+jQuery(this).children("option:selected").attr('data-family')+'"');
    	}
    });
        
    var formfield = '';
	
	jQuery('.metabox_upload_btn').on( 'click', function() {
	    jQuery('.fancybox-overlay').css('visibility', 'hidden');
	    jQuery('.fancybox-wrap').css('visibility', 'hidden');
     	formfield = jQuery(this).attr('rel');
	    
	    var send_attachment_bkp = wp.media.editor.send.attachment;
	    wp.media.editor.send.attachment = function(props, attachment) {
	     	jQuery('#'+formfield).attr('value', attachment.url);
	
	        wp.media.editor.send.attachment = send_attachment_bkp;
	        jQuery('.fancybox-overlay').css('visibility', 'visible');
	     	jQuery('.fancybox-wrap').css('visibility', 'visible');
	    }
	
	    wp.media.editor.open();
     	return false;
    });
    
    jQuery("input.upload_text").on( 'click', function() { jQuery(this).select(); } );
	
	jQuery("#ppb_sortable_add_button").on( 'click', function(){
	    var targetSelect = jQuery('#ppb_options');
	    var targetTitle = jQuery('#ppb_options_title');
	    var targetType = jQuery('#ppb_module_'+targetSelect.val()).data('type');
	    
	    randomId = jQuery.now();
	    myCheckId = targetSelect.val();
	    myCheckTitle = targetTitle.val();
	    postType = jQuery('#ppb_post_type').val();
	    
	    if(typeof targetType === 'undefined'){
			targetType = 'module'; 
		};
	    
	    //If select content builder module
	    if(myCheckId != '' && targetType == 'module')
	    {
	    	var builderItemData = {};
	    	builderItemData.id = randomId;
	    	builderItemData.shortcode = myCheckId;
	    	builderItemData.ppb_text_title = myCheckTitle;
	    	builderItemData.ppb_text_content = '';
	    	builderItemData.ppb_header_content = '';
	    	var builderItemDataJSON = JSON.stringify(builderItemData);
	
	    	builderItem = '<li id="'+randomId+'" class="ui-state-default one '+myCheckId+'" data-current-size="one">';
	    	builderItem+= '<div class="size"><a href="javascript:;" class="ppb_plus button">+</a>';
	    	builderItem+= '<a href="javascript:;" class="ppb_minus button">-</a></div>';
	    	builderItem+= '<div class="title">'+myCheckTitle+'</div>';
	    	builderItem+= '<a href="javascript:;" class="ppb_remove">x</a>';
	    	
	    	var editURL = tgAjax.ajaxurl+'?action=pp_ppb&ppb_post_type='+postType+'&shortcode='+myCheckId+'&rel='+randomId+'&width=800&height=900';
	    	
	    	builderItem+= '<a data-rel="'+randomId+'" href="'+editURL+'" class="thickbox ppb_edit"></a>';
	    	builderItem+= '<input type="hidden" class="ppb_setting_columns" value="one_fourth"/>';
	    	builderItem+= '</li>';
	
	    	jQuery('#content_builder_sort').append(builderItem);
	    	jQuery('#content_builder_sort').removeClass('empty');
	    	ppbBuildItem();
	    	jQuery('#'+randomId).data('ppb_setting', builderItemDataJSON);
	    	
	    	var prev1Li = jQuery('#'+randomId).prev();
	        var prev2Li = prev1Li.prev();
	        var prev3Li = prev2Li.prev();
	        
	        if(prev1Li.attr('data-current-size')=='one_third' && prev2Li.attr('data-current-size')=='one_third')
	    	{
	        	jQuery('#'+randomId).attr('data-current-size', 'one_third last');
	        	jQuery('#'+randomId).find('.ppb_setting_columns').attr('value', 'one_third last');	
	
	    	}
	    	
	    	if(myCheckId!='ppb_divider' && myCheckId!='ppb_empty_line')
	    	{
	    		jQuery('#'+randomId).find('.ppb_edit').trigger('click');
	    	}
	    }
	    else if(myCheckId != '' && targetType == 'demo_page')
	    {
	    	if(confirm('Are you sure you want to import this demo page. All current content builder data for this page will be overwrite? (this can not be undone)'))
			{
			    jQuery('#ppb_import_current').val(1);
			    
			    var demoPageFile = jQuery('#ppb_module_'+targetSelect.val()).data('file');
			    
			    jQuery('#ppb_import_demo_file').val(demoPageFile);
			    jQuery('#ppb_import_current_button').trigger('click');
			}
	    }
	    
	    return false;
	});
		
	jQuery( ".ppb_sortable" ).disableSelection();
	
	jQuery(window).scroll(function(){
	    if(jQuery(this).scrollTop() >= 100){
	    	jQuery('.header_wrap').addClass('fixed');
	    }
	    else if(jQuery(this).scrollTop() < 100)
	    {
	        jQuery('.header_wrap').removeClass('fixed');
	    }
	});
	
	jQuery('input[title!=""]').hint();
	jQuery('textarea[title!=""]').hint();
	
	jQuery('#pp_import_default_button').on( 'click', function(){
	    jQuery('#pp_import_default').val(1);
	});
	
	jQuery('#import_demo li').on( 'click', function(){
	    jQuery('#import_demo li').removeClass('selected');
	    jQuery(this).addClass('selected');
	    
	    var selectedDemo = jQuery(this).data('demo');
	    jQuery('#pp_import_demo').val(selectedDemo);
	});
	
	jQuery('#import_demo_content li').on( 'click', function(){
	    jQuery('#import_demo_content li').removeClass('selected');
	    jQuery(this).addClass('selected');
	    
	    var selectedDemo = jQuery(this).data('demo');
	    jQuery('#pp_import_demo_content').val(selectedDemo);
	});
	
	jQuery('#pp_import_content_button').on( 'click', function(){
		if(jQuery('#pp_import_demo_content').val()=='')
		{
			alert('Please select demo content you want to import');
			return false;
		}
	
	    import_true = confirm('Are you sure to import demo content? it will overwrite the existing data');
        if(import_true == false) return;

        jQuery('.import_message').show();
        jQuery(this).hide();
       
        var data = {
            'action': 'grandblog_import_demo_content',
            'demo': jQuery('#pp_import_demo_content').val()
        };

        jQuery.post(ajaxurl, data, function(response) {
            jQuery('.import_message').html('<div class="import_message_success">All done. Have fun!</div>');
        });
	});
	
	jQuery('#pp_theme_go_update_bth').on( 'click', function(){
		update_true = confirm('Are you sure to update the theme?');
        if(update_true == false) return;

        jQuery('.update_message').show();
        jQuery(this).hide();
       
        var data = {
            'action': 'pp_update_theme'
        };

        jQuery.post(ajaxurl, data, function(response) {
            jQuery('.update_message').html('<div class="update_message_success">'+ response +'</div>');
        });
	});	
	
	//Custom functions for handle post options box
	var postType = jQuery('#post_ft_type').val();
	switch(postType) 
	{
	    case 'Vimeo Video':
	        jQuery('#post_option_post_ft_vimeo').show();
	        jQuery('#post_option_post_ft_gallery').hide();
	        jQuery('#post_option_post_ft_youtube').hide();
	    break;
	    
	    case 'Youtube Video':
	        jQuery('#post_option_post_ft_youtube').show();
	        jQuery('#post_option_post_ft_vimeo').hide();
	        jQuery('#post_option_post_ft_gallery').hide();
	    break;
	    
	    case 'Gallery':
	        jQuery('#post_option_post_ft_gallery').show();
	        jQuery('#post_option_post_ft_vimeo').hide();
	        jQuery('#post_option_post_ft_youtube').hide();
	    break;
	    
	    case 'Image':
	    	jQuery('#post_option_post_ft_gallery').hide();
	        jQuery('#post_option_post_ft_vimeo').hide();
	        jQuery('#post_option_post_ft_youtube').hide();
	    break;
	}
	
	jQuery('#post_ft_type').on( 'change', function(){
		var postType = jQuery(this).val();
		switch(postType) 
		{
		    case 'Vimeo Video':
	        jQuery('#post_option_post_ft_vimeo').show();
	        jQuery('#post_option_post_ft_gallery').hide();
	        jQuery('#post_option_post_ft_youtube').hide();
	    break;
	    
	    case 'Youtube Video':
	        jQuery('#post_option_post_ft_youtube').show();
	        jQuery('#post_option_post_ft_vimeo').hide();
	        jQuery('#post_option_post_ft_gallery').hide();
	    break;
	    
	    case 'Gallery':
	        jQuery('#post_option_post_ft_gallery').show();
	        jQuery('#post_option_post_ft_vimeo').hide();
	        jQuery('#post_option_post_ft_youtube').hide();
	    break;
	    
	    case 'Image':
	    	jQuery('#post_option_post_ft_gallery').hide();
	        jQuery('#post_option_post_ft_vimeo').hide();
	        jQuery('#post_option_post_ft_youtube').hide();
	    break;
		}
	});
});