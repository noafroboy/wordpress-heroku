<?php

/**
 * Main class of TWP_Link_Shortening
 *
 * @class TWP_Link_Shortening
 * @since 1.0
 */

class TWP_Link_Shortening {
    
    public static $_instance = null;
    
    /**
     * The URI of the standard bitly v3 API.
     */
    private $bitly_api_url = 'http://api.bit.ly/v3/';

    /**
     * The URI of the bitly OAuth endpoints.
     */
    private $bitly_oauth_url = 'https://api-ssl.bit.ly/v3/';

    /**
     * The URI for OAuth access token requests.
     */
    private $bitly_oauth_access_token = 'https://api-ssl.bit.ly/oauth/';
    
    // ...
    
	/**
	 * Main TWP_Link_Shortening Instance
	 *
	 * Ensures only one instance of TWP_Link_Shortening is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 * @return TWP_Link_Shortening object
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
    
    // ...
    
	/**
	 * TWP_Link_Shortening __construct
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
        
        // Check if there is a response from Bit.ly to handle
        add_action( 'updated_option', array( $this, 'maybe_handle_response' ), 10, 1 );
        
        // Hook in new options
        add_filter( 'twp_settings_options', array( 'TWP_Link_Shortening','options' ), 10, 1 );
       
        if( $this->is_authed() )
            add_filter( 'twp_tweet_allowed_tags', array( $this, 'parse_url_tag' ) );

    }
    
    public function options( $options ) {
        
        // Link Shortening

        $options[] = array( 'name' => __( 'Link Shortening', TWP_TEXTDOMAIN ), 'type' => 'heading' );

        if( TWP()->link_shortening()->is_authed() ) :
        
            $options[] = array( 
                'name' => __( 'Bit.ly', TWP_TEXTDOMAIN ), 
                'type' => 'title', 
                'desc' => __( 'Tweet Wheel is authorised with Bit.ly! Happy stalking on statistics!', TWP_TEXTDOMAIN ) 
            );

            $user_info = TWP()->link_shortening()->user_info();
        
            if( $user_info != false && is_array( $user_info->domain_options ) ) : 

                $domains = array();

                foreach( $user_info->domain_options as $domain ):

                    $domains[$domain] = $domain;

                endforeach;

                $options[] = array(
                    'name' => __( 'Domain used for shortening', TWP_TEXTDOMAIN ),
                    'id'   => 'bitly_domain',
                    'type' => 'select',
                    'options' => $domains,
                    'std' => TWP()->link_shortening()->get_domain()
                );

            endif;
        
            $options[] = array(
                'name' => __( 'Access token', TWP_TEXTDOMAIN ),
                'id'   => 'bitly_access_token',
                'type' => 'text',
                'placeholder' => 'Access token from Bit.ly'
            );
        
        else :
        
            $options[] = array( 'name' => __( 'Bit.ly', TWP_TEXTDOMAIN ), 'type' => 'title', 'desc' => sprintf( '%s<br/>%s <a href="https://bitly.com/a/oauth_apps" target="_blank">%s</a> %s', __( 'Before you can use link shortening & track clicks you have to authorise Tweet Wheel to access your Bit.ly account. It\'s totally safe.', TWP_TEXTDOMAIN ), __( 'Please follow', TWP_TEXTDOMAIN ), __( 'this link', TWP_TEXTDOMAIN ), __( 'to retrieve required information from Bit.ly and paste it into the field below.', TWP_TEXTDOMAIN ) ) );
        
            $options[] = array(
                'name' => __( 'Access Token', TWP_TEXTDOMAIN ),
                'id'   => 'bitly_access_token',
                'type' => 'text',
                'placeholder' => 'Access token from Bit.ly'
            );

        endif;

        return $options;
        
    }
    
    // ...
    
    /**
     * Talks to Bit.ly. Handles authorisation, deauthorisation.
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return N/A
     **/
    
    public function maybe_handle_response() {
        
        if( 
            ( $access_token = $this->get_access_token() ) != false && 
            get_option( 'twp_bitly_current_access_token', 'brap' ) != $access_token 
        ) :
    
            if( $this->user_info() != false ) :
        
                update_option( 'twp_bitly_current_access_token', $access_token );
                update_option( 'twp_bitly_is_authed', 1 );
        
            else :
        
                delete_option( 'twp_bitly_current_access_token' );
                update_option( 'twp_bitly_is_authed', 0 );
        
            endif;
        
        endif;
        
    }
    
    // ...
    
    public function is_authed() {
        
        if( get_option( 'twp_bitly_is_authed' ) == 1 )
            return true;
        
        return false;
        
    }
    
    // ...
    
    public function get_domain() {
    
        if( twp_get_option( 'twp_settings', 'bitly_domain' ) )
            return twp_get_option( 'twp_settings', 'bitly_domain' );
        
        return '';
        
    }
    
    // ...
    
    public function parse_url_tag( $tags ) {
     
        $tags['URL'] = 'twp_tweet_parse_shorten_url';
        
        return $tags;
        
    }
    
    // ...
    
    public function get_access_token() {
        
        return twp_get_option( 'twp_settings', 'bitly_access_token' ) != '' ? twp_get_option( 'twp_settings', 'bitly_access_token' ) : false;
        
    }
    
    /**
     *
     * Bit.ly functions
     *
     */
    
    public function user_info() {

        $results = array();
        
        $url = $this->bitly_oauth_url . "user/info?access_token=" . $this->get_access_token();
        
        $result = wp_remote_get( $url );
        
        if( ! is_wp_error( $result ) && json_decode( $result['body'] )->status_code == 200 ) :
            return json_decode( $result['body'] )->data;
        
        endif;
        
        return false;
        
    }
    
    // ...

    public function shorten_url( $longUrl, $domain = '' ) {
        
        $result = array();
        
        $url = $this->bitly_oauth_url . "shorten?access_token=" . $this->get_access_token() . "&longUrl=" . urlencode($longUrl);
        
        if ($domain != '') {
            $url .= "&domain=" . $domain;
        }

        $result = wp_remote_get( $url );
        
        if( ! is_wp_error( $result ) ) 
            return json_decode( $result['body'] );
        
        return false;

    }
    
    // ...
    
    public function url_clicks( $bitlink ) {
     
        $results = array();

        $url = $this->bitly_oauth_url . "link/clicks?access_token=" . $this->get_access_token() . "&unit=month&&link=" . $bitlink;
        
        $result = wp_remote_get( $url );
        
        if( ! is_wp_error( $result ) && json_decode( $result['body'] )->status_code == 200 ) :
            return json_decode( $result['body'] )->data;
        
        endif;

        return $results;
        
    }

}

add_filter( 'twp_settings_options', array( 'TWP_Link_Shortening', 'options' ), 10, 1 );