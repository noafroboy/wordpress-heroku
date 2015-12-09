<?php

/**
 * Function that runs on plugin uninstallation (not deactivation)
 *
 * @type function
 * @date 16/06/2015
 * @since 1.0
 *
 * @param N/A
 * @return N/A
 **/

function twp_uninstall() {
    
    global $wpdb;

    //drop a custom db table
    $wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "twp_queue" );
    $wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "twp_log" );
    $wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "twp_stats" );
    
    twp_unload_settings();
    twp_unschedule_task();
    
}

// ...

/**
 * Remove plugin's default settings on plugin uninstallation
 *
 * @type function
 * @date 16/06/2015
 * @since 1.0
 *
 * @param N/A
 * @return N/A
 **/

function twp_unload_settings() {
    
    delete_option( 'twp_queue_status' );
    delete_option( 'twp_settings_options' );
    delete_option( 'twp_db_version' );
    
    // Here goes authorisation...
    delete_option( 'twp_twitter_is_authed' );
    delete_option( 'twp_twitter_oauth_token' );
    delete_option( 'twp_twitter_oauth_token_secret' );
    delete_option( 'twp_license_purchase_code' );
    delete_option( 'twp_license_is_active' );
    delete_option( 'twp_license_purchase_code' );
    delete_option( 'twp_bitly_current_access_token' );
    delete_option( 'twp_bitly_is_authed' );
    delete_option( 'twp_license_purchase_code' );
    delete_option( 'twp_activation_redirect' );
    
}

// ...

/**
 * Delete Cron Job
 *
 * @type function
 * @date 16/06/2015
 * @since 1.0
 *
 * @param N/A
 * @return N/A
 **/

function twp_unschedule_task() {
    
    wp_clear_scheduled_hook( 'tweet_wheel_tweet' );
    wp_clear_scheduled_hook( 'tweet_wheel_stats' );

}  