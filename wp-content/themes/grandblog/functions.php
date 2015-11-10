<?php
/*
Theme Name: Grand Blog Theme
Theme URI: http://themes.themegoods2.com/grandblog
Author: ThemeGoods
Author URI: http://themeforest.net/user/ThemeGoods
License: GPLv2
*/

//Setup theme default constant and data
require_once get_template_directory() . "/lib/config.lib.php";

//Setup theme translation
require_once get_template_directory() . "/lib/translation.lib.php";

//Setup theme admin action handler
require_once get_template_directory() . "/lib/admin.action.lib.php";

//Setup theme support and image size handler
require_once get_template_directory() . "/lib/theme.support.lib.php";

//Get custom function
require_once get_template_directory() . "/lib/custom.lib.php";

//Setup menu settings
require_once get_template_directory() . "/lib/menu.lib.php";

//Setup CSS compression related functions
require_once get_template_directory() . "/lib/cssmin.lib.php";

//Setup JS compression related functions
require_once get_template_directory() . "/lib/jsmin.lib.php";

//Setup Sidebar
require_once get_template_directory() . "/lib/sidebar.lib.php";

//Setup theme custom widgets
require_once get_template_directory() . "/lib/widgets.lib.php";

//Setup auto update
require_once get_template_directory() . "/lib/theme.update.lib.php";

//Setup theme admin settings
require_once get_template_directory() . "/lib/admin.lib.php";


//Create theme admin panel
function grandblog_add_admin() {
 
	global $options;
	
	if ( isset($_GET['page']) && $_GET['page'] == basename(__FILE__) ) {
	 
		if ( isset($_REQUEST['action']) && 'save' == $_REQUEST['action'] ) {
	 
			foreach ($options as $value) 
			{
				if($value['type'] != 'image' && isset($value['id']) && isset($_REQUEST[ $value['id'] ]))
				{
					update_option( $value['id'], $_REQUEST[ $value['id'] ] );
				}
			}
			
			foreach ($options as $value) {
			
				if( isset($value['id']) && isset( $_REQUEST[ $value['id'] ] )) 
				{ 
	
					if($value['id'] != SHORTNAME."_sidebar0" && $value['id'] != SHORTNAME."_ggfont0")
					{
						//if sortable type
						if(is_admin() && $value['type'] == 'sortable')
						{
							$sortable_array = serialize($_REQUEST[ $value['id'] ]);
							
							$sortable_data = $_REQUEST[ $value['id'].'_sort_data'];
							$sortable_data_arr = explode(',', $sortable_data);
							$new_sortable_data = array();
							
							foreach($sortable_data_arr as $key => $sortable_data_item)
							{
								$sortable_data_item_arr = explode('_', $sortable_data_item);
								
								if(isset($sortable_data_item_arr[0]))
								{
									$new_sortable_data[] = $sortable_data_item_arr[0];
								}
							}
							
							update_option( $value['id'], $sortable_array );
							update_option( $value['id'].'_sort_data', serialize($new_sortable_data) );
						}
						elseif(is_admin() && $value['type'] == 'font')
						{
							if(!empty($_REQUEST[ $value['id'] ]))
							{
								update_option( $value['id'], $_REQUEST[ $value['id'] ] );
								update_option( $value['id'].'_value', $_REQUEST[ $value['id'].'_value' ] );
							}
							else
							{
								delete_option( $value['id'] );
								delete_option( $value['id'].'_value' );
							}
						}
						elseif(is_admin())
						{
							if($value['type']=='image')
							{
								update_option( $value['id'], esc_url($_REQUEST[ $value['id'] ])  );
							}
							elseif($value['type']=='textarea')
							{
								if(isset($value['validation']) && !empty($value['validation']))
								{
									update_option( $value['id'], esc_textarea($_REQUEST[ $value['id'] ]) );
								}
								else
								{
									update_option( $value['id'], $_REQUEST[ $value['id'] ] );
								}
							}
							elseif($value['type']=='iphone_checkboxes' OR $value['type']=='jslider')
							{
								update_option( $value['id'], $_REQUEST[ $value['id'] ]  );
							}
							else
							{
								if(isset($value['validation']) && !empty($value['validation']))
								{
									$request_value = $_REQUEST[ $value['id'] ];
									
									//Begin data validation
									switch($value['validation'])
									{
										case 'text':
										default:
											$request_value = sanitize_text_field($request_value);
										
										break;
										
										case 'email':
											$request_value = sanitize_email($request_value);
	
										break;
										
										case 'javascript':
											$request_value = sanitize_text_field($request_value);
	
										break;
										
									}
									update_option( $value['id'], $request_value);
								}
								else
								{
									update_option( $value['id'], $_REQUEST[ $value['id'] ]  );
								}
							}
						}
					}
					elseif(is_admin() && isset($_REQUEST[ $value['id'] ]) && !empty($_REQUEST[ $value['id'] ]))
					{
						if($value['id'] == SHORTNAME."_sidebar0")
						{
							//get last sidebar serialize array
							$current_sidebar = get_option(SHORTNAME."_sidebar");
							$request_value = $_REQUEST[ $value['id'] ];
							$request_value = sanitize_text_field($request_value);
							
							$current_sidebar[ $request_value ] = $request_value;
				
							update_option( SHORTNAME."_sidebar", $current_sidebar );
						}
						elseif($value['id'] == SHORTNAME."_ggfont0")
						{
							//get last ggfonts serialize array
							$current_ggfont = get_option(SHORTNAME."_ggfont");
							$current_ggfont[ $_REQUEST[ $value['id'] ] ] = $_REQUEST[ $value['id'] ];
				
							update_option( SHORTNAME."_ggfont", $current_ggfont );
						}
					}
				} 
				else 
				{ 
					if(is_admin() && isset($value['id']))
					{
						delete_option( $value['id'] );
					}
				} 
			}
	
			header("Location: admin.php?page=functions.php&saved=true".$_REQUEST['current_tab']);
		}  
	} 
	 
	add_theme_page('Theme Setting', 'Theme Setting', 'administrator', basename(__FILE__), 'grandblog_admin', '');
	}
	
	function grandblog_fonts_url() 
	{
		//Get all Google Web font CSS
		global $grandblog_google_fonts;
		
		$tg_fonts_family = array();
		if(is_array($grandblog_google_fonts) && !empty($grandblog_google_fonts))
		{
			foreach($grandblog_google_fonts as $tg_font)
			{
				$tg_fonts_family[] = kirki_get_option($tg_font);
			}
		}
	
		$tg_fonts_family = array_unique($tg_fonts_family);
		$font_families = array();
	
		foreach($tg_fonts_family as $key => $tg_google_font)
		{	    
		    if(!empty($tg_google_font))
		    {
		    	$font_families[] = $tg_google_font.':300,400,600,700,900,400italic';
		    }
		}
		
		$query_args = array(
		    'family' => urlencode( implode( '|', $font_families ) ),
		    'subset' => urlencode( 'latin,cyrillic-ext,greek-ext,cyrillic' ),
		);
		
		$fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
	 
	    return esc_url_raw( $fonts_url );
	}
	
	function grandblog_enqueue_admin_page_scripts() {
	
		global $current_screen;
		
		//Register theme admin CSS
		wp_enqueue_style(array('thickbox'));
		wp_enqueue_style("grandblog-functions", get_template_directory_uri()."/functions/functions.css", false, THEMEVERSION, "all");
		
		if(property_exists($current_screen, 'post_type') && ($current_screen->post_type == 'page'))
		{
			wp_enqueue_style("grandblog-jqueryui", get_template_directory_uri()."/css/jqueryui/custom.css", false, THEMEVERSION, "all");
		}
		wp_enqueue_style("grandblog-icheck", get_template_directory_uri()."/functions/skins/flat/green.css", false, THEMEVERSION, "all");
		
		//Register theme admin javascript
		wp_enqueue_script(array('jquery-ui-core', 'jquery-ui-sortable', 'media-upload', 'thickbox'));
		wp_enqueue_script("grandblog-jquery.icheck.min", get_template_directory_uri()."/functions/jquery.icheck.min.js", false, THEMEVERSION);
		wp_enqueue_script("grandblog-hint", get_template_directory_uri()."/js/hint.js", false, THEMEVERSION, true);
		
		wp_register_script( "grandblog-admin-cript", get_template_directory_uri()."/functions/rm_script.js", false, THEMEVERSION, true);
		$params = array(
		  'ajaxurl' => esc_url(admin_url('admin-ajax.php')),
		);
		wp_localize_script( 'grandblog-admin-cript', 'tgAjax', $params );
		wp_enqueue_script( 'grandblog-admin-cript' );
	
	}
	
	add_action('admin_enqueue_scripts',	'grandblog_enqueue_admin_page_scripts' );
	
	function grandblog_enqueue_front_page_scripts() {
	
	    //enqueue frontend css files
		$pp_advance_combine_css = get_option('pp_advance_combine_css');
		
		//If enable animation
		$pp_animation = get_option('pp_animation');
		
		//Get theme cache folder
		$upload_dir = wp_upload_dir();
		$cache_dir = '';
		$cache_url = '';
		
		if(isset($upload_dir['basedir']))
		{
			$cache_dir = THEMEUPLOAD;
		}
		
		if(isset($upload_dir['baseurl']))
		{
			$cache_url = THEMEUPLOADURL;
		}
		    
		if(!empty($pp_advance_combine_css))
		{
		    if(!file_exists($cache_dir."/combined.css"))
		    {
		    	$cssmin = new CSSMin();
		    	
		    	$css_arr = array(
		    	    get_template_directory().'/css/reset.css',
		    	    get_template_directory().'/css/wordpress.css',
		    	    get_template_directory().'/css/animation.css',
		    	    get_template_directory().'/css/magnific-popup.css',
		    	    get_template_directory().'/css/jqueryui/custom.css',
		    	    get_template_directory().'/js/flexslider/flexslider.css',
		    	    get_template_directory().'/css/tooltipster.css',
		    	    get_template_directory().'/css/screen.css',
		    	    get_template_directory().'/css/customize.css',
		    	);
		    	
		    	//If using child theme
		    	if(!is_child_theme())
		    	{
		    		$css_arr[] = get_template_directory().'/css/screen.css';
		    	}
		    	else
		    	{
		    		$css_arr[] = get_template_directory().'/style.css';
		    	}
		    	
		    	$cssmin->addFiles($css_arr);
		    	
		    	// Set original CSS from all files
		    	$cssmin->setOriginalCSS();
		    	$cssmin->compressCSS();
		    	
		    	$css = $cssmin->printCompressedCSS();
		    	
		    	global $wp_filesystem;
				$wp_filesystem->put_contents(
				  $cache_dir."combined.css",
				  $css,
				  FS_CHMOD_FILE
				);
		    }
		    
		    wp_enqueue_style("grandblog-combined-css", $cache_url."combined.css", false, "");
		}
		else
		{
			wp_enqueue_style("grandblog-reset-css", get_template_directory_uri()."/css/reset.css", false, "");
			wp_enqueue_style("grandblog-wordpress-css", get_template_directory_uri()."/css/wordpress.css", false, "");
			wp_enqueue_style("grandblog-animation", get_template_directory_uri()."/css/animation.css", false, "", "all");
		    wp_enqueue_style("grandblog-magnific-popup-css", get_template_directory_uri()."/css/magnific-popup.css", false, "", "all");
		    wp_enqueue_style("grandblog-jquery-ui-css", get_template_directory_uri()."/css/jqueryui/custom.css", false, "");
		    wp_enqueue_style("grandblog-flexslider-css", get_template_directory_uri()."/js/flexslider/flexslider.css", false, "", "all");
		    wp_enqueue_style("grandblog-tooltipster-css", get_template_directory_uri()."/css/tooltipster.css", false, "", "all");
		    wp_enqueue_style("grandblog-screen-css", get_template_directory_uri().'/css/screen.css', false, "", "all");
		    wp_enqueue_style("grandblog-customize-css", get_template_directory_uri().'/css/customize.css', false, "", "all");
		}
		
		//Add Google Font
		wp_enqueue_style( 'grandblog-fonts', grandblog_fonts_url(), array(), null );
		
		//Add Font Awesome Support
		wp_enqueue_style("grandblog-fontawesome-css", get_template_directory_uri()."/css/font-awesome.min.css", false, "", "all");
		
		//Add custom CSS on theme admin support
		wp_enqueue_style("grandblog-script-custom-css", get_template_directory_uri()."/templates/script-custom-css.php", false, "", "all");
		
		$tg_boxed = kirki_get_option('tg_boxed');
	    if(THEMEDEMO && isset($_GET['boxed']) && !empty($_GET['boxed']))
	    {
	    	$tg_boxed = 1;
	    }
	    
	    //If enable boxed layout
	    if(!empty($tg_boxed))
	    {
	    	wp_enqueue_style("grandblog-boxed-css", get_template_directory_uri().'/css/boxed.css', false, "", "all");
	    }
		
		//If using child theme
		if(is_child_theme())
		{
		    wp_enqueue_style('grandblog-childtheme-css', get_stylesheet_directory_uri()."/style.css", false, "", "all");
		}
		
		//Enqueue javascripts
		wp_enqueue_script(array('jquery'));
		
		$js_path = get_template_directory()."/js/";
		$js_arr = array(
			'jquery.magnific-popup.js',
			'jquery.easing.js',
		    'waypoints.min.js',
		    'jquery.isotope.js',
		    'jquery.tooltipster.min.js',
		    'custom_plugins.js',
		    'custom.js',
		);
		$js = "";
	
		$pp_advance_combine_js = get_option('pp_advance_combine_js');
		
		if(!empty($pp_advance_combine_js))
		{	
			if(!file_exists($cache_dir."combined.js"))
			{
				global $wp_filesystem;
			
				foreach($js_arr as $file) {
					if($file != 'jquery.js' && $file != 'jquery-ui.js')
					{
	    				$js .= JSMin::minify($wp_filesystem->get_contents($js_path.$file));
	    			}
				}
				
				$wp_filesystem->put_contents(
				  $cache_dir."combined.js",
				  $js,
				  FS_CHMOD_FILE
				);
			}
	
			wp_enqueue_script("grandblog-combined-js", $cache_url."/combined.js", false, "", true);
		}
		else
		{
			foreach($js_arr as $file) {
				if($file != 'jquery.js' && $file != 'jquery-ui.js')
				{
					wp_enqueue_script($file, get_template_directory_uri()."/js/".$file, false, "", true);
				}
			}
		}
	}
	add_action( 'wp_enqueue_scripts', 'grandblog_enqueue_front_page_scripts' );
	
	
	//Enqueue mobile CSS after all others CSS load
	function grandblog_register_mobile_css() {
		//Check if enable responsive layout
		$tg_mobile_responsive = kirki_get_option('tg_mobile_responsive');
		
		if(!empty($tg_mobile_responsive))
		{
			//enqueue frontend css files
			$pp_advance_combine_css = get_option('pp_advance_combine_css');
		
			if(!empty($pp_advance_combine_css))
			{
				wp_enqueue_style('grandblog-script-responsive-css', get_template_directory_uri()."/templates/script-responsive-css.php", false, "", "all");
			}
			else
			{
		    	wp_enqueue_style('grandblog-script-responsive-css', get_template_directory_uri()."/css/grid.css", false, "", "all");
		    }
		}
	}
	add_action('wp_enqueue_scripts', 'grandblog_register_mobile_css', 15);
	
	
	function grandblog_admin() {
	 
	global $options;
	$i=0;
	
	$pp_font_family = get_option('pp_font_family');
	
	if(function_exists( 'wp_enqueue_media' )){
	    wp_enqueue_media();
	}
	?>
		
		<div id="pp_loading"><span><?php esc_html_e( 'Updating...', 'grandblog-translation' ); ?></span></div>
		<div id="pp_success"><span><?php esc_html_e( 'Successfully<br/>Update', 'grandblog-translation' ); ?></span></div>
		
		<?php
			if(isset($_GET['saved']) == 'true')
			{
		?>
			<script>
				jQuery('#pp_success').show();
		            	
		        setTimeout(function() {
	              jQuery('#pp_success').fadeOut();
	            }, 2000);
			</script>
		<?php
			}
		?>
		
		<form id="pp_form" method="post" enctype="multipart/form-data">
		<div class="pp_wrap rm_wrap">
		
		<div class="header_wrap">
			<div style="float:left">
			<h2><?php esc_html_e( 'Theme Setting', 'grandblog-translation' ); ?><span class="pp_version">v<?php echo THEMEVERSION; ?></span></h2><br/>
			<a href="https://themegoods.ticksy.com/" target="_blank"><?php esc_html_e( 'Theme Support', 'grandblog-translation' ); ?></a>
			</div>
			<div style="float:right;margin:32px 0 0 0">
				<input id="save_ppsettings" name="save_ppsettings" class="button button-primary button-large" type="submit" value="<?php esc_html_e( 'Save All Changes', 'grandblog-translation' ); ?>" />
				<br/><br/>
				<input type="hidden" name="action" value="save" />
				<input type="hidden" name="current_tab" id="current_tab" value="#pp_panel_general" />
			</div>
			<input type="hidden" name="pp_admin_url" id="pp_admin_url" value="<?php echo get_template_directory_uri(); ?>"/>
			<br style="clear:both"/><br/>
	
	<?php
		//Check if theme has new update
	?>
	
		</div>
		
		<div class="pp_wrap">
		<div id="pp_panel">
		<?php 
			foreach ($options as $value) {
				
				$active = '';
				
				if($value['type'] == 'section')
				{
					if($value['name'] == 'General')
					{
						$active = 'nav-tab-active';
					}
					echo '<a id="pp_panel_'.strtolower($value['name']).'_a" href="#pp_panel_'.strtolower($value['name']).'" class="nav-tab '.$active.'"><img src="'.get_template_directory_uri().'/functions/images/icon/'.$value['icon'].'" class="ver_mid"/>'.str_replace('-', ' ', $value['name']).'</a>';
				}
			}
		?>
		</h2>
		</div>
	
		<div class="rm_opts">
		
	<?php 
	$url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	
	foreach ($options as $value) {
	switch ( $value['type'] ) {
	 
	case "open":
	?> <?php break;
	 
	case "close":
	?>
		
		</div>
		</div>
	
	
		<?php break;
	 
	case "title":
	?>
		<br />
	
	
	<?php break;
	 
	case 'text':
		
		//if sidebar input then not show default value
		if($value['id'] != SHORTNAME."_sidebar0" && $value['id'] != SHORTNAME."_ggfont0")
		{
			$default_val = get_option( $value['id'] );
		}
		else
		{
			$default_val = '';	
		}
	?>
	
		<div id="<?php echo esc_attr($value['id']); ?>_section" class="rm_input rm_text"><label for="<?php echo esc_attr($value['id']); ?>"><?php echo stripslashes($value['name']); ?></label><br/>
		<input name="<?php echo esc_attr($value['id']); ?>"
			id="<?php echo esc_attr($value['id']); ?>" type="<?php echo esc_attr($value['type']); ?>"
			value="<?php if ($default_val != "") { echo esc_attr(get_option( $value['id'])) ; } else { echo esc_attr($value['std']); } ?>"
			<?php if(!empty($value['size'])) { echo 'style="width:'.intval($value['size']).'"'; } ?> />
			<small><?php echo esc_attr($value['desc']); ?></small>
		<div class="clearfix"></div>
		
		<?php
		if($value['id'] == SHORTNAME."_sidebar0")
		{
			$current_sidebar = get_option(SHORTNAME."_sidebar");
			
			if(!empty($current_sidebar))
			{
		?>
			<br class="clear"/><br/>
		 	<div class="pp_sortable_wrapper">
			<ul id="current_sidebar" class="rm_list">
	
		<?php
			foreach($current_sidebar as $sidebar)
			{
		?> 
				
				<li id="<?php echo esc_attr($sidebar); ?>"><div class="title"><?php echo esc_html($sidebar); ?></div><a href="<?php echo esc_url($url); ?>" class="sidebar_del" rel="<?php echo esc_attr($sidebar); ?>"><?php esc_html_e( 'Delete', 'grandblog-translation' ); ?></a><br style="clear:both"/></li>
		
		<?php
			}
		?>
		
			</ul>
			</div>
		
		<?php
			}
		}
		elseif($value['id'] == SHORTNAME."_ggfont0")
		{
		?>
			<?php esc_html_e( 'Below are fonts that already installed.', 'grandblog-translation' ); ?><br/>
			<select name="<?php echo SHORTNAME; ?>_sample_ggfont" id="<?php echo SHORTNAME; ?>_sample_ggfont">
			<?php 
				foreach ($pp_font_arr as $key => $option) { ?>
			<option
			<?php if (get_option( $value['id'] ) == $option['css-name']) { echo 'selected="selected"'; } ?>
				value="<?php echo esc_attr($option['css-name']); ?>" data-family="<?php echo esc_attr($option['font-name']); ?>"><?php echo esc_html($option['font-name']); ?></option>
			<?php } ?>
			</select> 
		<?php
			$current_ggfont = get_option(SHORTNAME."_ggfont");
			
			if(!empty($current_ggfont))
			{
		?>
			<br class="clear"/><br/>
		 	<div class="pp_sortable_wrapper">
			<ul id="current_ggfont" class="rm_list">
	
		<?php
		
			foreach($current_ggfont as $ggfont)
			{
		?> 
				
				<li id="<?php echo esc_attr($ggfont); ?>"><div class="title"><?php echo esc_html($ggfont); ?></div><a href="<?php echo esc_url($url); ?>" class="ggfont_del" rel="<?php echo esc_attr($ggfont); ?>"><?php esc_html_e( 'Delete', 'grandblog-translation' ); ?></a><br style="clear:both"/></li>
		
		<?php
			}
		?>
		
			</ul>
			</div>
		
		<?php
			}
		}
		?>
	
		</div>
		<?php
	break;
	
	case 'password':
	?>
	
		<div id="<?php echo esc_attr($value['id']); ?>_section" class="rm_input rm_text"><label for="<?php echo esc_attr($value['id']); ?>"><?php echo stripslashes($value['name']); ?></label><br/>
		<input name="<?php echo esc_attr($value['id']); ?>"
			id="<?php echo esc_attr($value['id']); ?>" type="<?php echo esc_attr($value['type']); ?>"
			value="<?php if ( get_option( $value['id'] ) != "") { echo stripslashes(get_option( $value['id'])  ); } else { echo esc_attr($value['std']); } ?>"
			<?php if(!empty($value['size'])) { echo 'style="width:'.$value['size'].'"'; } ?> />
		<small><?php echo esc_html($value['desc']); ?></small>
		<div class="clearfix"></div>
	
		</div>
		<?php
	break;
	
	break;
	
	case 'image':
	case 'music':
	?>
	
		<div id="<?php echo esc_attr($value['id']); ?>_section" class="rm_input rm_text"><label for="<?php echo esc_attr($value['id']); ?>"><?php echo stripslashes($value['name']); ?></label><br/>
		<input id="<?php echo esc_attr($value['id']); ?>" type="text" name="<?php echo esc_attr($value['id']); ?>" value="<?php echo get_option($value['id']); ?>" style="width:200px" class="upload_text" readonly />
		<input id="<?php echo esc_attr($value['id']); ?>_button" name="<?php echo esc_attr($value['id']); ?>_button" type="button" value="Browse" class="upload_btn button" rel="<?php echo esc_attr($value['id']); ?>" style="margin:5px 0 0 5px" />
		<small><?php echo esc_html($value['desc']); ?></small>
		<div class="clearfix"></div>
		
		<script>
		jQuery(document).ready(function() {
			jQuery('#<?php echo esc_js($value['id']); ?>_button').click(function() {
	         	var send_attachment_bkp = wp.media.editor.send.attachment;
			    wp.media.editor.send.attachment = function(props, attachment) {
			    	formfield = jQuery('#<?php echo esc_js($value['id']); ?>').attr('name');
		         	jQuery('#'+formfield).attr('value', attachment.url);
			
			        wp.media.editor.send.attachment = send_attachment_bkp;
			    }
			
			    wp.media.editor.open();
	        });
	    });
		</script>
		
		<?php 
			$current_value = get_option( $value['id'] );
			
			if(!is_bool($current_value) && !empty($current_value))
			{
				$url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
			
				if($value['type']=='image')
				{
		?>
		
			<div id="<?php echo esc_attr($value['id']); ?>_wrapper" style="width:380px;font-size:11px;"><br/>
				<img src="<?php echo get_option($value['id']); ?>" style="max-width:500px"/><br/><br/>
				<a href="<?php echo esc_url($url); ?>" class="image_del button" rel="<?php echo esc_attr($value['id']); ?>"><?php esc_html_e( 'Delete', 'grandblog-translation' ); ?></a>
			</div>
			<?php
				}
				else
				{
			?>
			<div id="<?php echo esc_attr($value['id']); ?>_wrapper" style="width:380px;font-size:11px;">
				<br/><a href="<?php echo get_option( $value['id'] ); ?>">
				<?php esc_html_e( 'Listen current music', 'grandblog-translation' ); ?></a>&nbsp;<a href="<?php echo esc_url($url); ?>" class="image_del button" rel="<?php echo esc_attr($value['id']); ?>"><?php esc_html_e( 'Delete', 'grandblog-translation' ); ?></a>
			</div>
		<?php
				}
			}
		?>
	
		</div>
		<?php
	break;
	 
	case 'textarea':
	?>
	
		<div id="<?php echo esc_attr($value['id']); ?>_section" class="rm_input rm_textarea"><label
			for="<?php echo esc_attr($value['id']); ?>"><?php echo stripslashes($value['name']); ?></label><br/>
		<textarea name="<?php echo esc_attr($value['id']); ?>"
			type="<?php echo esc_attr($value['type']); ?>" cols="" rows=""><?php if ( get_option( $value['id'] ) != "") { echo stripslashes(get_option( $value['id']) ); } else { echo stripslashes($value['std']); } ?></textarea>
		<small><?php echo esc_html($value['desc']); ?></small>
		<div class="clearfix"></div>
	
		</div>
	
		<?php
	break;
	 
	case 'select':
	?>
	
		<div id="<?php echo esc_attr($value['id']); ?>_section" class="rm_input rm_select"><label
			for="<?php echo esc_attr($value['id']); ?>"><?php echo stripslashes($value['name']); ?></label><br/>
	
		<select name="<?php echo esc_attr($value['id']); ?>"
			id="<?php echo esc_attr($value['id']); ?>">
			<?php foreach ($value['options'] as $key => $option) { ?>
			<option
			<?php if (get_option( $value['id'] ) == $key) { echo 'selected="selected"'; } ?>
				value="<?php echo esc_attr($key); ?>"><?php echo esc_html($option); ?></option>
			<?php } ?>
		</select> <small><?php echo esc_html($value['desc']); ?></small>
		<div class="clearfix"></div>
		</div>
		<?php
	break;
	 
	case 'radio':
	?>
	
		<div id="<?php echo esc_attr($value['id']); ?>_section" class="rm_input rm_select"><label
			for="<?php echo esc_attr($value['id']); ?>"><?php echo stripslashes($value['name']); ?></label><br/><br/>
	
		<div style="margin-top:5px;float:left;<?php if(!empty($value['desc'])) { ?>width:300px<?php } else { ?>width:500px<?php } ?>">
		<?php foreach ($value['options'] as $key => $option) { ?>
		<div style="float:left;<?php if(!empty($value['desc'])) { ?>margin:0 20px 20px 0<?php } ?>">
			<input style="float:left;" id="<?php echo esc_attr($value['id']); ?>" name="<?php echo esc_attr($value['id']); ?>" type="radio"
			<?php if (get_option( $value['id'] ) == $key) { echo 'checked="checked"'; } ?>
				value="<?php echo esc_attr($key); ?>"/><?php echo esc_html($option); ?>
		</div>
		<?php } ?>
		</div>
		
		<?php if(!empty($value['desc'])) { ?>
			<small><?php echo esc_html($value['desc']); ?></small>
		<?php } ?>
		<div class="clearfix"></div>
		</div>
		<?php
	break;
	
	case 'sortable':
	?>
	
		<div id="<?php echo esc_attr($value['id']); ?>_section" class="rm_input rm_select"><label
			for="<?php echo esc_attr($value['id']); ?>"><?php echo stripslashes($value['name']); ?></label><br/>
	
		<div style="float:left;width:100%;">
		<?php 
		$sortable_array = array();
		if(get_option( $value['id'] ) != 1)
		{
			$sortable_array = unserialize(get_option( $value['id'] ));
		}
		
		$current = 1;
		
		if(!empty($value['options']))
		{
		?>
		<select name="<?php echo esc_attr($value['id']); ?>"
			id="<?php echo esc_attr($value['id']); ?>" class="pp_sortable_select">
		<?php
		foreach ($value['options'] as $key => $option) { 
			if($key > 0)
			{
		?>
		<option value="<?php echo esc_attr($key); ?>" data-rel="<?php echo esc_attr($value['id']); ?>_sort" title="<?php echo html_entity_decode($option); ?>"><?php echo html_entity_decode($option); ?></option>
		<?php }
		
				if($current>1 && ($current-1)%3 == 0)
				{
		?>
		
				<br style="clear:both"/>
		
		<?php		
				}
				
				$current++;
			}
		?>
		</select>
		<a class="button pp_sortable_button" data-rel="<?php echo esc_attr($value['id']); ?>" class="button" style="margin-top:10px;display:inline-block">Add</a>
		<?php
		}
		?>
		 
		 <br style="clear:both"/><br/>
		 
		 <div class="pp_sortable_wrapper">
		 <ul id="<?php echo esc_attr($value['id']); ?>_sort" class="pp_sortable" rel="<?php echo esc_attr($value['id']); ?>_sort_data"> 
		 <?php
		 	$sortable_data_array = unserialize(get_option( $value['id'].'_sort_data' ));
	
		 	if(!empty($sortable_data_array))
		 	{
		 		foreach($sortable_data_array as $key => $sortable_data_item)
		 		{
			 		if(!empty($sortable_data_item))
			 		{
		 		
		 ?>
		 		<li id="<?php echo esc_attr($sortable_data_item); ?>_sort" class="ui-state-default"><div class="title"><?php echo esc_html($value['options'][$sortable_data_item]); ?></div><a data-rel="<?php echo esc_attr($value['id']); ?>_sort" href="javascript:;" class="remove">x</a><br style="clear:both"/></li> 	
		 <?php
		 			}
		 		}
		 	}
		 ?>
		 </ul>
		 
		 </div>
		 
		</div>
		
		<input type="hidden" id="<?php echo esc_attr($value['id']); ?>_sort_data" name="<?php echo esc_attr($value['id']); ?>_sort_data" value="" style="width:100%"/>
		<br style="clear:both"/><br/>
		
		<div class="clearfix"></div>
		</div>
		<?php
	break;
	 
	case "checkbox":
	?>
	
		<div id="<?php echo esc_attr($value['id']); ?>_section" class="rm_input rm_checkbox"><label
			for="<?php echo esc_attr($value['id']); ?>"><?php echo stripslashes($value['name']); ?></label><br/>
	
		<?php if(get_option($value['id'])){ $checked = "checked=\"checked\""; }else{ $checked = "";} ?>
		<input type="checkbox" name="<?php echo esc_attr($value['id']); ?>"
			id="<?php echo esc_attr($value['id']); ?>" value="true" <?php echo esc_html($checked); ?> />
	
	
		<small><?php echo esc_html($value['desc']); ?></small>
		<div class="clearfix"></div>
		</div>
	<?php break; 
	
	case "iphone_checkboxes":
	?>
	
		<div id="<?php echo esc_attr($value['id']); ?>_section" class="rm_input rm_checkbox"><label
			for="<?php echo esc_attr($value['id']); ?>"><?php echo stripslashes($value['name']); ?></label>
	
		<?php if(get_option($value['id'])){ $checked = "checked=\"checked\""; }else{ $checked = "";} ?>
		<input type="checkbox" class="iphone_checkboxes" name="<?php echo esc_attr($value['id']); ?>"
			id="<?php echo esc_attr($value['id']); ?>" value="true" <?php echo esc_html($checked); ?> />
	
		<small><?php echo esc_html($value['desc']); ?></small>
		<div class="clearfix"></div>
		</div>
	
	<?php break; 
	
	case "html":
	?>
	
		<div id="<?php echo esc_attr($value['id']); ?>_section" class="rm_input rm_checkbox"><label
			for="<?php echo esc_attr($value['id']); ?>"><?php echo stripslashes($value['name']); ?></label><br/>
	
		<?php echo stripslashes($value['html']); ?>
	
		<small><?php echo esc_html($value['desc']); ?></small>
		<div class="clearfix"></div>
		</div>
	
	<?php break; 
	
	case "shortcut":
	?>
	
		<div id="<?php echo esc_attr($value['id']); ?>_section" class="rm_input rm_shortcut">
	
		<ul class="pp_shortcut_wrapper">
		<?php 
			$count_shortcut = 1;
			foreach ($value['options'] as $key_shortcut => $option) { ?>
			<li><a href="#<?php echo esc_attr($key_shortcut); ?>" <?php if($count_shortcut==1) { ?>class="active"<?php } ?>><?php echo esc_html($option); ?></a></li>
		<?php $count_shortcut++; } ?>
		</ul>
	
		<div class="clearfix"></div>
		</div>
	
	<?php break; 
		
	case "section":
	
	$i++;
	
	?>
	
		<div id="pp_panel_<?php echo strtolower($value['name']); ?>" class="rm_section">
		<div class="rm_title">
		<h3><img
			src="<?php echo get_template_directory_uri(); ?>/functions/images/trans.png"
			class="inactive" alt=""><?php echo stripslashes($value['name']); ?></h3>
		<span class="submit"><input class="button-primary" name="save<?php echo esc_attr($i); ?>" type="submit"
			value="Save changes" /> </span>
		<div class="clearfix"></div>
		</div>
		<div class="rm_options"><?php break;
	 
	}
	}
	?>
	 	
	 	<div class="clearfix"></div>
	 	</form>
	 	</div>
	</div>

<?php
}

add_action('admin_menu', 'grandblog_add_admin');

/**
*	End Theme Setting Panel
**/ 


//Setup theme custom filters
require_once get_template_directory() . "/lib/theme.filter.lib.php";

//Setup required plugin activation
require_once get_template_directory() . "/lib/tgm.lib.php";

//Setup Theme Customizer
require_once get_template_directory() . "/modules/kirki/kirki.php";
require_once get_template_directory() . "/lib/customizer.lib.php";

//Setup page custom fields and action handler
require_once get_template_directory() . "/fields/page.fields.php";

// Setup shortcode generator
require_once get_template_directory() . "/modules/shortcode_generator.php";

/**
*	Setup one click importer function
**/
add_action('wp_ajax_grandblog_import_demo_content', 'grandblog_import_demo_content');
add_action('wp_ajax_nopriv_grandblog_import_demo_content', 'grandblog_import_demo_content');

function grandblog_import_demo_content() {
	if(is_admin() && isset($_POST['demo']) && !empty($_POST['demo']))
	{
	    if ( !defined('WP_LOAD_IMPORTERS') ) define('WP_LOAD_IMPORTERS', true);
	
	    // Load Importer API
	    require_once ABSPATH . 'wp-admin/includes/import.php';
	
	    if ( ! class_exists( 'WP_Importer' ) ) {
	        $class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
	        if ( file_exists( $class_wp_importer ) )
	        {
	            require $class_wp_importer;
	        }
	    }
	
	    if ( ! class_exists( 'WP_Import' ) ) {
	        $class_wp_importer = get_template_directory() ."/modules/import/wordpress-importer.php";
	        if ( file_exists( $class_wp_importer ) )
	            require $class_wp_importer;
	    }
	    
	    $import_files = array();
	    $page_on_front ='';
	
		//Check import selected demo
	    if ( class_exists( 'WP_Import' ) ) 
	    { 
	    	switch($_POST['demo'])
	    	{
		    	case 1:
		    	default:
		    		$import_filepath = get_template_directory() ."/cache/demos/1.xml" ;
		    		$oldurl = 'http://themes.themegoods2.com/grandblog/demo';
		    	break;
	    	}
			
			//Run and download demo contents
			$wp_import = new WP_Import();
	        $wp_import->fetch_attachments = true;
	        $wp_import->import($import_filepath);
	    }
	    
	    //Set default custom menu settings
	    $locations = get_theme_mod('nav_menu_locations');
		$locations['primary-menu'] = 23;
		$locations['top-menu'] = 25;
		$locations['side-menu'] = 24;
		$locations['footer-menu'] = 22;
		set_theme_mod( 'nav_menu_locations', $locations );
		
		//Import widgets
		$import_widget_filepath = get_template_directory() ."/cache/demos/1.wie" ;
		
		// Get file contents and decode
		WP_Filesystem();
		global $wp_filesystem;
		$data = $wp_filesystem->get_contents($import_widget_filepath);
		$data = json_decode( $data );
	
		// Import the widget data
		// Make results available for display on import/export page
		$widget_import_results = grandblog_import_data( $data );
		
		//Set default Blog Slider category
		set_theme_mod( 'tg_blog_slider_cat', 7 );
		
		//Change all URLs from demo URL to localhost
		$update_options = array ( 0 => 'content', 1 => 'excerpts', 2 => 'links', 3 => 'attachments', 4 => 'custom', 5 => 'guids', );
		$newurl = esc_url( site_url() ) ;
		grandblog_update_urls($update_options, $oldurl, $newurl);
		
		//Refresh rewrite rules
		flush_rewrite_rules();
	    
		exit();
	}
}

/**
*	Setup AJAX search function
**/
add_action('wp_ajax_grandblog_ajax_search', 'grandblog_ajax_search');
add_action('wp_ajax_nopriv_grandblog_ajax_search', 'grandblog_ajax_search');

function grandblog_ajax_search() {
	global $wpdb;
	
	if (strlen($_POST['s'])>0) {
		$limit=5;
		$s=strtolower(addslashes($_POST['s']));
		$querystr = "
			SELECT $wpdb->posts.*
			FROM $wpdb->posts
			WHERE 1=1 AND ((lower($wpdb->posts.post_title) like %s))
			AND $wpdb->posts.post_type IN ('post', 'page', 'attachment', 'projects', 'galleries')
			AND (post_status = 'publish')
			ORDER BY $wpdb->posts.post_date DESC
			LIMIT $limit;
		 ";

	 	$pageposts = $wpdb->get_results($wpdb->prepare($querystr, '%'.$wpdb->esc_like($s).'%'), OBJECT);
	 	
	 	if(!empty($pageposts))
	 	{
			echo '<ul>';
	
	 		foreach($pageposts as $result_item) 
	 		{
	 			$post=$result_item;
	 			
	 			$post_type = get_post_type($post->ID);
				$post_type_class = '';
				$post_type_title = '';
				
				switch($post_type)
				{
				    case 'galleries':
				    	$post_type_class = '<i class="fa fa-picture-o"></i>';
				    	$post_type_title = esc_html__('Gallery', 'grandblog-translation' );
				    break;
				    
				    case 'page':
				    default:
				    	$post_type_class = '<i class="fa fa-file-text-o"></i>';
				    	$post_type_title = esc_html__('Page', 'grandblog-translation' );
				    break;
				    
				    case 'projects':
				    	$post_type_class = '<i class="fa fa-folder-open-o"></i>';
				    	$post_type_title = esc_html__('Projects', 'grandblog-translation' );
				    break;
				    
				    case 'services':
				    	$post_type_class = '<i class="fa fa-star"></i>';
				    	$post_type_title = esc_html__('Service', 'grandblog-translation' );
				    break;
				    
				    case 'clients':
				    	$post_type_class = '<i class="fa fa-user"></i>';
				    	$post_type_title = esc_html__('Client', 'grandblog-translation' );
				    break;
				}
				
				$post_thumb = array();
				if(has_post_thumbnail($post->ID, 'thumbnail'))
				{
				    $image_id = get_post_thumbnail_id($post->ID);
				    $post_thumb = wp_get_attachment_image_src($image_id, 'thumbnail', true);
				    $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
				    
				    if(isset($post_thumb[0]) && !empty($post_thumb[0]))
				    {
				        $post_type_class = '<div class="search_thumb"><img src="'.$post_thumb[0].'" alt="'.esc_attr($image_alt).'"/></div>';
				    }
				}
	 			
				echo '<li>';
				
				if(!isset($post_thumb[0]))
				{
					echo '<div class="post_type_icon">';
				}
				
				echo '<a href="'.get_permalink($post->ID).'">'.$post_type_class.'</i></a>';
				
				if(!isset($post_thumb[0]))
				{
					echo '</div>';
				}
				
				echo '<div class="ajax_post">';
				echo '<a href="'.get_permalink($post->ID).'"><strong>'.$post->post_title.'</strong><br/>';
				echo '<span class="post_attribute">'.date(THEMEDATEFORMAT, strtotime($post->post_date)).'</span></a>';
				echo '</div>';
				echo '</li>';
			}
			
			echo '<li class="view_all"><a href="javascript:jQuery(\'#searchform\').submit()">'.esc_html__('View all results', 'grandblog-translation' ).'</a></li>';
	
			echo '</ul>';
		}

	}
	else 
	{
		echo '';
	}
	die();

}


/**
*	End theme custom AJAX calls handler
**/

/**
*	Setup contact form mailing function
**/
add_action('wp_ajax_grandblog_contact_mailer', 'grandblog_contact_mailer');
add_action('wp_ajax_nopriv_grandblog_contact_mailer', 'grandblog_contact_mailer');

function grandblog_contact_mailer() {
	check_ajax_referer( 'tgajax-post-contact-nonce', 'tg_security' );
	
	//Error message when message can't send
	define('ERROR_MESSAGE', 'Oops! something went wrong, please try to submit later.');
	
	if (isset($_POST['your_name'])) {
	
		//Get your email address
		$contact_email = get_option('pp_contact_email');
		$pp_contact_thankyou = esc_html__('Thank you! We will get back to you as soon as possible', 'grandblog-translation' );
		
		/*
		|
		| Begin sending mail
		|
		*/
		
		$from_name = $_POST['your_name'];
		$from_email = $_POST['email'];
		
		//Get contact subject
		if(!isset($_POST['subject']))
		{
			$contact_subject = esc_html__('[Email Contact]', 'grandblog-translation' ).' '.get_bloginfo('name');
		}
		else
		{
			$contact_subject = $_POST['subject'];
		}
		
		$headers = "";
	   	//$headers.= 'From: '.$from_name.' <'.$from_email.'>'.PHP_EOL;
	   	$headers.= 'Reply-To: '.$from_name.' <'.$from_email.'>'.PHP_EOL;
	   	$headers.= 'Return-Path: '.$from_name.' <'.$from_email.'>'.PHP_EOL;
		
		$message = 'Name: '.$from_name.PHP_EOL;
		$message.= 'Email: '.$from_email.PHP_EOL.PHP_EOL;
		$message.= 'Message: '.PHP_EOL.$_POST['message'].PHP_EOL.PHP_EOL;
		
		if(isset($_POST['address']))
		{
			$message.= 'Address: '.$_POST['address'].PHP_EOL;
		}
		
		if(isset($_POST['phone']))
		{
			$message.= 'Phone: '.$_POST['phone'].PHP_EOL;
		}
		
		if(isset($_POST['mobile']))
		{
			$message.= 'Mobile: '.$_POST['mobile'].PHP_EOL;
		}
		
		if(isset($_POST['company']))
		{
			$message.= 'Company: '.$_POST['company'].PHP_EOL;
		}
		
		if(isset($_POST['country']))
		{
			$message.= 'Country: '.$_POST['country'].PHP_EOL;
		}
		    
		
		if(!empty($from_name) && !empty($from_email) && !empty($message))
		{
			wp_mail($contact_email, $contact_subject, $message, $headers);
			echo '<p>'.$pp_contact_thankyou.'</p>';
			
			die;
		}
		else
		{
			echo '<p>'.ERROR_MESSAGE.'</p>';
			
			die;
		}

	}
	else 
	{
		echo '<p>'.ERROR_MESSAGE.'</p>';
	}
	die();
}

/**
*	End theme contact form mailing function
**/

/**
*	Setup post infinite scroll function
**/
add_action('wp_ajax_grandblog_post', 'grandblog_post');
add_action('wp_ajax_nopriv_grandblog_post', 'grandblog_post');

function grandblog_post() {
	check_ajax_referer( 'tgajax-post-contact-nonce', 'tg_security' );
	
	$current_query = '';
	$items = 1;
	$items_ini = 0;
	$columns = 3;
	$offset = 0;
	
	if(isset($_POST['current_query']))
	{
		$current_query = $_POST['current_query'];
	}
	
	if(isset($_POST['items']))
	{
		$items = $_POST['items'];
	}
	
	if(isset($_POST['items_ini']))
	{
		$items_ini = $_POST['items_ini'];
	}
	
	if(isset($_POST['columns']))
	{
		$columns = $_POST['columns'];
	}
	
	if(isset($_POST['offset']))
	{
		$offset = $_POST['offset'];
	}
	
	$return_html = '';
	
	if(is_array($current_query))
	{
		$args = $current_query;
	}
	
	//Get post items
	$args['numberposts'] = $items;
	$args['offset'] = $offset;
	$args['order'] = 'DESC';
	$args['orderby'] = 'date';
	$args['post_type'] = 'post';
	$args['suppress_filters'] = false;

	
	$post_arr = get_posts($args);

	if(!empty($post_arr))
	{	
		foreach($post_arr as $key => $current_post)
		{
			$image_thumb = '';
			$key++;
										
			if(has_post_thumbnail($current_post->ID, 'large'))
			{
			    $image_id = get_post_thumbnail_id($current_post->ID);
			    $image_thumb = wp_get_attachment_image_src($image_id, 'large', true);
			}
		
			$return_html.= '<!-- Begin each blog post --><div id="post-'.$current_post->ID.'" class="post-'.$current_post->ID.' post type-post fadeIn" ';

			if($key%$columns==0) 
			{ 
				$return_html.= 'data-column="last"';
			}

			$return_html.= '<div class="post_wrapper">
	    
				<div class="post_content_wrapper">
	    
					<div class="post_header">';
				    
				    if(!empty($image_thumb))
				    {
				       	$small_image_url = wp_get_attachment_image_src($image_id, 'grandblog_blog_thumb', true);
				
				   	$return_html.= '<div class="post_img static small">
				   	    <a href="'.esc_url(get_permalink($current_post->ID)).'">
				   	    	<img src="'.esc_url($small_image_url[0]).'" alt="" class="" style="width:'.esc_attr($small_image_url[1]).'px;height:'.esc_attr($small_image_url[2]).'px;"/>
				   	    </a>
				   	</div>';
			   		}

			   		$return_html.= '<br class="clear"/><div class="post_header_title">';
			   	
					//Get Post's Categories
					$post_categories = wp_get_post_categories($current_post->ID);
					if(!empty($post_categories))
					{
						$return_html.= '<div class="post_info_cat"><span>';
	
						$i = 0;
						$len = count($post_categories);
						foreach($post_categories as $c)
						{
						    $cat = get_category( $c );
						    
						        $return_html.= '<a href="'.esc_url(get_category_link($cat->term_id)).'">'.esc_html($cat->name).'</a>';
						    	
						    	if ($i != $len - 1) 
						    	{	
						    		if(THEMEDEMO && $i == 1)
						    		{
							    		break;
						    		}
									
									$return_html.= '&nbsp;/';
						    	}
						    	$i++;
						}
							$return_html.= '</span>
						</div>';
					}
					
			      	$return_html.= '<h5><a href="'.esc_url(get_permalink($current_post->ID)).'" title="'.esc_attr($current_post->post_title).'">'.esc_html($current_post->post_title).'</a></h5>
			      	<hr class="title_break small">
			      	<div class="post_detail post_date">
			      		<span class="post_info_date">
			      			<span>'.date_i18n(THEMEDATEFORMAT, get_the_time('U', $current_post->ID)).'
			      			</span>
			      		</span>
				  	</div>
			   </div><p>'.grandblog_get_excerpt_by_id($current_post->ID).'</p>
			</div>
			
	    </div>
	    
	</div>

</div>
<!-- End each blog post -->';
		}
	}
	
	echo stripslashes($return_html);
	die();
}

/**
*	End post infinite scroll function
**/


//Setup custom settings when theme is activated
if (isset($_GET['activated']) && $_GET['activated']){
	//Add default contact fields
	$pp_contact_form = get_option('pp_contact_form');
	if(empty($pp_contact_form))
	{
		add_option( 'pp_contact_form', 's:1:"3";' );
	}
	
	$pp_contact_form_sort_data = get_option('pp_contact_form_sort_data');
	if(empty($pp_contact_form_sort_data))
	{
		add_option( 'pp_contact_form_sort_data', 'a:3:{i:0;s:1:"1";i:1;s:1:"2";i:2;s:1:"3";}' );
	}

	wp_redirect(admin_url("themes.php?page=functions.php&activate=true"));
}
?>