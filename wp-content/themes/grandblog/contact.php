<?php
/**
 * Template Name: Contact
 * The main template file for display page.
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

$page_style = 'Right Sidebar';
$page_sidebar = get_post_meta($current_page_id, 'page_sidebar', true);

if(empty($page_sidebar))
{
	$page_sidebar = 'Contact Sidebar';
}

get_header(); 
?>

<?php
    //Include custom header feature
	get_template_part("/templates/template-header");
?>

    <div class="inner">
    
    <!-- Begin main content -->
    <div class="inner_wrapper">
        	
        <div class="sidebar_content full_width nopadding">
        	<div class="sidebar_content page_content">
	        	 <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
				 	<?php the_content(); ?>
				 <?php endwhile; ?>
				 
				 <?php
    				$pp_contact_form = unserialize(get_option('pp_contact_form_sort_data'));
    				wp_enqueue_script("grandblog-jquery-validate", get_template_directory_uri()."/js/jquery.validate.js", false, THEMEVERSION, true);
    				wp_register_script("grandblog-script-contact-form", get_stylesheet_directory_uri()."/templates/script-contact-form.php", false, THEMEVERSION, true);
					$params = array(
					  'ajaxurl' => esc_url(admin_url('admin-ajax.php')),
					  'ajax_nonce' => wp_create_nonce('tgajax-post-contact-nonce'),
					);
					wp_localize_script( 'grandblog-script-contact-form', 'tgAjax', $params );
					wp_enqueue_script("grandblog-script-contact-form", get_stylesheet_directory_uri()."/templates/script-contact-form.php", false, THEMEVERSION, true);
    			?>
    			<div id="reponse_msg"><ul></ul></div>
    			
    			<form id="contact_form" method="post" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>">
	    			<input type="hidden" id="action" name="action" value="grandblog_contact_mailer"/>

    				<?php 
			    		if(is_array($pp_contact_form) && !empty($pp_contact_form))
			    		{
			    			foreach($pp_contact_form as $form_input)
			    			{
			    				switch($form_input)
			    				{
			    					case 1:
			    	?>
			    					<label for="your_name"><?php echo esc_html_e( 'Name *', 'grandblog-translation' ); ?></label>
			        				<input id="your_name" name="your_name" type="text" class="required_field" style="width:100%"/>
			        				<br/>		
			    	<?php
			    					break;
			    					
			    					case 2:
			    	?>
			    					
			    					<label for="email"><?php echo esc_html_e( 'Email *', 'grandblog-translation' ); ?></label>
			        				<input id="email" name="email" type="text" class="required_field email" style="width:100%"/>
			        				<br/>			
			    	<?php
			    					break;
			    					
			    					case 3:
			    	?>
			    					
			    					<label for="message"><?php echo esc_html_e( 'Message *', 'grandblog-translation' ); ?></label>
			        				<textarea id="message" name="message" rows="7" cols="10" class="required_field" style="width:100%"></textarea>
			        				<br/>			
			    	<?php
			    					break;
			    					
			    					case 4:
			    	?>
			    					
			    					<label for="address"><?php echo esc_html_e( 'Address', 'grandblog-translation' ); ?></label>
			        				<input id="address" name="address" type="text" style="width:100%"/>
			        				<br/>		
			    	<?php
			    					break;
			    					
			    					case 5:
			    	?>
			    					
			    					<label for="phone"><?php echo esc_html_e( 'Phone', 'grandblog-translation' ); ?></label>
			        				<input id="phone" name="phone" type="text" style="width:100%"/>
			        				<br/>		
			    	<?php
			    					break;
			    					
			    					case 6:
			    	?>
			    					
			    					<label for="mobile"><?php echo esc_html_e( 'Mobile', 'grandblog-translation' ); ?></label>
			        				<input id="mobile" name="mobile" type="text" style="width:100%"/>
			        				<br/>			
			    	<?php
			    					break;
			    					
			    					case 7:
			    	?>
			    					
			    					<label for="company"><?php echo esc_html_e( 'Company Name', 'grandblog-translation' ); ?></label>
			        				<input id="company" name="company" type="text" style="width:100%"/>
			        				<br/>			
			    	<?php
			    					break;
			    					
			    					case 8:
			    	?>
			    					
			    					<label for="country"><?php echo esc_html_e( 'Country', 'grandblog-translation' ); ?></label>				
			        				<input id="country" name="country" type="text" style="width:100%"/>
			        				<br/>			
			    	<?php
			    					break;
			    				}
			    			}
			    		}
			    	?>
			    	
			    	<?php
			    		$pp_contact_enable_captcha = get_option('pp_contact_enable_captcha');
			    		
			    		if(!empty($pp_contact_enable_captcha))
			    		{
			    	?>
			    		
			    		<div id="captcha-wrap">
							<div class="captcha-box">
								<img src="<?php echo get_stylesheet_directory_uri(); ?>/get_captcha.php" alt="" id="captcha" />
							</div>
							<div class="text-box">
								<label>Type the two words:</label>
								<input name="captcha-code" type="text" id="captcha-code">
							</div>
							<div class="captcha-action">
								<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/refresh.jpg"  alt="" id="captcha-refresh" />
							</div>
						</div>
						<br class="clear"/><br/><br/>
					
					<?php
					}
					?>
			    	
			    	<p>
    					<input id="contact_submit_btn" type="submit" value="<?php echo esc_html_e( 'Submit Form', 'grandblog-translation' ); ?>"/>
			    	</p>
    			</form>
				 
				<?php
				if (comments_open($post->ID)) 
				{
				?>
				<div class="fullwidth_comment_wrapper sidebar">
					<?php comments_template( '', true ); ?>
				</div>
				<?php
				}
				?>
        	</div>
        	
        	<div class="sidebar_wrapper">
	            <div class="sidebar">
	            
	            	<div class="content">
	            
	            		<?php 
						$page_sidebar = sanitize_title($page_sidebar);
						
						if (is_active_sidebar($page_sidebar)) { ?>
		    	    		<ul class="sidebar_widget">
		    	    		<?php dynamic_sidebar($page_sidebar); ?>
		    	    		</ul>
		    	    	<?php } ?>
	            	
	            	</div>
	        
	            </div>
            <br class="clear"/>
			</div>
        </div>
    
    </div>
    <!-- End main content -->
    </div>
</div>
<br class="clear"/><br/>
<?php get_footer(); ?>
