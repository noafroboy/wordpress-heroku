<?php

/**
 * Main class of TWP_Queue
 *
 * @class TWP_Queue
 */

class TWP_Queue {
    
    public static $_instance = null;
    
    // ...
    
	/**
	 * Main TWP_Queue Instance
	 *
	 * Ensures only one instance of TWP_Queue is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 * @return TWP_Queue object
	 */
    
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
    
    // ...
    
	/**
	 * TWP_Queue _construct
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

        // Add admin menu
        add_filter( 'twp_load_admin_menu', array( $this, 'menu' ) );
        
        // Add some post actions to the post list screen
        add_filter( 'admin_footer-edit.php', array( $this, 'bulk_queue_option' ) );
        add_action( 'load-edit.php', array( $this, 'bulk_queue' ) );
        add_action( 'admin_notices', array( $this, 'bulk_queue_admin_notice' ) );
        
        // Hooks to action on particular post status changes
		$post_types = twp_get_all_enabled_post_types();
		
		if( $post_types != '' ) :
			
			foreach( $post_types as $post_type ) :
				
				add_filter( $post_type . '_row_actions', array( $this, 'post_row_queue' ), 10, 2);
				add_action( 'publish_' . $post_type, array( $this, 'on_publish_or_update' ), 999, 1 );
				
     	   	endforeach;
		
		endif;
		
        add_action( 'transition_post_status', array( $this, 'on_unpublish_post' ), 999, 3 );
        
        // AJAX actions        
        add_action( 'wp_ajax_save_queue', 'twp_ajax_save_queue' );
        add_action( 'wp_ajax_empty_queue_alert', 'twp_ajax_hide_empty_queue_alert' );
        add_action( 'wp_ajax_change_queue_status', 'twp_ajax_change_queue_status' );
        add_action( 'wp_ajax_remove_from_queue', 'twp_ajax_remove_from_queue' );
        add_action( 'wp_ajax_add_to_queue', 'twp_ajax_add_to_queue' );
        add_action( 'wp_ajax_found_posts', 'twp_ajax_found_posts' );
    
        // Display notice about empty queue
        if( $this->has_queue_items() == false && ! get_transient( '_twp_empty_queue_alert_' . get_current_user_id() ) )
            add_action( 'admin_notices', array( $this, 'alert_empty_queue' ), 999 );
        
        if( isset( $_REQUEST['twp_fill_up'] ) && isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'twp_queue' ) :
            $this->fill_up();
            wp_safe_redirect( admin_url( '/admin.php?page=twp_queue' ) ); exit;
        endif;
        
        if( isset( $_REQUEST['twp_remove_all'] ) && isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'twp_queue' ):
            $this->remove_all();
            wp_safe_redirect( admin_url( '/admin.php?page=twp_queue' ) ); exit;
        endif;
        
        if( isset( $_REQUEST['twp_queue'] ) )
            $this->insert_post( $_REQUEST['twp_queue'] );
        
        if( isset( $_REQUEST['twp_dequeue'] ) )
            $this->remove_post( $_REQUEST['twp_dequeue'] );
        
    }
    
    // ...
    
	/**
	 * Adds "Queue" item to the Tweet Wheel menu tab
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
            'page_title' => __( 'Queue', TWP_TEXTDOMAIN ),
            'menu_title' => __( 'Queue', TWP_TEXTDOMAIN ),
            'menu_slug'  => 'twp_queue',
            'function'   => array( $this, 'page' )
        );
        
        return $menu;
        
    }
    
    // ...
    
	/**
	 * Loads the Queue screen
	 *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return n/a
	 */
    
    public function page() {
        
        // On submit
        if( ! empty( $_POST ) && check_admin_referer( 'fill_up_queue', 'fill_up_queue_nonce' ) ) :
        
            $errors = array();
        
            $pts = $_POST['fillup'];
        
            foreach( $pts as $pt => $value ) :
        
                if( ! isset( $value['included'] ) )
                    continue;

                // Standard args for the query
                $args = array (
                    'posts_per_page' => ctype_digit( $value['number'] ) ? $value['number'] : '-1',
                    'post_type' => $pt
                );
        
                // Check if date range hsa been set
                if( ! empty( $value['from'] ) || ! empty( $value['to'] ) ) :
        
                    $args['date_query'] = array(
                        'inclusive' => 'true'
                    );
        
                    if( ! empty( $value['from'] ) )
                        $args['date_query']['after'] = $value['from'];
        
                    if( ! empty( $value['to'] ) )
                        $args['date_query']['before'] = $value['to'];
        
                endif;
        
                $data = get_posts( $args );
        
                if( count( $data ) > 0 )
                    $this->fill_up( $data );
        
            endforeach;
        
        endif;
        
        ?>
        
		<div class="wrap tweet-wheel tw-queue-page">
			<h2><img class="alignleft" style="margin-right:10px;" src="<?php echo TWP_PLUGIN_URL . '/assets/images/tweet-wheel-page-icon.png'; ?>"> <?php _e( 'Queue', TWP_TEXTDOMAIN ); ?></h2>
        
            <div id="tw-queue" <?php echo $this->has_queue_items() == false || isset( $_GET['refill'] ) ? 'class="form"' : ''; ?>>
                
                <?php
        
                if( $this->has_queue_items() == true && ! isset( $_GET['refill'] ) ) :
                    
                    $this->tools();
                    
                    $this->display_queued_items();

                else : 
        
                    $this->fill_up_form();
                
                endif; ?>
                
            </div>
            
        </div>
        
        <?php
        
    }
    
    // ...
    
	/**
	 * Admin notice showed when queue is empty
	 *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return n/a
	 */
    
    public function alert_empty_queue() {
        ?>
        <div id="tw-empty-queue-alert" class="error tw-alert">
            <p><?php _e( 'Your Tweet Wheel Queue is empty! Go ahead and fill it up to start sharing! <a href="'.admin_url('/admin.php?page=twp_queue&twp_fill_up=true').'" class="button" style="margin-left:10px;">Fill-Up The Queue</a><a id="empty-queue-alert-hide" href="#" class="button" style="margin-left:10px;">Hide this</a>', TWP_TEXTDOMAIN ); ?></p>
        </div>
        <?php
    }
    
    // ...
    
	/**
	 * Toolbar for each item in the queue
	 *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return n/a
	 */
    
    public function item_tools( $post_id ) {
        
        $item_tools = array();
        
        $item_tools = apply_filters( 
            'twp_queue_item_tools', 
            array(
                array(
                    'button_label' => __( 'Tweet Now', TWP_TEXTDOMAIN ),
                    'button_class' => 'tweet-now',
                    'button_attrs' => array(
                        'data-post-id=' . $post_id
                    )
                ),
                array(
                    'button_label' => __( 'Remove', TWP_TEXTDOMAIN ),
                    'button_class' => 'tw-dequeue',
                    'button_attrs' => array(
                        'data-post-id=' . $post_id
                    ) 
                ),
                array(
                    'button_label' => __( 'Edit Post', TWP_TEXTDOMAIN ),
                    'button_href' => get_edit_post_link( $post_id )
                )
            ), 
            $item_tools 
        );
        
        if( ! is_array( $item_tools ) || empty( $item_tools ) )
            return;
        
        echo '<div class="queue-item-sidebar">';
        
        echo '<ul class="queue-item-tools">';
        
        foreach( $item_tools as $item ) : 
            
            $item = wp_parse_args( $item, array(
                'button_id' => '',
                'button_class' => '',
                'button_href' => '#',
                'button_label' => 'Button!',
                'button_attrs' => array()
            ) );
            
            extract( $item );
        
            ?>
        
            <li><a id="<?php echo $button_id; ?>" class="<?php echo $button_class; ?>" href="<?php echo $button_href; ?>" <?php echo implode( ' ', $button_attrs ); ?>><?php echo $button_label; ?></a></li>

            <?php
        
        endforeach;
        
        echo '</ul>';
        
        echo '<ul class="queue-icons">';
        
        if( TWP()->tweet()->has_custom_templates( $post_id ) )  
            echo '<li><span title="' . __( 'Custom template', TWP_TEXTDOMAIN ) . '" class="dashicons dashicons-admin-tools"></span></li>';
           
        if( TWP()->tweet()->has_multiple_templates( $post_id ) )  
            echo '<li><span title="' . __( 'Multiple templates', TWP_TEXTDOMAIN ) . '" class="dashicons dashicons-screenoptions"></span></li>';
        
        if( TWP()->tweet()->get_tweeting_order( $post_id ) == 'random' )
            echo '<li><span title="' . __( 'Random order', TWP_TEXTDOMAIN ) . '" class="dashicons dashicons-randomize"></span></li>';
        
        if( TWP()->tweet()->has_image( $post_id ) )  
            echo '<li><span title="' . __( 'Attached image', TWP_TEXTDOMAIN ) . '" class="dashicons dashicons-format-image"></span></li>';
        
        echo '</ul>';
        
        echo '</div>';
        
    }
    
    // ...
    
	/**
	 * Queue tools / buttons
	 *
	 * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return n/a
	 */
    
    public function tools() {

        ?>
        <ul class="queue-tools">
            <li><a href="#" id="change-queue-status" class="button"><?php echo $this->get_queue_status() == 'paused' ? __( 'Resume', TWP_TEXTDOMAIN ) : __( 'Pause', TWP_TEXTDOMAIN ) ?></a></li>
            <li><a href="<?php echo admin_url( '/admin.php?page=twp_queue&refill=true' ); ?>" class="button"><?php _e( 'Refill', TWP_TEXTDOMAIN ); ?></a></li>
            <li><a id="tw-empty-queue" href="#" class="button"><?php _e( 'Empty', TWP_TEXTDOMAIN ); ?></a></li>
            <li><a id="tw-simple-view" href="#" class="button"><?php _e( 'Simple View', TWP_TEXTDOMAIN ); ?></a></li>
            <li id="tw-saving-progress" style="line-height: 28px;"><span></span></li>
        </ul>
        <span id="queue-status">Status: <?php echo $this->get_queue_status() == 'paused' ? __( 'Paused', TWP_TEXTDOMAIN ) : __( 'Running', TWP_TEXTDOMAIN ) ?></span>
        <script>
        jQuery.noConflict();
        jQuery(document).ready(function(){
           jQuery('#tw-empty-queue').click(function(){
               var r = confirm( "<?php _e( 'Are you sure? This will remove ALL your posts from Tweet Wheel\'s Queue!', TWP_TEXTDOMAIN ); ?>");
               if (r == true) {
                   window.location.href = '<?php echo admin_url( '/admin.php?page=twp_queue&twp_remove_all=true' ); ?>';
               }
           });
        });
        </script>
        
        <?php
        
    }
    
    // ...
    
    public function fill_up_form() {
        
        if( $this->has_queue_items() ) : ?>
        <h3 style="margin-bottom: 40px;"><?php _e( 'Refill your queue with awesomeness.', TWP_TEXTDOMAIN ); ?></h3>      
        <?php else : ?>    
        <h3 style="margin-bottom: 40px;"><?php _e( 'Your queue is currently empty. Don\'t be shy!', TWP_TEXTDOMAIN ); ?></h3>
        <?php endif; ?>

        <form style="width: 100%; float: left;" method="post" action="<?php echo admin_url( '/admin.php?page=twp_queue' ); ?>">

            <?php wp_nonce_field( 'fill_up_queue', 'fill_up_queue_nonce' ); ?>

            <?php

                $post_type = twp_get_all_enabled_post_types();

                if( ! empty( $post_type ) ) :

                    foreach( $post_type as $pt ) :

                        ?>

                        <div class="fill-up-pt" data-pt="<?php echo $pt; ?>">

                            <div class="row">
                                <div class="col third"><strong><?php echo ucfirst( $pt ); ?></strong></div>
                                <div class="col two-thirds">
                                    <input type="checkbox" name="fillup[<?php echo $pt; ?>][included]" value="1" checked>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col third"><?php _e( 'Max. posts to import', TWP_TEXTDOMAIN ); ?></div>
                                <div class="col two-thirds">
                                    <input type="text" class="regular-text max-posts" name="fillup[<?php echo $pt; ?>][number]">
                                    <p><?php _e( 'Leave blank for all posts.', TWP_TEXTDOMAIN ); ?></p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col third"><?php _e( 'Date range', TWP_TEXTDOMAIN ); ?></div>
                                <div class="col two-thirds">
                                    <input type="date" value="" name="fillup[<?php echo $pt; ?>][from]" class="date-from"> -  <input type="date" value="" name="fillup[<?php echo $pt; ?>][to]" class="date-to">
                                    <p><?php _e( 'Leave blank for all posts.', TWP_TEXTDOMAIN ); ?></p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col third">&nbsp;</div>
                                <div class="col two-thirds fill-counter <?php echo $pt; ?>-count">
                                    <?php 
                                        $number = count( get_posts( 'posts_per_page=-1&post_type=' . $pt ) );
                                        echo sprintf( _n( '%d item will be imported', '%d items will be imported', $number, TWP_TEXTDOMAIN ), $number ); 
                                    ?>
                                </div>
                            </div>

                        </div>

                        <?php

                    endforeach;
        
                    if( $this->has_queue_items() ) : ?>
                    
                        <p><?php _e( 'Please note posts that are already in the queue will be omitted.', TWP_TEXTDOMAIN ); ?></p>
                    
                    <?php
                    endif;

                    echo '<input type="submit" class="button button-primary tw-fill-up" value="' . __( 'Fill-Up The Queue', TWP_TEXTDOMAIN ) . '">';

                else :

                    _e( 'Bummer! You need to allow some post types into the queue! Head off to the <a href="' . admin_url( '/admin.php?page=twp_settings' ) . '">settings page</a> to fix it.', TWP_TEXTDOMAIN );

                endif;

            ?>

        </form>

        <?php       
        
    }
    
    // ...
    
	/**
	 * Fills up the queue with ALL blog posts
     * 
     * @TODO: give user a bit more control over it
	 *
	 * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return n/a
	 */
    
    public function fill_up( $data = null, $key = 'ID' ) {
        
        if( $data == null ) :
        
            $cpt = twp_get_all_enabled_post_types();
            
            if( empty( $cpt ) )
                return false;            
            
            $args = apply_filters( 'twp_queue_fill_up_args', array(
                'post_type' => $cpt,
                'post_status' => 'publish',
                'posts_per_page' => -1
            ) );
        
            $posts = get_posts( $args );
        
        else :
            
            $posts = $data;
            
        endif;
        
        if( empty( $posts ) )
            return false;
        
        foreach( $posts as $p ) :
            
            $this->insert_post( $p->{$key} );
            
        endforeach;
        
    }
    
    // ...
    
	/**
	 * Inserts a post to the queue. Performs checks for duplication and exclusion. 
     * The check be skipped giving "true" as a value for last two parameters.
	 *
	 * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return WP Insert | false
	 */
    
    public function insert_post( $post_id, $skip_queue = false, $skip_exclusion = false ) {
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        
        global $wpdb;
        
        // check status... dont add anything not published
        if( get_post_status( $post_id ) != 'publish' )
            return false;
        
        // Is item already queued?
        if( $this->is_item_queued( $post_id ) == true && $skip_queue == false )
            return false;
        
        // Is item excluded from the queue?
        if( $this->is_item_excluded( $post_id ) == true && $skip_exclusion == false )
            return false;
        
        $result = $wpdb->insert(
            $wpdb->prefix . 'twp_queue',
            array(
                'queue' => $this->get_last_queued()+1,
                'post_ID' => $post_id
            )
        );
        
        return $result;
        
    }
    
    // ...
    
	/**
	 * Removes post from the queue
	 *
	 * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return n/a
	 */
    
    public function remove_post( $post_id, $skip = false ) {
        
        global $wpdb;
        
        $result = $wpdb->query(
            "DELETE FROM " . $wpdb->prefix . "twp_queue WHERE post_ID = " . $post_id
        );
        
        return $result;
        
    }
    
    // ...
    
	/**
	 * Excludes a post from the queue (and removes if exists)
	 *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     * 
     * @param int
	 * @return boolean
	 */
    
    public function exclude_post( $post_id ) {
        
        $this->remove_post( $post_id );

        update_post_meta( $post_id, 'twp_post_excluded', 1 );
        
        return true;
        
    }
    
    // ...
    
	/**
	 * Unchecks the Post Exclude option (doesn't insert a post)
	 *
	 * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param int
	 * @return boolean
	 */
    
    public function include_post( $post_id ) {
        
        $excluded = array();

        update_post_meta( $post_id, 'post_exclude', $excluded );
        
        return true;
        
    }
    
    // ...
    
	/**
	 * Adds an action to posts on the edit.php screen
	 *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return n/a
	 */
    
    public function post_row_queue( $actions, $post ) {
        
        //check for your post type
        if ( twp_is_post_type_enabled( $post->post_type ) && $post->post_status == "publish" ) :

            if( $this->is_item_excluded( $post->ID ) ) 
                
                $actions['excluded'] = '<span style="color:#aaa">' . __( 'Excluded', TWP_TEXTDOMAIN ) . '</span>';
            
            else if( $this->is_item_queued( $post->ID ) ) :
                
                $actions['dequeue'] = '<a href="#" class="tw-dequeue-post" style="color:#a00" data-post-id="'.$post->ID.'">' . __( 'Dequeue', TWP_TEXTDOMAIN ) . '</a>';
                
            else :
                
                $actions['queue'] = '<a class="tw-queue-post" href="#" data-post-id="'.$post->ID.'">' . __( 'Queue', TWP_TEXTDOMAIN ) . '</a>';
                
            endif;
            
        endif;
        
        return $actions;
        
    }
    
    // ...
    
	/**
	 * Injects options to Bulk Actions dropdown on the edit.php screen
	 *
	 * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return n/a
	 */
    
    public function bulk_queue_option() {
        
        global $post_type;
        
        $screen = $_REQUEST['post_status'];
        
        if( $screen != '' && $screen != 'publish' )
            return;

		if( twp_is_post_type_enabled( $post_type ) ) {

		?>
		
		<script type="text/javascript">
			jQuery(document).ready(function() {
			jQuery("select[name^='action']").append('<option disabled></option><option disabled>Tweet Wheel</option>');
			jQuery('<option>').val('queue').text('- <?php _e('Queue',TWP_TEXTDOMAIN)?>').appendTo("select[name='action']");
			jQuery('<option>').val('queue').text('- <?php _e('Queue',TWP_TEXTDOMAIN)?>').appendTo("select[name='action2']");
			jQuery('<option>').val('dequeue').text('- <?php _e('Dequeue',TWP_TEXTDOMAIN)?>').appendTo("select[name='action']");
			jQuery('<option>').val('dequeue').text('- <?php _e('Dequeue',TWP_TEXTDOMAIN)?>').appendTo("select[name='action2']");
			jQuery('<option>').val('exclude').text('- <?php _e('Exclude',TWP_TEXTDOMAIN)?>').appendTo("select[name='action']");
			jQuery('<option>').val('exclude').text('- <?php _e('Exclude',TWP_TEXTDOMAIN)?>').appendTo("select[name='action2']");
			});
		</script>
		
		<?php
		
		}
        
    }
    
    // ...
    
	/**
	 * Handles bulk actions
	 *
	 * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return n/a
	 */
    
    public function bulk_queue() {

        // 1. get the action
        $wp_list_table = _get_list_table('WP_Posts_List_Table');
        $action = $wp_list_table->current_action();
        
		if(isset($_REQUEST['post'])) {
			$post_ids = array_map('intval', $_REQUEST['post']);
		}
		
        if(empty($post_ids)) return;
        
        // 2. security check
        check_admin_referer('bulk-posts');

        switch($action) {

        // 3. Perform the action
        case 'queue':

            $queued = 0;

            foreach( $post_ids as $post_id ) {
                
                if( get_post_status( $post_id ) != 'publish' )
                    continue;
                
                if ( $this->include_post( $post_id ) && $this->insert_post($post_id) )
                    $queued++;
                
            }

            // build the redirect url
            $sendback = add_query_arg( array('queued' => $queued , 'post_type' => get_post_type( $post_id ) ), $sendback );

            break;

        case 'dequeue':
            
            $dequeued = 0;

            foreach( $post_ids as $post_id ) {
                if ( $this->remove_post($post_id) )
                    $dequeued++;
            }

            // build the redirect url
            $sendback = add_query_arg( array('dequeued' => $dequeued, 'post_type' => get_post_type( $post_id ) ), $sendback );
            
            break;
            
        case 'exclude':
            
            $excluded = 0;

            foreach( $post_ids as $post_id ) {
                if ( $this->exclude_post($post_id) )
                    $excluded++;
            }

            // build the redirect url
            $sendback = add_query_arg( array('excluded' => $excluded, 'post_type' => get_post_type( $post_id ) ), $sendback );
            
            break;

        default: return;

        }

        // ...

        // 4. Redirect client
        wp_redirect($sendback);

        exit();
        
    }
    
    // ...
    
	/**
	 * Display relevant notice after a bulk action has been performed
	 *
	 * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return n/a
	 */
    
    public function bulk_queue_admin_notice() {
 
		global $post_type, $pagenow;

		// Posts queued

		if($pagenow == 'edit.php' && twp_is_post_type_enabled( $post_type ) &&
			isset($_REQUEST['queued']) && (int) $_REQUEST['queued']) {
			$message = sprintf( _n( 'Post queued.', '%s posts queued.', $_REQUEST['queued'], TWP_TEXTDOMAIN ), number_format_i18n( $_REQUEST['queued'] ) );
			echo '<div class="updated"><p>' . $message . '</p></div>';
		}

		// ...

		// Posts dequeued

		if($pagenow == 'edit.php' && twp_is_post_type_enabled( $post_type ) &&
			isset($_REQUEST['dequeued']) && (int) $_REQUEST['dequeued']) {
			$message = sprintf( _n( 'Post dequeued.', '%s posts dequeued.', $_REQUEST['dequeued'], TWP_TEXTDOMAIN ), number_format_i18n( $_REQUEST['dequeued'] ) );
			echo '<div class="updated"><p>' . $message . '</p></div>';
		}

		// ...

		// Posts excluded

		if($pagenow == 'edit.php' && twp_is_post_type_enabled( $post_type ) &&
			isset($_REQUEST['excluded']) && (int) $_REQUEST['excluded']) {
			$message = sprintf( _n( 'Post excluded.', '%s posts excluded.', $_REQUEST['excluded'], TWP_TEXTDOMAIN ), number_format_i18n( $_REQUEST['excluded'] ) );
			echo '<div class="updated"><p>' .$message .'</p></div>';
		}
      
    }
    
    // ...
    
	/**
	 * Displays the queue of items
	 *
	 * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return n/a
	 */
    
    public function display_queued_items() {
        
        $in_queue = $this->get_queued_items();
        
        ?>
        
        <div id="the-queue">

            <ul>
            <?php foreach( $in_queue as $q ) : ?>
                <li class="the-queue-item" id="<?php echo $q->post_ID; ?>">
                    <div class="post-header">
                        <span class="title"><?php echo get_the_title( $q->post_ID ); ?></span>
                        <span class="drag-handler"><img src="<?php echo TWP_PLUGIN_URL; ?>/assets/images/reorder.png"/></span>
                        <?php $this->item_tools( $q->post_ID ); ?>
                    </div>
                    <div class="post-content">
                        <ul>
                            <?php if ( TWP()->tweet()->has_custom_templates( $q->post_ID ) ) : ?>
                            
                                <?php 
                                    
                                    $templates = TWP()->tweet()->get_custom_templates( $q->post_ID );
                                    
                                    foreach( $templates as $t ) : 
                            
                                ?>
                            
                                    <li>
                                        <?php echo $t; ?>
                                        <ul class="item-icons">
                                        <?php 
                                            if( 
                                                TWP()->tweet()->get_tweeting_order( $q->post_ID ) == 'order' && 
                                                TWP()->tweet()->get_next_template( $q->post_ID ) == $t
                                            ) :
                                        ?>
                                            <li>
                                                <span title="<?php _e( 'Next tweet\'s template', TWP_TEXTDOMAIN ); ?>" class="dashicons dashicons-clock"></span>
                                            </li>
                                        <?php endif; ?>
                                            
                                        <?php if( twp_compare_tweet_templates( TWP()->tweet()->get_last_tweeted_template( $q->post_ID ), $t ) ) : ?>
                                            <li>
                                                <span title="<?php _e( 'Recently tweeted template', TWP_TEXTDOMAIN ); ?>" class="dashicons dashicons-share"></span>
                                            </li>
                                        <?php endif; ?>
                                        </ul>
                                    </li>
                            
                                <?php endforeach; ?>
                            
                            <?php else : ?>
                            
                                <li><?php echo TWP()->tweet()->get_default_template(); ?></li>
                            
                            <?php endif; ?>
                        </ul>
                        <?php if( TWP()->tweet()->has_multiple_templates( $q->post_ID ) ) : ?>
                        
                            <span class="show-all-templates dashicons dashicons-arrow-down"></span>
                        
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
            </ul>
        </div>
        
        <?php
        
    }
    
    // ...
    
	/**
	 * Empties the queue
	 *
	 * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return n/a
	 */
    
    public function remove_all() {
        
        global $wpdb;
        
        $wpdb->query( 'DELETE FROM ' . $wpdb->prefix . 'twp_queue' );
        
    }
    
    // ...
    
	/**
	 * Pauses the queue
	 *
	 * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return n/a
	 */
    
    public function pause() {
        
        update_option( 'twp_queue_status', 'paused' );
        
    }
    
    // ...
    
	/**
	 * Resumes the queue
	 *
	 * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return n/a
	 */
    
    public function resume() {
        
        update_option( 'twp_queue_status', 'running' );
        
    }
    
    // ...
    
    /**
     * Action on post publishing (from any status)
     * Deals with default post exclusion from settings
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param int
	 * @return n/a
     */
    
    public function on_publish_or_update( $post_id ) {
        
        // Load meta once for performance...
        $meta = get_post_meta( $post_id, 'twp_post_excluded' );

        // If new and should be excluded
        if(
            twp_get_option( 'twp_settings', 'queue_new_post' ) == 1 &&
            $meta == ''  
        )
            return;
			
			
        
        // check if post is only just published...
        // I know the new_post hook, but this works just fine
        // if there is no post_meta simply means its fresh post
        // or is switching from excluded to included in the queue
        if( 
            is_array( $meta ) && empty( $meta ) && 
            ! isset( $_POST['twp_post_excluded'] ) ||
            ! empty( $meta ) && 
            ! isset( $_POST['twp_post_excluded'] )
        ) :
            $this->insert_post( $post_id, false, true );
            return;
        endif;
            
        
        // Switching from included to excluded - dequeue it
        if( 
            ! empty( $meta ) && 
            isset( $_POST['twp_post_excluded'] ) &&
            $_POST['twp_post_excluded'] == 1
        ) 
            $this->remove_post( $post_id, true );
            
        return;
        
    }
    
    // ...
    
    /**
     * Action on unpublishing post
     * Removes post from the queue
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param string | string | object
	 * @return n/a
     */
    
    public function on_unpublish_post( $new_status, $old_status, $post ) {
        
        if ( $old_status == 'publish'  &&  $new_status != 'publish' ) {
            $this->remove_post( $post->ID );
        }
    
        return;
        
    }

    // ...
    
    /**
     * Checks if queue has items in it
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return boolean
     **/
        
    public function has_queue_items() {

        $items = $this->get_queued_items();

        if( ! is_array( $items ) )
            return false;

        if( empty( $items ) )
            return false;
        
        return true;
        
    }
    
    // ...
    
    /**
     * Checks if given post is queued
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param int
	 * @return boolean
     */
    
    public function is_item_queued( $post_id ) {
        
        global $wpdb;
        
        $results = $wpdb->get_results(
            "SELECT * FROM " . $wpdb->prefix . "twp_queue WHERE post_ID = " . $post_id
        );
        
        if( empty( $results ) )
            return false;
        
        return true;
        
    }
    
    // ...
    
    /**
     * Checks if given post is excluded from being added to the queue
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param int
	 * @return boolean
     */
    
    public function is_item_excluded( $post_id ) {
        
        // If is excluded and is not forced
        $excluded = get_post_meta( $post_id, 'twp_post_excluded', true );
        
        if( $excluded == 1 )
            return true;
        
        return false;
        
    }
    
    // ...
    
    /**
     * Retrieves all queued items
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     * @update 1.3.7 (25.08.2015)
     *
     * @param n/a
	 * @return array | boolean
     */
    
    public function get_queued_items() {
        
        global $wpdb;
        
        $results = $wpdb->get_results(
            "SELECT * FROM " . $wpdb->prefix . "twp_queue ORDER BY queue ASC"
        );
        
        return $results;
        
    }
    
    // ...
    
    /**
     * Retrieve queue status
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return string | null
     */
    
    public function get_queue_status() {
        
        return get_option( 'twp_queue_status' );
        
    }
    
    // ...
    
    /**
     * Retrieve an item from bottom of the queue
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return array
     */
    
    public function get_last_queued() {
        
        global $wpdb;
        
        if( $this->has_queue_items() == false )
            return 0;
        
        $query = $wpdb->get_row(
            "SELECT * FROM " . $wpdb->prefix . "twp_queue ORDER BY queue DESC LIMIT 1"
        );
        
        return $query->queue;
        
    }
    
    // ...
    
    /**
     * Retrieve an item from top of the queue
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return array
     */
    
    public function get_first_queued_item() {
        
        global $wpdb;
        
        if( ! $this->has_queue_items() )
            return;
        
        return $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "twp_queue ORDER BY queue ASC LIMIT 1" );
        
    }
    
}

/**
 * Returns the main instance of TWP_Queue
 *
 * @since  0.1
 * @return TWP_Queue
 */
function TWP_Queue() {
	return TWP_Queue::instance();
}