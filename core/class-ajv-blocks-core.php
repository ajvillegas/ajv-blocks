<?php
/**
 * The core block functionality of the plugin.
 *
 * @link       https://www.alexisvillegas.com
 * @since      1.0.0
 *
 * @package    AJV_Blocks
 * @subpackage AJV_Blocks/core
 */

/**
 * The core block functionality of the plugin.
 *
 * Defines the plugin name, version, and enqueues the
 * combined block-specific stylesheet and JavaScript.
 *
 * @since      1.0.0
 * @package    AJV_Blocks
 * @subpackage AJV_Blocks/core
 * @author     Alexis J. Villegas <alexis@ajvillegas.com>
 */
class AJV_Blocks_Core {

	/**
	 * The ID of this plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		// Get setting options from database.
		$this->options = get_option( $this->plugin_name );

	}

	/**
	 * Remove core block styles in the front-end.
	 *
	 * For this to work, the `should_load_separate_core_block_assets`
	 * filter must be enabled by the theme.
	 *
	 * @since 1.0.0
	 */
	public function remove_core_block_styles() {

		// Bail if block enhancements option is not enabled.
		if ( isset( $this->options['core-block-enhancements'] ) && 0 === $this->options['core-block-enhancements'] ) {
			return;
		}

		// Core Columns block styles.
		wp_deregister_style( 'wp-block-columns' );

	}

	/**
	 * Enqueue block styles in both the admin editor and frontend of the site.
	 *
	 * @since 1.0.0
	 */
	public function block_assets() {

		// Bail if block enhancements option is not enabled.
		if ( isset( $this->options['core-block-enhancements'] ) && 0 === $this->options['core-block-enhancements'] ) {
			return;
		}

		// Define the file suffix for debugging.
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		// Core block styles.
		wp_enqueue_style(
			$this->plugin_name . '-core',
			plugin_dir_url( __FILE__ ) . "css/ajv-core-blocks{$suffix}.css",
			array(),
			$this->version,
			'all'
		);

		// Block admin inline styles.
		if ( is_admin() ) {
			wp_add_inline_style(
				$this->plugin_name . '-core',
				"div[data-align='full'] { margin-left: -8px !important; margin-right: -8px !important; }"
			);
		}

	}

	/**
	 * Enqueue block scripts in the admin editor only.
	 *
	 * @since 1.0.0
	 * @link https://developer.wordpress.org/block-editor/contributors/develop/scripts/
	 */
	public function block_editor_assets() {

		// Bail if block enhancements option is not enabled.
		if ( isset( $this->options['core-block-enhancements'] ) && 0 === $this->options['core-block-enhancements'] ) {
			return;
		}

		// Define the file suffix for debugging.
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		// Block editor scripts.
		wp_enqueue_script(
			$this->plugin_name . '-core',
			plugin_dir_url( __FILE__ ) . "js/ajv-core-blocks{$suffix}.js",
			array(
				'wp-blocks',
				'wp-editor',
				'wp-element',
				'wp-components',
				'wp-i18n',
			),
			$this->version,
			true
		);

	}

}
