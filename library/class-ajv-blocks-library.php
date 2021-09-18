<?php
/**
 * The block library functionality of the plugin.
 *
 * @link       https://www.alexisvillegas.com
 * @since      1.0.0
 *
 * @package    AJV_Blocks
 * @subpackage AJV_Blocks/library
 */

/**
 * The block library functionality of the plugin.
 *
 * Defines the plugin name, version, and enqueues the
 * combined block-specific stylesheet and JavaScript.
 *
 * @since      1.0.0
 * @package    AJV_Blocks
 * @subpackage AJV_Blocks/library
 * @author     Alexis J. Villegas <alexis@ajvillegas.com>
 */
class AJV_Blocks_Library {

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
	 * An array of blocks with rendered styles.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    array $rendered_styles Blocks with rendered styles.
	 */
	private static $rendered_styles = array();

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

		// Load the Filesystem API in the front-end.
		include_once ABSPATH . 'wp-admin/includes/file.php';

		// Initialize the WP_Filesystem class.
		WP_Filesystem();

		// Define the global $wp_filesystem variable.
		global $wp_filesystem;
		$this->wp_filesystem = $wp_filesystem;

		// Get setting options from database.
		$this->options = get_option( $this->plugin_name );

	}

	/**
	 * Get an array of subdirectory names.
	 *
	 * @since 1.0.0
	 * @param string $dir The file path of the parent diretory.
	 * @return array $sub_dir An array of subdirectory names.
	 */
	public function get_subdirectories( $dir ) {

		// Create an empty array.
		$sub_dir = array();

		// Create array of subdirectory paths.
		$directories = array_filter( glob( $dir ), 'is_dir' );

		// Merge the subdirectory paths into the empty array.
		$sub_dir = array_merge( $sub_dir, $directories );

		// Get the subdirectory names from the subdirectory paths.
		$sub_dir = array_map( 'basename', $sub_dir );

		// Return list of subdirectory names.
		return $sub_dir;

	}

	/**
	 * Add custom block category.
	 *
	 * @since 1.0.0
	 * @param array $categories Existing block categories.
	 * @return array $categories Amended block categories.
	 */
	public function custom_block_category( $categories ) {

		return array_merge(
			array(
				array(
					'slug'  => 'ajv-blocks',
					'title' => esc_html__( 'AJV Blocks', 'ajv-blocks' ),
				),
			),
			$categories
		);

	}

	/**
	 * Conditionally register blocks and their assets.
	 *
	 * @since 1.0.0
	 */
	public function register_blocks() {

		// Get list of block subdirectories.
		$blocks = $this->get_subdirectories( plugin_dir_path( __FILE__ ) . 'blocks/*' );

		// Define the source folder and suffix for debugging.
		$src    = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? 'src/' : '';
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		foreach ( $blocks as $block ) {
			// Skip block registration if block is disabled in settings.
			if ( isset( $this->options[ $block . '-block' ] ) && 0 === $this->options[ $block . '-block' ] ) {
				continue;
			}

			// Define file paths.
			$editor_script_file   = plugin_dir_path( __FILE__ ) . 'blocks/' . $block . "/{$src}block{$suffix}.js";
			$editor_style_file    = plugin_dir_path( __FILE__ ) . 'blocks/' . $block . "/{$src}editor{$suffix}.css";
			$frontend_script_file = plugin_dir_path( __FILE__ ) . 'blocks/' . $block . "/{$src}frontend{$suffix}.js";

			// Block editor script.
			if ( $this->wp_filesystem->exists( $editor_script_file ) ) {
				wp_register_script(
					'ajvbl-block-' . $block,
					plugin_dir_url( __FILE__ ) . 'blocks/' . $block . "/{$src}block{$suffix}.js",
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

			// Block editor styles.
			if ( $this->wp_filesystem->exists( $editor_style_file ) ) {
				wp_register_style(
					'ajvbl-block-editor-' . $block,
					apply_filters(
						'ajvbl_' . $block . '_editor_style_url',
						plugin_dir_url( __FILE__ ) . 'blocks/' . $block . "/{$src}editor{$suffix}.css"
					),
					array(),
					$this->version,
					'all'
				);
			}

			// Block front-end script.
			if ( $this->wp_filesystem->exists( $frontend_script_file ) ) {
				wp_register_script(
					'ajvbl-block-frontend-' . $block,
					plugin_dir_url( __FILE__ ) . 'blocks/' . $block . "/{$src}frontend{$suffix}.js",
					array(
						'jquery',
					),
					$this->version,
					true
				);
			}

			// Define script handles.
			$editor_script = $this->wp_filesystem->exists( $editor_script_file ) ? 'ajvbl-block-' . $block : null;
			$editor_style  = $this->wp_filesystem->exists( $editor_style_file ) ? 'ajvbl-block-editor-' . $block : null;

			// Register block.
			register_block_type(
				'ajvbl/' . $block,
				array(
					'editor_script'   => $editor_script, // Editor script.
					'editor_style'    => $editor_style, // Editor styles.
					'render_callback' => array( $this, 'enqueue_frontend_scripts' ), // Callback function.
				)
			);
		}

	}

	/**
	 * Block type render callback.
	 *
	 * This function is used to enqueue the block's front-end
	 * scripts only when the block is rendered on the page.
	 *
	 * @since 1.2.0
	 * @param array  $attributes The block attributes.
	 * @param string $content The block content.
	 * @return string
	 */
	public function enqueue_frontend_scripts( $attributes, $content ) {

		// Bail if not front-end.
		if ( is_admin() ) {
			return $content;
		}

		// Get list of block subdirectories.
		$blocks = $this->get_subdirectories( plugin_dir_path( __FILE__ ) . 'blocks/*' );

		foreach ( $blocks as $block ) {
			wp_enqueue_script( 'ajvbl-block-frontend-' . $block );
		}

		return $content;

	}

	/**
	 * Inject inline block styles in the front-end when rendering a block.
	 *
	 * This function checks to see if block exists on the $rendered_styles
	 * static array first so it only injects styles to first block instance.
	 *
	 * @since 1.0.0
	 * @param string $block_content The block content about to be appended.
	 * @param array  $block_object The full block, including name and attributes.
	 * @return string $block_content The block with modified content.
	 */
	public function render_block_styles( $block_content, $block_object ) {

		// Bail if admin or JSON request.
		if ( is_admin() || wp_is_json_request() ) {
			return $block_content;
		}

		// Get list of block subdirectories.
		$blocks = $this->get_subdirectories( plugin_dir_path( __FILE__ ) . 'blocks/*' );
		$blocks = substr_replace( $blocks, 'ajvbl/', 0, 0 );

		// Define the source folder and suffix for debugging.
		$src    = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? 'src/' : '';
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		if (
			isset( $block_object['blockName'] ) &&
			in_array( $block_object['blockName'], $blocks, true ) &&
			! in_array( $block_object['blockName'], self::$rendered_styles, true )
		) {
			// Reformat block name.
			$block = str_replace( 'ajvbl/', '', $block_object['blockName'] );

			// Define file paths.
			$frontend_style_file = apply_filters(
				'ajvbl_' . $block . '_style_path',
				plugin_dir_path( __FILE__ ) . 'blocks/' . $block . "/{$src}style{$suffix}.css"
			);

			// Inject inline block CSS.
			if ( $this->wp_filesystem->exists( $frontend_style_file ) ) {
				$html  = '<style id="ajvbl-block-' . esc_html( $block ) . '-inline-css">';
				$html .= $this->wp_filesystem->get_contents( $frontend_style_file );
				$html .= '</style>';

				// Inject inline CSS directly before the first block instance.
				$content = substr_replace( $block_content, $html, 0, 0 );

				// Add to $rendered_styles array.
				self::$rendered_styles[] = $block_object['blockName'];

				return $content;
			}
		}

		return $block_content;

	}

}
