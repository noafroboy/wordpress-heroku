<?php

/**
 * The PHP code for setup Theme page custom fields.
 *
 * @package WordPress
 * @subpackage Pai
 */


/*
	Begin creating custom fields
*/

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
	Get gallery list
*/
$args = array(
    'numberposts' => -1,
    'post_type' => array('galleries'),
);

$galleries_arr = get_posts($args);
$galleries_select = array();
$galleries_select['(Display Post Featured Image)'] = '';

foreach($galleries_arr as $gallery)
{
	$galleries_select[$gallery->ID] = $gallery->post_title;
}

/*
	Get all menus available
*/
$menus = get_terms('nav_menu');
$menus_select = array(
	 '' => '---- Default Menu ----'
);
foreach($menus as $each_menu)
{
	$menus_select[$each_menu->slug] = $each_menu->name;
}

$grandblog_page_postmetas = 
	array (
		/*
			Begin Page custom fields
		*/
		
		array("section" => "Page Title", "id" => "page_show_title", "type" => "checkbox", "title" => "Hide Page Title & Tagline", "description" => "Check this option if you want to hide page title and tagline."),
		
		array("section" => "Page Tagline", "id" => "page_tagline", "type" => "textarea", "title" => "Page Tagline (Optional)", "description" => "Enter page tagline. It will displays under page title (optional)"),
		
		array("section" => "Select Sidebar (Optional)", "id" => "page_sidebar", "type" => "select", "title" => "Page Sidebar (Optional)", "description" => "Select this page's sidebar to display.<br/><br/><strong>NOTE: to use this option, you have to select page template end with \"...Sidebar\" only</strong>", "items" => $theme_sidebar),
	);
?>
<?php

function grandblog_page_create_meta_box() 
{

	global $grandblog_page_postmetas;
	if ( function_exists('add_meta_box') && isset($grandblog_page_postmetas) && count($grandblog_page_postmetas) > 0 ) {  
		add_meta_box( 'page_metabox', 'Page Options', 'grandblog_page_new_meta_box', 'page', 'side', 'high' );  
	}

}  

function grandblog_page_new_meta_box() 
{
	global $post, $grandblog_page_postmetas;

	wp_nonce_field( 'grandblog_meta_form_save', 'grandblog_meta_form_nonce' );
	echo '<br/>';
	
	$meta_section = '';

	foreach ( $grandblog_page_postmetas as $postmeta ) {

		$meta_id = $postmeta['id'];
		$meta_title = $postmeta['title'];
		$meta_description = $postmeta['description'];
		$meta_section = $postmeta['section'];
		
		$meta_type = '';
		if(isset($postmeta['type']))
		{
			$meta_type = $postmeta['type'];
		}
		
		echo "<strong>".$meta_title."</strong><hr class='pp_widget_hr'/>";

		echo "<div class='pp_widget_description'>$meta_description</div>";

		if ($meta_type == 'checkbox') {
			$checked = get_post_meta($post->ID, $meta_id, true) == '1' ? "checked" : "";
			echo "<br style='clear:both'><input type='checkbox' name='$meta_id' id='$meta_id' class='iphone_checkboxes' value='1' $checked /><br style='clear:both'/>";
		}
		else if ($meta_type == 'select') {
			echo "<p><select name='$meta_id' id='$meta_id'>";
			
			if(!empty($postmeta['items']))
			{
				foreach ($postmeta['items'] as $key => $item)
				{
					$page_style = get_post_meta($post->ID, $meta_id);
				
					if(isset($page_style[0]) && $key == $page_style[0])
					{
						$css_string = 'selected';
					}
					else
					{
						$css_string = '';
					}
				
					echo '<option value="'.$key.'" '.$css_string.'>'.$item.'</option>';
				}
			}
			
			echo "</select></p>";
		}
		else if ($meta_type == 'file') { 
		    echo "<p><input type='text' name='$meta_id' id='$meta_id' class='' value='".get_post_meta($post->ID, $meta_id, true)."' style='width:89%' /><input id='".$meta_id."_button' name='".$meta_id."_button' type='button' value='Upload' class='metabox_upload_btn button' readonly='readonly' rel='".$meta_id."' style='margin:7px 0 0 5px' /></p>";
		}
		else if ($meta_type == 'textarea') { 
			echo "<p><textarea name='$meta_id' id='$meta_id' class='' style='width:99%' rows='5'>".get_post_meta($post->ID, $meta_id, true)."</textarea></p>";
		}
		else {
			echo "<p><input type='text' name='$meta_id' id='$meta_id' class='' value='".get_post_meta($post->ID, $meta_id, true)."' style='width:99%' /></p>";
		}
		
		echo '<br/>';
	}
	
	echo '<br/>';

}

function grandblog_page_save_postdata( $post_id ) 
{
	global $grandblog_page_postmetas;

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
			//If upload import builder file
			if(isset($_FILES['ppb_import_current_file']['name']) && !empty($_FILES['ppb_import_current_file']['name']))
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
			}
			//If import demo pages
			else if(isset($_POST['ppb_import_demo_file']) && !empty($_POST['ppb_import_demo_file']))
			{
				$is_zip = FALSE;
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

				global $wp_filesystem;
				$import_options_json = $wp_filesystem->get_contents($cache_dir.'/'.$new_filename);
				
				global $wp_filesystem;
				
				
				unlink($cache_dir.'/'.$import_filename);
				unlink($cache_dir.'/'.$new_filename);
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
						grandblog_page_update_custom_meta($post_id, $import_options_arr[$ppb_item_arr.'_size'][0], $ppb_item_arr.'_size');
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
		
			$json_file_name = strtolower(sanitize_title(THEMENAME)).'page'.strtolower(sanitize_title($page_title)).'_export_'.date('m-d-Y_hia');
	
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
	
		foreach ( $grandblog_page_postmetas as $postmeta ) 
		{
		
			if (isset($_POST[$postmeta['id']]) && $_POST[$postmeta['id']]) {
				grandblog_page_update_custom_meta($post_id, $_POST[$postmeta['id']], $postmeta['id']);
			}
	
			if (isset($_POST[$postmeta['id']]) && $_POST[$postmeta['id']] == "") {
				delete_post_meta($post_id, $postmeta['id']);
			}
			
			if (!isset($_POST[$postmeta['id']])) {
				delete_post_meta($post_id, $postmeta['id']);
			}
		}
		
		// Saving Page Builder Data
		if(isset($_POST['ppb_enable']) && !empty($_POST['ppb_enable']))
		{
			grandblog_page_update_custom_meta($post_id, $_POST['ppb_enable'], 'ppb_enable');
		}
		else
		{
			delete_post_meta($post_id, 'ppb_enable');
		}

		if(isset($_POST['ppb_form_data_order']) && !empty($_POST['ppb_form_data_order']))
		{
			grandblog_page_update_custom_meta($post_id, $_POST['ppb_form_data_order'], 'ppb_form_data_order');
			
			$ppb_item_arr = explode(',', $_POST['ppb_form_data_order']);
			if(is_array($ppb_item_arr) && !empty($ppb_item_arr))
			{
				foreach($ppb_item_arr as $key => $ppb_item_arr)
				{
					if(isset($_POST[$ppb_item_arr.'_data']) && !empty($_POST[$ppb_item_arr.'_data']))
					{
						grandblog_page_update_custom_meta($post_id, $_POST[$ppb_item_arr.'_data'], $ppb_item_arr.'_data');
					}
					
					if(isset($_POST[$ppb_item_arr.'_size']) && !empty($_POST[$ppb_item_arr.'_size']))
					{
						grandblog_page_update_custom_meta($post_id, $_POST[$ppb_item_arr.'_size'], $ppb_item_arr.'_size');
					}
				}
			}
		}
		//If content builder is empty
		else
		{
			grandblog_page_update_custom_meta($post_id, '', 'ppb_form_data_order');
		}
	}
	
	//If enable Content Builder then also copy its content to standard page content
	if (isset($_POST['ppb_enable']) && !empty($_POST['ppb_enable']) && ! wp_is_post_revision( $post_id ) )
	{
		//unhook this function so it doesn't loop infinitely
		remove_action('save_post', 'grandblog_page_save_postdata');
	
		//update the post, which calls save_post again
		$ppb_page_content = tg_apply_builder($post_id, 'page', FALSE);
		
		$current_post = array (
	      'ID'           => $post_id,
	      'post_content' => $ppb_page_content,
	    );
	    
	    wp_update_post($current_post);
	    if (is_wp_error($post_id)) {
			$errors = $post_id->get_error_messages();
			foreach ($errors as $error) {
				echo esc_html($error);
			}
		}

		//re-hook this function
		add_action('save_post', 'grandblog_page_save_postdata');
	}

}

function grandblog_page_update_custom_meta($postID, $newvalue, $field_name) 
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

add_action('admin_menu', 'grandblog_page_create_meta_box'); 
add_action('save_post', 'grandblog_page_save_postdata');  

/*
	End creating custom fields
*/

?>
