jQuery.noConflict();

jQuery(document).ready(function() {

    jQuery( document ).on('click','.tw-remove-tweet-template',function(e){

        e.preventDefault();

        jQuery( this ).parent().remove();

    });

    // Check how many templates are there
    var no_of_templates = jQuery('.tweet-template-item').length;

    // Adjust index
    var i = no_of_templates != null ? no_of_templates : 0;

    jQuery( '#add-tweet-template' ).click( function(e) {

        e.preventDefault();

        // Append a tweet template
        jQuery('.tw-tweet-templates').append( tweet_template );

        // Fix name indexing for jQuery validator plugin. It doesn't like array names with no specified index e.g. name[]
        jQuery('.tw-tweet-templates > div:last-of-type textarea').attr('name','twp_post_templates['+i+']');
        
        // Adjust initial counter if image has been attached
        var exclude_tweet_image = jQuery( '.exclude-tweet-image').is(':checked') ? true : false;
        if( ! exclude_tweet_image )
            jQuery('.tw-tweet-templates > div:last-of-type textarea').next('.twp-counter').text(23);

        i++;                                                          
        // reinitialise autosize for textareas
        jQuery('.tweet-template-textarea').autosize();

    } );

    jQuery('.tw-learn-more').click(function(e){

        e.preventDefault();

        var el = jQuery('#' + jQuery(this).data('content') );

        el.slideToggle();

    });

    // Is 140 chars
    jQuery.validator.addMethod(
        "tweetFit", 
        function(value, element) {
            var has_tweet_image = jQuery( '.exclude-tweet-image').is(':checked') ? false : true;
            return twp_character_counter( value, has_tweet_image ) > 140 ? false : true;
        }, 
        "Sorry, amigo. Maximum 140 characters."
    );

    // Has post url
    jQuery.validator.addMethod(
        "tweetURL", 
        function(value, element) {
            if( /{{URL}}/i.test(value) ) {
                return true;
            }

            return false;
        }, 
        "Please add {{URL}} tag to your template."
    );
    
    // ...

	// Hook the script only to post types that are used by the plugin
    if( typenow == 'undefined' )
        var typenow;
    
	if( jQuery.inArray( typenow, TWAJAX.post_types ) !== -1 ) {
    
	    // Some WP hacking to skip the bug with posts not being published (just saved as drafts)
	    // more: http://wordpress.stackexchange.com/questions/119814/validating-custom-meta-boxes-with-jquery-results-in-posts-being-saved-as-draft-i

	    var form = jQuery("#post");
	    var send = form.find("#publish");
    
	    send.addClass('tw-submit');

	    jQuery('.tw-submit').click(function(e){

	        form.validate();

	        jQuery('.tweet-template-textarea').each(function(){

	            jQuery(this).rules("add",{
	                required : true,
	                tweetFit : true,
	                tweetURL : true
	            });

	        });
            
	        if(jQuery(form).valid()) {
	            jQuery("#publishing-action .spinner").show();
	            return true;
	        } else {
	            jQuery("#publishing-action .spinner").hide();
	            jQuery('html, body').animate({
	                scrollTop: jQuery(".tweet-template-textarea.error").offset().top - 30
	            }, 2000);
	        }

	        return false;

	    });
	
	}
    
    // ...
    
    jQuery( '#tos-enable' ).click(function() {
        
        if( jQuery(this).is(':checked') ) {
            
            jQuery( '#tos-enabled' ).show();
            
            // Set cursor at the end of value
            var el = jQuery('textarea[name=twp_tos]').get(0);
            var elemLen = el.value.length;

            el.selectionStart = elemLen;
            el.selectionEnd = elemLen;
            el.focus();
            
        } else {
            
            jQuery( '#tos-enabled' ).hide();
            
        }
        
    });
    
    // ...
    
    jQuery( '#tos-template' ).change(function() {
        
        jQuery('textarea[name=twp_tos]').val(jQuery(this).val()).autosize();
        
        // Set cursor at the end of value
        var el = jQuery('textarea[name=twp_tos]').get(0);
        var elemLen = el.value.length;

        el.selectionStart = elemLen;
        el.selectionEnd = elemLen;
        el.focus();
        
        twp_refresh_counters();

    });
    
});