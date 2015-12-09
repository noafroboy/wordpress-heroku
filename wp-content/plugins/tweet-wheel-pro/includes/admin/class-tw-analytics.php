<?php

use Abraham\TwitterOAuth\TwitterOAuth;

/**
 * Main class of TWP_Analytics
 *
 * @class TWP_Analytics
 */

class TWP_Analytics {
    
    public static $_instance = null;
    
    // ...
    
	/**
	 * Main TWP_Analytics Instance
	 *
	 * Ensures only one instance of TWP_Analytics is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 * @return TWP_Analytics object
	 */
    
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
    
    // ...
    
	/**
	 * TWP_Analytics _construct
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return n/a
	 */
    
    public function __construct() {
        
        // Settings only for authed users
        if( ! TWP()->twitter()->is_authed() )
            return;
        
        // Add admin menu
        if( twp_get_option( 'twp_settings', 'analytics' ) != 1 )
            add_filter( 'twp_load_admin_menu', array( $this, 'menu' ) );
        
    }
    
    // ...
    
	/**
	 * Adds "Analytics" item to the Tweet Wheel menu tab
	 *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     * 
     * @param array
	 * @return array
	 */
    
    public function menu( $menu ) {
        
        $menu[] = array(
            'page_title' => __( 'Analytics', TWP_TEXTDOMAIN ),
            'menu_title' => __( 'Analytics', TWP_TEXTDOMAIN ),
            'menu_slug'  => 'twp_analytics',
            'function'   => array( $this, 'page' )
        );
        
        return $menu;
        
    }
    
    // ...
    
	/**
	 * Loads the Analytics screen
	 *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return n/a
	 */
    
    public function page() {
        
        ?>
        
		<div class="wrap tweet-wheel tw-analytics-page">
			<h2><img class="alignleft" style="margin-right:10px;" src="<?php echo TWP_PLUGIN_URL . '/assets/images/tweet-wheel-page-icon.png'; ?>"><?php _e( 'Analytics', TWP_TEXTDOMAIN ); ?></h2>
            
            <?php
        
            $this->display();
        
            ?>
        
        </div>
        
        <?php
        
    }
    
    // ...
    
    public function display( $s = null ) {
        
        // Pagiantion
        $paged = isset( $_GET['paged'] ) ? esc_attr( $_GET['paged'] ) : 1;
        
        ?>
        
        <div id="tw-analytics">

            <div class="row">
                
                <div class="col two-thirds">
                
                    <div class="twp-pagination">

                        <?php    

                        // Check if there are any records in the previous week
                        $prev_week = TWP()->logs()->get_all_tweets_stats( 'DESC', null, $paged + 1 );

                        if( count( $prev_week ) > 0) 
                            echo '<a class="twp-analytics-pagination" style="float:right" href="' . admin_url( 'admin.php?page=twp_analytics&paged=' . ( $paged + 1 ) ) . '">Previous Week &raquo;</a>';

                        if( $paged > 1 )
                            echo '<a class="twp-analytics-pagination" style="float:left" href="' . admin_url( 'admin.php?page=twp_analytics&paged=' . ( $paged - 1 ) ) . '">&laquo; Next Week</a>';

                        $tweets = TWP()->logs()->get_all_tweets_stats( 'DESC', null, $paged );
                        $is_bitly = TWP()->link_shortening->is_authed();

                        ?>
                    
                    </div>
                    
                    <?php

                    if( $tweets ) :
        
                        echo '<ul class="tw-analytics-list">';

                        foreach( $tweets as $tweet ) : 
        
                            $date = strtotime($tweet->timestamp);

                            $day = date('jS F Y',$date);

                            if ($day != $last_day) {
                                echo '<li class="day">' . $day . '</li>';
                                $last_day = $day;
                            }
        
                            $stats = explode( ',', $tweet->stats);

                            ?>

                            <li class="tw-analytics-item">

                            <div>
                                <p>
                                    <?php echo stripslashes( $tweet->tweet ); ?>
                                </p>
                                <ul class="tw-analytics-meta">
                                    <li>
                                        <span><?php echo $stats[1]; ?></span>
                                        <small><?php _e( 'Retweets', TWP_TEXTDOMAIN ); ?></small>
                                    </li>
                                    <li>
                                        <span><?php echo $stats[0]; ?></span>
                                        <small><?php _e( 'Favorites', TWP_TEXTDOMAIN ); ?></small>
                                    </li>
                                    <li>
                                        <?php if( $is_bitly ) : ?>
                                        
                                            <span><?php echo $stats[2]; ?></span> <small><?php _e( 'Clicks', TWP_TEXTDOMAIN ); ?></small>
                                        
                                        <?php else : ?>
                                        
                                        <a href="<?php echo admin_url( '/admin.php?page=twp_settings&tab=link-shortening' ); ?>">(<?php _e( 'Not Available', TWP_TEXTDOMAIN ); ?>)</a> <small><?php _e( 'Clicks', TWP_TEXTDOMAIN ); ?></small>
                                        
                                        <?php endif; ?>
                                        
                                    </li>
                                </ul>

                            </div>

                            </li>

                            <?php

                        endforeach;

                        echo '</ul>';
        
                    else :
        
                        echo '<p>Your tweets will start appearing here once we gather some analytics data about them. <br/>Usually it takes an hour to update after the first tweet has been sent out.</p>';

                    endif;
        
                    ?>
        
                </div>
                
            </div>
            
            <div class="sidebar col third">
            
                
            
            </div>
            
        </div>

    <?php
 
    }
    
    public function get_retweets( $tweet_id ) {
    
        global $wpdb;
        
        $results = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "twp_stats WHERE stat_type = 'retweet' AND tweet_ID = " . $tweet_id . " ORDER BY timestamp DESC LIMIT 1" );
        
        return $results != '' ? $results->stat_value : false;
        
    }
    
    public function update_retweets( $tweet_id, $new_value ) {
        
        global $wpdb;
    
        if( $this->get_retweets( $tweet_id ) !== false ) :
        
            $wpdb->update(
                $wpdb->prefix . 'twp_stats',
                array(
                    'stat_value' => $new_value,
                    'timestamp' => current_time( 'mysql' )
                ),
                array(
                    'tweet_ID' => $tweet_id,
                    'stat_type' => 'retweet'
                )
            );
        
            return true;
        
        else :
        
            $wpdb->insert(
                $wpdb->prefix . 'twp_stats',
                array(
                    'tweet_ID' => $tweet_id,
                    'stat_type' => 'retweet',
                    'stat_value' => $new_value,
                    'timestamp' => current_time( 'mysql' )
                )
            );
        
        endif;
        
        return true;
        
    }
    
    public function get_favorites( $tweet_id ) {
    
        global $wpdb;
        
        $results = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "twp_stats WHERE stat_type = 'favorite' AND tweet_ID = " . $tweet_id . " ORDER BY timestamp DESC LIMIT 1" );
        
        return $results != '' ? $results->stat_value : false;
        
    }
    
    public function update_favorites( $tweet_id, $new_value ) {
        
        global $wpdb;
    
        if( $this->get_favorites( $tweet_id ) !== false ) :
        
            $wpdb->update(
                $wpdb->prefix . 'twp_stats',
                array(
                    'stat_value' => $new_value,
                    'timestamp' => current_time( 'mysql' )
                ),
                array(
                    'tweet_ID' => $tweet_id,
                    'stat_type' => 'favorite'
                )
            );
        
            return true;
        
        else :
        
            $wpdb->insert(
                $wpdb->prefix . 'twp_stats',
                array(
                    'tweet_ID' => $tweet_id,
                    'stat_type' => 'favorite',
                    'stat_value' => $new_value,
                    'timestamp' => current_time( 'mysql' )
                )
            );
        
        endif;
        
        return true;
        
    }

    public function get_clicks( $tweet_id ) {
    
        global $wpdb;
        
        $results = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "twp_stats WHERE stat_type = 'click' AND tweet_ID = " . $tweet_id . " ORDER BY timestamp DESC LIMIT 1" );
        
        return $results != '' ? $results->stat_value : false;
        
    }
    
    public function update_clicks( $tweet_id, $new_value ) {
        
        global $wpdb;
    
        if( $this->get_clicks( $tweet_id ) !== false ) :
        
            $wpdb->update(
                $wpdb->prefix . 'twp_stats',
                array(
                    'stat_value' => $new_value,
                    'timestamp' => current_time( 'mysql' )
                ),
                array(
                    'tweet_ID' => $tweet_id,
                    'stat_type' => 'click'
                )
            );
        
            return true;
        
        else :
        
            $wpdb->insert(
                $wpdb->prefix . 'twp_stats',
                array(
                    'tweet_ID' => $tweet_id,
                    'stat_type' => 'click',
                    'stat_value' => $new_value,
                    'timestamp' => current_time( 'mysql' )
                )
            );
        
        endif;
        
        return true;
        
    }
    
    public function refresh_stats( $tweet_id = null ) {
        
        global $wpdb;
     
        $twitter = TWP()->twitter()->get_connection();
        
        $timeline = $twitter->get( 
            'statuses/user_timeline', 
            array(
                'screen_name' => get_option( 'twp_twitter_screen_name' ),
                'exclude_replies' => true,
                'include_rts' => false,
                'count' => 200 // limited by Twitter API :(
            ) 
        );
        
        if( empty( $timeline ) )
            return;
        
        $logs = TWP()->logs()->get_all_tweets( 'DESC', '200' );
        
        $filtered_timeline = array();
        
        $stats = array();
        
        // Filter out custom tweet posts
        
        foreach( $timeline as $t ):
        
            foreach( $logs as $l ) :
    
                if( $l->tweet_ID == $t->id ) :
        
                    $filtered_timeline[] = $t;
        
                endif;
        
            endforeach;
        
        endforeach;
        
        if( empty( $filtered_timeline ) )
            return;
        
        foreach( $filtered_timeline as $k => $v ) :
        
            // update rts
            $this->update_favorites( $v->id, $v->favorite_count );
        
            // update rts
            $this->update_retweets( $v->id, $v->retweet_count );
        
            // update clicks
            if( TWP()->link_shortening()->is_authed() ) :
            
                $tweet_data = TWP()->logs()->get_tweet_stats( $v->id );
            
                if( $tweet_data->short_url != '' ) :
        
                    $bitly_data = TWP()->link_shortening()->url_clicks( $tweet_data->short_url );
        
                    $this->update_clicks( $v->id, $bitly_data->link_clicks );
        
                endif;
                
            endif;
        
        endforeach;

    }
    
}