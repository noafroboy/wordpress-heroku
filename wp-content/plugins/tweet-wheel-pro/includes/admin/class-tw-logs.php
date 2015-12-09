<?php

/**
 * Main class of TWP_Logs
 *
 * @class TWP_Logs
 */

class TWP_Logs {
    
    public static $_instance = null;
    
    // ...
    
	/**
	 * Main TWP_Logs Instance
	 *
	 * Ensures only one instance of TWP_Logs is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 * @return TWP_Logs object
	 */
    
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
    
    // ...
    
	/**
	 * TWP_Logs _construct
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return n/a
	 */
    
    public function __construct() {}
    
    // ...
    
    /**
     * Retrieves last tweeted tweet
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param array
	 * @return string | false
     */

    public function get_last_tweet( $types = null ) {
        
        global $wpdb;
        
        $sql = "SELECT * FROM " . $wpdb->prefix . "twp_log";
        
        if( $types != null && ! empty( $types ) ) :
        
            $sql .= " WHERE ";
        
            foreach( $types as $t ):
        
                $sql .= "type = '" . $t . "' OR ";
        
            endforeach;
        
            $sql = substr( $sql, 0, strrpos( $sql, " OR " ) );
        
        endif;
        
        $sql .= " ORDER BY ID DESC LIMIT 1";

        $results = $wpdb->get_row( $sql );
        
        if( $results == '' )
            return false;
        
        return $results;
        
    }
    
    // ...
    
    /**
     * Retrieves all tweeted posts
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param array
	 * @return string | false
     */

    public function get_all_tweets( $order = 'DESC', $limit = null ) {
        
        global $wpdb;
        
        $sql = "SELECT * FROM " . $wpdb->prefix . "twp_log ORDER BY timestamp " . $order;
        
        if( $limit != null )
            $sql .= " LIMIT " . $limit;
        
        return $wpdb->get_results( $sql );
        
    }
    
    // ...
    
    public function get_tweet_stats( $tweet_id ) {
        
        global $wpdb;
        
        $sql = "SELECT * FROM " . $wpdb->prefix . "twp_log WHERE tweet_ID = '" . $tweet_id . "'";
        
        $results = $wpdb->get_row( $sql );

        if( $results == '' )
            return false;
        
        return $results;
        
    }
    
    // ...
    
    public function get_all_tweets_stats( $order = 'DESC', $limit = null, $paged = 1 ) {
        
        global $wpdb;
        
        $sql = "
            SELECT l.*,s.stat_type,s.tweet_ID, GROUP_CONCAT(s.stat_value) stats FROM " . $wpdb->prefix . "twp_log l
LEFT JOIN " . $wpdb->prefix . "twp_stats s
ON l.tweet_ID = s.tweet_ID
WHERE ( s.stat_type = 'favorite' OR s.stat_type = 'retweet' OR s.stat_type = 'click' )";
        
        if( $paged > 1 ) :
            $sql .= " AND l.timestamp > '" . date( 'Y-m-d', strtotime( '-' . $paged . ' week', current_time( 'timestamp' ) ) ) . "' AND l.timestamp < '" . date( 'Y-m-d', strtotime( '-' . ( $paged - 1 ) . ' week', current_time( 'timestamp' ) ) ) . "'";
        else :
            $sql .= " AND l.timestamp > '" . date( 'Y-m-d', strtotime( '-1 week', current_time( 'timestamp' ) ) ) . "'";
        endif;

        $sql .= "
        GROUP BY s.tweet_ID
        ORDER BY l.timestamp
        DESC";

        if( $limit != null )
            $sql .= " LIMIT " . $limit;
        
        return $wpdb->get_results( $sql );
        
    }
    
}