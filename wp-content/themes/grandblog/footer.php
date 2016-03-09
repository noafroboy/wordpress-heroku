<?php
/**
 * The template for displaying the footer.
 *
 * @package WordPress
 */
?>

<?php
	//Check if blank template
	global $grandblog_is_no_header;
	global $grandblog_screen_class;
	
	if(!is_bool($grandblog_is_no_header) OR !$grandblog_is_no_header)
	{

	global $grandblog_homepage_style;
	
	//If display photostream
	$pp_photostream = get_option('pp_photostream');
	if(THEMEDEMO && isset($_GET['footer']) && !empty($_GET['footer']))
	{
		$pp_photostream = 0;
	}

	if(!empty($pp_photostream))
	{
		$photos_arr = array();
	
		if($pp_photostream == 'flickr')
		{
			$pp_flickr_id = get_option('pp_flickr_id');
			$photos_arr = grandblog_get_flickr(array('type' => 'user', 'id' => $pp_flickr_id, 'items' => 27));
		}
		else
		{
			$pp_instagram_username = get_option('pp_instagram_username');
			$pp_instagram_access_token = get_option('pp_instagram_access_token');
			$photos_arr = grandblog_get_instagram($pp_instagram_username, $pp_instagram_access_token, 27);
		}
		
		wp_enqueue_script("grandblog-modernizr", get_template_directory_uri()."/js/modernizr.js", false, THEMEVERSION, true);
		wp_enqueue_script("grandblog-jquery-gridrotator", get_template_directory_uri()."/js/jquery.gridrotator.js", false, THEMEVERSION, true);
		wp_enqueue_script("grandblog-script-footer-gridrotator", get_template_directory_uri()."/templates/script-gridrotator.php?grid=footer_photostream&amp;rows=2", false, THEMEVERSION, true);
?>
<br class="clear"/>
<div id="footer_photostream" class="footer_photostream_wrapper ri-grid ri-grid-size-3">
	<h2 class="widgettitle photostream">
		<?php
			if($pp_photostream == 'instagram')
			{
		?>
			<i class="fa fa-instagram marginright"></i><?php echo esc_html($pp_instagram_username); ?>
		<?php
			}
			else
			{
		?>
			<i class="fa fa-flickr marginright"></i>Flickr
		<?php
			}
		?>
	</h2>
	<ul>
		<?php
			foreach($photos_arr as $photo)
			{
		?>
			<li><a target="_blank" href="<?php echo esc_url($photo['link']); ?>"><img src="<?php echo esc_url($photo['thumb_url']); ?>" alt="" /></a></li>
		<?php
			}
		?>
	</ul>
</div>
<?php
	}
?>

<?php
	//Get Footer Sidebar
	$tg_footer_sidebar = kirki_get_option('tg_footer_sidebar');
	if(THEMEDEMO && isset($_GET['footer']) && !empty($_GET['footer']))
	{
	    $tg_footer_sidebar = 0;
	}
?>
<div class="footer_bar <?php if(isset($grandblog_homepage_style) && !empty($grandblog_homepage_style)) { echo esc_attr($grandblog_homepage_style); } ?> <?php if(!empty($grandblog_screen_class)) { ?>split<?php } ?> <?php if(empty($tg_footer_sidebar)) { ?>noborder<?php } ?>">

	<?php
	    if(!empty($tg_footer_sidebar))
	    {
	    	$footer_class = '';
	    	
	    	switch($tg_footer_sidebar)
	    	{
	    		case 1:
	    			$footer_class = 'one';
	    		break;
	    		case 2:
	    			$footer_class = 'two';
	    		break;
	    		case 3:
	    			$footer_class = 'three';
	    		break;
	    		case 4:
	    			$footer_class = 'four';
	    		break;
	    		default:
	    			$footer_class = 'four';
	    		break;
	    	}
	    	
	    	global $grandblog_homepage_style;
	?>
	<div id="footer" class="<?php if(isset($grandblog_homepage_style) && !empty($grandblog_homepage_style)) { echo esc_attr($grandblog_homepage_style); } ?>">
	<ul class="sidebar_widget <?php echo esc_attr($footer_class); ?>">
	    <?php dynamic_sidebar('Footer Sidebar'); ?>
	</ul>
	</div>
	<br class="clear"/>
	<?php
	    }
	?>

	<div class="footer_bar_wrapper <?php if(isset($grandblog_homepage_style) && !empty($grandblog_homepage_style)) { echo esc_attr($grandblog_homepage_style); } ?>">
		<?php
			//Check if display social icons or footer menu
			$tg_footer_copyright_right_area = kirki_get_option('tg_footer_copyright_right_area');
			
			if($tg_footer_copyright_right_area=='social')
			{
				if($grandblog_homepage_style!='flow' && $grandblog_homepage_style!='fullscreen' && $grandblog_homepage_style!='carousel' && $grandblog_homepage_style!='flip' && $grandblog_homepage_style!='fullscreen_video')
				{	
					//Check if open link in new window
					$tg_footer_social_link = kirki_get_option('tg_footer_social_link');
			?>
			<div class="social_wrapper">
			    <ul>
			    	<?php
			    		$pp_facebook_url = get_option('pp_facebook_url');
			    		
			    		if(!empty($pp_facebook_url))
			    		{
			    	?>
			    	<li class="facebook"><a <?php if(!empty($tg_footer_social_link)) { ?>target="_blank"<?php } ?> href="<?php echo esc_url($pp_facebook_url); ?>"><i class="fa fa-facebook"></i></a></li>
			    	<?php
			    		}
			    	?>
			    	<?php
			    		$pp_twitter_username = get_option('pp_twitter_username');
			    		
			    		if(!empty($pp_twitter_username))
			    		{
			    	?>
			    	<li class="twitter"><a <?php if(!empty($tg_footer_social_link)) { ?>target="_blank"<?php } ?> href="http://twitter.com/<?php echo esc_attr($pp_twitter_username); ?>"><i class="fa fa-twitter"></i></a></li>
			    	<?php
			    		}
			    	?>
			    	<?php
			    		$pp_flickr_username = get_option('pp_flickr_username');
			    		
			    		if(!empty($pp_flickr_username))
			    		{
			    	?>
			    	<li class="flickr"><a <?php if(!empty($tg_footer_social_link)) { ?>target="_blank"<?php } ?> title="Flickr" href="http://flickr.com/people/<?php echo esc_attr($pp_flickr_username); ?>"><i class="fa fa-flickr"></i></a></li>
			    	<?php
			    		}
			    	?>
			    	<?php
			    		$pp_youtube_url = get_option('pp_youtube_url');
			    		
			    		if(!empty($pp_youtube_url))
			    		{
			    	?>
			    	<li class="youtube"><a <?php if(!empty($tg_footer_social_link)) { ?>target="_blank"<?php } ?> title="Youtube" href="<?php echo esc_url($pp_youtube_url); ?>"><i class="fa fa-youtube"></i></a></li>
			    	<?php
			    		}
			    	?>
			    	<?php
			    		$pp_vimeo_username = get_option('pp_vimeo_username');
			    		
			    		if(!empty($pp_vimeo_username))
			    		{
			    	?>
			    	<li class="vimeo"><a <?php if(!empty($tg_footer_social_link)) { ?>target="_blank"<?php } ?> title="Vimeo" href="http://vimeo.com/<?php echo esc_attr($pp_vimeo_username); ?>"><i class="fa fa-vimeo-square"></i></i></a></li>
			    	<?php
			    		}
			    	?>
			    	<?php
			    		$pp_tumblr_username = get_option('pp_tumblr_username');
			    		
			    		if(!empty($pp_tumblr_username))
			    		{
			    	?>
			    	<li class="tumblr"><a <?php if(!empty($tg_footer_social_link)) { ?>target="_blank"<?php } ?> title="Tumblr" href="http://<?php echo esc_attr($pp_tumblr_username); ?>.tumblr.com"><i class="fa fa-tumblr"></i></a></li>
			    	<?php
			    		}
			    	?>
			    	<?php
			    		$pp_google_url = get_option('pp_google_url');
			    		
			    		if(!empty($pp_google_url))
			    		{
			    	?>
			    	<li class="google"><a <?php if(!empty($tg_footer_social_link)) { ?>target="_blank"<?php } ?> title="Google+" href="<?php echo esc_url($pp_google_url); ?>"><i class="fa fa-google-plus"></i></a></li>
			    	<?php
			    		}
			    	?>
			    	<?php
			    		$pp_dribbble_username = get_option('pp_dribbble_username');
			    		
			    		if(!empty($pp_dribbble_username))
			    		{
			    	?>
			    	<li class="dribbble"><a <?php if(!empty($tg_footer_social_link)) { ?>target="_blank"<?php } ?> title="Dribbble" href="http://dribbble.com/<?php echo esc_attr($pp_dribbble_username); ?>"><i class="fa fa-dribbble"></i></a></li>
			    	<?php
			    		}
			    	?>
			    	<?php
			    		$pp_linkedin_username = get_option('pp_linkedin_username');
			    		
			    		if(!empty($pp_linkedin_username))
			    		{
			    	?>
			    	<li class="linkedin"><a <?php if(!empty($tg_footer_social_link)) { ?>target="_blank"<?php } ?> title="Linkedin" href="<?php echo esc_url($pp_linkedin_username); ?>"><i class="fa fa-linkedin"></i></a></li>
			    	<?php
			    		}
			    	?>
			    	<?php
			            $pp_pinterest_username = get_option('pp_pinterest_username');
			            
			            if(!empty($pp_pinterest_username))
			            {
			        ?>
			        <li class="pinterest"><a <?php if(!empty($tg_footer_social_link)) { ?>target="_blank"<?php } ?> title="Pinterest" href="http://pinterest.com/<?php echo esc_attr($pp_pinterest_username); ?>"><i class="fa fa-pinterest"></i></a></li>
			        <?php
			            }
			        ?>
			        <?php
			        	$pp_instagram_username = get_option('pp_instagram_username');
			        	
			        	if(!empty($pp_instagram_username))
			        	{
			        ?>
			        <li class="instagram"><a <?php if(!empty($tg_footer_social_link)) { ?>target="_blank"<?php } ?> title="Instagram" href="http://instagram.com/<?php echo esc_attr($pp_instagram_username); ?>"><i class="fa fa-instagram"></i></a></li>
			        <?php
			        	}
			        ?>
			        <?php
					    $pp_behance_username = get_option('pp_behance_username');
					    
					    if(!empty($pp_behance_username))
					    {
					?>
					<li class="behance"><a <?php if(!empty($pp_topbar_social_link_blank)) { ?>target="_blank"<?php } ?> title="Behance" href="http://behance.net/<?php echo esc_attr($pp_behance_username); ?>"><i class="fa fa-behance-square"></i></a></li>
					<?php
					    }
					?>
			    </ul>
			</div>
		<?php
				}
			} //End if display social icons
			else
			{
				if ( has_nav_menu( 'footer-menu' ) ) 
			    {
				    wp_nav_menu( 
				        	array( 
				        		'menu_id'			=> 'footer_menu',
				        		'menu_class'		=> 'footer_nav',
				        		'theme_location' 	=> 'footer-menu',
				        	) 
				    ); 
				}
			}
		?>
	    <?php
	    	//Display copyright text
	        $tg_footer_copyright_text = kirki_get_option('tg_footer_copyright_text');

	        if(!empty($tg_footer_copyright_text))
	        {
	        	echo '<div id="copyright">'.wp_kses_post(htmlspecialchars_decode($tg_footer_copyright_text)).'</div>';
	        }
	    ?>
	    
	    <?php
	    	//Check if display to top button
	    	$tg_footer_copyright_totop = kirki_get_option('tg_footer_copyright_totop');
	    	
	    	if(!empty($tg_footer_copyright_totop))
	    	{
	    ?>
	    	<a id="toTop"><i class="fa fa-angle-up"></i></a>
	    <?php
	    	}
	    ?>
	    
	    <?php
	    	$tg_boxed = kirki_get_option('tg_boxed');
		    if(THEMEDEMO && isset($_GET['boxed']) && !empty($_GET['boxed']))
		    {
		    	$tg_boxed = 1;
		    }
		    
		    //If enable boxed layout
		    if(!empty($tg_boxed))
		    {
	    ?>
	    <br class="clear"/>
	    <?php
	    	}
	    ?>
	</div>
</div>

</div>

<?php
    } //End if not blank template
?>

<div id="overlay_background">
	<?php
		if(is_single())
		{
	?>
	<div id="fullscreen_share_wrapper">
		<div class="fullscreen_share_content">
	<?php
			get_template_part("/templates/template-share");
	?>
		</div>
	</div>
	<?php
		}
	?>
</div>

<?php
    //Check if theme demo then enable layout switcher
    if(THEMEDEMO)
    {
?>
    <div id="option_wrapper">
    <div class="inner">
    	<div style="text-align:center">
    	<a target="_blank" href="http://themeforest.net/item/lets-blog-responsive-blog-wordpress-theme/12340419?ref=ThemeGoods&license=regular&open_purchase_for_item_id=12340419&purchasable=source&ref=ThemeGoods" class="button buy">BUY THIS THEME NOW!</a>
    	<br/><br/>
    	<span class="readmore" style="color:#222;">Predefined Styling</span><br/><br/>
    	<p>
    	Grand Blog is so powerful theme allow you to easily create your own style of blog and magazine site. Here are example styling that can be imported in one click.</p>
    	<ul class="demo_list">
    		<li>
        		<img src="<?php echo get_stylesheet_directory_uri(); ?>/cache/demos/screen1.jpg" alt=""/>
        		<div class="demo_thumb_hover_wrapper">
        		    <div class="demo_thumb_hover_inner">
        		    	<div class="demo_thumb_desc">
    	    	    		<h6>Classic</h6>
    	    	    		<a href="<?php echo site_url(); ?>" target="_blank" class="button white">Launch</a>
        		    	</div> 
        		    </div>	   
        		</div>		   
    		</li>
    		<li>
        		<img src="<?php echo get_stylesheet_directory_uri(); ?>/cache/demos/screen2.jpg" alt=""/>
        		<div class="demo_thumb_hover_wrapper">
        		    <div class="demo_thumb_hover_inner">
        		    	<div class="demo_thumb_desc">
    	    	    		<h6>3 Columns Slider</h6>
    	    	    		<a href="<?php echo site_url('/?layout=fullwidth'); ?>" target="_blank" class="button white">Launch</a>
        		    	</div> 
        		    </div>	   
        		</div>		   
    		</li>
    		<li>
        		<img src="<?php echo get_stylesheet_directory_uri(); ?>/cache/demos/screen3.jpg" alt=""/>
        		<div class="demo_thumb_hover_wrapper">
        		    <div class="demo_thumb_hover_inner">
        		    	<div class="demo_thumb_desc">
    	    	    		<h6>Grid With Frame</h6>
    	    	    		<a href="<?php echo site_url('/?layout=3cols&frame=1'); ?>" target="_blank" class="button white">Launch</a>
        		    	</div> 
        		    </div>	   
        		</div>		   
    		</li>
    		<li>
        		<img src="<?php echo get_stylesheet_directory_uri(); ?>/cache/demos/screen4.jpg" alt=""/>
        		<div class="demo_thumb_hover_wrapper">
        		    <div class="demo_thumb_hover_inner">
        		    	<div class="demo_thumb_desc">
    	    	    		<h6>Boxed Layout</h6>
    	    	    		<a href="<?php echo site_url('/?layout=fullwidth&frame=noframe&boxed=1'); ?>" target="_blank" class="button white">Launch</a>
        		    	</div> 
        		    </div>	   
        		</div>		   
    		</li>
    		<li>
        		<img src="<?php echo get_stylesheet_directory_uri(); ?>/cache/demos/screen5.jpg" alt=""/>
        		<div class="demo_thumb_hover_wrapper">
        		    <div class="demo_thumb_hover_inner">
        		    	<div class="demo_thumb_desc">
    	    	    		<h6>Classic With Top Menu</h6>
    	    	    		<a href="<?php echo site_url('/?topbar=1'); ?>" target="_blank" class="button white">Launch</a>
        		    	</div> 
        		    </div>	   
        		</div>		   
    		</li>
    		<li>
        		<img src="<?php echo get_stylesheet_directory_uri(); ?>/cache/demos/screen6.jpg" alt=""/>
        		<div class="demo_thumb_hover_wrapper">
        		    <div class="demo_thumb_hover_inner">
        		    	<div class="demo_thumb_desc">
    	    	    		<h6>Simple Menu</h6>
    	    	    		<a href="<?php echo site_url('/?menu=1'); ?>" target="_blank" class="button white">Launch</a>
        		    	</div> 
        		    </div>	   
        		</div>		   
    		</li>
    	</ul>
    	</div>
    </div>
    </div>
    <div id="option_btn">
    	<i class="fa fa-cog fa-spin"></i>
    </div>
<?php
    	wp_enqueue_script("grandblog-jquery-cookie", get_stylesheet_directory_uri()."/js/jquery.cookie.js", false, THEMEVERSION, true);
    	wp_enqueue_script("grandblog-script-demo", get_stylesheet_directory_uri()."/templates/script-demo.php", false, THEMEVERSION, true);
    }
?>

<?php
    $tg_frame = kirki_get_option('tg_frame');
    if(THEMEDEMO && isset($_GET['frame']) && $_GET['frame'] == 1)
    {
	    $tg_frame = 1;
?>
<style>
#option_btn
{
	right: 0;
}
</style>
<?php
    }
    
    if(!empty($tg_frame))
    {
    	wp_enqueue_style("grandblog-frame-css", get_template_directory_uri()."/css/frame.css", false, THEMEVERSION, "all");
?>
    <div class="frame_top"></div>
    <div class="frame_bottom"></div>
    <div class="frame_left"></div>
    <div class="frame_right"></div>
<?php
    }
    if(THEMEDEMO && isset($_GET['frame_color']) && !empty($_GET['frame_color']))
    {
?>
<style>
.frame_top, .frame_bottom, .frame_left, .frame_right { background: <?php echo esc_html($_GET['frame_color']); ?> !important; }
</style>
<?php
	}
?>

<?php
	/* Always have wp_footer() just before the closing </body>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to reference JavaScript files.
	 */

	wp_footer();
?>

	<!-- KEVIN -->
	<!-- Google Code for Remarketing Tag -->
	<script type="text/javascript">
	  /* <![CDATA[ */
	  var google_conversion_id = 964523981;
	  var google_custom_params = window.google_tag_params;
	  var google_remarketing_only = true;
	  /* ]]> */
	</script>
	<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js"></script>
	<noscript>
	  <div style="display:inline;">
	    <img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/964523981/?value=0&amp;guid=ON&amp;script=0"/>
	  </div>
	</noscript>

</body>
</html>
