<?php

// Library that handles Twitter API
require_once( TW_PLUGIN_DIR . '/includes/libraries/twitteroauth/autoloader.php' );

use Abraham\TwitterOAuth\TwitterOAuth;

/**
 * TW_Twitter Class
 *
 * @class TW_Twitter
 */

class TW_Twitter {
    
    // Keeps Twitter OAuth data
    private $auth;
    
    public static $_instance = null;

    // ...
    
	/**
	 * Main TweetWheel Twitter Instance
	 *
	 * Ensures only one instance of TweetWheel Twitter is loaded or can be loaded.
	 *
	 * @since 0.1
	 * @static
	 * @return TweetWheel - Main instance
	 */
    
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
    
    public function __construct() {
    
        // Load auth data to the plugin
        $this->auth = (object) array(
            'consumer_key' => get_option( 'tw_twitter_consumer_key' ),
            'consumer_secret' => get_option( 'tw_twitter_consumer_secret' ),
            'oauth_token' => get_option( 'tw_twitter_oauth_token' ),
            'oauth_token_secret' => get_option( 'tw_twitter_oauth_token_secret' )
        );
        
        add_action( 'tw_settings_options_type_deauth', array( $this, 'show_deauth_url' ) );

        // Check if there is a response from Twitter to handle
        add_action( 'init', array( $this, 'maybe_handle_response' ) );
        
        if( ! $this->is_authed() )
            add_filter( 'tw_load_admin_menu', array( $this, 'menu' ) );
        
    }
    
    // ...
    
    /**
     * Adds "Authorize" menu link
     *
     * @type function
     * @date 28/01/2015
	 * @since 0.1
     *
     * @param array
	 * @return array
     */
    
    public function menu( $menu ) {
        
        $menu[] = array(
            'page_title' => 'Authorize',
            'menu_title' => 'Authorize',
            'menu_slug'  => 'tw_twitter_auth',
            'function'   => array( $this, 'page' )
        );
        
        return $menu;
        
    }
    
    // ...
    
    /**
     * Authorize page content
     *
     * @type function
     * @date 28/01/2015
	 * @since 0.1
     *
     * @param n/a
	 * @return n/a
     */
    
    public function page() {
        
        if( $this->is_authed() )
            return;
        
	    ?>
        
		<div class="wrap tweet-wheel about-wrap">
            
            <div class="headline-feature">
                <h2><?php _e( 'One more thing before we continue...', TWP_TEXTDOMAIN ); ?></h2>
                <div class="feature-image">
                    <img style="margin:auto;display:block" src="<?php echo TW_PLUGIN_URL ?>/assets/images/tweet-wheel-auth-pic.png">
                </div>
                
                <div class="feature-section" style="text-align:center">
                    <h3><?php _e( 'Twitter Authorization', TWP_TEXTDOMAIN ); ?></h3>
                    <p><?php _e( 'Before you can unleash the awesomeness of Tweet Wheel, you need to authorize our app to access your Twitter account. We promise to behave :)', TWP_TEXTDOMAIN ); ?></p>
                    <p>
                        <a href="https://nerdcow.ticksy.com/article/5410/" target="_blank">Click here for step-by-step guide how to obtain required values for the authorisation.</a>
                    </p>
                    <form action="<?php echo admin_url('admin.php?page=tw_twitter_auth'); ?>" method="post">
                        <p>
                            <label>
                                Access Token:
                                <input style="width:400px" type="text" name="access_token" value="<?php echo isset( $_POST['access_token'] ) ? $_POST['access_token'] : ''; ?>">
                            </label>
                        </p>
                        <p>
                            <label>
                                Access Token Secret:
                                <input style="width:400px" type="text" name="access_token_secret" value="<?php echo isset( $_POST['access_token_secret'] ) ? $_POST['access_token_secret'] : ''; ?>">
                            </label>
                        </p>
                        <p>
                            <label>
                                Consumer Key:
                                <input style="width:400px" type="text" name="consumer_key" value="<?php echo isset( $_POST['consumer_key'] ) ? $_POST['consumer_key'] : ''; ?>">
                            </label> 
                        </p>
                        <p>
                            <label>
                                Consumer Secret:
                                <input style="width:400px" type="text" name="consumer_secret" value="<?php echo isset( $_POST['consumer_secret'] ) ? $_POST['consumer_secret'] : ''; ?>">
                            </label>   
                        </p>
                        <p>
                            <input type="submit" class="tw-start button" value="Authorise">
                        </p>
                    </form>
                </div>
            
            </div>
            
        </div>

		<?php
        
    }    
    
    // ...
    
    /**
     * Talks to Twitter. Handles authorisation, deauthorisation.
     *
     * @type function
     * @date 28/01/2015
     * @since 0.1
     *
     * @param N/A
     * @return N/A
     **/
    
    public function maybe_handle_response() {
        
        if( isset( $_GET['deauth'] ) )
            $this->deauthorize();
        
        if( isset( $_POST['consumer_key'] ) ) :
        
            $access_token = $_REQUEST['access_token'];
            $access_token_secret = $_REQUEST['access_token_secret'];
            $consumer_key = $_REQUEST['consumer_key'];
            $consumer_secret = $_REQUEST['consumer_secret'];
        
            $connection = new TwitterOAuth( 
                $consumer_key, 
                $consumer_secret,
                $access_token,
                $access_token_secret
            );

            // Try to authorize with given values
            try {

                $account = $connection->get( 
                    'account/verify_credentials'
                );
                
                if( isset( $account->errors ) )
                    return;
                
                if( $account ) :
                
                    update_option( 'tw_twitter_oauth_token', $access_token );
                    update_option( 'tw_twitter_oauth_token_secret', $access_token_secret );
                    update_option( 'tw_twitter_consumer_key', $consumer_key );
                    update_option( 'tw_twitter_consumer_secret', $consumer_secret );
                    update_option( 'tw_twitter_is_authed', 1 );
                    update_option( 'tw_twitter_screen_name', $account->screen_name );
                
                    if( self::is_authed() == 1 )
                        wp_redirect( admin_url( '/admin.php?page=tw_settings' ) );
                    exit;
                
                endif;
            
            } catch ( Exception $e ) {

                _e( "Your app details were incorrect. Please make sure you got them right!", TWP_TEXTDOMAIN );

            }
        
        endif;
        
    }
    
    // ...
    
    /**
     * Builds an authorisation button.
     * User clicks and is redirected to Twitter to complete the process.
     *
     * @type function
     * @date 28/01/2015
     * @since 0.1
     *
     * @param N/A
     * @return string (html)
     **/
    
    public function get_auth_url() {
        
        $connection = new TwitterOAuth( 
            $this->auth->consumer_key, 
            $this->auth->consumer_secret
        );

        try {
            
            $request_token = $connection->oauth( 'oauth/request_token', array('oauth_callback' => admin_url( '/admin.php?page=' . $_GET['page'] ) ) );
            
            set_transient('tw_temp_oauth_token', $request_token['oauth_token'], 60*60);
            
            set_transient('tw_temp_oauth_token_secret', $request_token['oauth_token_secret'], 60*60);
        
            $url = $connection->url( 'oauth/authorize', 
                array( 'oauth_token' => get_transient('tw_temp_oauth_token' ) )
            );
    
            return '<a href="' . $url . '" class="tw-start-button button">Authorize &raquo;</a><p>You will be redirected to twitter.com and brought back after authorization.';
        
        } catch ( Exception $e ) {
            
            return "<span style='color:red'>Invalid consumer key and/or consumer secret.</span>";
            
        }
        
    }
    
    // ...
    
    /**
     * Returns user's authorisation data
     *
     * @type function
     * @date 28/01/2015
     * @since 0.1
     *
     * @param N/A
     * @return object
     **/
    
    public function get_auth_data() {
        
        return $this->auth;
        
    }
    
    // ...
    
    /**
     * Determines if user is authorised with Twitter
     *
     * @type function
     * @date 28/01/2015
     * @since 0.1
     *
     * @param N/A
     * @return boolean
     **/
    
    public static function is_authed() {
        
        if( get_option( 'tw_twitter_is_authed' ) == 1 )
            return true;
        
        return false;
        
    }
    
    // ...
    
    /**
     * Build a deauthorisation button on settings page
     *
     * @type function
     * @date 02/02/2015
     * @since 0.1
     *
     * @param N/A
     * @return string (html)
     **/
    
    public function get_deauth_url() {
        
        return '<a href="' . admin_url( '/admin.php?page=tw_settings&deauth=true' ) . '" class="button button-primary" style="background:#D3000D;border-color:#9A0009">De-Authorize &raquo;</a><p>Tweet Wheel will cease from working after de-authorization. Re-authorization will be required to resume the plugin.</p>';
        
    }
    
    // ...
    
    public static function show_deauth_url() {
     
        echo '<a href="' . admin_url( '/admin.php?page=tw_settings&deauth=true' ) . '" class="button button-primary" style="background:#D3000D;border-color:#9A0009">De-Authorize &raquo;</a><p>Tweet Wheel will cease from working after de-authorization. Re-authorization will be required to resume the plugin.</p>';
        
    }
    
    // ...
    
    /**
     * Deauthorises and redirects to authorisation screen
     *
     * @type function
     * @date 02/02/2015
     * @since 0.1
     *
     * @param N/A
     * @return N/A
     **/
    
    public function deauthorize() {

        if( self::is_authed() == true ) :
            
            delete_option( 'tw_twitter_oauth_token' );
            delete_option( 'tw_twitter_oauth_token_secret' );
            delete_option( 'tw_twitter_is_authed' );

            wp_redirect( admin_url( '/admin.php?page=tw_twitter_auth' ) );
            
        endif;
        
        return;
        
    }
    
}

