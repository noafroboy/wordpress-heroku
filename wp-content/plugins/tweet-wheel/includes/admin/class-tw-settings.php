<?php

/**
 * WP-Simple-Settings-Framework
 *
 * Copyright (c) 2012 Matt Gates.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the names of the copyright holders nor the names of the
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @subpackage  WP-Simple-Settings-Framework
 * @copyright   2012 Matt Gates.
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://mgates.me
 * @version     1.1
 * @author      Matt Gates <info@mgates.me>
 * @package     WordPress
 */


if ( ! class_exists( 'SF_Settings_API' ) ) {

	class SF_Settings_API
	{

		private $data = array();

		/**
		 * Init
		 *
		 * @param string  $id
		 * @param string  $title
		 * @param string  $menu  (optional)
		 * @param string  $file
		 */
		public function __construct( $id, $title, $file )
		{
			$this->assets_url = trailingslashit( plugins_url( 'tweet-wheel/assets/' ) );
			$this->id = $id;
			$this->title = $title;

			$this->file = $file;

			$this->includes();
			$this->actions();
		}


		// ==================================================================
		//
		// Getter and setter.
		//
		// ------------------------------------------------------------------

		/**
		 * Setter
		 *
		 * @param unknown $name
		 * @param unknown $value
		 */
		public function __set( $name, $value )
		{
			if ( isset ( $this->data[$name] ) && is_array( $this->data[$name] ) ) {
				$this->data[$name] = array_merge( $this->data[$name], $value );
			} else {
				$this->data[$name] = $value;
			}
		}


		/**
		 * Getter
		 *
		 * @param unknown $name
		 * @return unknown
		 */
		public function __get( $name )
		{
			if ( array_key_exists( $name, $this->data ) ) {
				return $this->data[$name];
			}
			return null;
		}


		/**
		 * Isset
		 *
		 * @param unknown $name
		 * @return unknown
		 */
		public function __isset( $name )
		{
			return isset( $this->data[$name] );
		}


		/**
		 * Unset
		 *
		 * @param unknown $name
		 */
		public function __unset( $name )
		{
			unset( $this->data[$name] );
		}

		// ==================================================================
		//
		// Begin initialization.
		//
		// ------------------------------------------------------------------

		/**
		 * Core files
		 */
		private function includes()
		{
			require_once TW_PLUGIN_DIR . '/includes/libraries/sf-class-sanitize.php';
			require_once TW_PLUGIN_DIR . '/includes/libraries/sf-class-format-options.php';
			
			new SF_Sanitize;
		}


		/**
		 * Hooks
		 */
		private function actions()
		{
			add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ) );
			add_action( 'admin_init', array( &$this, 'register_options' ) );
		}


		/**
		 * Admin scripts and styles
		 */
		public function admin_enqueue_scripts()
		{
			wp_register_script( 'bootstrap-tooltip' , $this->assets_url . 'js/bootstrap-tooltip.js' ,  array( 'jquery' ), '1.0' );
			wp_register_script( 'select2' , $this->assets_url . 'js/select2/select2.min.js' ,  array( 'jquery' ), '1.0' );
			wp_register_script( 'sf-scripts' , $this->assets_url . 'js/sf-jquery.js' ,  array( 'jquery' ), '1.0' );
			wp_register_style( 'select2' , $this->assets_url . 'js/select2/select2.css' );
			wp_register_style( 'sf-styles' , $this->assets_url . 'css/sf-styles.css' );
			
			$this->admin_print_scripts();
		}


		/**
		 * Admin scripts and styles
		 */
		public function admin_print_scripts()
		{
			global $wp_version;

			//Check wp version and load appropriate scripts for colorpicker.
			if ( 3.5 <= $wp_version ) {
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_script( 'wp-color-picker' );
			} else {
				wp_enqueue_style( 'farbtastic' );
				wp_enqueue_script( 'farbtastic' );
			}

			wp_enqueue_script( 'bootstrap-tooltip' );
			wp_enqueue_script( 'select2' );
			wp_enqueue_script( 'sf-scripts' );

			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'select2' );
			wp_enqueue_style( 'sf-styles' );
		}


		/**
		 * Register setting
		 */
		public function register_options()
		{
			register_setting( $this->id . '_options_nonce', $this->id . '_options', array( &$this, 'validate_options' ) );
		}


		/**
		 * Parse options into tabbed organization
		 *
		 * @return array
		 */
		private function parse_options()
		{
			$options = $this->options;

			foreach ( $options as $option ) {

				if ( $option['type'] == 'heading' ) {
					$tab_name = sanitize_title( $option['name'] );
					$this->tab_headers = array( $tab_name => $option['name'] );

					continue;
				}

				$option['tab'] = $tab_name;
				$tabs[$tab_name][] = $option;

			}

			$this->tabs = $tabs;

			return $tabs;
		}


		/**
		 * Load the options array from a file
		 *
		 * @param string  $option_file
		 */
		public function load_options( $option_file )
		{
			if ( !empty( $this->options ) ) return;

			if ( file_exists( $option_file ) ) {
				require $option_file;
				$this->options = apply_filters( $this->id . '_options', $options );
				$this->parse_options();

				$this->current_options = $this->get_current_options();

				/* If the option has no saved data, load the defaults. */
				/* @TODO: Can prob add this to the activation hook. */
				$this->set_defaults( $this->current_options );
			} else {
				wp_die( __( 'Could not load settings at: ', 'geczy' ) . '<br/><code>' . $option_file . '</code>', __( 'Error - WP Settings Framework', 'geczy' ) );
			}
		}


		/**
		 *
		 *
		 * @return unknown
		 */
		public function get_current_options()
		{
			if ( !empty( $this->current_options ) )
				return $this->current_options;

			$options = get_option( $this->id . '_options' );

			if ( $options ) {
				$options = array_map( 'maybe_unserialize', $options );
			}

			return $options;
		}


		/**
		 * Sanitize and validate post fields
		 *
		 * @param unknown $input
		 * @return unknown
		 */
		public function validate_options( $input )
		{            	
			if ( !isset( $_POST['update'] ) )
				return $this->get_defaults();

			$clean = $this->current_options;
			$tabname = $_POST['currentTab'];
            
            // custom
            $this->tabs = apply_filters( $this->id . '_tab_options-' . $tabname, $this->tabs, $_POST[$this->id . '_options' ] );
			
			foreach ( $this->tabs[$tabname] as $option ) :

				if ( ! isset( $option['id'] ) )
					continue;

				if ( ! isset( $option['type'] ) )
					continue;

				if ( $option['type'] == 'select' ) {
					$option['options'] = apply_filters( $this->id . '_select_options', $option['options'], $option );
				}
            
				$id = sanitize_text_field( strtolower( $option['id'] ) );

				// Set checkbox to false if it wasn't sent in the $_POST
				if ( 'checkbox' == $option['type'] && ! isset( $input[$id] ) )
					$input[$id] = 0;
            
				// For a value to be submitted to database it must pass through a sanitization filter
				if ( has_filter( 'geczy_sanitize_' . $option['type'] ) ) {
					$clean[$id] = apply_filters( 'geczy_sanitize_' . $option['type'], $input[$id], $option );
				}

			endforeach;
			
			do_action( $this->id . '_options_updated', $clean, $tabname );
            
			add_settings_error( $this->id, 'save_options', __( 'Settings saved.', 'geczy' ), 'updated' );

			return apply_filters( $this->id . '_options_on_update', $clean, $tabname );
		}


		/**
		 * Create default options
		 *
		 * @param unknown $current_options (optional)
		 */
		private function set_defaults( $current_options = array() )
		{
			$options = $this->get_defaults( $current_options );
			if ( $options ) {
				update_option( $this->id . '_options', $options );
			}
		}


		/**
		 * Retrieve default options
		 *
		 * @param unknown $currents (optional)
		 * @return array
		 */
		private function get_defaults( $currents = array() )
		{
			$output = array();
			$config = $this->options;
			$flag = false;

			if ( $currents ) {
				foreach ( $config as $value ) {
					if ( ! isset( $value['id'] ) || ! isset( $value['std'] ) || ! isset( $value['type'] ) )
						continue;

					if ( ! isset( $currents[$value['id']] ) ) {
						$flag = true;
					}
				}
			}

			foreach ( $config as $option ) {
				if ( ! isset( $option['id'] ) || ! isset( $option['std'] ) || ! isset( $option['type'] ) )
					continue;

				if ( $currents && isset( $currents[$option['id']] ) ) {
					$output[$option['id']] = $currents[$option['id']];
				} else if ( has_filter( 'geczy_sanitize_' . $option['type'] ) ) {
						$output[$option['id']] = apply_filters( 'geczy_sanitize_' . $option['type'], $option['std'], $option );
					}
			}

			if ( $currents ) {
				$output = array_merge( $currents, $output );
			}

			return ! $flag && $currents ? array() : $output;
		}


		/**
		 * HTML header
		 */
		private function template_header()
		{
?>
			<div class="wrap">
				<?php screen_icon(); ?><h2><?php echo $this->title; ?></h2>

				<h2 class="nav-tab-wrapper">
					<?php echo $this->display_tabs(); ?>
				</h2><?php

			if ( !empty ( $_REQUEST['settings-updated'] ) )
				settings_errors();

		}


		/**
		 * HTML body
		 *
		 * @return unknown
		 */
		private function template_body()
		{

			if ( empty( $this->options ) ) return false;

			$options = $this->options;
			$tabs = $this->get_tabs();
			$tabname = !empty ( $_GET['tab'] ) ? $_GET['tab'] : $tabs[0]['slug'];
            
			$options = apply_filters( $this->id . '_options_tab-' . $tabname, $this->tabs[$tabname] );  ?>
				
            <?php do_action( $this->id . '_before_form_tab-' . $tabname ); ?>   
			<form id="tw-<?php echo $tabname ?>" method="post" action="options.php">
				<?php settings_fields( $this->id . '_options_nonce' ); ?>
				<table class="form-table">

				<?php
			
			foreach ( $options as $value ) :
				
				/*
				 * This settings framework by Geczy has a bug, which I have found out about after deeply integrating it into plugin.
				 * When WordPress is in the debug mode, this throws plenty of errors regarding misuse of a static method.
				 * There is a ticket raised with an author on the GitHub repo, but no response ever since.
				 * Since this is not a major issue, I will stick to this framework for a while, but I will be looking
				 * forward to replace it when I find a better substitute.
				 */
				
				@SF_Format_Options::settings_options_format( $value );
				
			endforeach;

			do_action( $this->id . '_options_tab-' . $tabname );
?>

				</table>

				<p class="submit">
					<input type="hidden" name="currentTab" value="<?php echo $tabname; ?>">
					<input type="submit" name="update" class="button-primary" value="<?php echo sprintf( __( 'Save %s changes', 'geczy' ), $this->tab_headers[$tabname] ); ?>" />
				</p>
			</form> <?php
            
            do_action( $this->id . '_after_form_tab-' . $tabname );

		}


		/**
		 * HTML footer
		 */
		private function template_footer()
		{
			echo '</div>';
		}


		/**
		 * Create the settings page
		 */
		public function init_settings_page()
		{

			$this->template_header();
			$this->template_body();
			$this->template_footer();

		}


		/**
		 * Retrieve tabs
		 *
		 * @return array
		 */
		private function get_tabs()
		{
			$tabs = array();
			foreach ( $this->options as $option ) {

				if ( $option['type'] != 'heading' )
					continue;

				$option['slug'] = sanitize_title( $option['name'] );
				unset( $option['type'] );

				$tabs[] = $option;
			}
			return $tabs;
		}


		/**
		 * Heading for navigation
		 *
		 * @return string
		 */
		private function display_tabs()
		{
			$tabs = $this->get_tabs();
			$tabname = !empty ( $_GET['tab'] ) ? $_GET['tab'] : $tabs[0]['slug'];
			$menu = '';

			foreach ( $tabs as $tab ) {
				$class = $tabname == $tab['slug'] ? 'nav-tab-active' : '';

				$fields = array(
					'page' => $this->id,
					'tab'  => $tab['slug'],
				);

				$query = http_build_query( $fields );
				// print json_encode($_GET);
				// print json_encode($fields);
				$menu .= sprintf( '<a style="font-size:14px;" id="%s-tab" class="nav-tab %s" title="%s" href="?%s">%s</a>', $tab['slug'], $class, $tab['name'], $query, esc_html( $tab['name'] ) );
			}
            
            $menu .= '<a style="font-size:14px;" id="link-shortening-tab" class="nav-tab" title="Link Shortening" href="http://nrdd.co/1LpxHSY" target="_blank">Link Shortening <small>(Pro Only)</small></a>';
            
			return $menu;
		}


		/**
		 * Update an option
		 *
		 * @param string  $name
		 * @param string  $value
		 * @return bool
		 */
		public function update_option( $name, $value )
		{
			// Overwrite the key/value pair
			$this->current_options = array( $name => $value ) + (array) $this->current_options;

			return update_option( $this->id .'_options', $this->current_options );
		}


		/**
		 * Get an option
		 *
		 * @param string  $name
		 * @param string  $default (optional)
		 * @return bool
		 */
		public function get_option( $name, $default = false )
		{
			return isset( $this->current_options[$name] ) ? maybe_unserialize( $this->current_options[$name] ) : $default;
		}


	}


}
