<?php
/**
 * The main template file.
 *
 * @package WordPress
 */

$tg_blog_home_layout = kirki_get_option('tg_blog_home_layout');

if(THEMEDEMO && isset($_GET['layout']) && $_GET['layout'] == 'fullwidth')
{
	$tg_blog_home_layout = 'blog_f';
}
else if(THEMEDEMO && isset($_GET['layout']) && $_GET['layout'] == 'left_sidebar')
{
	$tg_blog_home_layout = 'blog_l';
}
else if(THEMEDEMO && isset($_GET['layout']) && $_GET['layout'] == 'right_sidebar')
{
	$tg_blog_home_layout = 'blog_r';
}
else if(THEMEDEMO && isset($_GET['layout']) && $_GET['layout'] == '2cols')
{
	$tg_blog_home_layout = 'blog_2cols';
}
else if(THEMEDEMO && isset($_GET['layout']) && $_GET['layout'] == '3cols')
{
	$tg_blog_home_layout = 'blog_3cols';
}
else if(THEMEDEMO && isset($_GET['layout']) && $_GET['layout'] == '2cols_infinite')
{
	$tg_blog_home_layout = 'blog_2cols_infinite';
}
else if(THEMEDEMO && isset($_GET['layout']) && $_GET['layout'] == '3cols_infinite')
{
	$tg_blog_home_layout = 'blog_3cols_infinite';
}
else if(THEMEDEMO && isset($_GET['layout']) && $_GET['layout'] == 'full_right_sidebar')
{
	$tg_blog_home_layout = 'blog_r_grid';
}
else if(THEMEDEMO && isset($_GET['layout']) && $_GET['layout'] == 'full_left_sidebar')
{
	$tg_blog_home_layout = 'blog_l_grid';
}
else if(THEMEDEMO && isset($_GET['layout']) && $_GET['layout'] == 'full_fullwidth')
{
	$tg_blog_home_layout = 'blog_f_grid';
}
else if(THEMEDEMO && isset($_GET['layout']) && $_GET['layout'] == 'full_list')
{
	$tg_blog_home_layout = 'blog_s_grid';
}
else if(THEMEDEMO && isset($_GET['layout']) && $_GET['layout'] == 'fixedwidth')
{
	$tg_blog_home_layout = 'blog_r_grid';
}
else if(THEMEDEMO && isset($_GET['layout']) && $_GET['layout'] == 'list')
{
	$tg_blog_home_layout = 'blog_s';
}

switch($tg_blog_home_layout)
{
    case "blog_r":
    default:
    	get_template_part("blog_r");
    	exit;
    break;
    
    case "blog_l":
    	get_template_part("blog_l");
    	exit;
    break;
    
    case "blog_f":
    	get_template_part("blog_f");
    	exit;
    break;
    
    case "blog_2cols":
    	get_template_part("blog_2cols");
    	exit;
    break;
    
    case "blog_3cols":
    	get_template_part("blog_3cols");
    	exit;
    break;
    
    case "blog_2cols_infinite":
    	get_template_part("blog_2cols_infinite");
    	exit;
    break;
    
    case "blog_3cols_infinite":
    	get_template_part("blog_3cols_infinite");
    	exit;
    break;
    
    case "blog_s":
    	get_template_part("blog_s");
    	exit;
    break;
    
    case "blog_r_grid":
    	get_template_part("blog_r_grid");
    	exit;
    break;
    
    case "blog_l_grid":
    	get_template_part("blog_l_grid");
    	exit;
    break;
    
    case "blog_f_grid":
    	get_template_part("blog_f_grid");
    	exit;
    break;
    
    case "blog_s_grid":
    	get_template_part("blog_s_grid");
    	exit;
    break;
}
?>