<?php
/**
 * Plugin Name: Tweet Wheel Pro
 * Plugin URI: http://www.tweetwheel.com
 * Description: A super-charged powerful tool that keeps your Twitter profile active. Even when you are busy.
 * Version: 1.4.1
 * Author: Tomasz Lisiecki from Nerd Cow
 * Author URI: https://nerdcow.co.uk
 * Requires at least: 3.8
 * Tested up to: 4.3
 *
 * Text Domain: tweetwheelpro
 * Domain Path: /i18n/languages/
 *
 * @package Tweet Wheel Pro
 * @category Core
 * @author Tomasz Lisiecki from Nerd Cow
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Prevent the plugin from crashing with the Lite version.
 * Assume user prefers to use Pro version from now on
 * so deactivate the Lite one
 **/

if( in_array( 'tweet-wheel/tweetwheel.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) :
    
   deactivate_plugins( 'tweet-wheel/tweetwheel.php' );
   wp_redirect( $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING'] );
   exit;

endif;

define( 'TWP_TEXTDOMAIN', 'tweetwheelpro' );

if ( ! class_exists( 'TweetWheelPro' ) ) :

/**
 * Plugin install / uninstall hooks
 */

include_once( 'includes/install.php' );
include_once( 'includes/uninstall.php' );

// Install
register_activation_hook( __FILE__, 'twp_install' );

// Activation
register_activation_hook( __FILE__, 'twp_activate' );

// Uninstall
register_uninstall_hook( __FILE__, 'twp_uninstall' );

// ...

/**
 * Main TweetWheel Class
 *
 * @class TweetWheel
 */
    
final class TweetWheelPro {
    
    /**
     * @var string
     */
    public $version = '1.4.1';
    
    // ...
    
    /**
     * @var the singleton
     * @static
     */
    protected static $_instance = null;
    
    // ...
    
    /**
     * @var TWP_Twitter object
     */
    public $twitter = null;
    
    // ...
    
    /**
     * @var TWP_Queue object
     */
    public $queue = null;
	
    // ...
    
    /**
     * @var TWP_Schedule object
     */
    public $schedule = null;
    
    // ...
    
	/**
	 * Main TweetWheel Instance
	 *
	 * Ensures only one instance of TweetWheel is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 * @return TweetWheel - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
    
    // ...
    
	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', TWP_TEXTDOMAIN ), $this->version );
	}
    
    // ...

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', TWP_TEXTDOMAIN ), $this->version );
	}
    
    // ...
    
    /**
     * TweetWheel Constructor
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return N/A 
     */
    
    public function __construct() {
        
        // Add translations
        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

        // Define all necessary constants
        $this->constants();
        
        // Load dependencies
        $this->includes();
        
        // Make sure WP Cron is setup
        // It lies in the root of the plugin, so if this doesn't trigger on the activation user is left with a useless plugin.
        // Therefore I decided to check if tasks are scheduled on every page load. Doesn't hurt anybody.
        if( twp_get_option( 'twp_settings', 'analytics' ) != 1 ) : 
            twp_schedule_task();
        else :
            wp_clear_scheduled_hook( 'tweet_wheel_stats' );
        endif;
        
        // Hooks
        add_action( 'admin_init', array( $this, 'redirect' ) );
        
        // Init plugin
        add_action( 'init', array( $this, 'init' ) );
        
        // Hook after loading the plugin. You welcome.
        do_action( 'tweetwheel_loaded' );
        
    }
    
    // ...
    
    /**
     * Define constants used in the plugin
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return N/A
     **/
    
    private function constants() {
        
        // Plugin Version
        if( ! defined( 'TWP_VERSION' ) )
            define( 'TWP_VERSION', $this->version );
        
        // Paths
        if( ! defined( 'TWP_PLUGIN_FILE' ) )
            define( 'TWP_PLUGIN_FILE', __FILE__ );
        
        if( ! defined( 'TWP_PLUGIN_BASENAME' ) )
            define( 'TWP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
        
        if( ! defined( 'TWP_PLUGIN_DIR' ) )
            define( 'TWP_PLUGIN_DIR', dirname( __FILE__ ) );
        
        if( ! defined( 'TWP_PLUGIN_URL' ) )
            define( 'TWP_PLUGIN_URL', plugins_url( '/tweet-wheel-pro' ) );
        
    }
    
    // ...
    
    /**
     * Include all dependencies, not loaded by autoload of course
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return N/A
     **/
    
    private function includes() {
        
        // initial stuff
        include_once( 'includes/helpers.php' );
        
        // Fundamental settings
        include_once( 'includes/admin/class-tw-menus.php' );
        include_once( 'includes/admin/tw-metaboxes.php' );
        include_once( 'includes/admin/class-tw-settings.php' );
        include_once( 'includes/admin/class-tw-settings-general.php' );

        // Third-parties
        include_once( 'includes/libraries/twitteroauth/autoload.php' );
        
        // Twitter Class
        include_once( 'includes/admin/class-tw-twitter.php' );
        
        // Tweet Class
        include_once( 'includes/admin/class-tw-tweet.php' );
        
        // Schedule Class
        include_once( 'includes/admin/class-tw-schedule.php' );
        
        // Queue Class
        include_once( 'includes/admin/class-tw-queue.php' );
        
        // Dashboard Class
        include_once( 'includes/admin/class-tw-dashboard.php' );
        
        // Cron class
        include_once( 'includes/admin/class-tw-cron.php' ); 
        
        // Debug class
        include_once( 'includes/admin/class-tw-debug.php' );
        
        // Analytics class
        include_once( 'includes/admin/class-tw-analytics.php' );
        
        // Logs class
        include_once( 'includes/admin/class-tw-logs.php' );
        
        // Link Shortening class
        include_once( 'includes/admin/class-tw-link-shortening.php' );
        
        // Licensing & Support
        include_once( 'includes/admin/class-tw-license.php' );
        
        if( defined( 'DOING_AJAX' ) ) :
            $this->ajax_includes();
        endif;
        
    }
    
    // ..
    
    /**
     * Include admin assets
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return N/A
     */
    
    public function assets() {
        
        // Custom CSS
        wp_register_style( 'tw-style', TWP_PLUGIN_URL . '/assets/css/tweet-wheel.css', null, $this->version );
        wp_enqueue_style( 'tw-style' );
        
        // ...
        
        // WP Core
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-ui-sortable' );
        
        // Other JS Libraries
        wp_register_script( 'autosize', TWP_PLUGIN_URL . '/assets/js/autosize.js', array( 'jquery' ), $this->version );
        wp_enqueue_script( 'autosize' );
  
        wp_register_script( 'validate', TWP_PLUGIN_URL . '/assets/js/jquery.validate.min.js', array( 'jquery' ), $this->version );
        wp_enqueue_script( 'validate' );
        
        // Tweet Wheel Main JS
        if( ! wp_script_is( 'tw-js' ) ) : 
            wp_register_script( 'tw-js', TWP_PLUGIN_URL . '/assets/js/tweet-wheel.js', array( 'jquery' ), $this->version );    
            wp_localize_script( 'tw-js', 'TWAJAX', array(
                'twNonce' => wp_create_nonce( 'tweet-wheel-nonce' ),
				'post_types' => twp_get_option( 'twp_settings', 'post_type' )
                )
            );
            wp_enqueue_script( 'tw-js' );
        endif;
        
        // ...
        
        // Tweet Templates JS
        if( ! wp_script_is( 'tw-metabox-templates' ) ) : 
            wp_register_script( 'tw-metabox-templates', TWP_PLUGIN_URL . '/assets/js/tweet-templates.js', array( 'jquery' ), $this->version );    
            wp_localize_script( 'tw-metabox-templates', 'tweet_template', sprintf( twp_tweet_template_default(), 0, '', 0 ) ); // @TODO - insert default tweet template from settings instead of ''
            wp_localize_script( 'tw-metabox-templates', 'default_tweet_template', TWP()->tweet()->get_default_template() );
            wp_enqueue_script( 'tw-metabox-templates' );
        endif;
        
    }
    
    // ...
    
    function load_textdomain() {
        load_plugin_textdomain( TWP_TEXTDOMAIN, false, dirname( plugin_basename(__FILE__) ) . '/i18n/languages/' );
    }
    
    // ...
    
    /**
     * Include all dependencies for AJAX needs
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return N/A
     **/
    
	public function ajax_includes() {
        
		include_once( 'includes/tw-ajax.php' );
        
	}
    
    // ...
    
    /**
     * Initialize the plugin! Woop!
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return N/A
     **/
    
    public function init() {
        
        if ( ! current_user_can( 'manage_options' ) )
            return;
        
        // Another gift.. Hook before plugin init
        do_action( 'before_tweetwheel_init' );
        
        // Load assets
        add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
        
        // Load Twitter class instance
        $this->twitter = $this->twitter();
        
        // Load Schedule class instance
        $this->schedule = $this->schedule();

        // Load Queue class instance
        $this->queue = $this->queue();
        
        // Load Analytics class instance
        $this->analytics = $this->analytics();
        
        // Load Logs class instance
        $this->logs = $this->logs();
        
        // Load Logs class instance
        $this->link_shortening = $this->link_shortening();
        
        // Hook right after init
        do_action( 'tweetwheel_init' );
        
    }
    
    // ...
    
    /**
     * Redirect after plugin activation (unless its a bulk update)
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return N/A
     **/

    public function redirect() {
        if (get_option('twp_activation_redirect', false)) {
            delete_option('twp_activation_redirect');
            if(!isset($_GET['activate-multi']))
            {
                wp_redirect(admin_url('/admin.php?page=tweetwheel'));
            }
        }
    }
    
    /*
    
    
    
    */
    
    // ... Helpers ...
    
    // ...
    
    /**
     * Get plugin path
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return string
     **/
    
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}
    
    /*
    
    
    
    */
    
    // ... Class Instances ...
    
    /**
     * Gets an instance of TWP_Twitter class
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return object
     **/
    
    public function twitter() {
        return TWP_Twitter::instance();
    }
    
    // ...
    
    /**
     * Gets an instance of TWP_Teet class
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return object
     **/
    
    public function tweet() {
        return TWP_Tweet::instance();
    }
    
    // ...
    
    /**
     * Gets an instance of TWP_Queue class
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return object
     **/
    
    public function queue() {
        return TWP_Queue::instance();
    }
	
    // ...
    
    /**
     * Gets an instance of TWP_Schedule class
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return object
     **/
    
    public function schedule() {
        return TWP_Schedule::instance();
    }
    
    // ...
    
    /**
     * Gets an instance of TWP_Analytics class
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return object
     **/
    
    public function analytics() {
        return TWP_Analytics::instance();
    }
    
    // ...
    
    /**
     * Gets an instance of TWP_Logs class
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return object
     **/
    
    public function logs() {
        return TWP_Logs::instance();
    }
    
    // ...
    
    /**
     * Gets an instance of TWP_Link_Shortening class
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return object
     **/
    
    public function link_shortening() {
        return TWP_Link_Shortening::instance();
    }
    
}

/**
 * Returns the main instance of TWP
 *
 * @since  1.0
 * @return TweetWheelPro
 */

function TWP() {
	return TweetWheelPro::instance();
}
TWP();
   
// I will find you, if you remove it or cheat it into believing you own a license.
// Thank you for cooperation.

if( get_option( 'twp_license_is_active' ) == 1 ) :

    require TWP_PLUGIN_DIR . '/includes/libraries/plugin-update-checker/plugin-update-checker.php';

    $MyUpdateChecker = PucFactory::buildUpdateChecker(
        'http://api.nerdcow.co.uk/updates/?action=get_metadata&slug=tweet-wheel-pro',
        __FILE__,
        'tweet-wheel-pro'
    );

endif;

endif;