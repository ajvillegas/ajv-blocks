<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.alexisvillegas.com
 * @since      1.0.0
 *
 * @package    AJV_Blocks
 * @subpackage AJV_Blocks/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and enqueues
 * the admin-specific stylesheet and JavaScript.
 *
 * @package    AJV_Blocks
 * @subpackage AJV_Blocks/admin
 * @author     Alexis J. Villegas <alexis@ajvillegas.com>
 */
class AJV_Blocks_Admin {

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

		// Get list of block subdirectories.
		$this->library = new AJV_Blocks_Library( $this->plugin_name, $this->version );
		$this->blocks  = $this->library->get_subdirectories( plugin_dir_path( dirname( __FILE__ ) ) . 'library/blocks/*' );

	}

	/**
	 * Register the admin menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since 1.0.0
	 * @link  http://codex.wordpress.org/Administration_Menus
	 */
	public function add_admin_menu() {

		add_options_page(
			esc_html__( 'AJV Block Library Settings', 'ajv-blocks' ),
			esc_html__( 'AJV Blocks', 'ajv-blocks' ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'display_settings_page' )
		);

	}

	/**
	 * Render the plugin settings page.
	 *
	 * @since 1.0.0
	 */
	public function display_settings_page() {

		include_once 'partials/ajv-blocks-admin-display.php';

	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since 1.0.0
	 * @link  https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
	 *
	 * @param array $links An array of plugin action links.
	 *
	 * @return array
	 */
	public function add_action_links( $links ) {

		$settings_link = array(
			'<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_name ) . '">' . esc_html__( 'Settings', 'ajv-blocks' ) . '</a>',
		);

		return array_merge( $settings_link, $links );

	}

	/**
	 * Register setting and its sanitization callback.
	 *
	 * @since 1.0.0
	 */
	public function options_update() {

		register_setting( $this->plugin_name, $this->plugin_name, array( $this, 'validate' ) );

	}

	/**
	 * Validate the input fields.
	 *
	 * @since 1.0.0
	 *
	 * @param array $input An array of input fields to validate.
	 *
	 * @return array
	 */
	public function validate( $input ) {

		// Get setting options from database.
		$options = get_option( $this->plugin_name );

		// Remove orphan keys that don't match with the list of block subdirectories.
		$settings = array_intersect_key( $options, $this->blocks );

		/**
		 * Validate custom block fields.
		 */
		foreach ( $this->blocks as $block ) {
			if ( isset( $input[ $block . '-block' ] ) && 1 == $input[ $block . '-block' ] ) { // phpcs:ignore WordPress.PHP.StrictComparisons
				$settings[ $block . '-block' ] = 1;
			} else {
				$settings[ $block . '-block' ] = 0;
			}
		}

		/**
		 * Validate core block enhancements.
		 */
		if ( isset( $input['core-block-enhancements'] ) && 1 == $input['core-block-enhancements'] ) { // phpcs:ignore WordPress.PHP.StrictComparisons
			$settings['core-block-enhancements'] = 1;
		} else {
			$settings['core-block-enhancements'] = 0;
		}

		return $settings;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {

		// Define the file suffix for debugging.
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		wp_enqueue_style(
			$this->plugin_name . '-admin',
			plugin_dir_url( __FILE__ ) . "css/ajv-blocks-admin{$suffix}.css",
			array(),
			$this->version,
			'all'
		);

	}

}
