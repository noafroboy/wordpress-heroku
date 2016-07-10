<?php
function grandblog_add_menu_icons_styles(){
?>
 
<style>
#adminmenu .menu-icon-galleries div.wp-menu-image:before {
  content: '\f161';
}
</style>
 
<?php
}
add_action( 'admin_head', 'grandblog_add_menu_icons_styles' );

function strip_images_from_content_for_mobile($content) {
	$detect = new Mobile_Detect;

	if ($detect->isMobile() ) {
		return preg_replace("/<img[^>]+\>/i", "", $content);
	} else {
		return $content;
	}
}

add_filter('the_content', 'strip_images_from_content_for_mobile');

function grandblog_tag_cloud_filter($args = array()) {
   $args['smallest'] = 13;
   $args['largest'] = 13;
   $args['unit'] = 'px';
   return $args;
}

add_filter('widget_tag_cloud_args', 'grandblog_tag_cloud_filter', 90);

//Control post excerpt length
function grandblog_custom_excerpt_length( $length ) {
	return 40;
}
add_filter( 'excerpt_length', 'grandblog_custom_excerpt_length', 200 );

/**
 * Change default fields, add placeholder and change type attributes.
 *
 * @param  array $fields
 * @return array
 */
add_filter( 'comment_form_default_fields', 'grandblog_comment_placeholders' );
 
function grandblog_comment_placeholders( $fields )
{
    $fields['author'] = str_replace('<input', '<input placeholder="'. __('Name', 'grandblog-translation'). '*"',$fields['author']);
    $fields['email'] = str_replace('<input id="email" name="email" type="text"', '<input type="email" placeholder="'.__('Email', 'grandblog-translation').'*"  id="email" name="email"',$fields['email']);
    $fields['url'] = str_replace('<input id="url" name="url" type="text"', '<input placeholder="'.__('Website', 'grandblog-translation').'" id="url" name="url" type="url"',$fields['url']);

    return $fields;
}

//Make widget support shortcode
add_filter('widget_text', 'do_shortcode');

// remove version query string from scripts and stylesheets
function grandblog_remove_script_styles_version( $src ){
    return remove_query_arg( 'ver', $src );
}
add_filter( 'script_loader_src', 'grandblog_remove_script_styles_version' );
add_filter( 'style_loader_src', 'grandblog_remove_script_styles_version' );

//Add class name to post navigation links
add_filter('next_posts_link_attributes', 'grandblog_posts_link_attributes_prev');
add_filter('previous_posts_link_attributes', 'grandblog_posts_link_attributes_next');

function grandblog_posts_link_attributes_prev() {
    return 'class="prev_button""';
}

function grandblog_posts_link_attributes_next() {
    return 'class="next_button""';
}

function grandblog_search_form( $form ) {
	$form = '<form role="search" method="get" id="searchform" class="searchform" action="' . esc_url(home_url( '/' )) . '" >
	<div>
	<input type="text" value="' . get_search_query() . '" name="s" id="s" />
	<button type="submit" id="searchsubmit" class="button"><i class="fa fa-search"></i></button>
	</div>
	</form>';

	return $form;
}

add_filter( 'get_search_form', 'grandblog_search_form' );


function grandblog_add_meta_tags() {
    global $post;
    
    echo '<meta charset="'.get_bloginfo( 'charset' ).'" />';
    
    //Check if responsive layout is enabled
    $tg_mobile_responsive = kirki_get_option('tg_mobile_responsive');
	
	if(!empty($tg_mobile_responsive))
	{
		echo '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />';
	}
	
	//meta for phone number link on mobile
	echo '<meta name="format-detection" content="telephone=no">';
    
    //check if single post then add meta description and keywords
    if (is_single()) 
    {
        //Prepare data for Facebook opengraph sharing
        if(has_post_thumbnail(get_the_ID(), 'grandblog_blog'))
		{
		    $image_id = get_post_thumbnail_id(get_the_ID());
		    $fb_thumb = wp_get_attachment_image_src($image_id, 'grandblog_blog', true);
		}
	
		if(isset($fb_thumb[0]) && !empty($fb_thumb[0]))
		{
			$post_content = get_post_field('post_excerpt', $post->ID);
			
			echo '<meta property="og:type" content="article" />';
			echo '<meta property="og:image" content="'.esc_url($fb_thumb[0]).'"/>';
			echo '<meta property="og:title" content="'.esc_attr(get_the_title()).'"/>';
			echo '<meta property="og:url" content="'.esc_url(get_permalink($post->ID)).'"/>';
			echo '<meta property="og:description" content="'.esc_attr(strip_tags($post_content)).'"/>';
		}
    }
}
add_action( 'wp_head', 'grandblog_add_meta_tags' , 2 );
?>