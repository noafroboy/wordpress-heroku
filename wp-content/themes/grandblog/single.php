<?php
/**
 * The main template file for display single post page.
 *
 * @package WordPress
*/

/**
*	Get current page id
**/

$current_page_id = $post->ID;

if($post->post_type == 'galleries')
{
	//Get gallery template
	$gallery_template = get_post_meta($current_page_id, 'gallery_template', true);
	
	switch($gallery_template)
	{	
		case 'Gallery 2 Columns':
			get_template_part("gallery-2");
		break;
		
		case 'Gallery 3 Columns':
		default:
			get_template_part("gallery-3");
		break;
		
		case 'Gallery 4 Columns':
			get_template_part("gallery-4");
		break;
	}

	exit;
}
else
{
	$post_layout = get_post_meta($post->ID, 'post_layout', true);
	
	if(THEMEDEMO && isset($_GET['layout']))
	{
		switch($_GET['layout'])
		{
			case "right_sidebar":
				$post_layout = 'With Right Sidebar';
			break;
			
			case "left_sidebar":
				$post_layout = 'With Left Sidebar';
			break;
			
			case "fullwidth":
				$post_layout = 'Fullwidth';
			break;
		}
	}
	
	switch($post_layout)
	{
		case "With Right Sidebar":
		default:
			get_template_part("single-post-r");
			exit;
		break;
		
		case "With Left Sidebar":
			get_template_part("single-post-l");
			exit;
		break;
		
		case "Fullwidth":
			get_template_part("single-post-f");
			exit;
		break;
		
		case "Split Screen":
			get_template_part("single-post-split");
			exit;
		break;
	}
}
?>