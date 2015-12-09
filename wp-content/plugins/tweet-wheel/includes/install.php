<?php

/**
 * Gets an instance of TW_Twitter class
 *
 * @type function
 * @date 28/01/2015
 * @since 0.1
 *
 * @param N/A
 * @return object
 **/

// Declare global database / table version variable
global $tw_db_version;

// Define current database / table version
$tw_db_version = '1.0';

/**
 * Function that runs on plugin activation / installation
 *
 * @type function
 * @date 30/01/2015
 * @since 0.1
 *
 * @param N/A
 * @return N/A
 **/

function tw_install() {
    
    global $wpdb;
    
    
    $table_name = $wpdb->prefix . 'tw_queue';
    
    $charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		ID bigint NOT NULL DEFAULT nextval(('wp_tw_queue_seq'::text)::regclass),
        post_ID bigint NOT NULL,
		queue integer NOT NULL
	) $charset_collate;";

    // Create / Upgrade table structure
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

    $sql = pg_prepare("create_sequence", 'CREATE SEQUENCE "wp_tw_queue_seq"');
    // Execute the prepared query.  Note that it is not necessary to escape
    // the string "Joe's Widgets" in any way
    $sql = pg_execute("create_sequence", array());
    // pg_execute("CREATE SEQUENCE \"wp_tw_queue_seq\";");

    // Load default settings
    tw_load_settings();
    tw_schedule_task();
    
}

// ...

/**
 * Provide plugin with default settings on plugin activation (if not defined)
 *
 * @type function
 * @date 30/01/2015
 * @since 0.1
 *
 * @param N/A
 * @return N/A
 **/

function tw_load_settings() {
    
    global $tw_db_version;
    
    $default = array(
		'post_type' => array( 0 => 'post' ),
        'queue_new_post' => 0,
        'tweet_text' => '{{TITLE}} - {{URL}}',
        'loop' => 1
    );
    
    add_option( 'tw_queue_status', 'paused' );
    add_option( 'tw_settings_options', $default );
    add_option( 'tw_db_version', $tw_db_version );
    
    if( get_option( 'tw_refresh_tokens' ) != 2 ) :
        delete_option( 'tw_twitter_oauth_token' );
        delete_option( 'tw_twitter_oauth_token_secret' );
        delete_option( 'tw_twitter_is_authed' );
        update_option( 'tw_refresh_tokens', 2 );
    endif;
    
}

// ...

/**
 * Prevent redirection on plugin's re-activation. Once is enough :)
 *
 * @type function
 * @date 30/01/2015
 * @since 0.1
 *
 * @param N/A
 * @return N/A
 **/

function tw_activate() {
    wp_clear_scheduled_hook('tweet_wheel');
    add_option('tw_activation_redirect', true);
}

// ...

/**
 * Schedule Cron Job
 *
 * @type function
 * @date 03/04/2015
 * @since 0.4
 *
 * @param N/A
 * @return N/A
 **/

function tw_schedule_task() {
    if( ! wp_next_scheduled( 'tweet_wheel' ) )
        wp_schedule_event( current_time( 'timestamp' ), 'every_ten', 'tweet_wheel' );

}  