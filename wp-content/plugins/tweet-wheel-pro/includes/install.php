<?php

/**
 * Gets an instance of TWP_Twitter class
 *
 * @type function
 * @date 16/06/2015
 * @since 1.0
 *
 * @param N/A
 * @return object
 **/

// Declare global database / table version variable
global $twp_db_version;

// Define current database / table version
$twp_db_version = '1.0';

/**
 * Function that runs on plugin activation / installation
 *
 * @type function
 * @date 16/06/2015
 * @since 1.0
 *
 * @param N/A
 * @return N/A
 **/

function twp_install() {

    global $wpdb;
 
    $charset_collate = $wpdb->get_charset_collate();
    
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	$sql = "CREATE TABLE " . $wpdb->prefix . "twp_queue (
		ID bigint NOT NULL DEFAULT nextval(('wp_twp_queue_seq'::text)::regclass),
        post_ID bigint NOT NULL,
		queue integer NOT NULL
	)" .  $charset_collate;

    dbDelta( $sql );
    pg_prepare("create_sequence_1", 'CREATE SEQUENCE "wp_twp_queue_seq"');
    pg_execute("create_sequence_1", array());

    // ...
    
    $sql = "CREATE TABLE " . $wpdb->prefix . "twp_log (
		ID bigint NOT NULL DEFAULT nextval(('wp_twp_log_seq'::text)::regclass),
        post_ID bigint NOT NULL,
        type text NOT NULL,
		tweet text NOT NULL,
        short_url text NOT NULL,
        timestamp timestamp NOT NULL,
        tweet_ID bigint NOT NULL
	) " . $charset_collate;

    dbDelta( $sql );
    pg_prepare("create_sequence_2", 'CREATE SEQUENCE "wp_twp_log_seq"');
    pg_execute("create_sequence_2", array());
    // ...
    
    $sql = "CREATE TABLE " . $wpdb->prefix . "twp_stats (
		ID bigint NOT NULL DEFAULT nextval(('wp_twp_stats_seq'::text)::regclass),
        tweet_ID bigint NOT NULL,
        stat_type text NOT NULL,
		stat_value text NOT NULL,
        timestamp timestamp NOT NULL
	) " . $charset_collate;

    dbDelta( $sql );        
    pg_prepare("create_sequence_3", 'CREATE SEQUENCE "wp_twp_stats_seq"');
    pg_execute("create_sequence_3", array());

    // Load default settings
    twp_load_settings();
    twp_schedule_task();
    
}

// ...

/**
 * Provide plugin with default settings on plugin activation (if not defined)
 *
 * @type function
 * @date 16/06/2015
 * @since 1.0
 *
 * @param N/A
 * @return N/A
 **/

function twp_load_settings() {
    
    global $twp_db_version;
    
    $default = array(
		'post_type' => array( 0 => 'post' ),
        'queue_new_post' => 0,
        'tweet_text' => '{{TITLE}} - {{URL}}',
        'loop' => 1
    );
    
    add_option( 'twp_queue_status', 'paused' );
    add_option( 'twp_settings_options', $default );
    add_option( 'twp_db_version', $twp_db_version );
    
}

// ...

/**
 * Prevent redirection on plugin's re-activation. Once is enough :)
 *
 * @type function
 * @date 16/06/2015
 * @since 1.0
 *
 * @param N/A
 * @return N/A
 **/

function twp_activate() {
    
    global $wpdb;
    
    if( twp_get_option( 'twp_settings', 'analytics' ) != 1 ) 
        twp_schedule_task();
    
    add_option('twp_activation_redirect', true);
    
    if( get_option( 'twp_refresh_tokens' ) != 2 ) :
        delete_option( 'twp_twitter_oauth_token' );
        delete_option( 'twp_twitter_oauth_token_secret' );
        delete_option( 'twp_twitter_is_authed' );
        update_option( 'twp_refresh_tokens', 2 );
    endif;
    
    if( get_option( 'twp_stats_truncated' ) != 1 ) :
        $wpdb->query( "TRUNCATE " . $wpdb->prefix . "twp_stats" );
        update_option( 'twp_stats_truncated', 1 );
    endif;
}

// ...

/**
 * Schedule Cron Job
 *
 * @type function
 * @date 16/06/2015
 * @since 1.0
 * @updated 1.3.5
 *
 * @param N/A
 * @return N/A
 **/

function twp_schedule_task() {
    
    if( ! wp_next_scheduled( 'tweet_wheel_tweet' ) )
        wp_schedule_event( current_time( 'timestamp' ), 'minutely', 'tweet_wheel_tweet' );
    
    if( ! wp_next_scheduled( 'tweet_wheel_stats' ) )
        wp_schedule_event( current_time( 'timestamp' ), 'hourly', 'tweet_wheel_stats' );

}  