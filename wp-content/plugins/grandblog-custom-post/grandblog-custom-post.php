<?php
/*
Plugin Name: Grand Blog Theme Gallery and Shortcode
Plugin URI: http://themes.themegoods2.com/grandblog
Description: Plugin that will create gallery post for Grand Blog theme.
Version: 1.0
Author: ThemeGoods
Author URI: http://themeforest.net/user/ThemeGoods
License: GPLv2
*/

//Setup translation string via PO file
add_action('plugins_loaded', 'grandblog_load_textdomain');
function grandblog_load_textdomain() 
{
	load_plugin_textdomain( 'grandblog-custom-post', FALSE, dirname( plugin_basename(__FILE__) ) . '/languages/' );
}

function post_type_galleries() {
	$labels = array(
    	'name' => _x('Galleries', 'post type general name', 'grandblog-custom-post'),
    	'singular_name' => _x('Gallery', 'post type singular name', 'grandblog-custom-post'),
    	'add_new' => _x('Add New Gallery', 'book', 'grandblog-custom-post'),
    	'add_new_item' => __('Add New Gallery', 'grandblog-custom-post'),
    	'edit_item' => __('Edit Gallery', 'grandblog-custom-post'),
    	'new_item' => __('New Gallery', 'grandblog-custom-post'),
    	'view_item' => __('View Gallery', 'grandblog-custom-post'),
    	'search_items' => __('Search Gallery', 'grandblog-custom-post'),
    	'not_found' =>  __('No Gallery found', 'grandblog-custom-post'),
    	'not_found_in_trash' => __('No Gallery found in Trash', 'grandblog-custom-post'), 
    	'parent_item_colon' => ''
	);		
	$args = array(
    	'labels' => $labels,
    	'public' => true,
    	'publicly_queryable' => true,
    	'show_ui' => true, 
    	'query_var' => true,
    	'rewrite' => true,
    	'capability_type' => 'post',
    	'hierarchical' => false,
    	'menu_position' => null,
    	'supports' => array('title', 'thumbnail'),
    	'menu_icon' => ''
	); 		

	register_post_type( 'galleries', $args );
	
  	$labels = array(			  
  	  'name' => _x( 'Gallery Categories', 'taxonomy general name', 'grandblog-custom-post' ),
  	  'singular_name' => _x( 'Gallery Category', 'taxonomy singular name', 'grandblog-custom-post' ),
  	  'search_items' =>  __( 'Search Gallery Categories', 'grandblog-custom-post' ),
  	  'all_items' => __( 'All Gallery Categories', 'grandblog-custom-post' ),
  	  'parent_item' => __( 'Parent Gallery Category', 'grandblog-custom-post' ),
  	  'parent_item_colon' => __( 'Parent Gallery Category:', 'grandblog-custom-post' ),
  	  'edit_item' => __( 'Edit Gallery Category', 'grandblog-custom-post' ), 
  	  'update_item' => __( 'Update Gallery Category', 'grandblog-custom-post' ),
  	  'add_new_item' => __( 'Add New Gallery Category', 'grandblog-custom-post' ),
  	  'new_item_name' => __( 'New Gallery Category Name', 'grandblog-custom-post' ),
  	); 							  	  
  	
  	register_taxonomy(
		'gallerycat',
		'galleries',
		array(
			'public'=>true,
			'hierarchical' => true,
			'labels'=> $labels,
			'query_var' => 'gallerycat',
			'show_ui' => true,
			'rewrite' => array( 'slug' => 'gallerycat', 'with_front' => false ),
		)
	);	
} 
								  
add_action('init', 'post_type_galleries');


add_filter( 'manage_posts_columns', 'rt_add_gravatar_col');
function rt_add_gravatar_col($cols) {
	$cols['thumbnail'] = __('Thumbnail', 'grandblog-custom-post');
	return $cols;
}

add_action( 'manage_posts_custom_column', 'rt_get_author_gravatar');
function rt_get_author_gravatar($column_name ) {
	if ( $column_name  == 'thumbnail'  ) {
		echo get_the_post_thumbnail(get_the_ID(), array(100, 100));
	}
}

/*
	Get gallery list
*/
$args = array(
    'numberposts' => -1,
    'post_type' => array('galleries'),
);

$galleries_arr = get_posts($args);
$galleries_select = array();
$galleries_select[''] = '';

foreach($galleries_arr as $gallery)
{
	$galleries_select[$gallery->post_title] = $gallery->ID;
}

/*
	Get post layouts
*/
$post_layout_select = array();
$post_layout_select = array(
	'With Right Sidebar' => 'With Right Sidebar',
	'With Left Sidebar' => 'With Left Sidebar',
	'Fullwidth' => 'Fullwidth',
);

//Get all sidebars
$theme_sidebar = array(
	'' => '',
	'Page Sidebar' => 'Page Sidebar', 
	'Contact Sidebar' => 'Contact Sidebar', 
	'Blog Sidebar' => 'Blog Sidebar',
);

$dynamic_sidebar = get_option('pp_sidebar');

if(!empty($dynamic_sidebar))
{
	foreach($dynamic_sidebar as $sidebar)
	{
		$theme_sidebar[$sidebar] = $sidebar;
	}
}

/*
	Begin creating custom fields
*/

$postmetas = 
	array (
		'post' => array(
			array("section" => "Content Type", "id" => "post_layout", "type" => "select", "title" => "Post Layout", "description" => "You can select layout of this single post page.", "items" => $post_layout_select),
			array(
    		"section" => "Featured Content Type", "id" => "post_ft_type", "type" => "select", "title" => "Featured Content Type", "description" => "Select featured content type for this post. Different content type will be displayed on single post page", 
				"items" => array(
					"Image" => "Image",
					"Gallery" => "Gallery",
					"Vimeo Video" => "Vimeo Video",
					"Youtube Video" => "Youtube Video",
				)),
			
			array("section" => "Gallery", "id" => "post_ft_gallery", "type" => "select", "title" => "Gallery (Optional)", "description" => "Please select a gallery<br/><br/><strong>NOTE: enter if you select \"Gallery\" as Featured Content Type</strong>", "items" => $galleries_select),
				
			array("section" => "Vimeo Video ID", "id" => "post_ft_vimeo", "type" => "text", "title" => "Vimeo Video ID (Optional)", "description" => "Please enter Vimeo Video ID for example 73317780"),
			
			array("section" => "Youtube Video ID", "id" => "post_ft_youtube", "type" => "text", "title" => "Youtube Video ID (Optional)", "description" => "Please enter Youtube Video ID for example 6AIdXisPqHc"),
		),
		'galleries' => array(
			array("section" => "Gallery Template", "id" => "gallery_template", "type" => "select", "title" => "Gallery Template", "description" => "Select gallery template for this gallery", 
				"items" => array(
					"Gallery 2 Columns" => "Gallery 2 Columns",
					"Gallery 3 Columns" => "Gallery 3 Columns",
					"Gallery 4 Columns" => "Gallery 4 Columns",
				)),
		),
);

function grandblog_create_meta_box() 
{
	global $postmetas;
	
	if(!isset($_GET['post_type']) OR empty($_GET['post_type']))
	{
		if(isset($_GET['post']) && !empty($_GET['post']))
		{
			$post_obj = get_post($_GET['post']);
			$_GET['post_type'] = $post_obj->post_type;
		}
		else
		{
			$_GET['post_type'] = 'post';
		}
	}
	
	if ( function_exists('add_meta_box') && isset($postmetas) && count($postmetas) > 0 ) {  
		foreach($postmetas as $key => $postmeta)
		{
			if($_GET['post_type']==$key && !empty($postmeta))
			{
				add_meta_box( 'metabox', ucfirst($key).' Options', 'grandblog_new_meta_box', $key, 'side', 'high' );
			}
		}
	}

}  

function grandblog_new_meta_box() 
{
	global $post, $postmetas;
	
	if(!isset($_GET['post_type']) OR empty($_GET['post_type']))
	{
		if(isset($_GET['post']) && !empty($_GET['post']))
		{
			$post_obj = get_post($_GET['post']);
			$_GET['post_type'] = $post_obj->post_type;
		}
		else
		{
			$_GET['post_type'] = 'post';
		}
	}
	wp_nonce_field( 'grandblog_meta_form_save', 'grandblog_meta_form_nonce' );
	
	$meta_section = '';

	foreach ( $postmetas as $key => $postmeta ) {
	
		if($_GET['post_type'] == $key)
		{
		
			foreach ( $postmeta as $postmeta_key => $each_meta ) {
		
				$meta_id = $each_meta['id'];
				$meta_title = $each_meta['title'];
				$meta_description = $each_meta['description'];
				
				if(isset($postmeta['section']))
				{
					$meta_section = $postmeta['section'];
				}
				
				$meta_type = '';
				if(isset($each_meta['type']))
				{
					$meta_type = $each_meta['type'];
				}
				
				echo '<div id="post_option_'.strtolower($each_meta['id']).'">';
				echo "<br/><strong>".$meta_title."</strong><hr class='pp_widget_hr'/>";
				echo "<div class='pp_widget_description'>$meta_description</div>";
				
				if ($meta_type == 'checkbox') {
					$checked = get_post_meta($post->ID, $meta_id, true) == '1' ? "checked" : "";
					echo "<br style='clear:both'><input type='checkbox' name='$meta_id' id='$meta_id' class='iphone_checkboxes' value='1' $checked /><br style='clear:both'><br/><br/>";
				}
				else if ($meta_type == 'select') {
					echo "<p><select name='$meta_id' id='$meta_id'>";
					
					if(!empty($each_meta['items']))
					{
						foreach ($each_meta['items'] as $key => $item)
						{
							echo '<option value="'.$item.'"';
							
							if($item == get_post_meta($post->ID, $meta_id, true))
							{
								echo ' selected ';
							}
							
							echo '>'.$key.'</option>';
						}
					}
					
					echo "</select></p>";
				}
				else if ($meta_type == 'file') { 
				    echo "<p><input type='text' name='$meta_id' id='$meta_id' class='' value='".get_post_meta($post->ID, $meta_id, true)."' style='width:89%' /><input id='".$meta_id."_button' name='".$meta_id."_button' type='button' value='Upload' class='metabox_upload_btn button' readonly='readonly' rel='".$meta_id."' style='margin:7px 0 0 5px' /></p>";
				}
				else if ($meta_type == 'textarea') {
					if(isset($postmeta[$postmeta_key]['sample']))
					{
						echo "<p><textarea name='$meta_id' id='$meta_id' class=' hint' style='width:100%' rows='7' title='".$postmeta[$postmeta_key]['sample']."'>".get_post_meta($post->ID, $meta_id, true)."</textarea></p>";
					}
					else
					{
						echo "<p><textarea name='$meta_id' id='$meta_id' class='' style='width:100%' rows='7'>".get_post_meta($post->ID, $meta_id, true)."</textarea></p>";
					}
				}			
				else {
					if(isset($postmeta[$postmeta_key]['sample']))
					{
						echo "<p><input type='text' name='$meta_id' id='$meta_id' class='' title='".$postmeta[$postmeta_key]['sample']."' value='".get_post_meta($post->ID, $meta_id, true)."' style='width:99%' /></p>";
					}
					else
					{
						echo "<p><input type='text' name='$meta_id' id='$meta_id' class='' value='".get_post_meta($post->ID, $meta_id, true)."' style='width:99%' /></p>";
					}
				}
				
				echo '</div>';
			}
		}
	}
	
	echo '<br/>';

}

function grandblog_save_postdata( $post_id ) 
{

	global $postmetas;
	
	// verify if this is an auto save routine. If it is our form has not been submitted, so we dont want to do anything

	if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || (defined('DOING_AJAX') && DOING_AJAX) || isset($_REQUEST['bulk_edit']))
	{
        return;
    }

	// verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times

	if ( isset($_POST['grandblog_meta_form_nonce']) && !wp_verify_nonce( $_POST['grandblog_meta_form_nonce'], 'grandblog_meta_form_save')) 
	{
		return;
	}

	// Check permissions of current user

	if ( isset($_POST['post_type']) && 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) )
			return;
		} else {
		if ( !current_user_can( 'edit_post', $post_id ) )
			return;
	}

	if ( $parent_id = wp_is_post_revision($post_id) )
	{
		$post_id = $parent_id;
	}
	
	if (isset($_POST['grandblog_meta_form_nonce'])) 
	{
		//If import page content builder
		if(is_admin() && isset($_POST['ppb_import_current']) && !empty($_POST['ppb_import_current']))
		{
			//Check if zip file
			$import_filename = $_FILES['ppb_import_current_file']['name'];
			$import_type = $_FILES['ppb_import_current_file']['type'];
			$is_zip = FALSE;
			$new_filename = basename($import_filename, '_.zip');
			
			$accepted_types = array('application/zip', 
                                'application/x-zip-compressed', 
                                'multipart/x-zip', 
                                'application/s-compressed');
 
		    foreach($accepted_types as $mime_type) {
		        if($mime_type == $import_type) {
		            $is_zip = TRUE;
		            break;
		        } 
		    }
			
			if($is_zip)
			{
				WP_Filesystem();
				$upload_dir = wp_upload_dir();
				$cache_dir = '';
				
				if(isset($upload_dir['basedir']))
				{
					$cache_dir = $upload_dir['basedir'].'/meteors';
				}
				
				move_uploaded_file($_FILES["ppb_import_current_file"]["tmp_name"], $cache_dir.'/'.$import_filename);
				//$unzipfile = unzip_file( $cache_dir.'/'.$import_filename, $cache_dir);
				
				$zip = new ZipArchive();
				$x = $zip->open($cache_dir.'/'.$import_filename);
				
				for($i = 0; $i < $zip->numFiles; $i++) {
			        $new_filename = $zip->getNameIndex($i);
			        break;
			    }  
				
				if ($x === true) {
					$zip->extractTo($cache_dir); 
					$zip->close();
				}

				$import_options_json = file_get_contents($cache_dir.'/'.$new_filename);
				unlink($cache_dir.'/'.$import_filename);
				unlink($cache_dir.'/'.$new_filename);
			}
			else
			{
				//If .json file then import
				$import_options_json = file_get_contents($_FILES["ppb_import_current_file"]["tmp_name"]);
			}
			
			$import_options_arr = json_decode($import_options_json, true);
			
			if(isset($import_options_arr['ppb_form_data_order'][0]) && !empty($import_options_arr['ppb_form_data_order'][0]))
			{
				grandblog_page_update_custom_meta($post_id, $import_options_arr['ppb_form_data_order'][0], 'ppb_form_data_order');
			}
			
			$ppb_item_arr = explode(',', $import_options_arr['ppb_form_data_order'][0]);
			
			if(is_array($ppb_item_arr) && !empty($ppb_item_arr))
			{
				foreach($ppb_item_arr as $key => $ppb_item_arr)
				{
					if(isset($import_options_arr[$ppb_item_arr.'_data'][0]) && !empty($import_options_arr[$ppb_item_arr.'_data'][0]))
					{
						grandblog_page_update_custom_meta($post_id, $import_options_arr[$ppb_item_arr.'_data'][0], $ppb_item_arr.'_data');
					}
					
					if(isset($import_options_arr[$ppb_item_arr.'_size'][0]) && !empty($import_options_arr[$ppb_item_arr.'_size'][0]))
					{
						grandblog_page_update_custom_meta($post_id, 'one', $ppb_item_arr.'_size');
					}
				}
			}
			
			header("Location: ".$_SERVER['HTTP_REFERER']);
			exit;
		}
	
		//If export page content builder
		if(is_admin() && isset($_POST['ppb_export_current']) && !empty($_POST['ppb_export_current']))
		{
			$page_title = get_the_title($post_id);
		
			$json_file_name = THEMENAME.'Page'.$page_title.'_Export_'.date('m-d-Y_hia');
	
			header('Content-disposition: attachment; filename='.$json_file_name.'.json');
			header('Content-type: application/json');
			
			//Get current content builder data
			$ppb_form_data_order = get_post_meta($post_id, 'ppb_form_data_order');
			$export_options_arr = array();
			
			if(!empty($ppb_form_data_order))
			{
				$export_options_arr['ppb_form_data_order'] = $ppb_form_data_order;

				//Get each builder module data
				$ppb_form_item_arr = explode(',', $ppb_form_data_order[0]);
			
				foreach($ppb_form_item_arr as $key => $ppb_form_item)
				{
					$ppb_form_item_data = get_post_meta($post_id, $ppb_form_item.'_data');
					$export_options_arr[$ppb_form_item.'_data'] = $ppb_form_item_data;
					
					$ppb_form_item_size = get_post_meta($post_id, $ppb_form_item.'_size');
					$export_options_arr[$ppb_form_item.'_size'] = $ppb_form_item_size;
				}
			}
		
			echo json_encode($export_options_arr);
			
			exit;
		}
	
		foreach ( $postmetas as $postmeta ) {
			foreach ( $postmeta as $each_meta ) {
		
				if (isset($_POST[$each_meta['id']]) && $_POST[$each_meta['id']]) {
					grandblog_update_custom_meta($post_id, $_POST[$each_meta['id']], $each_meta['id']);
				}
				
				if (isset($_POST[$each_meta['id']]) && $_POST[$each_meta['id']] == "") {
					delete_post_meta($post_id, $each_meta['id']);
				}
				
				if (!isset($_POST[$each_meta['id']])) {
					delete_post_meta($post_id, $each_meta['id']);
				}
			
			}
		}
	
		// Saving Page Builder Data
		if(isset($_POST['ppb_enable']) && !empty($_POST['ppb_enable']))
		{
		    grandblog_update_custom_meta($post_id, $_POST['ppb_enable'], 'ppb_enable');
		}
		else
		{
		    delete_post_meta($post_id, 'ppb_enable');
		}
		
		if(isset($_POST['ppb_form_data_order']) && !empty($_POST['ppb_form_data_order']))
		{
		    grandblog_update_custom_meta($post_id, $_POST['ppb_form_data_order'], 'ppb_form_data_order');
		    
		    $ppb_item_arr = explode(',', $_POST['ppb_form_data_order']);
		    if(is_array($ppb_item_arr) && !empty($ppb_item_arr))
		    {
		    	foreach($ppb_item_arr as $key => $ppb_item_arr)
		    	{
		    		if(isset($_POST[$ppb_item_arr.'_data']) && !empty($_POST[$ppb_item_arr.'_data']))
		    		{
		    			grandblog_update_custom_meta($post_id, $_POST[$ppb_item_arr.'_data'], $ppb_item_arr.'_data');
		    		}
		    		
		    		if(isset($_POST[$ppb_item_arr.'_size']) && !empty($_POST[$ppb_item_arr.'_size']))
		    		{
		    			grandblog_update_custom_meta($post_id, $_POST[$ppb_item_arr.'_size'], $ppb_item_arr.'_size');
		    		}
		    	}
		    }
		}
		//If content builder is empty
		else
		{
		    grandblog_update_custom_meta($post_id, '', 'ppb_form_data_order');
		}
	}
}

function grandblog_update_custom_meta($postID, $newvalue, $field_name) 
{

	if (isset($_POST['grandblog_meta_form_nonce'])) 
	{
		if (!get_post_meta($postID, $field_name)) {
			add_post_meta($postID, $field_name, $newvalue);
		} else {
			update_post_meta($postID, $field_name, sanitize_text_field($newvalue));
		}
	}

}

//init

add_action('admin_menu', 'grandblog_create_meta_box'); 
add_action('save_post', 'grandblog_save_postdata'); 

/*
	End creating custom fields
*/

//Include gallery admin interface
include_once (plugin_dir_path( __FILE__ ) . "/gallery/tg-gallery.php");

//Include Theme Shortcode
include_once (plugin_dir_path( __FILE__ ) . "tg-shortcode.php");
?>