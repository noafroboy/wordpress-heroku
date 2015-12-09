<?php

/**
 * Dashboard class
 *
 * @TODO: Eventually, this will be an actual dashboard with useful widgets and shortcuts, but for now let's leave it as About page.
 */

class TWP_Dashboard {

    public static $_instance = null;
    
    // ...
    
	/**
	 * Main TweetWheel Twitter Instance
	 *
	 * Ensures only one instance of TweetWheel Twitter is loaded or can be loaded.
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
     * Construct
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return N/A
     **/
    
    public function __construct() {}
    
    // ...
    
    /**
     * About us page content
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return N/A
     **/
    
    public static function page() {

        ?>
        
		<div class="wrap tweet-wheel intro-page about-wrap">
            
			<h1><?php _e( 'Welcome to Tweet Wheel Pro!', TWP_TEXTDOMAIN ); ?></h1>
        
            <div class="about-text">
                <?php printf( __( 
                'Thank you for joining a happy family of all Tweet Wheel Pro users!<br/>
                Tweet Wheel Pro %s is now ready to keep your <br/>
                Twitter profile active and engaging at all times!', TWP_TEXTDOMAIN ), TWP_VERSION ); ?>
            </div>
            
            <div class="tw-badge"><?php _e( 'Version', TWP_TEXTDOMAIN ); ?> <?php echo TWP_VERSION; ?></div>
            
            <hr>
            
            <div class="changelog point-releases">
            	<h3><?php _e( 'What\'s new', TWP_TEXTDOMAIN ); ?></h3>
            	<p><?php _e( 'Tweet Wheel Pro introduces many new features and improvements in comparison to Tweet Wheel free version. With TWP you can: shorten links with your own domain, attach photos to your tweets, track retweets, favorites and clicks and many more! To top it up you also get a lovely &amp; free premium support from the author. Enjoy!', TWP_TEXTDOMAIN ); ?></p>
            </div>

            <div class="headline-feature">
                <h2><?php _e( 'Let\'s meet', TWP_TEXTDOMAIN ); ?></h2>
                <div class="feature-image">
                    <img src="<?php echo TWP_PLUGIN_URL ?>/assets/images/featured-image.png">
                </div>
                
                <div class="feature-section">
                    <h3><?php _e( 'If there is one desire of every website owner, it is the exposure.', TWP_TEXTDOMAIN ); ?></h3>
                    <p><?php _e( 'Tweet Wheel is a simple and yet powerful tool that everyone will fall in love with. The idea behind Tweet Wheel is to take the burden off website owners\' shoulders and let them focus on the thing they are best at.', TWP_TEXTDOMAIN ); ?></p>
                    <p><?php _e( 'Promote your blog entires, shop products, case studies, pages and anything you like!', TWP_TEXTDOMAIN ); ?></p>
                    <p><?php _e( 'Thanks to a built-in queueing system, Tweet Wheel is as easy to manage as a music playlist!', TWP_TEXTDOMAIN ); ?></p>
                </div>
            
            </div>
            
            <hr>
            
            <div class="headline-feature">
                <h2><?php _e( 'Beauty of the automation', TWP_TEXTDOMAIN ); ?></h2>
                
                <p style="text-align:center"><?php _e( 'Never worry again tweeting regularly about your content. Tweet Wheel will do it for you. Automatically.', TWP_TEXTDOMAIN ); ?></p>
                
                <div class="feature-image">
                    
                    <img src="<?php echo TWP_PLUGIN_URL ?>/assets/images/queue-explained.png">
                    
                </div>
                
                <hr>
                
                <h2><?php _e( 'Say it in many ways', TWP_TEXTDOMAIN ); ?></h2>
                
                <p style="text-align:center"><?php _e( 'We introduced multiple tweet templates to avoid sounding like a broken record. Now you can set as many tweet variations for each post as you like!', TWP_TEXTDOMAIN ); ?></p>
                
                <div class="feature-image">
                    
                    <img src="<?php echo TWP_PLUGIN_URL ?>/assets/images/multitemplate.png">
                    
                </div>
                
                <hr>
                
                <h2><?php _e( 'Benefit from controlled regularity', TWP_TEXTDOMAIN ); ?></h2>
                
                <p style="text-align:center"><?php _e( 'With an in-built scheduler, you can tweet regularly on specific days at specific time. Just the way your followers would expect you to.', TWP_TEXTDOMAIN ); ?></p>
                
                <div class="feature-image">
                    
                    <img src="<?php echo TWP_PLUGIN_URL ?>/assets/images/scheduling.png">
                    
                </div>
                
                <hr>
                
                <div class="feature-list">
                    <h2><?php _e( 'The amazing bit', TWP_TEXTDOMAIN ); ?></h2>
                    <div class="feature-section col two-col">
                        <div>
                            <h4><?php _e( 'Customise the queue', TWP_TEXTDOMAIN ); ?></h4>
                            <p><?php _e( 'Add, remove, exclude and shuffle the queue the way you please! Tweet Wheel will never tweet without your consent.', TWP_TEXTDOMAIN ); ?></p>
                        </div>
                        <div class="last-feature">
                            <h4><?php _e( 'Control the timing', TWP_TEXTDOMAIN ); ?></h4>
                            <p><?php _e( 'Keep your profile consistent and organised. Schedule posts on specific days at specific times!', TWP_TEXTDOMAIN ); ?></p>
                        </div>
                    </div>
                    <div class="feature-section col two-col">
                        <div>
                            <h4><?php _e( 'Tweet once or infinitely', TWP_TEXTDOMAIN ); ?></h4>
                            <p><?php _e( 'Let Tweet Wheel reschedule every tweeted post.. or don\'t. It\'s up to you!', TWP_TEXTDOMAIN ); ?> </p>
                        </div>
                        <div class="last-feature">
                            <h4><?php _e( 'Benefit from templating', TWP_TEXTDOMAIN ); ?></h4>
                            <p><?php _e( 'Set a default post tweet template or overwrite it with each post\'s custom one! Now multi-templating available, too!', TWP_TEXTDOMAIN ); ?></p>
                        </div>
                    </div>
                    <div class="feature-section col two-col">
                        <div>
                            <h4><?php _e( 'Automatically queue new posts', TWP_TEXTDOMAIN ); ?></h4>
                            <p><?php _e( 'Why bother when it can be done automagically!', TWP_TEXTDOMAIN ); ?></p>
                        </div>
                        <div class="last-feature">
                            <h4><?php _e( 'Engage with your audience', TWP_TEXTDOMAIN ); ?></h4>
                            <p><?php _e( 'Focus on running your blog or your business. Leave the rest to Tweet Wheel.', TWP_TEXTDOMAIN ); ?></p>
                        </div>
                    </div>
                </div>
                
            </div>
            
            <div class="return-to-dashboard">
                <a class="tw-start-button button" href="<?php echo TWP()->twitter()->is_authed() == false ? admin_url('/admin.php?page=twp_twitter_auth') : admin_url('/admin.php?page=twp_queue') ?>"><?php _e( 'Start wheelin\' !', TWP_TEXTDOMAIN ); ?></a>
            </div>
            
        </div>
        
        <?php
        
    }
    
}

/**
 * Returns the main instance of TWP_Dashboard
 *
 * @since  0.1
 * @return TWP_Dashboard
 */
function TWP_Dashboard() {
	return TWP_Dashboard::instance();
}
TWP_Dashboard();