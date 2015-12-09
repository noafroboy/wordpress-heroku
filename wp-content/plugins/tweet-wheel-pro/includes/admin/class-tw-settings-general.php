<?php

class TWP_Settings_General {
	
	private $settings_framework = null;
    
    public function __construct() {
        
        // Settings only for authed users
        if( TWP()->twitter()->is_authed() == 0 )
            return;
        
        add_filter( 'twp_load_admin_menu', array( $this, 'menu' ) );

		$this->settings_framework = new SF_Settings_API( $id = 'twp_settings', $title = '', __FILE__);
        
		$this->settings_framework->load_options( $this->options() );
		
		add_action( 'wp_ajax_get_post_types', 'twp_ajax_get_post_types' );

		add_filter( 'twp_settings_tab_options-general', array( $this, 'post_type_value' ), 10, 2 );
		add_action( 'twp_settings_options_type_post_type', array( $this, 'post_type_settings' ) );
		add_action( 'twp_settings_after_form_tab-general', array( $this, 'post_type_js' ) );

    }
    
    public function options() {
     
        // General tab
        $options[] = array( 'name' => __( 'General', TWP_TEXTDOMAIN ), 'type' => 'heading' );
        $options[] = array( 'name' => __( 'General options', TWP_TEXTDOMAIN ), 'type' => 'title', 'desc' => '' );

        $options[] = array(
            'name' => __( 'Allowed post types', TWP_TEXTDOMAIN ),
            'desc' => __( 'Select custom post types, which should be used by the plugin', TWP_TEXTDOMAIN ),
            'id'   => 'post_type',
            'type' => 'post_type'
        );

        $options[] = array(
            'name' => __( 'Exclude new posts from the queue?', TWP_TEXTDOMAIN ),
            'desc' => __( 'Check if you want new posts to be excluded from the queue by default.', TWP_TEXTDOMAIN ),
            'id'   => 'queue_new_post',
            'type' => 'checkbox',
            'options' => array(
                'exclude_by_default' => 1
            )
        );

        $options[] = array(
            'name' => __( 'Default tweet template', TWP_TEXTDOMAIN ),
            'desc' => __( 'Default tweet text can be overriden by custom post tweet text setting available on edit page of each post. Allowed tags: {{TITLE}} for post title and {{URL}} for post permalink.', TWP_TEXTDOMAIN ),
            'id'   => 'tweet_template',
            'type' => 'textarea',
            'placeholder' => __( 'What\'s happenng?', TWP_TEXTDOMAIN ),
            'std' => '{{TITLE}} - {{URL}}'
        );

        $options[] = array(
            'name' => __( 'Loop infinitely?', TWP_TEXTDOMAIN ),
            'desc' => __( 'Check if you want the most recent tweeted post to be re-queued automatically.', TWP_TEXTDOMAIN ),
            'id'   => 'loop',
            'type' => 'checkbox',
            'options' => array(
                'loop' => 1
            ),
            'std' => 1
        );
        
        $options[] = array(
            'name' => __( 'Disable analytics', TWP_TEXTDOMAIN ),
            'desc' => __( 'Analytics can cause a high CPU load for some users. In this case just disable analytics entirely', TWP_TEXTDOMAIN ),
            'id' => 'analytics',
            'type' => 'checkbox',
            'options' => array(
                'analytics' => 1    
            ),
            'std' => 0
        );

        $options[] = array(
            'name' => __( 'Disconnect Twitter Account', TWP_TEXTDOMAIN ),
            'desc' => __( 'You will need to authorize another account to resume using this plugin.', TWP_TEXTDOMAIN ),
            'id'   => 'deauth',
            'type' => 'deauth'
        );
        
        return $options;
        
    }
    
    public function menu( $menu ) {
        
        $menu[] = array(
            'page_title' => __( 'Settings', TWP_TEXTDOMAIN ),
            'menu_title' => __( 'Settings', TWP_TEXTDOMAIN ),
            'menu_slug'  => 'twp_settings',
            'function'   => array( $this, 'page' )
        );
        
        return $menu;
        
    }
    
    public function page() {
        
	    ?>
		<div class="wrap tw-settings-page">
			<h2><img class="alignleft" style="margin-right:10px;" src="<?php echo TWP_PLUGIN_URL . '/assets/images/tweet-wheel-page-icon.png'; ?>"><?php _e( 'Tweet Wheel Settings', TWP_TEXTDOMAIN ); ?></h2>
			<?php $this->settings_framework->init_settings_page(); ?>
		</div>
		<?php
        
    }
	
	public function post_type_settings() {
		
		echo '<div id="post_type_wrapper"></div>';
		
	}
	
    public function post_type_value( $tabs, $post ) {

        if( ! isset( $tabs['general'] ) )
            return $tabs;
        
        if( ! isset( $post['post_type'] ) )
            $post['post_type'] = array();

        $tabs['general'][] = array(
            'name' => __( 'Allowed post types', TWP_TEXTDOMAIN ),
            'id' => 'post_type',
            'type' => 'post_type',
            'options' => $post['post_type']
        );
        
        return $tabs;
        
    }
	
	public function post_type_js() {
		
		$options = twp_get_option( 'twp_settings', 'post_type' );
		
		?>
		
		<script>
		jQuery.noConflict();
		jQuery(window).load(function(){
	
			var el = jQuery('#post_type_wrapper');
			var post_types = jQuery.parseJSON('<?php echo json_encode($options); ?>');
		
			if( el.length == 0 )
				return;
		
			el.text( 'Loading...' );
		
			jQuery.get(
				ajaxurl, 
				{
					action: 'get_post_types',
					twnonce: TWAJAX.twNonce
				},
				function( response ) {
				
					var data = jQuery.parseJSON( response );
				
					if( data.response == 'error' ) {
						el.text( data.message );
					}
				
					el.empty();

					jQuery.each( data.data, function( k,v ) {
						
						var is_checked = jQuery.inArray( k, post_types ) != -1 ? true : false;
					
						var html = '<label for="post_type_'+k+'"><input name="twp_settings_options[post_type][]" id="post_type_'+k+'" type="checkbox" value="'+k+'" '+( is_checked ? 'checked' : '' )+'>'+v.label+'</label><br/>';

						el.append(html);
					
					} );
				
				}
			);
		
		});
		
		</script>
		
		<?php		
	}

}
new TWP_Settings_General;