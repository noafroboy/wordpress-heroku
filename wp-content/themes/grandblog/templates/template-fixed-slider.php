<?php
	//Check if display slider
	$tg_blog_slider = kirki_get_option('tg_blog_slider');

	if(!empty($tg_blog_slider) && !is_search() && !is_category() && !is_tag() && !is_archive())
	{
		//Get post featured category
		$args = array( 
			'orderby' => 'date',
			'order' => 'DESC',
			'order' => 'post',
			'suppress_filters' => 0,
		);
		
		//Check if filter slider posts by selected category
		$tg_blog_slider_cat = kirki_get_option('tg_blog_slider_cat');
		if(!empty($tg_blog_slider_cat))
		{
		    $args['cat'] = $tg_blog_slider_cat;
		}
		
		//Check if filter slider posts by selected category
		$tg_blog_slider_cat = kirki_get_option('tg_blog_slider_cat');
		if(THEMEDEMO)
		{
			$args['cat'] = 7;
		}
		
		//Check slider post items
		$tg_blog_slider_items = kirki_get_option('tg_blog_slider_items');
		if(!empty($tg_blog_slider_items) && is_numeric($tg_blog_slider_items))
		{
			$args['posts_per_page'] = $tg_blog_slider_items;
		}
		else
		{
			$args['posts_per_page'] = 5;
		}
		
		// the query
		$theme_query = new WP_Query( $args );
		
		wp_enqueue_script("grandblog-flexslider-js", get_template_directory_uri()."/js/flexslider/jquery.flexslider-min.js", false, THEMEVERSION, true);
		wp_enqueue_script("grandblog-script-gallery-flexslider", get_template_directory_uri()."/templates/script-slider-flexslider.php", false, THEMEVERSION, true);
?>
	<div id="post_featured_slider" class="slider_wrapper fixed_width">
		<div class="flexslider" data-height="550">
			<ul class="slides">
	<?php
		//Display slide content
		if ($theme_query->have_posts()) : while ($theme_query->have_posts()) : $theme_query->the_post();
			//Get post featured image
			$slide_ID = get_the_ID();
			$image_url = array();
						
			if(has_post_thumbnail($slide_ID, 'large'))
			{
			    $image_id = get_post_thumbnail_id($slide_ID);
			    $image_url = wp_get_attachment_image_src($image_id, 'original', true);
			}
			
			if(isset($image_url[0]) && !empty($image_url[0]))
			{
	?>
			<li>
				<a href="<?php echo get_permalink($slide_ID); ?>">
					<div class="slider_image" style="background-image:url('<?php echo esc_url($image_url[0]); ?>');"></div>
				</a>
				<div class="slide_post">
					<?php
						//Get Post's Categories
					    $post_categories = wp_get_post_categories($post->ID);
					    if(!empty($post_categories))
					    {
					?>
					<div class="post_info_cat">
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
					    		if(THEMEDEMO && $i == 1)
					    		{
						    		break;
					    		}
					    
					    		if ($i != $len - 1) {
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
				    <h2><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
				    <hr class="title_break slider">
			      	<div class="slide_post_excerpt">
			      		<?php echo grandblog_get_excerpt_by_id($slide_ID); ?>
				  	</div>
				</div>
			</li>
	<?php
			}
			
		endwhile; endif;
	?>
			</ul>
		</div>
	</div>
<?php	
		wp_reset_postdata();
	} //End if display slider
?>