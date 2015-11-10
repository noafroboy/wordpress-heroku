<?php

function dropcap_func($atts, $content) {
	extract(shortcode_atts(array(
		'style' => 1
	), $atts));

	//get first char
	$first_char = substr($content, 0, 1);
	$text_len = strlen($content);
	$rest_text = substr($content, 1, $text_len);

	$return_html = '<span class="dropcap'.esc_attr($style).'">'.$first_char.'</span>';
	$return_html.= do_shortcode($rest_text);

	return $return_html;

}
add_shortcode('dropcap', 'dropcap_func');


function quote_func($atts, $content) {
	$return_html = '<blockquote>'.do_shortcode($content).'</blockquote>';

	return $return_html;
}
add_shortcode('quote', 'quote_func');


function tg_small_content_func($atts, $content) {
	extract(shortcode_atts(array(
		'class' => ''
	), $atts));

	$return_html = '<div class="post_excerpt ';
	if(!empty($class))
	{
		$return_html.= $class;
	}
	
	$return_html.= '">'.do_shortcode($content).'</div>';

	return $return_html;
}
add_shortcode('tg_small_content', 'tg_small_content_func');


function pre_func($atts, $content) {
	$return_html = '<pre>'.strip_tags($content).'</pre>';

	return $return_html;
}
add_shortcode('pre', 'pre_func');


function tg_social_icons_func($atts, $content) {

	extract(shortcode_atts(array(
		'style' => '',
		'size' => 'small',
	), $atts));

	$return_html = '<div class="social_wrapper shortcode '.esc_attr($style).' '.esc_attr($size).'"><ul>';
	
	$pp_facebook_url = get_option('pp_facebook_url');
    if(!empty($pp_facebook_url))
    {
		$return_html.='<li class="facebook"><a target="_blank" title="Facebook" href="'.esc_url($pp_facebook_url).'"><i class="fa fa-facebook"></i></a></li>';
	}
	
	$pp_twitter_username = get_option('pp_twitter_username');
	if(!empty($pp_twitter_username))
	{
		$return_html.='<li class="twitter"><a target="_blank" title="Twitter" href="http://twitter.com/'.$pp_twitter_username.'"><i class="fa fa-twitter"></i></a></li>';
	}
	
	$pp_flickr_username = get_option('pp_flickr_username');
		    		
	if(!empty($pp_flickr_username))
	{
		$return_html.='<li class="flickr"><a target="_blank" title="Flickr" href="http://flickr.com/people/'.$pp_flickr_username.'"><i class="fa fa-flickr"></i></a></li>';
	}
		    		
	$pp_youtube_username = get_option('pp_youtube_username');
	if(!empty($pp_youtube_username))
	{
		$return_html.='<li class="youtube"><a target="_blank" title="Youtube" href="http://youtube.com/user/'.$pp_youtube_username.'"><i class="fa fa-youtube"></i></a></li>';
	}

	$pp_vimeo_username = get_option('pp_vimeo_username');
	if(!empty($pp_vimeo_username))
	{
		$return_html.='<li class="vimeo"><a target="_blank" title="Vimeo" href="http://vimeo.com/'.$pp_vimeo_username.'"><i class="fa fa-vimeo-square"></i></a></li>';
	}

	$pp_tumblr_username = get_option('pp_tumblr_username');
	if(!empty($pp_tumblr_username))
	{
		$return_html.='<li class="tumblr"><a target="_blank" title="Tumblr" href="http://'.$pp_tumblr_username.'.tumblr.com"><i class="fa fa-tumblr"></i></a></li>';
	}
	
	$pp_google_url = get_option('pp_google_url');
    		
    if(!empty($pp_google_url))
    {
		$return_html.='<li class="google"><a target="_blank" title="Google+" href="'.esc_url($pp_google_username).'"><i class="fa fa-google-plus"></i></a></li>';
	}
		    		
	$pp_dribbble_username = get_option('pp_dribbble_username');
	if(!empty($pp_dribbble_username))
	{
		$return_html.='<li class="dribbble"><a target="_blank" title="Dribbble" href="http://dribbble.com/'.$pp_dribbble_username.'"><i class="fa fa-dribbble"></i></a></li>';
	}
	
	$pp_linkedin_url = get_option('pp_linkedin_url');
    if(!empty($pp_linkedin_url))
    {
		$return_html.='<li class="linkedin"><a target="_blank" title="Linkedin" href="'.esc_url($pp_linkedin_url).'"><i class="fa fa-linkedin"></i></a></li>';
	}
		            
	$pp_pinterest_username = get_option('pp_pinterest_username');
	if(!empty($pp_pinterest_username))
	{
		$return_html.='<li class="pinterest"><a target="_blank" title="Pinterest" href="http://pinterest.com/'.$pp_pinterest_username.'"><i class="fa fa-pinterest"></i></a></li>';
	}
		        	
	$pp_instagram_username = get_option('pp_instagram_username');
	if(!empty($pp_instagram_username))
	{
		$return_html.='<li class="instagram"><a target="_blank" title="Instagram" href="http://instagram.com/'.$pp_instagram_username.'"><i class="fa fa-instagram"></i></a></li>';
	}
	
	$pp_behance_username = get_option('pp_behance_username');
    if(!empty($pp_behance_username))
	{
		$return_html.='<li class="behance"><a target="_blank" title="Behance" href="http://behance.net/'.$pp_behance_username.'"><i class="fa fa-behance-square"></i></a></li>';
	}
	
	$return_html.= '</ul></div>';

	return $return_html;

}
add_shortcode('tg_social_icons', 'tg_social_icons_func');


function one_half_func($atts, $content) {
	extract(shortcode_atts(array(
		'class' => '',
		'custom_css' => '',
	), $atts));

	$return_html = '<div class="one_half '.esc_attr($class).'" style="'.esc_attr($custom_css).'">'.do_shortcode($content).'</div>';	

	return $return_html;
}
add_shortcode('one_half', 'one_half_func');


function one_half_bg_func($atts, $content) {
	extract(shortcode_atts(array(
		'class' => '',
		'bg' => '',
		'bgcolor' => '',
		'fontcolor' => '',
		'custom_css' => '',
		'padding' => 20,
	), $atts));

	$return_html = '<div class="one_half_bg '.esc_attr($class).'"';
	
	if(!empty($bgcolor))
	{
		$custom_css.= 'background-color:'.esc_attr($bgcolor).';';
	}
	if(!empty($fontcolor))
	{
		$custom_css.= 'color:'.esc_attr($fontcolor).';';
	}
	
	if(!empty($bg))
	{
		$custom_css.= 'background: transparent url('.esc_url($bg).') no-repeat;'.esc_attr($style).';';
	}
	
	$return_html.= ' style="'.esc_attr($custom_css).'">';
	$return_html.= '<div style="padding:'.esc_attr($padding).'px;box-sizing:border-box">';
	$return_html.= do_shortcode($content).'</div></div>';	

	return $return_html;
}
add_shortcode('one_half_bg', 'one_half_bg_func');


function one_half_last_func($atts, $content) {
	extract(shortcode_atts(array(
		'class' => '',
		'custom_css' => '',
	), $atts));

	$return_html = '<div class="one_half last '.esc_attr($class).'" style="'.esc_attr($custom_css).'">'.do_shortcode($content).'</div><br class="clear"/>';

	return $return_html;
}
add_shortcode('one_half_last', 'one_half_last_func');


function one_third_func($atts, $content) {
	extract(shortcode_atts(array(
		'class' => '',
		'custom_css' => '',
	), $atts));

	$return_html = '<div class="one_third '.esc_attr($class).'" style="'.esc_attr($custom_css).'">'.do_shortcode($content).'</div>';

	return $return_html;
}
add_shortcode('one_third', 'one_third_func');


function one_third_bg_func($atts, $content) {
	extract(shortcode_atts(array(
		'class' => '',
		'bg' => '',
		'bgcolor' => '',
		'fontcolor' => '',
		'custom_css' => '',
		'padding' => 10,
	), $atts));

	$return_html = '<div class="one_third_bg '.esc_attr($class).'"';
	
	if(!empty($bgcolor))
	{
		$custom_css.= 'background-color:'.esc_attr($bgcolor).';';
	}
	if(!empty($fontcolor))
	{
		$custom_css.= 'color:'.esc_attr($fontcolor).';';
	}
	
	if(!empty($bg))
	{
		$return_html.= 'background: transparent url('.esc_url($bg).') no-repeat;'.esc_attr($style).';';
	}
	
	$return_html.= ' style="'.esc_attr($custom_css).'">';
	$return_html.= '<div style="padding:'.esc_attr($padding).'px;box-sizing:border-box">';
	$return_html.= do_shortcode($content).'</div></div>';	

	return $return_html;
}
add_shortcode('one_third_bg', 'one_third_bg_func');


function one_third_last_func($atts, $content) {
	extract(shortcode_atts(array(
		'class' => '',
		'custom_css' => '',
	), $atts));

	$return_html = '<div class="one_third last '.esc_attr($class).'" style="'.esc_attr($custom_css).'">'.do_shortcode($content).'</div><br class="clear"/>';

	return $return_html;
}
add_shortcode('one_third_last', 'one_third_last_func');


function two_third_func($atts, $content) {
	extract(shortcode_atts(array(
		'class' => '',
		'custom_css' => '',
	), $atts));

	$return_html = '<div class="two_third '.esc_attr($class).'" style="'.esc_attr($custom_css).'">'.do_shortcode($content).'</div>';

	return $return_html;
}
add_shortcode('two_third', 'two_third_func');


function two_third_bg_func($atts, $content) {
	extract(shortcode_atts(array(
		'class' => '',
		'bg' => '',
		'bgcolor' => '',
		'fontcolor' => '',
		'custom_css' => '',
		'padding' => 20,
	), $atts));

	$return_html = '<div class="two_third_bg '.esc_attr($class).'"';
	
	if(!empty($bgcolor))
	{
		$custom_css.= 'background-color:'.esc_attr($bgcolor).';';
	}
	if(!empty($fontcolor))
	{
		$custom_css.= 'color:'.esc_attr($fontcolor).';';
	}
	
	if(!empty($bg))
	{
		$return_html.= 'background: transparent url('.esc_url($bg).') no-repeat;'.esc_attr($style).';';
	}
	
	$return_html.= ' style="'.esc_attr($custom_css).'">';
	$return_html.= '<div style="padding:'.esc_attr($padding).'px;box-sizing:border-box">';
	$return_html.= do_shortcode($content).'</div></div>';	

	return $return_html;
}
add_shortcode('two_third_bg', 'two_third_bg_func');


function two_third_last_func($atts, $content) {
	extract(shortcode_atts(array(
		'class' => '',
		'custom_css' => '',
	), $atts));

	$return_html = '<div class="two_third last '.esc_attr($class).'" style="'.esc_attr($custom_css).'">'.do_shortcode($content).'</div><br class="clear"/>';

	return $return_html;
}
add_shortcode('two_third_last', 'two_third_last_func');


function one_fourth_func($atts, $content) {
	extract(shortcode_atts(array(
		'class' => '',
		'custom_css' => '',
	), $atts));

	$return_html = '<div class="one_fourth '.esc_attr($class).'" style="'.esc_attr($custom_css).'">'.do_shortcode($content).'</div>';

	return $return_html;
}
add_shortcode('one_fourth', 'one_fourth_func');


function one_fourth_bg_func($atts, $content) {
	extract(shortcode_atts(array(
		'class' => '',
		'bg' => '',
		'bgcolor' => '',
		'fontcolor' => '',
		'custom_css' => '',
		'padding' => 10,
	), $atts));

	$return_html = '<div class="one_fourth_bg '.esc_attr($class).'"';
	
	if(!empty($bgcolor))
	{
		$custom_css.= 'background-color:'.esc_attr($bgcolor).';';
	}
	if(!empty($fontcolor))
	{
		$custom_css.= 'color:'.esc_attr($fontcolor).';';
	}
	
	if(!empty($bg))
	{
		$return_html.= 'background: transparent url('.esc_url($bg).') no-repeat;'.esc_attr($style).';';
	}
	
	$return_html.= ' style="'.esc_attr($custom_css).'">';
	$return_html.= '<div style="padding:'.esc_attr($padding).'px;box-sizing:border-box">';
	$return_html.= do_shortcode($content).'</div></div>';	

	return $return_html;
}
add_shortcode('one_fourth_bg', 'one_fourth_bg_func');


function one_fourth_last_func($atts, $content) {
	extract(shortcode_atts(array(
		'class' => '',
		'custom_css' => '',
	), $atts));

	$return_html = '<div class="one_fourth last '.esc_attr($class).'" style="'.esc_attr($custom_css).'">'.do_shortcode($content).'</div><br class="clear"/>';

	return $return_html;
}
add_shortcode('one_fourth_last', 'one_fourth_last_func');


function one_fifth_func($atts, $content) {
	extract(shortcode_atts(array(
		'class' => '',
		'custom_css' => '',
	), $atts));

	$return_html = '<div class="one_fifth '.esc_attr($class).'" style="'.esc_attr($custom_css).'">'.do_shortcode($content).'</div>';

	return $return_html;
}
add_shortcode('one_fifth', 'one_fifth_func');


function one_fifth_last_func($atts, $content) {
	extract(shortcode_atts(array(
		'class' => '',
		'custom_css' => '',
	), $atts));

	$return_html = '<div class="one_fifth last '.esc_attr($class).'" style="'.esc_attr($custom_css).'">'.do_shortcode($content).'</div><br class="clear"/>';

	return $return_html;
}
add_shortcode('one_fifth_last', 'one_fifth_last_func');


function one_sixth_func($atts, $content) {
	extract(shortcode_atts(array(
		'class' => '',
		'custom_css' => '',
	), $atts));

	$return_html = '<div class="one_sixth '.esc_attr($class).'" style="'.esc_attr($custom_css).'">'.do_shortcode($content).'</div>';

	return $return_html;
}
add_shortcode('one_sixth', 'one_sixth_func');


function one_sixth_last_func($atts, $content) {
	extract(shortcode_atts(array(
		'class' => '',
		'custom_css' => '',
	), $atts));

	$return_html = '<div class="one_sixth last '.esc_attr($class).'" style="'.esc_attr($custom_css).'">'.do_shortcode($content).'</div><br class="clear"/>';

	return $return_html;
}
add_shortcode('one_sixth_last', 'one_sixth_last_func');


function tg_pre_func($atts, $content) {
	extract(shortcode_atts(array(
		'title' => '',
		'close' => 1,
	), $atts));
	
	$return_html = '';
	$return_html.= '<pre>';
	$return_html.= $content;
	$return_html.= '</pre>';

	return $return_html;
}
add_shortcode('tg_pre', 'tg_pre_func');


function tg_divider_func($atts, $content) {

	//extract short code attr
	extract(shortcode_atts(array(
		'style' => 'normal'
	), $atts));

	$return_html = '<hr class="'.$style.'"/>';
	if($style == 'totop')
	{
		$return_html.= '<a class="hr_totop" href="#">'.__( 'Go to top', 'grandblog-custom-post' ).'&nbsp;<i class="fa fa-arrow-up"></i></a>';
	}

	return $return_html;
}

add_shortcode('tg_divider', 'tg_divider_func');


function tg_lightbox_func($atts, $content) {

	extract(shortcode_atts(array(
		'type' => 'image',
		'src' => '',
		'href' => '',
		'youtube_id' => '',
		'vimeo_id' => '',
	), $atts));

	$class = 'lightbox';

	if($type != 'image')
	{
		$class.= '_'.$type;
	}

	if($type == 'youtube')
	{
		$href = '#video_'.$youtube_id;
	}

	if($type == 'vimeo')
	{
		$href = '#video_'.$vimeo_id;
	}
	
	$return_html = '<div class="post_img">';
	$return_html.= '<a href="'.esc_url($href).'" class="img_frame">';
	
	if(!empty($src))
	{
		$return_html.= '<img src="'.esc_url($src).'"img_frame"/>';
	}

	if(!empty($youtube_id))
	{
		$return_html.= '<div style="display:none;"><div id="video_'.$youtube_id.'" style="width:900px;height:488px;overflow:hidden;" class="video-container"><iframe width="900" height="488" src="http://www.youtube.com/embed/'.$youtube_id.'?theme=dark&amp;rel=0&amp;wmode=opaque" frameborder="0"></iframe></div></div>';
	}

	if(!empty($vimeo_id))
	{
		$return_html.= '<div style="display:none;"><div id="video_'.$vimeo_id.'" style="width:900px;height:506px;overflow:hidden;" class="video-container"><iframe src="http://player.vimeo.com/video/'.$vimeo_id.'?title=0&amp;byline=0&amp;portrait=0" width="900" height="506" frameborder="0"></iframe></div></div>';
	}
	
	$return_html.= '</a></div>';

	return $return_html;

}

add_shortcode('tg_lightbox', 'tg_lightbox_func');


function tg_youtube_func($atts) {
	extract(shortcode_atts(array(
		'width' => 640,
		'height' => 385,
		'video_id' => '',
	), $atts));

	$custom_id = time().rand();

	$return_html = '<div class="video-container"><iframe title="YouTube video player" width="'.esc_attr($width).'" height="'.esc_attr($height).'" src="http://www.youtube.com/embed/'.$video_id.'?theme=dark&rel=0&wmode=transparent" frameborder="0" allowfullscreen></iframe></div>';

	return $return_html;
}

add_shortcode('tg_youtube', 'tg_youtube_func');


function tg_vimeo_func($atts, $content) {
	extract(shortcode_atts(array(
		'width' => 640,
		'height' => 385,
		'video_id' => '',
	), $atts));

	$custom_id = time().rand();

	$return_html = '<div class="video-container"><iframe src="http://player.vimeo.com/video/'.$video_id.'?title=0&amp;byline=0&amp;portrait=0" width="'.esc_attr($width).'" height="'.esc_attr($height).'" frameborder="0"></iframe></div>';

	return $return_html;
}

add_shortcode('tg_vimeo', 'tg_vimeo_func');


function tg_gallery_slider_func($atts, $content) {
	extract(shortcode_atts(array(
		'gallery_id' => '',
		'size' => 'original',
		'autoplay' => '',
		'caption' => '',
		'timer' => 5,
	), $atts));
	
	wp_enqueue_script("lestblog-flexslider-js", get_template_directory_uri()."/js/flexslider/jquery.flexslider-min.js", false, THEMEVERSION, true);
	wp_enqueue_script("lestblog-script-gallery-flexslider", get_template_directory_uri()."/templates/script-gallery-flexslider.php?autoplay=".$autoplay.'&amp;caption='.$caption.'&amp;timer='.$timer, false, THEMEVERSION, true);

	$images_arr = get_post_meta($gallery_id, 'wpsimplegallery_gallery', true);
	$images_arr = grandblog_resort_gallery_img($images_arr);
	
	$return_html = '';

	if(!empty($images_arr))
	{
		$return_html.= '<div class="slider_wrapper tg_gallery">';
		$return_html.= '<div class="flexslider tg_gallery" data-height="750">';
		$return_html.= '<ul class="slides">';
		
		foreach($images_arr as $key => $image)
		{
			$image_url = wp_get_attachment_image_src($image, $size, true);
			
			$return_html.= '<li>';
			$return_html.= '<img src="'.esc_url($image_url[0]).'" alt=""/>';
			
			if(!empty($caption))
			{
				//Get image meta data
		    	$image_caption = get_post_field('post_excerpt', $image);
			
				$return_html.= '<div class="gallery_image_caption">'.$image_caption.'</div>';
			}
			
			$return_html.= '</li>';
		}
		
		$return_html.= '</ul>';
		$return_html.= '</div>';
		$return_html.= '</div>';
	}
	else
	{
		$return_html.= __( 'Empty gallery item. Please make sure you have upload image to it or check the short code.', 'grandblog-custom-post' );
	}

	return $return_html;
}
add_shortcode('tg_gallery_slider', 'tg_gallery_slider_func');


function googlefont_func($atts, $content) {

	//extract short code attr
	extract(shortcode_atts(array(
		'font' => '',
		'fontsize' => '',
		'style' => '',
	), $atts));

	$return_html = '';

	if(!empty($font))
	{
		$encoded_font = urlencode($font);
		
		if(!is_ssl())
		{
			wp_enqueue_style($encoded_font, "http://fonts.googleapis.com/css?family=".$encoded_font, false, "", "all");
		}
		else
		{
			wp_enqueue_style($encoded_font, "https://fonts.googleapis.com/css?family=".$encoded_font, false, "", "all");
		}
		
		$return_html = '<div class="googlefont" style="font-family:'.$font.';font-size:'.esc_attr($fontsize).'px;'.$style.'">'.$content.'</div>';
	}

	return $return_html;
}

add_shortcode('googlefont', 'googlefont_func');


// Actual processing of the shortcode happens here
function tg_last_run_shortcode( $content ) {
    global $shortcode_tags;
 
    // Backup current registered shortcodes and clear them all out
    $orig_shortcode_tags = $shortcode_tags;
    remove_all_shortcodes();
 
    add_shortcode( 'one_half', 'one_half_func' );
    add_shortcode( 'one_half_last', 'one_half_last_func' );
    add_shortcode( 'one_half_bg', 'one_half_bg_func' );
    add_shortcode( 'one_third', 'one_third_func' );
    add_shortcode( 'one_third_last', 'one_third_last_func' );
    add_shortcode( 'one_third_bg', 'one_third_bg_func' );
    add_shortcode( 'two_third', 'two_third_func' );
    add_shortcode( 'two_third_bg', 'two_third_bg_func' );
    add_shortcode( 'two_third_last', 'two_third_last_func' );
    add_shortcode( 'one_fourth', 'one_fourth_func' );
    add_shortcode( 'one_fourth_bg', 'one_fourth_bg_func' );
    add_shortcode( 'one_fourth_last', 'one_fourth_last_func' );
    add_shortcode( 'one_fifth', 'one_fifth_func' );
    add_shortcode( 'one_fifth_last', 'one_fifth_last_func' );
    add_shortcode( 'pp_pre', 'pp_pre_func' );
 
    // Do the shortcode (only the one above is registered)
    $content = do_shortcode( $content );
 
    // Put the original shortcodes back
    $shortcode_tags = $orig_shortcode_tags;
 
    return $content;
}
 
add_filter( 'the_content', 'tg_last_run_shortcode', 7 );

?>