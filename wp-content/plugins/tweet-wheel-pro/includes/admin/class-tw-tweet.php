<?php

use Abraham\TwitterOAuth\TwitterOAuth;

class TWP_Tweet {
    
    private $tags;
    
    public static $_instance = null;

    // ...
    
	/**
	 * Main TWP_Tweet Instance
	 *
	 * Ensures only one instance of TWP_Tweet is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 * @return TWP_Tweet - Main instance
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

        // Loads allowed tags for tweet template
        $this->tags = $this->allowed_tags();
        
        // Required JS variables for template tags
        add_action( 'admin_print_scripts', array( $this, 'mb_print_js' ) );
        
        // Handles tweeting on demand
        add_action( 'wp_ajax_tweet', 'twp_ajax_tweet' );
        
    }
    
    // ...
    
    /**
     * Metabox JS variables - template tags.
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return N/A
     **/
    
    public function mb_print_js() {
        
        global $post;
        
        if( $post == null || empty( $this->tags ) )
            return;
        
        $id = $post->ID;
        
        ?>

        <script>
            var twp_template_tags = {
        <?php
        
        $i = 1;
        
        foreach( $this->tags as $tag => $func ) :
        
            ?>
            <?php echo strtoupper( $tag ); ?> : '<?php echo call_user_func( $func, $id ); ?>'<?php echo $i != count($this->tags) ? ',' : ''; ?>
            <?php
            
            $i++;
        
        endforeach; 
        
        ?>
                
            };
        
        </script>
        
        <?php
        
    }
    
    // ...
    
    /**
     * Returns a ready-to-go tweet; a tweet in its final form
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return string
     **/
    
    public function preview( $post_id ) {
        
        return $this->parse( $post_id );
        
    }
    
    // ...
    
    /**
     * Parses a tweet template; replaces tags with a proper values.
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return string
     **/
    
    public function parse( $post_id, $tweet = null ) {
        
        if( empty( $this->tags ) )
            return;
        
        foreach( $this->tags as $tag => $func ) :
            
            $tweet = str_replace( '{{'.$tag.'}}', call_user_func( $func, $post_id, $tweet ), $tweet );
            
        endforeach; 
        
        return html_entity_decode( $tweet, ENT_QUOTES, 'UTF-8' );
        
    }

    // ...
    
    /**
     * Include allowed template tags. Feel free to add your own using the filter.
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return array
     **/
    
    public function allowed_tags() {
        
        $tags = array(
            'URL' => 'twp_tweet_parse_url',
            'TITLE' => 'twp_tweet_parse_title'
        );

        $tags = apply_filters( 'twp_tweet_allowed_tags', $tags );
        
        return $tags;
        
    }
    
    // ...
    
    /**
     * The Magic!
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     * @update 1.3.5
     *
     * @param N/A
     * @return N/A
     **/
    
    public function tweet( $post_id = null, $analytics = true ) {
        
        global $wpdb;
        
        if( ! TWP()->twitter()->is_authed() )
            return false;
        
        $auth = TWP()->twitter()->get_auth_data();

        if( $post_id == null && ! TWP()->queue()->has_queue_items() )
            return false;

        $post_id = $post_id != null ? $post_id : TWP()->queue()->get_first_queued_item()->post_ID;

        $order = $this->get_tweeting_order( $post_id );

        switch( $order ) :

            case 'random';
            $raw_tweet = $this->get_random_template( $post_id );
            break;

            default:
            $raw_tweet = $this->get_next_template( $post_id );
            break;

        endswitch;

        $raw_tweet = apply_filters( 'twp_tweet_text', $raw_tweet, $post_id );
        
        $tweet = $this->parse( $post_id, $raw_tweet );
        
        // Make sure a tweet is 140 chars. 
        // Consider it a user error and stop script
        if( twp_character_counter( $tweet, $post_id ) > 140 )
            return false;

        // Create a connection with Twitter
        $connection = new TwitterOAuth( 
            $auth->consumer_key, 
            $auth->consumer_secret,
            $auth->oauth_token,
            $auth->oauth_token_secret
        );
        
        // Start building args to send to Twitter API
        $args = array(
            'status' => stripslashes($tweet)
        );
        
        // Attempt to upload an image if attached...
        if( 
            isset( $_REQUEST['tos_enable'] ) && 
            $_REQUEST['exclude_tweet_image'] != 1 && 
            has_post_thumbnail( $post_id ) || // TOS
            $this->has_image( $post_id ) && ! isset( $_REQUEST['exclude_tweet_image'] ) // Queue
        ) :
        
            $image = get_attached_file( get_post_thumbnail_id( $post_id ) );
        
            if( filesize( $image ) / 1024 < ( 5 * 1024 ) ) :
        
                $response = $connection->upload( "media/upload", array( "media" => $image ) );
        
                if( isset( $response->media_id ) )
                    $args['media_ids'] = $response->media_id;

            endif;

        endif;
        
        
        // Parse short url data from transient and clean-up
        $short_url_data = '';
        
        if( get_transient( 'twp_short_url_data_' . $post_id ) ) :
        
            $short_url_data = get_transient( 'twp_short_url_data_' . $post_id );
        
            delete_transient( 'twp_short_url_data_' . $post_id );
        
        endif;

        // Sending a tweet....
        $response = $connection->post( "statuses/update", $args );

        if( isset( $response->errors ) && is_array( $response->errors ) ) :
            
            do_action( 'twp_tweet_error', $post_id, $response );

            return array( 'status' => 'error', 'errormsg' => $response );
            
        endif;   
        
        if( $analytics ) :
        
            $wpdb->insert( 
                $wpdb->prefix . 'twp_log',
                array(
                    'post_ID' => $post_id,
                    'type' => defined( 'DOING_CRON' ) ? 'cron' : 'demand',
                    'tweet' => $tweet,
                    'short_url' => $short_url_data,
                    'timestamp' => date( "Y-m-d H:i:s", current_time( 'timestamp' ) ),
                    'tweet_ID' => $response->id
                )
            );
        
        endif;
        
        update_post_meta( $post_id,  'twp_last_tweeted_template', $raw_tweet );
        
        do_action( 'twp_before_tweet_dequeue', $post_id );
        
        // Remove post from the queue
        TWP()->queue()->remove_post( $post_id );
        
        do_action( 'twp_after_tweet_dequeue', $post_id );
        
        // If loop goes infinitely
        if( twp_get_option( 'twp_settings', 'loop' ) == 1 )
            TWP()->queue()->insert_post( $post_id );
        
        do_action( 'twp_after_tweet', $post_id );

        return $post_id;
        
    }

    // ...
    
    /**
     * Check if a post has multiple templates
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param int
	 * @return boolean
     */
    
    public function has_multiple_templates( $post_id ) {
     
        if( $post_id == null )
            return;
        
        $meta = get_post_meta( $post_id, 'twp_post_templates', true );
        
        if( count( $meta ) > 1 )
            return true;
        
        return false;
        
    }
    
    // ...
    
    /**
     * Count post templates
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param int
	 * @return int | null
     */
    
    public function count_templates( $post_id ) {
     
        if( $post_id == null )
            return;
        
        $meta = get_post_meta( $post_id, 'twp_post_templates', true );
        
        return count( $meta );
        
    }
    
    // ...
    
    /**
     * Checks if a post has any custom templates (even one)
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param int
	 * @return boolean
     */
    
    public function has_custom_templates( $post_id ) {
     
        if( $post_id == null )
            return;
        
        $meta = get_post_meta( $post_id, 'twp_post_templates', true );
        
        if( $meta == '' || count( $meta ) == 0 )
            return false;
        
        return true;
        
    }
    
    // ...
    
    /**
     * Checks if a post has an attached image
     *
     * @type function
     * @date 06/06/2015
	 * @since 1.0
     *
     * @param int
	 * @return boolean
     */
    
    public function has_image( $post_id ) {
     
        if( $post_id == null )
            return;
        
        $meta = get_post_meta( $post_id, 'exclude_tweet_image', true );

        if( has_post_thumbnail( $post_id ) && $meta != 1 )
            return true;
        
        return false;
        
    }
    
    // ...
    
    /**
     * Retrieve post's all custom templates
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param int
	 * @return null | array
     */
    
    public function get_custom_templates( $post_id ) {
        
        if( $post_id == null )
            return;
        
        return get_post_meta( $post_id, 'twp_post_templates', true );
        
    }
    
    // ...
    
    /**
     * Retrieves default template setting
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return string
     */
    
    public function get_default_template() {
     
        return twp_get_option( 'twp_settings', 'tweet_template' );

    }
    
    // ...
    
    /**
     * Retrieves last tweeted template for a post
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param int
	 * @return string | false
     */

    public function get_last_tweeted_template( $post_id ) {
        
        $template = get_post_meta( $post_id, 'twp_last_tweeted_template', true );
        
        if( '' != $template )
            return $template;
        
        return false;
        
    }
    
    // ...
    
    /**
     * Retrieves tweeting order for a post (random or following the order)
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param int
	 * @return string
     */
    
    public function get_tweeting_order( $post_id ) {
        
        return get_post_meta( $post_id, 'twp_templates_order', true ); 
        
    }
    
    // ...
    
    /**
     * Retrieves random template for a post
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param int
	 * @return string
     */
    
    public function get_random_template( $post_id ) {
        
        // fallback if misused on single-templated post
        if( ! TWP()->tweet()->has_multiple_templates( $post_id ) )
            return $this->get_next_template( $post_id );
        
        $meta = TWP()->tweet()->get_custom_templates( $post_id );
        $sanitized = '';

        foreach( $meta as $k => $v ) :
        
            $sanitized[$k] = sanitize_title_with_dashes( $v );
        
        endforeach;
        
        // check for last tweeted
        $last_tweeted_template = $this->get_last_tweeted_template( $post_id );
        
        if( $last_tweeted_template ) :
        
            $last_tweeted_template = sanitize_title_with_dashes( $last_tweeted_template );

            $key = array_search( $last_tweeted_template, $sanitized );

            if( false !== $key && isset( $meta[$key] ) )
                unset( $meta[$key] );
        
        endif;
        
        return $meta[array_rand( $meta )];
        
    }
    
    // ...
    
    /**
     * Retrieves next template for a post (the one after recently tweeted one)
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param int
	 * @return string
     */
    
    public function get_next_template( $post_id ) {
        
        // custom & multiple
        if( $this->has_multiple_templates( $post_id ) ) :
        
            $meta = $this->get_custom_templates( $post_id );
            $sanitized = '';

            foreach( $meta as $k => $v ) :

                $sanitized[$k] = sanitize_title_with_dashes( $v );

            endforeach;
        
            // @TODO - get it from post meta or sth
            $last_tweeted_template = sanitize_title_with_dashes( $this->get_last_tweeted_template( $post_id ) );
        
            $key = array_search( $last_tweeted_template, $sanitized );
        
            // If last tweeted template no longer exist, fallback to first in the array
            if( $key === false ) :
                
                $key = key($sanitized);
        
                return $meta[$key];    
            
            // If last tweeted template exists, go for next!
            else :
                
                return twp_get_next_in_array( $meta, $key );
                    
            endif;
        
        endif;
    
        // custom template
        if( $this->has_custom_templates( $post_id ) )
            return array_shift( $this->get_custom_templates( $post_id ) );
        
        // fallback to default
        return $this->get_default_template();
        
    }

}
// has to be here for js tags templates var to be printed in admin header... not sure why, gotta sort it later!
new TWP_Tweet;