<?php
/**
*	Get Current page object
**/
if(!is_null($post))
{
	$page_obj = get_page($post->ID);
}

$current_page_id = '';

/**
*	Get current page id
**/

if(!is_null($post) && isset($page_obj->ID))
{
    $current_page_id = $page_obj->ID;
}

//Get page header display setting
$page_title = get_the_title();
$page_show_title = get_post_meta($current_page_id, 'page_show_title', true);

if(is_tag())
{
	$page_show_title = 0;
	$page_title = single_cat_title( '', false );
	$term = 'tag';
} 
elseif(is_category())
{
    $page_show_title = 0;
	$page_title = single_cat_title( '', false );
	$term = 'category';
}
elseif(is_archive())
{
	$page_show_title = 0;

	if ( is_day() ) : 
		$page_title = get_the_date(); 
    elseif ( is_month() ) : 
    	$page_title = get_the_date('F Y'); 
    elseif ( is_year() ) : 
    	$page_title = get_the_date('Y'); 
    elseif ( !empty($term) ) : 
    	$ob_term = get_term_by('slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
    	$page_taxonomy = get_taxonomy($ob_term->taxonomy);
    	$page_title = $page_taxonomy->labels->name.' "'.$ob_term->name.'"';
    else :
    	$page_title = esc_html__('Blog Archives', 'grandblog-translation'); 
    endif;
    
    $term = 'archive';
    
}
else if(is_search())
{
	$page_show_title = 0;
	$page_title = esc_html__('Search', 'grandblog-translation' );
	$term = 'search';
}

global $grandblog_hide_title;
if($grandblog_hide_title == 1)
{
	$page_show_title = 1;
}

if(empty($page_show_title))
{
	//Get current page tagline
	$page_tagline = get_post_meta($current_page_id, 'page_tagline', true);
	
	//If on gallery post type page
	if(is_single() && $post->post_type == 'galleries')
	{
		$page_tagline = get_the_excerpt();
	}
	
	if(is_search())
	{
		$page_tagline = esc_html__('Search Results for ', 'grandblog-translation' ).get_search_query();
	}
	else if(is_category())
	{
		$page_tagline = category_description();
	}

	$pp_page_bg = '';
	//Get page featured image
	if(has_post_thumbnail($current_page_id, 'full') && empty($term))
    {
        $image_id = get_post_thumbnail_id($current_page_id); 
        $image_thumb = wp_get_attachment_image_src($image_id, 'full', true);
        
        if(isset($image_thumb[0]) && !empty($image_thumb[0]))
        {
        	$pp_page_bg = $image_thumb[0];
        }
    }
    
    //Check if add blur effect
	$tg_page_title_img_blur = kirki_get_option('tg_page_title_img_blur');
    
    global $grandblog_topbar;
    global $grandblog_screen_class;
?>
<div id="page_caption" class="<?php if(!empty($pp_page_bg)) { ?>hasbg parallax <?php } ?> <?php if(!empty($grandblog_topbar)) { ?>withtopbar<?php } ?> <?php if(!empty($grandblog_screen_class)) { ?>split<?php } ?>">
	<?php if(!empty($pp_page_bg)) { ?>
		<div id="bg_regular" style="background-image:url(<?php echo esc_url($pp_page_bg); ?>);"></div>
	<?php } else { ?>
	<div class="page_title_wrapper">
	    <div class="page_title_inner">
	    	<h1 <?php if(!empty($pp_page_bg) && !empty($grandblog_topbar)) { ?>class ="withtopbar"<?php } ?>><?php echo esc_html($page_title); ?></h1>
	    	<?php
	        	if(!empty($page_tagline))
	        	{
	        ?>
	        	<hr class="title_break">
	        	<div class="page_tagline">
	        		<?php echo wp_kses_post($page_tagline); ?>
	        	</div>
	        <?php
	        	}
	        ?>
	    </div>
	</div>
	<?php } ?>
	<?php
	    if(!empty($tg_page_title_img_blur) && !empty($pp_page_bg) && $grandblog_screen_class != 'split')
	    {
	?>
	<div id="bg_blurred" style="background-image:url(<?php echo get_template_directory_uri().'/modules/blurred.php?src='.esc_url($pp_page_bg); ?>);"></div>
	<?php
	    }
	?>

</div>

<?php if(!empty($pp_page_bg)) { ?>
<div class="page_title_wrapper">
    <div class="page_title_inner">
    	<h1 <?php if(!empty($pp_page_bg) && !empty($grandblog_topbar)) { ?>class ="withtopbar"<?php } ?>><?php echo esc_html($page_title); ?></h1>
    	<?php
        	if(!empty($page_tagline))
        	{
        ?>
        	<hr class="title_break">
        	<div class="page_tagline">
        		<?php echo wp_kses_post($page_tagline); ?>
        	</div>
        <?php
        	}
        ?>
    </div>
</div>
<?php } ?>

<?php
} //End if display title
?>

<!-- Begin content -->
<?php
global $grandblog_page_content_class;
?>
<div id="page_content_wrapper" class="<?php if(!empty($pp_page_bg)) { ?>hasbg <?php } ?><?php if(!empty($pp_page_bg) && !empty($grandblog_topbar)) { ?>withtopbar <?php } ?><?php if(!empty($grandblog_page_content_class)) { echo esc_attr($grandblog_page_content_class); } ?>">