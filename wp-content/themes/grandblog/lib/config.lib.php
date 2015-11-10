<?php
//Setup theme constant and default data
$theme_obj = wp_get_theme('grandblog');

define("THEMENAME", $theme_obj['Name']);
define("THEMEDEMO", FALSE);
define("SHORTNAME", "pp");
define("THEMEVERSION", $theme_obj['Version']);
define("THEMEDEMOURL", $theme_obj['ThemeURI']);
define("THEMEDATEFORMAT", get_option('date_format'));
define("THEMETIMEFORMAT", get_option('time_format'));
define("THEMEINFINITEITEMS", 6);

//Get default WP uploads folder
$wp_upload_arr = wp_upload_dir();
define("THEMEUPLOAD", $wp_upload_arr['basedir']."/".strtolower(sanitize_title(THEMENAME))."/");
define("THEMEUPLOADURL", $wp_upload_arr['baseurl']."/".strtolower(sanitize_title(THEMENAME))."/");

if(!is_dir(THEMEUPLOAD))
{
	mkdir(THEMEUPLOAD);
}

//Define all google font usages in customizer
$grandblog_google_fonts = array('tg_body_font', 'tg_header_font', 'tg_menu_font', 'tg_sidemenu_font', 'tg_sidebar_title_font', 'tg_button_font', 'tg_blog_title_font', 'tg_blog_date_font');
global $grandblog_google_fonts;
?>