<?php

/**
 * Creates all metaboxes used within the plugin
 *
 * @since 1.0
 * @date 16/06/2015
 */

// ...

/**
 * Fire our meta box setup function on the post editor screen. 
 *
 * @since 1.0
 */

if( TWP()->twitter()->is_authed() ) :

	add_action( 'load-post.php', 'twp_post_meta_boxes_setup' );
	add_action( 'load-post-new.php', 'twp_post_meta_boxes_setup' );

    //add_filter( 'admin_post_thumbnail_html', 'twp_exclude_tweet_image', 20, 2 );

endif;

// ...

/**
 * Meta box setup function. 
 *
 * @since 1.0
 */

function twp_post_meta_boxes_setup() {

  /* Add meta boxes on the 'add_meta_boxes' hook. */
  add_action( 'add_meta_boxes', 'twp_exclude_tweet_templates_meta' );
  add_action( 'add_meta_boxes', 'twp_exclude_tweet_on_save' );
  
  /* Save post meta on the 'save_post' hook. */
  add_action( 'save_post', 'twp_save_tweet_templates_meta', 10, 2 );
  add_action( 'save_post', 'twp_save_tweet_settings_meta', 10, 2 );
  add_action( 'save_post', 'twp_save_tweet_image', 10, 3 );
  add_action( 'publish_post', 'twp_tweet_on_save_metabox_tweet', 999, 2 );
  
}

// ...

/**
 * Add all metaboxes
 *
 * @since 1.0
 */

function twp_exclude_tweet_templates_meta() {
	
	$post_types = twp_get_option( 'twp_settings', 'post_type' );
	
	if( empty( $post_types ) || ! is_array( $post_types ) )
		return;
	
	foreach( $post_types as $post_type ) :

		add_meta_box(
			'tw-tweet-settings',
			esc_html__( 'Tweet Settings', TWP_TEXTDOMAIN ),
			'twp_tweet_settings_meta_box',
			$post_type,
			'normal',
			'default'
		);

		add_meta_box(
			'tw-tweet-templates',
			esc_html__( 'Tweet Templates', TWP_TEXTDOMAIN ),
			'twp_tweet_templates_meta_box',
			$post_type,
			'normal',
			'default'
		);

	endforeach;
    
}

// ...

/*

Particular metaboxes down below

*/

// ...

/***************************************
 * Tweet Wheel Settings Metabox
 **************************************/

function twp_tweet_settings_meta_box( $object, $box ) {
    
    wp_nonce_field( basename( __FILE__ ), 'tweet_settings_nonce' ); 
    
    $tweet_order = get_post_meta( $object->ID, 'twp_templates_order', true);
    $exclude_image = get_post_meta( $object->ID, 'exclude_tweet_image', true);
    $tweet_order = $tweet_order == '' ? 'order' : $tweet_order;

    ?>

    <div class="tw-metabox tw-tweet-settings">
        
        <p>
            <span class="section-title"><?php _e( 'Post Exclusion', TWP_TEXTDOMAIN ); ?></span>
            <span class="section-note"><?php _e( 'If you don\'t want this post to be queued, you can exclude it permanently by checking the box below. If a post is currently in the queue, it will be dequeued.', TWP_TEXTDOMAIN ); ?></span>
            
            <?php
    
                // if new post and should be excluded by default
                if( 
                    twp_get_option( 'twp_settings', 'queue_new_post' ) == 1 &&
                    @get_post_meta( $_GET['post'], 'twp_post_exclude' ) == ''
                ) :
    
                ?>
                
                    <input type="checkbox" name="_twp_post_excluded" id="_twp_post_excluded" checked disabled>
                    <input type="hidden" name="twp_post_excluded" id="twp_post_excluded" value="1" checked>
            
                    <label for="_twp_post_excluded"><?php sprintf( '%s <span style="color:red;font-size:11px;font-style:italic;">(%s)</span>', __( 'Exclude this post from the queue', TWP_TEXTDOMAIN ), __( 'excluded by default', TWP_TEXTDOMAIN ) ) ?></label>
            
                <?php   
    
                else :
    
                    $post_excluded = get_post_meta( $object->ID, 'twp_post_excluded', true);  
                    $post_excluded = $post_excluded == '' ? 0 : $post_excluded;
    
                    ?>
            
                    <input type="checkbox" name="twp_post_excluded" id="twp_post_excluded" value="1" <?php checked( $post_excluded, 1 ) ?>>
                    <label for="twp_post_excluded"><?php _e( 'Exclude this post from the queue', TWP_TEXTDOMAIN ); ?></label>
            
                    <?php

                endif;

            ?>

        </p>
        
        <hr/>
        
        <p>
            <span class="section-title"><?php _e( 'Featured Image', TWP_TEXTDOMAIN ); ?></span>
            <span class="section-note"><?php _e( 'If you don\'t want to attach a featured image to future tweets, tick this checkbox.', TWP_TEXTDOMAIN ); ?></span>
            <label><input class="exclude-tweet-image" type="checkbox" name="exclude_tweet_image" value="1" <?php checked( $exclude_image, 1 ); ?>> <?php _e( 'Exclude image from future tweets', TWP_TEXTDOMAIN ); ?></label>
        </p>
        
        <hr/>
        
        <p>
            <span class="section-title"><?php _e( 'Templates Order', TWP_TEXTDOMAIN ); ?></span>
            <span class="section-note"><?php _e( 'Ignore if you are using a default tweet template or a single custom one. Otherwise, please choose whether you would want your templates to be used in the order or randomly picked.', TWP_TEXTDOMAIN ); ?></span>
            <input type="radio" name="twp_templates_order" id="twp_templates_order" value="order" <?php checked( $tweet_order, 'order' ) ?>>
            <label for="twp_templates_order"><?php _e( 'Follow the order', TWP_TEXTDOMAIN ); ?></label><br/>
            <input type="radio" name="twp_templates_order" id="twp_templates_order_random" value="random" <?php checked( $tweet_order, 'random' ) ?>>
            <label for="twp_templates_order_random"><?php _e( 'Randomise selection', TWP_TEXTDOMAIN ); ?></label>
        </p>
        
    </div>
    
<?php }

// ...

/* Save the meta box's post metadata. */
function twp_save_tweet_settings_meta( $post_id, $post ) {

    /* Verify the nonce before proceeding. */
    if ( !isset( $_POST['tweet_settings_nonce'] ) || !wp_verify_nonce( $_POST['tweet_settings_nonce'], basename( __FILE__ ) ) )
        return $post_id;

    /* Get the post type object. */
    $post_type = get_post_type_object( $post->post_type );

    /* Check if the current user has permission to edit the post. */
    if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
        return $post_id;

    /* Get the posted data and sanitize it for use as an HTML class. */
    $post_excluded = isset( $_POST['twp_post_excluded'] ) ? $_POST['twp_post_excluded'] : '';
    $tweet_order = $_POST['twp_templates_order'];

    update_post_meta( $post_id, 'twp_post_excluded', $post_excluded );
    update_post_meta( $post_id, 'twp_templates_order', $tweet_order );
    
}


// ...

/***************************************
 * Tweet Templates Metabox
 **************************************/

function twp_tweet_template_default() {
    
    return apply_filters( 'twp_tweet_template_default', '<div class="tweet-template-item"><span class="tw-remove-tweet-template dashicons dashicons-no control" title="' . __( 'Delete this template', TWP_TEXTDOMAIN ) . '"></span><div><textarea class="widefat tweet-template-textarea" name="twp_post_templates[%d]" placeholder="' . __( 'Enter your custom tweet text', TWP_TEXTDOMAIN ) . '" required>%s</textarea><span class="twp-counter">%d</span></div></div>' ); 
    
}

function twp_tweet_templates_meta_box( $object, $box ) { 
    
    wp_nonce_field( basename( __FILE__ ), 'tweet_templates_nonce' ); 
    
    $tweet_templates = get_post_meta( $object->ID, 'twp_post_templates', true );
    
    $template = twp_tweet_template_default();
    
    ?>

    <div class="tw-metabox tw-tweet-templates">
        <a href="#add-tweet-template" id="add-tweet-template" class="button">
            <?php _e( 'Add a Tweet Template', TWP_TEXTDOMAIN ); ?>
        </a>
        <a href="#how-to" data-content="templates-learn-more" class="tw-learn-more tw-template-learn-more">Learn more<span class="dashicons dashicons-arrow-down"></span></a>
        
        <div id="templates-learn-more" class="tw-learn-more-content">
            <p><?php _e( 'Create as many tweet templates as you like by clicking "Add a Tweet Template" button above. Below you can find tags that you can use within tweet templates.', TWP_TEXTDOMAIN ); ?></p>
            <ul>
                <li>
                    <strong>{{URL}}</strong> - <?php _e( '(mandatory) displays link to this post', TWP_TEXTDOMAIN ); ?>
                </li>
                <li>
                    <strong>{{TITLE}}</strong> - <?php _e( '(optional) display this post title', TWP_TEXTDOMAIN ); ?>
                </li>
            </ul>
        </div>
        <?php

        /**
         * Backward compatibility for users of 0.3
         * We changed handling of metaboxes, but we don't want them to lose
        * their custom tweet texts on the plugin update.
        */
        
        if( '' == $tweet_templates ) :
            
            $tweet_text = get_post_meta( $object->ID, 'tweet_text', true );
        
            if( $tweet_text != '' )
                echo sprintf( $template, '', $tweet_text, twp_character_counter( $tweet_text ) );
        
        endif;
        
        // ... now load any others
        
        if( '' != $tweet_templates ) :
    
            $j = 0;
        
            foreach( $tweet_templates as $t ) :

                echo sprintf( $template, $j, $t, twp_character_counter( $t ) );
    
                $j++;
            
            endforeach;
        
        endif;
            
        ?>
        
    </div>
    
<?php }

// ...

/* Save the meta box's post metadata. */
function twp_save_tweet_templates_meta( $post_id, $post ) {

    /* Verify the nonce before proceeding. */
    if ( !isset( $_POST['tweet_templates_nonce'] ) || !wp_verify_nonce( $_POST['tweet_templates_nonce'], basename( __FILE__ ) ) )
        return $post_id;

    /* Get the post type object. */
    $post_type = get_post_type_object( $post->post_type );

    /* Check if the current user has permission to edit the post. */
    if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
        return $post_id;

    /* Get the posted data and sanitize it for use as an HTML class. */
    $new_meta_value = isset( $_POST['twp_post_templates'] ) ? $_POST['twp_post_templates'] : '';
    
    $sorted = array();
    
    // Reset keys
    if( ! empty( $new_meta_value ) ) : 

        foreach( $new_meta_value as $m ):

            $sorted[] = $m;

        endforeach;
    
    endif;
    
    /* Get the meta key. */
    $meta_key = 'twp_post_templates';

    /* Get the meta value of the custom field key. */
    $meta_value = get_post_meta( $post_id, $meta_key, true );

    /* If a new meta value was added and there was no previous value, add it. */
    if ( $new_meta_value && '' == $meta_value )
        add_post_meta( $post_id, $meta_key, $new_meta_value, true );

    /* If the new meta value does not match the old value, update it. */
    elseif ( $new_meta_value && $new_meta_value != $meta_value )
        update_post_meta( $post_id, $meta_key, $new_meta_value );

    /* If there is no new meta value but an old value exists, delete it. */
    elseif ( '' == $new_meta_value && $meta_value )
        delete_post_meta( $post_id, $meta_key, $meta_value );

}

// ...

/**
 * Tweet Card - Checkbox in the Featured Image metabox
 * 
 * @since 1.0
 * @date 16/06/2015
 **/

function twp_exclude_tweet_image( $content, $post_id ) {
    
    $populate = get_post_meta( $post_id, 'exclude_tweet_image', true );
    
    $content .= '<label><input class="exclude-tweet-image" type="checkbox" name="exclude_tweet_image" value="1" ' . checked( $populate, 1, false ) . '> ' . __( 'Exclude image from future tweets', TWP_TEXTDOMAIN ) . '</label>';    
    return $content;
    
}

// ...

/**
 * Tweet Card - Save metabox
 * 
 * @since 1.0
 * @date 16/06/2015
 **/

function twp_save_tweet_image( $post_id, $post, $update ) {
  
    $value = 0;
    if ( isset( $_REQUEST['exclude_tweet_image'] ) ) {
        $value = 1;
    }
 
    // Set meta value to either 1 or 0
    update_post_meta( $post_id, 'exclude_tweet_image', $value );
  
}

// ...

/**
 * Tweet On-Save metabox
 *
 * @since 1.3
 * @date 10/07/2015
 **/

function twp_exclude_tweet_on_save() {

    add_meta_box(
        'tw-tweet-on-save',
        esc_html__( 'Tweet On-Save', TWP_TEXTDOMAIN ),
        'twp_tweet_on_save_meta_box',
        $post_type,
        'side',
        'high'
    );
    
}

// ...

/**
 * Tweet On-Save - callback
 * 
 * @since 1.0
 * @date 16/06/2015
 **/

function twp_tweet_on_save_meta_box() {
    
    global $post;
    
    $templates = TWP()->tweet()->get_custom_templates( $post->ID );
    
    ?>
    <p style="margin-bottom:0px;">
        <input type="checkbox" value="1" id="tos-enable" name="tos_enable">
        <label for="tos-enable">
            <?php _e( 'Yes, please!', TWP_TEXTDOMAIN ); ?>
        </label>
        
        <a href="#how-to" data-content="tos-learn-more" class="tw-learn-more tw-tos-learn-more">Learn more<span class="dashicons dashicons-arrow-down"></span></a>
        
    </p>
        
    <div id="tos-learn-more" class="tw-learn-more-content">
        <p><?php _e( 'You can tweet about a new post or an update to an article right away! Enable "Tweet On-Save", create a template and simply publish or update the post. Feel free to use our template tags listed below.', TWP_TEXTDOMAIN ); ?></p>
        <ul>
            <li>
                <strong>{{URL}}</strong> - <?php _e( '(mandatory) displays link to this post', TWP_TEXTDOMAIN ); ?>
            </li>
            <li>
                <strong>{{TITLE}}</strong> - <?php _e( '(optional) display this post title', TWP_TEXTDOMAIN ); ?>
            </li>
        </ul>
    </div>
    
    <div id="tos-enabled">
      
        <?php if( $templates ) : ?>
        <select id="tos-template" name="tos-template">
            <option value>Use a template</option>
            <?php foreach( $templates as $t ) : ?>
                <option value="<?php echo esc_attr( $t ); ?>"><?php echo $t; ?></option>
            <?php endforeach; ?>
        </select>
        <?php endif; ?>
       
        <div class="tweet-template-item">
            <div style="padding:0px">
                <textarea class="widefat tweet-template-textarea" name="twp_tos" placeholder="Enter your custom tweet text" required="" style="overflow: hidden; word-wrap: break-word; resize: none; height: 31px;"><?php echo TWP()->tweet()->get_default_template(); ?></textarea>
                <span class="twp-counter"><?php echo twp_character_counter( TWP()->tweet()->get_default_template(), $post->ID ); ?></span>
            </div>
        </div>
        
    </div>
    
    
    
    <?php
    
}

// ...

/**
 * Tweet On-Save - tweet it!
 *
 * @since 1.3
 * @date 11/07/2015
 **/

function twp_tweet_on_save_metabox_tweet( $ID, $post ) {
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        
    if ( ! $post = get_post( $post ) ) return;

    if ( 'publish' != $post->post_status ) return;
  
    if ( isset( $_REQUEST['tos_enable'] ) && $_REQUEST['tos_enable'] == 1 ) {
        
        $template = $_REQUEST['twp_tos'];
        
        if( $template == '' )
            return false;

        add_filter( 'twp_tweet_text', 'twp_tweet_on_save_template', 10, 2 );
        
        TWP()->tweet()->tweet( $ID );
        
        return true;
        
    }
  
}

function twp_tweet_on_save_template( $raw_text, $post_id ) {
 
    $raw_text = $_REQUEST['twp_tos'];
    
    return $raw_text;
    
}