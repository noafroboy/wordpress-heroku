<?php
//Get category page layout setting
$tg_blog_category_layout = kirki_get_option('tg_blog_category_layout');

switch($tg_blog_category_layout)
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