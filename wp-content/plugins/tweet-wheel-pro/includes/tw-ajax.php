<?php

/**
 * This files consists of all AJAX related functions.
 * They are called from all over the place. Mainly classes.
 * Most of them are simple wrappers around class methods.
 */

/**
 * Handles saving of the queue. Saves posts in a new order.
 *
 * @type function
 * @date 16/06/2015
 * @since 1.0
 *
 * @param N/A
 * @return string
 **/

function twp_ajax_save_queue() {

    check_admin_referer( 'tweet-wheel-nonce', 'twnonce' );

    if ( current_user_can( 'manage_options' ) ) :

        // Dump the current queue in case something goes wrong...
        $old_queue = TWP()->queue()->get_queued_items();

        // Read new queue
        $new_queue = (array) $_POST['queue_order'];

        TWP()->queue()->remove_all();
    
        foreach( $new_queue as $post ) :
        
            $insert = TWP()->queue()->insert_post( $post );
        
            // If one saving fails, restore the backup
            if( $insert == false ) :
                TWP()->queue()->remove_all();
                TWP()->queue()->fill_up( $old_queue, 'post_ID' );
                echo json_encode( array( 'response' => 'error' ) );
                exit;
            endif;
        
        endforeach;
    
        echo json_encode( array( 'response' => 'ok' ) );
        
        exit;
    
    endif;
    
    echo json_encode( array( 'response' => 'error', 'message' => 'Not enough permissions' ) );
    
    exit;
    
}

// ...

/**
 * When a queue is empty, it shows an admin notification. This allows to hide it for a week.
 *
 * @type function
 * @date 16/06/2015
 * @since 1.0
 *
 * @param N/A
 * @return N/A
 **/

function twp_ajax_hide_empty_queue_alert() {
    
    check_admin_referer( 'tweet-wheel-nonce', 'twnonce' );

    if ( current_user_can( 'manage_options' ) ) :
    
        set_transient( '_twp_empty_queue_alert_' . get_current_user_id(), 'hide', 60*60*24*7 ); // hide for a week
        
        echo json_encode( array( 'response' => 'ok' ) );
    
    endif;
    
    exit;
    
}

// ...

/**
 * When WP cron is disabled and user acknowledges the problem, hide a message.
 *
 * @type function
 * @date 16/06/2015
 * @since 1.0
 *
 * @param N/A
 * @return N/A
 **/

function twp_ajax_wp_cron_alert() {
    
    check_admin_referer( 'tweet-wheel-nonce', 'twnonce' );

    if ( current_user_can( 'manage_options' ) ) :
    
        set_transient( '_twp_wp_cron_alert_' . get_current_user_id(), 'hide', 60*60*24*7 ); // hide for a week
        
        echo json_encode( array( 'response' => 'ok' ) );
    
    endif;
    
    exit;
    
}

// ...

/**
 * Resumes or pauses the queue. Used by WP Cron.
 *
 * @type function
 * @date 16/06/2015
 * @since 1.0
 *
 * @param N/A
 * @return string
 **/

function twp_ajax_change_queue_status() {
    
    check_admin_referer( 'tweet-wheel-nonce', 'twnonce' );

    if ( current_user_can( 'manage_options' ) ) :

        $status = TWP()->queue()->get_queue_status();
    
        if( $status == "paused" ) :
            TWP()->queue()->resume();
        endif;
    
        if( $status == "running" ) :
            TWP()->queue()->pause();
        endif;
        
        echo json_encode( array( 'response' => TWP()->queue()->get_queue_status() ) );
        
        exit;

    endif;
    
    echo json_encode( array( 'response' => 'error', 'message' => 'Not enough permissions to perform this action' ) );
    
    exit;
    
}

// ...

/**
 * Removes a single post from the queue. Nice and easy ;)
 *
 * @type function
 * @date 16/06/2015
 * @since 1.0
 *
 * @param N/A
 * @return json
 **/

function twp_ajax_remove_from_queue() {
    
    check_admin_referer( 'tweet-wheel-nonce', 'twnonce' );

    if ( current_user_can( 'manage_options' ) ) :
    
        if( TWP()->queue()->remove_post( $_POST['post_id'] ) ) :
            
            echo json_encode( array( 'response' => 'ok' ) );
            
            exit;
            
        endif;
    
    endif;
    
    echo json_encode( array( 'response' => 'error' ) );
    
    exit;
    
}

// ...

/**
 * Adds a single post to the queue. Nice and easy ;)
 *
 * @type function
 * @date 16/06/2015
 * @since 1.0
 *
 * @param N/A
 * @return json
 **/

function twp_ajax_add_to_queue() {
    
    check_admin_referer( 'tweet-wheel-nonce', 'twnonce' );

    if ( current_user_can( 'manage_options' ) ) :
    
        if( TWP()->queue()->insert_post( $_POST['post_id'] ) ) :
            
            echo json_encode( array( 'response' => 'ok' ) );
            
            exit;
            
        endif;
    
    endif;
    
    echo json_encode( array( 'response' => 'error' ) );
    
    exit;
    
}

// ...

/**
 * Sends a tweet.
 *
 * @type function
 * @date 16/06/2015
 * @since 1.0
 *
 * @param N/A
 * @return json
 **/

function twp_ajax_tweet() {
    
    check_admin_referer( 'tweet-wheel-nonce', 'twnonce' );

    if ( current_user_can( 'manage_options' ) ) :
    
        $tweet = TWP()->tweet()->tweet( $_POST['post_id'] );

        if( ! is_array( $tweet ) ) :
            
            echo json_encode( array( 'response' => 'ok' ) );
            
            exit;
            
        endif;
        
        echo json_encode( array( 'response' => 'error', 'message' => 'Cannot send a tweet. More likely problem with API. Check browser console for more info.', 'errormsg' => $tweet['errormsg'] ) );
        
        exit;
    
    endif;
    
    echo json_encode( array( 'response' => 'error' ) );
    
    exit;
    
}

// ...

/**
 * Retrieves registered post types
 *
 * @type function
 * @date 16/06/2015
 * @since 1.0
 *
 * @param N/A
 * @return json
 **/

function twp_ajax_get_post_types() {
	
    check_admin_referer( 'tweet-wheel-nonce', 'twnonce' );

    if ( current_user_can( 'manage_options' ) ) :
		
		$post_types = get_post_types( array( 'public' => true ), 'objects' );
		
		if( empty( $post_types ) ) :
			
			echo json_encode( array( 'response' => 'error', 'message' => 'No public post types enabled.' ) );
			
			exit;
			
		endif;

		echo json_encode( array( 'response' => 'success', 'data' => $post_types ) );
		
		exit;
		
	endif;
	
    echo json_encode( array( 'response' => 'error' ) );
    
    exit;
	
}

// ...

/**
 * Counts found posts
 *
 * @type function
 * @date 16/06/2015
 * @since 1.0
 *
 * @param N/A
 * @return json
 **/

function twp_ajax_found_posts() {
	
    check_admin_referer( 'tweet-wheel-nonce', 'twnonce' );

    if ( current_user_can( 'manage_options' ) ) :
    
        $post = $_POST['args'];
    
        $args = array (
            'posts_per_page' => ctype_digit( $post['number'] ) ? $post['number'] : '-1',
            'post_type' => $post['post_type']
        );

        // Check if date range hsa been set
        if( ! empty( $post['date_from'] ) || ! empty( $post['date_to'] ) ) :

            $args['date_query'] = array(
                'inclusive' => 'true'
            );

            if( ! empty( $post['date_from'] ) )
                $args['date_query']['after'] = $post['date_from'];

            if( ! empty( $post['date_to'] ) )
                $args['date_query']['before'] = $post['date_to'];

        endif;

        $data = get_posts( $args );

		echo json_encode( array( 'response' => 'success', 'data' => count( $data ) ) );
		
		exit;
		
	endif;
	
    echo json_encode( array( 'response' => 'error' ) );
    
    exit;
	
}

// ...

/**
 * Performs data import
 *
 * @type function
 * @date 08/07/2015
 * @since 1.2
 *
 * @param N/A
 * @return json
 **/

function twp_ajax_import_data_from_lite() {
    
    global $wpdb;
    
    check_admin_referer( 'tweet-wheel-nonce', 'twnonce' );

    if ( current_user_can( 'manage_options' ) ) :
    
        // Import settings framework (update option dont work, so gotta do hard way)
        $lite_settings = get_option( 'tw_settings_options' );
    
        $wpdb->query( 'DELETE FROM ' . $wpdb->prefix . 'options WHERE option_name = "twp_settings_options"');
    
        $wpdb->insert( 
            $wpdb->prefix . 'options', 
            array( 
                'option_name' => 'twp_settings_options',
                'option_value' => serialize( $lite_settings )
            )
        );

        // Migrate the rest of data
        update_option( 'twp_queue_status', get_option( 'tw_queue_status' ) );
        update_option( 'twp_last_tweet_time', get_option( 'tw_last_tweet_time' ) );
        update_option( 'twp_last_tweet', get_option( 'tw_last_tweet' ) );

        // Here goes authorisation...
        update_option( 'twp_twitter_is_authed', get_option( 'tw_twitter_is_authed' ) );
        update_option( 'twp_twitter_oauth_token', get_option( 'tw_twitter_oauth_token' ) );
        update_option( 'twp_twitter_oauth_token_secret', get_option( 'tw_twitter_oauth_token_secret' ) );
    
        // Loop through used post types & import templates etc
        $post_types = twp_get_all_enabled_post_types();
    
        if( $post_types ) :    

            foreach( $post_types as $pt ) :

                $args = array(
                    'post_type' => $pt,
                    'posts_per_page' => -1
                ); 
    
                $query = new WP_Query( $args );
    
                if( $query->have_posts() ) :
    
                    while( $query->have_posts() ) : $query->the_post();
    
                        $templates = get_post_meta( get_the_ID(), 'tw_post_templates', true );
                        if( $templates )
                            update_post_meta( get_the_ID(), 'twp_post_templates', $templates );
        
                        $exclude = get_post_meta( get_the_ID(), 'tw_post_excluded', true );
                        if( $exclude )
                            update_post_meta( get_the_ID(), 'twp_post_excluded', $exclude );
    
                        $order = get_post_meta( get_the_ID(), 'tw_templates_order', true );
                        if( $order )
                            update_post_meta( get_the_ID(), 'twp_templates_order', $order );
    
                    endwhile;
    
                endif;
    
                wp_reset_postdata();

            endforeach;

        endif;
    
        // Finally replicate the Queue
        $queue_items = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'tw_queue', OBJECT );
    
        $wpdb->query( 'TRUNCATE ' . $wpdb->prefix . 'twp_queue' );
        $wpdb->query( 'TRUNCATE ' . $wpdb->prefix . 'twp_log' );
        $wpdb->query( 'TRUNCATE ' . $wpdb->prefix . 'twp_stats' );
    
        if( $queue_items ) :
    
            foreach( $queue_items as $item ) :

                TWP()->queue()->insert_post( $item->post_ID  );
                    
            endforeach;
    
        endif;

        echo 'success'; exit;
    
    endif;
    
    echo 'failure'; exit;
    
}

add_action( 'wp_ajax_import_data_from_lite', 'twp_ajax_import_data_from_lite' );

// ...

/**
 * Performs Tweet Wheel Lite deinstallation
 *
 * @type function
 * @date 08/07/2015
 * @since 1.2
 *
 * @param N/A
 * @return json
 **/

function twp_ajax_delete_tweet_wheel_lite() {
    
    require_once ABSPATH . '/wp-admin/includes/class-wp-filesystem-base.php';
    require_once ABSPATH . '/wp-admin/includes/class-wp-filesystem-direct.php';
    
    $wp_filesystem = new WP_Filesystem_Direct('');
    
    check_admin_referer( 'tweet-wheel-nonce', 'twnonce' );

    if ( current_user_can( 'manage_options' ) && current_user_can('delete_plugins') ) :

        // make sure TWL is deactivated even though it deffo is
        @deactivate_plugins( 'tweet-wheel/tweetwheel.php' );

        // Remove data
        if( function_exists( 'tw_uninstall' ) )
            @tw_uninstall();

        // Remove files
        @delete_plugins( array( 'tweet-wheel/tweetwheel.php' ) );
    
        echo 'success'; exit;

    endif;
    
    echo 'failure'; exit;
    
}

add_action( 'wp_ajax_delete_tweet_wheel_lite', 'twp_ajax_delete_tweet_wheel_lite' );