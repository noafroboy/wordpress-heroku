<?php

class Kirki_Config {

	/** @var array The configuration values for Kirki */
	private $config = null;

	/**
	 * Constructor
	 */
	public function __construct() {
	}

	/**
	 * Get a configuration value
	 *
	 * @param string $key     The configuration key we are interested in
	 * @param string $default The default value if that configuration is not set
	 *
	 * @return mixed
	 */
	public function get( $key, $default='' ) {

		$cfg = $this->get_all();
		return isset( $cfg[$key] ) ? $cfg[$key] : $default;

	}

	/**
	 * Get a configuration value or throw an exception if that value is mandatory
	 *
	 * @param string $key     The configuration key we are interested in
	 *
	 * @return mixed
	 */
	public function getOrThrow( $key ) {

		$cfg = $this->get_all();
		if ( isset( $cfg[$key] ) ) {
			return $cfg[$key];
		}

		throw new RuntimeException( sprintf( esc_html__('Configuration key %s is mandatory and has not been specified', 'kirki' ), $key ) );

	}

	/**
	 * Get the configuration options for the Kirki customizer.
	 *
	 * @uses 'kirki/config' filter.
	 */
	public function get_all() {

		if ( is_null( $this->config ) ) {

			// Get configuration from the filter
			$this->config = apply_filters( 'kirki/config', array() );

			// Merge a default configuration with the one we got from the user to make sure nothing is missing
			$default_config = array(
				'stylesheet_id' => 'kirki-styles',
				'capability'    => 'edit_theme_options',
				'logo_image'    => '',
				'description'   => '',
				'url_path'      => '',
				'options_type'  => 'theme_mod',
				'compiler'      => array(),
			);
			$this->config = array_merge( $default_config, $this->config );

			// The logo image
			$this->config['logo_image']  = esc_url_raw( $this->config['logo_image'] );
			// The customizer description
			$this->config['description'] = esc_html( $this->config['description'] );
			// The URL path to Kirki. Used when Kirki is embedded in a theme for example.
			$this->config['url_path']    = esc_url_raw( $this->config['url_path'] );
			// Compiler configuration. Still experimental and under construction.
			$this->config['compiler']    = array(
				'mode'   => isset( $this->config['compiler']['mode'] ) ? sanitize_key( $this->config['compiler']['mode'] ) : '',
				'filter' => isset( $this->config['compiler']['filter'] ) ? esc_html( $this->config['compiler']['filter'] ) : '',
			);

			// Get the translation strings.
			$this->config['i18n'] = ( ! isset( $this->config['i18n'] ) ) ? array() : $this->config['i18n'];
			$this->config['i18n'] = array_merge( $this->translation_strings(), $this->config['i18n'] );

			// If we're using options instead of theme_mods then sanitize the option name & type here.
			if ( 'option' == $this->config['options_type'] && isset( $this->config['option_name'] ) && '' != $this->config['option_name'] ) {
				$option_name = $this->config['option_name'];
				$this->config['option_name'] = sanitize_key( $this->config['option_name'] );
			} else {
				$this->config['option_name'] = '';
			}

		}

		return $this->config;

	}

	/**
	 * The i18n strings
	 */
	public function translation_strings() {

		$strings = array(
			'background-color'      => esc_html__('Background Color',         'kirki' ),
			'background-image'      => esc_html__('Background Image',         'kirki' ),
			'no-repeat'             => esc_html__('No Repeat',                'kirki' ),
			'repeat-all'            => esc_html__('Repeat All',               'kirki' ),
			'repeat-x'              => esc_html__('Repeat Horizontally',      'kirki' ),
			'repeat-y'              => esc_html__('Repeat Vertically',        'kirki' ),
			'inherit'               => esc_html__('Inherit',                  'kirki' ),
			'background-repeat'     => esc_html__('Background Repeat',        'kirki' ),
			'cover'                 => esc_html__('Cover',                    'kirki' ),
			'contain'               => esc_html__('Contain',                  'kirki' ),
			'background-size'       => esc_html__('Background Size',          'kirki' ),
			'fixed'                 => esc_html__('Fixed',                    'kirki' ),
			'scroll'                => esc_html__('Scroll',                   'kirki' ),
			'background-attachment' => esc_html__('Background Attachment',    'kirki' ),
			'left-top'              => esc_html__('Left Top',                 'kirki' ),
			'left-center'           => esc_html__('Left Center',              'kirki' ),
			'left-bottom'           => esc_html__('Left Bottom',              'kirki' ),
			'right-top'             => esc_html__('Right Top',                'kirki' ),
			'right-center'          => esc_html__('Right Center',             'kirki' ),
			'right-bottom'          => esc_html__('Right Bottom',             'kirki' ),
			'center-top'            => esc_html__('Center Top',               'kirki' ),
			'center-center'         => esc_html__('Center Center',            'kirki' ),
			'center-bottom'         => esc_html__('Center Bottom',            'kirki' ),
			'background-position'   => esc_html__('Background Position',      'kirki' ),
			'background-opacity'    => esc_html__('Background Opacity',       'kirki' ),
			'ON'                    => esc_html__('ON',                       'kirki' ),
			'OFF'                   => esc_html__('OFF',                      'kirki' ),
			'all'                   => esc_html__('All',                      'kirki' ),
			'cyrillic'              => esc_html__('Cyrillic',                 'kirki' ),
			'cyrillic-ext'          => esc_html__('Cyrillic Extended',        'kirki' ),
			'devanagari'            => esc_html__('Devanagari',               'kirki' ),
			'greek'                 => esc_html__('Greek',                    'kirki' ),
			'greek-ext'             => esc_html__('Greek Extended',           'kirki' ),
			'khmer'                 => esc_html__('Khmer',                    'kirki' ),
			'latin'                 => esc_html__('Latin',                    'kirki' ),
			'latin-ext'             => esc_html__('Latin Extended',           'kirki' ),
			'vietnamese'            => esc_html__('Vietnamese',               'kirki' ),
			'serif'                 => _x( 'Serif', 'font style',      'kirki' ),
			'sans-serif'            => _x( 'Sans Serif', 'font style', 'kirki' ),
			'monospace'             => _x( 'Monospace', 'font style',  'kirki' ),
		);

		return $strings;

	}

}
