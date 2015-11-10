<?php
/**
 * The main template file for display blog page.
 *
 * @package WordPress
*/

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

get_header(); 

$is_standard_wp_post = FALSE;
$page_sidebar = 'page-sidebar';

if(is_tag())
{
    $is_standard_wp_post = TRUE;
    $page_sidebar = 'tag-sidebar';
} 
elseif(is_category())
{
    $is_standard_wp_post = TRUE;
    $page_sidebar = 'category-sidebar';
}
elseif(is_archive())
{
    $is_standard_wp_post = TRUE;
    $page_sidebar = 'archives-sidebar';
} 
elseif(is_search())
{
    $is_standard_wp_post = TRUE;
    $page_sidebar = 'search-sidebar';
} 
		
get_header(); 

//Include post featured slider
$tg_blog_slider_layout = kirki_get_option('tg_blog_slider_layout');

get_template_part("/templates/template-".$tg_blog_slider_layout);

if(is_category() OR is_tag() OR is_archive())
{
	get_template_part("/templates/template-header");
}
else
{
?>
<div id="page_content_wrapper">
<?php
}
?>
    <div class="inner">

    	<!-- Begin main content -->
    	<div class="inner_wrapper">

    			<div class="sidebar_content">
<?php
//Include post search bar
get_template_part("/templates/template-search");

if (have_posts()) : while (have_posts()) : the_post();
	$image_thumb = '';
								
	if(has_post_thumbnail(get_the_ID(), 'large'))
	{
	    $image_id = get_post_thumbnail_id(get_the_ID());
	    $image_thumb = wp_get_attachment_image_src($image_id, 'large', true);
	}
?>

<!-- Begin each blog post -->
<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="post_wrapper">
	    
	    <div class="post_content_wrapper">
	    
	    	<div class="post_header search">
		    	<?php
				    //Get post featured content
				    $post_content_class = 'one';
				    
				    if(!empty($image_thumb))
				    {
				        $small_image_url = wp_get_attachment_image_src($image_id, 'grandblog_blog_thumb', true);
				        $post_content_class = 'two_third last';
				?>
				
				    <div class="post_img static one_third">
				        <a href="<?php the_permalink(); ?>">
				        	<img src="<?php echo esc_url($small_image_url[0]); ?>" alt="" class="" style="width:<?php echo esc_attr($small_image_url[1]); ?>px;height:<?php echo esc_attr($small_image_url[2]); ?>px;"/>
					    </a>
				    </div>
				
				<?php
				    }
				?>
				
				<div class="post_header_title <?php echo esc_attr($post_content_class); ?>">
					<?php
						//Get Post's Categories
					    $post_categories = wp_get_post_categories($post->ID);
					    if(!empty($post_categories))
					    {
					?>
					<div class="post_info_cat list">
						<span>
					    <?php
					    	$i = 0;
					    	$len = count($post_categories);
					        foreach($post_categories as $c)
					        {
					        	$cat = get_category( $c );
					    ?>
					        <a href="<?php echo esc_url(get_category_link($cat->term_id)); ?>"><?php echo esc_html($cat->name); ?></a>
					    <?php
					    		if ($i != $len - 1) {
					    		
					    		if(THEMEDEMO && $i == 1)
					    		{
						    		break;
					    		}
					    ?>
					        &nbsp;/
					    <?php
					    		}
					    		$i++;
					        }
					    ?>
						</span>
					</div>
					<?php
						}
					?>
			      	<h5><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h5>
			      	<hr class="title_break small list">
			      	<div class="post_detail post_date">
			      		<span class="post_info_date search"><?php echo date_i18n(THEMEDATEFORMAT, get_the_time('U')); ?></span>
			      		</span>
				  	</div>
				  	<p><?php echo grandblog_get_excerpt_by_id(get_the_ID()); ?></p>
			   </div>
			</div>
			
	    </div>
	    
	</div>

</div>
<br class="clear"/>
<!-- End each blog post -->

<?php endwhile; endif; ?>

	    	<?php 
				if(!isset($paged) OR empty($paged))
				{
					$paged = 1;
				}
			?>
			
	    	<div class="pagination"><div class="pagination_page"><?php echo esc_html($paged); ?></div><?php posts_nav_link(' ', '<i class="fa fa-angle-double-left"></i>'.esc_html__('Newer Posts', 'grandblog-translation' ), esc_html__('Older Posts', 'grandblog-translation' ).'<i class="fa fa-angle-double-right"></i>'); ?></div>
    		
			</div>
    	
    		<div class="sidebar_wrapper">
    		
    			<div class="sidebar">
    			
    				<div class="content">

    					<?php 
						if (is_active_sidebar('page-sidebar')) { ?>
		    	    		<ul class="sidebar_widget">
		    	    			<?php dynamic_sidebar($page_sidebar); ?>
		    	    		</ul>
		    	    	<?php } ?>
    				
    				</div>
    		
    			</div>
    			<br class="clear"/>
    		</div>
    		
    	</div>
    <!-- End main content -->
	</div>
</div>
<?php get_footer(); ?>