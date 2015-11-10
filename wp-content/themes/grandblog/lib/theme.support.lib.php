<?php
if ( function_exists( 'add_theme_support' ) ) {
	// Setup thumbnail support
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'custom-background' );
}

if ( function_exists( 'add_image_size' ) ) { 
	add_image_size( 'grandblog_gallery_grid', 705, 529, true );
	add_image_size( 'grandblog_blog', 960, 9999, false );
	add_image_size( 'grandblog_blog_thumb', 700, 529, true );
}
?>