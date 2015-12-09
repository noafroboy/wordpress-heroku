jQuery.noConflict();

// ...

jQuery(function(){
	
	/**
	 * Custom Tweet Metabox Counter and Parsing
	 * @since 1.0
	 */

    // Count characters and display on page load
    jQuery(window).load(function(){

        jQuery('.tweet-template-textarea').autosize();

    });
    
    // Update global variable holding title
    jQuery(document).on('keyup keydown','#title', function(e) {
        
        twp_template_tags.TITLE = jQuery(this).val();
        twp_refresh_counters();
        
    });
    
    // Update global variable holding permalink
    //setInterval(twp_refresh_permalink,3000);

    // Handle custom tweet text box input and update counter
    jQuery(document).on('keyup keydown','.tweet-template-textarea', function(e) {

       twp_refresh_counters();

    } );
    
    // Handle tweet image and update counter    
    jQuery(document).on('change','.exclude-tweet-image', function(e) {

        twp_refresh_counters();

    } );

	jQuery( "#tw-schedule label[for^=day]" ).click(function(){
		
		if( jQuery(this).find('input').is(':checked') ) {
			jQuery(this).addClass('active');
		} else {
			jQuery(this).removeClass('active');
		}
		
	});
    
    // ...
    
    jQuery('#add-new-time').click(function(e) {
        
        e.preventDefault();
       
        var template = jQuery('.time-template').html();
        var last_index = 0;
        
        if( jQuery('.times li').length != 0 ) { 
            last_index = jQuery('.times li').last().data('index');
            last_index++;
        }
        
        template = template.replace(/\[(\d+)\]/g,'['+last_index+']');
        
        console.log(template.match(/\[(\d+)\]/)[1]);
        
        jQuery('.times').append( '<li data-index="'+last_index+'">' + template + '</li>' );
        
    });
    
    // ...
    
    jQuery(document).on( 'click', '.remove-time', function(e) {
        
        e.preventDefault();
        
        jQuery(this).parent().remove();
        
    });
	
});

/*



*/

jQuery(function() {
    
    jQuery(document).ready(function() {
    
        jQuery( "#the-queue ul" ).sortable({
            handle : '.drag-handler',
            update : function() {
                
                 jQuery('#tw-saving-progress span').text('Saving...');
                
                var data = jQuery('#the-queue > ul').sortable('toArray');

                jQuery.post( 
                    ajaxurl, 
                    { 
                        action: 'save_queue', 
                        twnonce: TWAJAX.twNonce,
                        queue_order : data
                    }, 
                    function(response){
                        var data = jQuery.parseJSON(response);
                        if( data.response == 'ok' ) {
                            jQuery('#tw-saving-progress span').text('All Saved');
                        } else {
                            alert( "Couldn't save changes. Not sure why... Restored original queue!" );
                        }
                    }
                ); 
            }
        });
        
    });

    jQuery( ".post-header .title" ).click(function() {
        jQuery(this).parent().parent().find( ".post-content" ).toggle();
    });
    
    jQuery('#empty-queue-alert-hide').click(function(e){
        e.preventDefault();
        jQuery('#tw-empty-queue-alert').slideUp();
        jQuery.post( 
            ajaxurl, 
            { 
                action: 'empty_queue_alert', 
                twnonce: TWAJAX.twNonce 
            }
        ); 
    });
    
    // ...
    
    jQuery('#wp-cron-alert-hide').click(function(e){
        e.preventDefault();
        jQuery('.tw-wp-cron-alert').slideUp();
        jQuery.post( 
            ajaxurl, 
            { 
                action: 'wp_cron_alert', 
                twnonce: TWAJAX.twNonce 
            }
        ); 
    });
    
    // ...

    jQuery('#change-queue-status').click(function(e){
        e.preventDefault();
        
        jQuery('#change-queue-status').addClass('disabled').text('Working...');
        
        jQuery.post( 
            ajaxurl, 
            { 
                action: 'change_queue_status',
                twnonce: TWAJAX.twNonce
            }, 
            function(response) {
            
                var data = jQuery.parseJSON(response);
            
                jQuery('#change-queue-status').removeClass('disabled')
            
                if( data.response == 'paused' ) {
                    jQuery('#change-queue-status').text('Resume');
                    jQuery('#queue-status').text( 'Status: Paused' );
                } else if( data.response == 'running' ) {
                    jQuery('#change-queue-status').text('Pause');
                    jQuery('#queue-status').text( 'Status: Running' );
                } else {
                    jQuery('#change-queue-status').text('Error :(');
                }
        
            } 
        ); 
        
        
    });
    
    jQuery('#tw-simple-view').click(function(e){
        
        e.preventDefault();
        
        jQuery(this).toggleClass('active');
        jQuery('#the-queue').find('> ul').toggleClass('simple');
         
    });
    
    /**
     * Tweet Now available on the Queue screen
     */
    
    jQuery('.tweet-now').click(function(e){
       
        e.preventDefault();
        
        var el = jQuery(this);
        
        el.text('Tweeting...');
        
        jQuery.post( 
            ajaxurl, 
            { 
                action: 'tweet', 
                post_id : el.data('post-id'),
                twnonce: TWAJAX.twNonce
            }, 
            function( response ) {

                var data = jQuery.parseJSON( response );

                if( data.response == "error" ) {
                    
                    console.log(data.errormsg);
                
                    jQuery('#'+el.data('post-id')).animate({backgroundColor:'red'}, 300).animate({backgroundColor:'#fff'}, 300);
                
                    el.text('Tweet Now');
                
                    alert( 'Twitter did not accept your tweet. In most cases it\'s because it\'s a duplicate. We suggest moving the post down the queue and re-tweeting it again later.' );
                
                } else {
                
                    jQuery('#'+el.data('post-id')).css( 'background', '#00AB2B' ).slideUp().remove();
                
                }
            
            } 
        );
        
    });
    
    // ...
    
    jQuery(document).on('click','.tw-dequeue-post',function(e){
       
        e.preventDefault();
        
        var el = jQuery(this);
        
        el.text('Dequeuing...');
        
        jQuery.post( 
            ajaxurl, 
            { 
                action: 'remove_from_queue', 
                post_id : el.data('post-id'),
                twnonce: TWAJAX.twNonce
            }, 
            function( response ) {
            
                var data = jQuery.parseJSON( response );
            
                if( data.response == "error" ) {
                
                    el.replaceWith('<a href="#" style="color:#a00" class="tw-dequeue-post" data-post-id="'+el.data('post-id')+'">Dequeue</a>');
                
                    alert( 'We couldn\'t remove your tweet... Not sure why. Try excluding it in the post edit screen.' );
                
                } else {
                
                    el.replaceWith('<a href="#" class="tw-queue-post" data-post-id="'+el.data('post-id')+'">Queue</a>');
                
                }
            
            } 
        );
        
    });
    
    // ...
    
    jQuery(document).on('click','.tw-queue-post',function(e){
       
        e.preventDefault();
        
        var el = jQuery(this);
        
        el.text('Queuing...');
        
        jQuery.post( 
            ajaxurl, 
            { 
                action: 'add_to_queue', 
                post_id : el.data('post-id'),
                twnonce: TWAJAX.twNonce
            }, 
            function( response ) {
            
                var data = jQuery.parseJSON( response );
            
                if( data.response == "error" ) {
                
                    el.replaceWith('<a href="#" class="tw-queue-post" data-post-id="'+el.data('post-id')+'">Queue</a>');
                
                    alert( 'We couldn\'t queue your tweet... Not sure why. Try excluding it in the post edit screen.' );
                
                } else {
                
                    el.replaceWith('<a href="#" style="color:#a00" class="tw-dequeue-post" data-post-id="'+el.data('post-id')+'">Dequeue</a>');
                
                }
            
            } 
        );
        
    });

    // ...

    jQuery('.tw-dequeue').click(function(e){
       
        e.preventDefault();
        
        var el = jQuery(this);
        
        el.text('Removing...');
        
        jQuery.post( 
            ajaxurl, 
            { 
                action: 'remove_from_queue', 
                post_id : el.data('post-id'),
                twnonce: TWAJAX.twNonce
            },
            function( response ) {
            
                var data = jQuery.parseJSON( response );
            
                if( data.response == "error" ) {
                
                    jQuery('#'+el.data('post-id')).animate({backgroundColor:'red'}, 300).animate({backgroundColor:'#fff'}, 300);
                
                    el.text('Remove');
                
                    alert( 'We couldn\'t remove your tweet... Not sure why. Try excluding it in the post edit screen.' );
                
                } else {
                
                    jQuery('#'+el.data('post-id')).css( 'background', '#00AB2B' ).slideUp().remove();
					
					if( jQuery('#tw-queue .the-queue-item').length == 0 ) {
						
						location.reload();
						
					}
                
                }
            
            } 
        );
        
    });
    
    // ...

    jQuery('#tw-queue .fill-up-pt input').bind( 'blur', function(e){
        
        var el = jQuery(this);
        var wrapper = el.parents('.fill-up-pt');
        var brap = {
            'post_type' : wrapper.data('pt'),
            'number' : wrapper.find('.max-posts').val(),
            'date_from' : wrapper.find('.date-from').val(),
            'date_to' : wrapper.find('.date-to').val()
        }
        
        if( wrapper.find('input[type=checkbox]').is(':checked') == false ) {
         
            wrapper.find( '.' + wrapper.data('pt') + '-count' ).html( '0 will be imported' );
            return false;
            
        }
        
        wrapper.find( '.' + wrapper.data('pt') + '-count' ).html( '<img width="20" height="20" src="/wp-admin/images/spinner-2x.gif">' );

        jQuery.post( 
            ajaxurl, 
            { 
                action: 'found_posts', 
                args : brap,
                twnonce: TWAJAX.twNonce
            },
            function( response ) {
            
                var data = jQuery.parseJSON( response );
                var count = 0;

                if( data.response != "error" ) {
                    count = data.data;
                }
                
                wrapper.find( '.' + wrapper.data('pt') + '-count' ).html( count + ' item' + ( count != 1 ? 's' : '' ) + ' will be imported' );
            
            } 
        );
        
    });
    
    // ...
    
    jQuery('.show-all-templates').click(function(e) {
        
        e.preventDefault();

        jQuery(this).parent().find('li').not(':first-child').toggleClass('visible');
        
    });

});

function twp_character_counter( raw, has_image ) {
    
    // Max characters accepted for a single tweet
    maxCharacters = 140;
    
    // Load custom tweet text to a variable
    var tweet_template = raw;
    
    // ...
    
    if( twp_template_tags.length != 0 || typeof twp_template_tags != undefined ) {
     
        jQuery.each( twp_template_tags, function(k,v) {
            
            var regex = new RegExp( '{{'+k+'}}', 'g' );
            tweet_template = tweet_template.replace( regex, v );
            
        });
        
    }
    
    /**
     * Calculate a whole string length
     */
    var current_length = 0;
    current_length = tweet_template.length;

    // ...
    
    /**
     * Amend character limit if URL is detected (22 characters per url)
     */
    
    var url_chars = 22;

    // urls will be an array of URL matches
    var urls = tweet_template.match(/(?:(?:https?|ftp):\/\/)?(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:\/?[^\s]*)?/g);
    
    // If urls were found, play the max character value accordingly
    if( urls != null ) {
        
        for (var i = 0, il = urls.length; i < il; i++) {
            
            // get url length difference
            var diff = url_chars - urls[i].length;
            
            // apply difference
            current_length += diff;
            
        }
        
    }
    
    // ...
    
    /**
     * Amend character limit if image is attached (23 characters = url + one space)
     **/
    
    var img_chars = 23;
    
    if( has_image == true )
        current_length += img_chars;
    
    // return actually tweet length
    return current_length;
    
}

function twp_refresh_counters() {
    
    var tweet_templates = jQuery( '.tweet-template-item textarea' );
    var exclude_tweet_image = jQuery( '.exclude-tweet-image' );

    tweet_templates.each( function( k, v ) {
        
        if( jQuery(this).val().length == null )
            return;

        var count = 0;

        if( exclude_tweet_image.is( ':checked' ) === false ) {

            count = twp_character_counter( jQuery(this).val(), true );

        } else {

            count = twp_character_counter( jQuery(this).val(), false );

        }
        
        jQuery(this).parent().find('.twp-counter').text( count );

        if( count > 140 ) {
            jQuery(this).parent().find('.twp-counter').addClass( 'too-long' );   
        } else {
            jQuery(this).parent().find('.twp-counter').removeClass( 'too-long' );
        }

    } );
    
}

function twp_refresh_permalink() {
    
    var permalink = jQuery('#sample-permalink').text();
    
    if( permalink !== '' && permalink !== twp_template_tags.URL && jQuery('#new-post-slug').length == '' ) {

        twp_template_tags.URL = permalink;
        twp_refresh_counters();
        
    }

}