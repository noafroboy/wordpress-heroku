<?php

class TWP_Cron {
    
    public static $_instance = null;

    // ...
    
	/**
	 * Main TweetWheel Cron Instance
	 *
	 * Ensures only one instance of TweetWheel Cron is loaded or can be loaded.
     * @type function
	 * @date 16/06/2015
	 * @since 1.0
     *
	 * @static
     * @param N/A
	 * @return TWP_Cron - Main instance
	 */
    
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
    
    // ...
    
    /**
     * Class constructor
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return N/A
     **/
    
    public function __construct() {
        
        // Add 15 minutes cron job
        add_filter( 'cron_schedules', array( $this, 'intervals' ), 10, 1 );

        // An actual cron task to be run by WP Cron
        add_action( 'tweet_wheel_tweet', array( $this, 'tweet_task' ) );
        
        // An actual cron task to be run by WP Cron
        add_action( 'tweet_wheel_stats', array( $this, 'stats_task' ) );
        
        // ...
        
        add_action( 'init', array( $this, 'cron_error' ) );
        
        add_action( 'wp_ajax_wp_cron_alert', 'twp_ajax_wp_cron_alert' );
    }
    
    // ...
    
    /**
     * Adds a custom interval to cron schedule (every minute)
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param array
     * @return array
     **/
    
    public function intervals( $schedules ) {
        
     	// Adds a minute interval to the existing schedules.
     	$schedules['minutely'] = array(
     		'interval' => 60,
     		'display' => __( 'Every Minute', TWP_TEXTDOMAIN )
     	);
        
     	return apply_filters( 'twp_cron_interval', $schedules );
        
     }

    // ...
    
    /**
     * Cron job
     * Checks if it is apprioriate time to tweet and tweets eventually
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return N/A
     **/
    
    public function tweet_task() {
        
        do_action( 'twp_tweet_before_cron' );
        
        // If queue is paused...
        if( 'running' != get_option( 'twp_queue_status' ) )
            return;
        
        // Check schedule
        // @TODO - turn it into a function "should_tweet" or "maybe_tweet"... to keep code organised
        
        $days = is_array( TWP()->schedule()->get_days() ) ? TWP()->schedule()->get_days() : array();
        
        $is_day = isset( $days[date('N')] ) && $days[date('N')] == 1 ? true : false;
        
        if( ! $is_day )
            return false;
        
        if( ! TWP()->schedule()->has_times() )
            return false;
        
        $closest_time = TWP()->schedule()->get_closest_time();

        if( $closest_time == false )
            return false;
        
        $last_tweet = TWP()->logs()->get_last_tweet( array( 'cron' ) );
        
        $last_tweeted_time = $last_tweet !== false ? strtotime( $last_tweet->timestamp ) : 0;
        
        // Last tweeted time is greater than latest time in the schedule, so it don't tweet again
        if( $last_tweeted_time > $closest_time )
            return false;
        
        // Go ahead then =]        
        if( TWP()->queue()->has_queue_items() == true ) :

            $queue_items = TWP()->queue()->get_queued_items();

            // Try until something is tweeted...
            foreach( $queue_items as $q ) :

                // If no error and Tweet was published, break out of the loop 
                if( TWP()->tweet()->tweet( $q->post_ID ) != false ) :
                    
                    return true;
                    
                endif;
            
            endforeach;
        
        endif;
        
        do_action( 'twp_tweet_after_cron' );
        
        return false;
    
    }
    
    // ...
    
    /**
     * Cron job
     * Refreshes stats
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return N/A
     **/
    
    public function stats_task() {
        
        do_action( 'twp_before_stats_cron' );
        
        TWP()->analytics()->refresh_stats();
        
        do_action( 'twp_after_stats_cron' );
        
        return true;
        
    }
    
    // ...
    
    /**
     * Shows hideable error about WP cron being disabled
     *
     * @type function
     * @date 04/07/2015
     * @since 1.1.1
     *
     * @param N/A
     * @return N/A
     **/
    
    public function cron_error() {

		if( $this->is_wp_cron_disabled() == true && ! get_transient( '_twp_wp_cron_alert_' . get_current_user_id() ) )
            add_action( 'admin_notices', array( $this, 'cron_error_notice' ) );
		
	}
    
    // ...
    
    /**
     * Cron error content
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return N/A
     **/

    public function cron_error_notice() {
        
        ?>
        <div class="tw-wp-cron-alert error">
            <p><?php _e( 'Tweet Wheel needs WP Cron to be enabled!', TWP_TEXTDOMAIN ); ?><a id="wp-cron-alert-hide" href="#" class="button" style="margin-left:10px;"><?php _e( 'I know, don\'t bug me.', TWP_TEXTDOMAIN ); ?></a></p>
        </div>

        <?php
        
    }
    
    // ...
    
    /**
     * Helpers
     */
    
    /**
     * Checks WP cron status
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return N/A
     **/
    
    public function is_wp_cron_disabled() {
        
        if( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON == true )
            return true;
        
        return false;
        
    }
    
    
}

/**
 * Returns the main instance of TWP_Cron
 *
 * @since  0.4
 * @return TWP_Cron
 */
function TWP_Cron() {
	return TWP_Cron::instance();
}
TWP_Cron();