<?php

/**
 * Main class TWP_Debug
 */

class TWP_Debug {
    
    public static $_instance = null;
    
    // ...
    
	/**
	 * Main TWP_Debug Instance
	 *
	 * Ensures only one instance of TWP_Debug is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 * @return TWP_Debug object
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
    
    // ...
    
	/**
	 * TWP_Debug constructor
     *
	 * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
	 * @return n/a
	 */

    public function __construct() {
        
        // Add admin menu
        add_filter( 'twp_load_admin_menu', array( $this, 'menu' ), 999 );
        
    }
    
    // ...

	/**
	 * Adds "Health Check" item to the Tweet Wheel menu tab
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
            'page_title' => __( 'Health Check', TWP_TEXTDOMAIN ),
            'menu_title' => __( 'Health Check', TWP_TEXTDOMAIN ),
            'menu_slug'  => 'twp_debug',
            'function'   => array( $this, 'page' )
        );
        
        return $menu;
        
    }
    
    // ...
    
	/**
	 * Loads the Debug screen
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
        
		<div class="wrap tweet-wheel tw-debug-page">
			<h2><img class="alignleft" style="margin-right:10px;" src="<?php echo TWP_PLUGIN_URL . '/assets/images/tweet-wheel-page-icon.png'; ?>"> <?php _e( 'Health Check', TWP_TEXTDOMAIN ); ?></h2>
        
           <table class="tw-report-table widefat" style="margin-top:20px;" cellspacing="0">
               
               <?php
                   
               foreach( $this->health_check() as $c ) :
                   
                   ?>
                   
                   <thead>
                       
                       <tr>
                           
                           <th colspan="2"><?php echo $c[0]; ?></th>
                           
                       </tr>
                       
                   </thead>
                   
                   <tbody>
                       
                       <?php foreach( $c[1] as $check ) : ?>
                           
                           <tr>
                               <td><?php echo $check[0]; ?></td>
                               <td><?php echo $check[1]; ?></td>
                           </tr>
                           
                       <?php endforeach; ?>
                       
                   </tbody>
                   
                   <?php
                   
               endforeach;
                   
               ?>
               
           </table>
            
        </div>
        
        <?php
        
    }
    
    // ...
    
    /**
     * Array of checks to perform
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param n/a
     * @return array
     */
    
    public function health_check() {
        
        global $twp_db_version;
        
        $checks = array(
            array(
                __( 'Tweet Wheel', TWP_TEXTDOMAIN ),
                array(
                    array( __( 'Version', TWP_TEXTDOMAIN ), TWP_VERSION ),
                    array( __( 'Database Version', TWP_TEXTDOMAIN ), $twp_db_version )
                )
            ),
            array(
                __( 'WordPress Installation', TWP_TEXTDOMAIN ),
                array(
                    array( __( 'Home URL', TWP_TEXTDOMAIN ), get_bloginfo( 'url' ) ),
                    array( __( 'Site URL', TWP_TEXTDOMAIN ), site_url() ),
                    array( __( 'WP Multisite', TWP_TEXTDOMAIN ), ( is_multisite() ? __( 'Yes', TWP_TEXTDOMAIN ) : __( 'No', TWP_TEXTDOMAIN ) ) ),
                    array( __( 'WP Version', TWP_TEXTDOMAIN ), get_bloginfo( 'version' ) ),
                    array( __( 'WP Cron', TWP_TEXTDOMAIN ), TWP_Cron()->is_wp_cron_disabled() ? '<span style="color:red">' . __( 'Disabled', TWP_TEXTDOMAIN ) . '</span>' : '<span style="color:green">' . __( 'Enabled', TWP_TEXTDOMAIN ) . '</span>' )
                )
            ),
            array(
                __( 'Server Environment', TWP_TEXTDOMAIN ),
                array(
                    array( __( 'Web Server', TWP_TEXTDOMAIN ), $_SERVER['SERVER_SOFTWARE'] ),
                    array( __( 'cURL Module', TWP_TEXTDOMAIN ), ( function_exists('curl_version') ? '<span style="color:green">' . __( 'Installed', TWP_TEXTDOMAIN ) . '</span>' : '<span style="color:red">' . __( 'Not Installed', TWP_TEXTDOMAIN ) . '</span>' ) )
                )
            )
        );
        
        return apply_filters( 'twp_debug_health_checks', $checks );
        
    }
    
}

/**
 * Returns the main instance of TWP_Debug
 *
 * @since  0.4
 * @return TWP_Debug
 */
function TWP_Debug() {
	return TWP_Debug::instance();
}
TWP_Debug();