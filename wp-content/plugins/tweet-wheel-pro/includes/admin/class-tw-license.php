<?php

/**
 * Main class of TWP_License
 *
 * @class TWP_License
 * @since 1.0
 */

class TWP_License {
    
    public static $_instance = null;
    
	/**
	 * Main TWP_License Instance
	 *
	 * Ensures only one instance of TWP_License is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 * @return TWP_License object
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
    
    // ...
    
	/**
	 * TWP_License __construct
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
 
        // Hook in new options
        add_filter( 'twp_settings_options', array( 'TWP_License' ,'options' ), 10, 1 );
        
        // Check if there is a response from envato to handle
        add_action( 'updated_option', array( $this, 'maybe_handle_response' ), 10, 1 );
        
        add_action( 'twp_settings_options_type_license_status', array( $this, 'license_status' ) );
        
    }
    
    // ...
    
    public function options( $options ) {
        
        // Link Shortening
        $options[] = array( 'name' => __( 'License', TWP_TEXTDOMAIN ), 'type' => 'heading' );
    
        $options[] = array( 
            'name' => __( 'License', TWP_TEXTDOMAIN ), 
            'type' => 'title', 
            'desc' => sprintf( '%s <a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Can-I-Find-my-Purchase-Code-" target="_blank">%s</a> %s', __( 'In order to receive automated plugin updates, you need to provide your username and an item purchase code available on CodeCanyon.', TWP_TEXTDOMAIN ), __( 'Click here', TWP_TEXTDOMAIN ), __( 'to learn where to find it.', TWP_TEXTDOMAIN ) )
        );
        
        $options[] = array(
            'name' => __( 'License Status', TWP_TEXTDOMAIN ),
            'id'   => 'license_status',
            'type' => 'license_status'
        );        
        
        $options[] = array(
            'name' => __( 'Envato Username', TWP_TEXTDOMAIN ),
            'id'   => 'envato_username',
            'type' => 'text'
        );
        
        $options[] = array(
            'name' => __( 'Item Purchase Code', TWP_TEXTDOMAIN ),
            'id'   => 'purchase_code',
            'type' => 'text'
        );
        
        return $options;
        
    }
    
    // ...
    
    /**
     * Talks to Envato
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
            ( $purchase_code = $this->get_purchase_code() ) != false &&
            ( $envato_username = $this->get_username() ) != false &&
            get_option( 'twp_license_purchase_code', 'brap' ) != $purchase_code
        ) :

            if( $this->verify_purchase() == 1 ) :
        
                update_option( 'twp_license_purchase_code', $purchase_code );
                update_option( 'twp_license_is_active', 1 );
        
            else :
        
                delete_option( 'twp_license_purchase_code' );
                update_option( 'twp_license_is_active', 0 );
        
            endif;
        
        endif;
        
    }
    
    // ...
    
    public function get_username() {
        
        if( twp_get_option( 'twp_settings', 'envato_username' ) )
            return twp_get_option( 'twp_settings', 'envato_username' );
        
        return '';
        
    }
    
    // ...
    
    public function get_purchase_code() {
     
        if( twp_get_option( 'twp_settings', 'purchase_code' ) )
            return twp_get_option( 'twp_settings', 'purchase_code' );
        
        return '';   
        
    }
    
    // ...
    
    public function verify_purchase() {
     
        $fields = array(
            'code' => urlencode( $this->get_purchase_code() ),
            'user' => urlencode( $this->get_username() ),
            'token' => uniqid(mt_rand(), TRUE)
        );

        //url-ify the data for the POST
        foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
        rtrim($fields_string, '&');
        
        // Open cURL channel
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, "https://nerdcow.co.uk/envato/api/verify.php");
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        // Decode returned JSON
        $result = json_decode( curl_exec($ch) , true );
        
        if( $result['response'] == 'success' )
            return true;
        
        return false;
        
    }
    
    // ...
    
    public function license_status() {
     
        if( get_option( 'twp_license_is_active' ) == 1 ) :
        
            echo '<span style="color:green">' . __( 'License is active! Thank you so much for your support!', TWP_TEXTDOMAIN ) . '</span>';
        
        else :
        
            echo '<span style="color:red">' . __( 'License is inactive. Please provide correct information below.', TWP_TEXTDOMAIN ) . '</span>';
        
        endif;
        
    }
}

function TWP_License() {
    return TWP_License::instance();
}

TWP_License();