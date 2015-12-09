<?php

/**
 * Main class TWP_Menus
 *
 * The idea is to be the superior class handling menus.
 * I wanted it to be extensible by hooks. Maybe it will come useful later.
 *
 * @class TWP_Menus
 */

class TWP_Menus {
    
    private $menus = array();
    
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
        
        $this->menus[] = array(
            'page_title' => __('About', TWP_TEXTDOMAIN ),
            'menu_title' => __( 'About', TWP_TEXTDOMAIN ),
            'capability' => 'administrator',
            'menu_slug' => 'tweetwheel',
            'auth_only' => false
        );
        
        add_action( 'admin_menu', array( $this, 'menu' ), 10 );
        add_action( 'admin_menu', array( $this, 'submenu' ), 10 );
        
    }
    
    // ...
    
    /**
     * Adds main parent menu tab Tweet Wheel
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return N/A
     **/
    
    public function menu() {
        
        add_menu_page( 
            __( 'Tweet Wheel Pro', TWP_TEXTDOMAIN ), 
            __( 'Tweet Wheel Pro', TWP_TEXTDOMAIN ), 
            'administrator', 
            'tweetwheel', 
            'TWP_Dashboard::page', TWP_PLUGIN_URL . '/assets/images/tweet-wheel-menu-icon.png'
        );

        
    }
    
    // ...
    
    /**
     * Add submenus. Here is where other classes add their own tabs.
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return N/A
     **/
    
    public function submenu() {
        
        $this->menus = apply_filters( 'twp_load_admin_menu', $this->menus );

        foreach( $this->menus as $menu ) :
            
            $menu = wp_parse_args( $menu, array(
                'parent_slug' => 'tweetwheel',
                'page_title' => 'Menu...',
                'menu_title' => 'Menu...',
                'capability' => 'administrator',
                'menu_slug' => 'menu_',
                'function' => '__return_false',
                'auth_only' => false
            ) );
            
            add_submenu_page( $menu['parent_slug'], __( $menu['page_title'], 'tweetwheel' ), __( $menu['menu_title'], 'tweetwheel' ), $menu['capability'], $menu['menu_slug'], $menu['function'] );
            
        endforeach;
        
    }
    
}

// Initiate
new TWP_Menus;