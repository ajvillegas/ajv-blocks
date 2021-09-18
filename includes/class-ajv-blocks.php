<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.alexisvillegas.com
 * @since      1.0.0
 *
 * @package    AJV_Blocks
 * @subpackage AJV_Blocks/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    AJV_Blocks
 * @subpackage AJV_Blocks/includes
 * @author     Alexis J. Villegas <alexis@ajvillegas.com>
 */
class AJV_Blocks {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    AJV_Blocks_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		if ( defined( 'AJV_BLOCKS_VERSION' ) ) {
			$this->version = AJV_BLOCKS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'ajv-blocks';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_library_hooks();
		$this->define_core_block_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - AJV_Blocks_Loader. Orchestrates the hooks of the plugin.
	 * - AJV_Blocks_i18n. Defines internationalization functionality.
	 * - AJV_Blocks_Admin. Defines all hooks for the admin area.
	 * - AJV_Blocks_Library. Defines all hooks for the block library.
	 * - AJV_Blocks_Core. Defines all hooks for the core block functionality.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ajv-blocks-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ajv-blocks-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ajv-blocks-admin.php';

		/**
		 * The class responsible for defining all actions related to the block library functionality.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'library/class-ajv-blocks-library.php';

		/**
		 * The class responsible for defining all actions related to the core block functionality.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'core/class-ajv-blocks-core.php';

		$this->loader = new AJV_Blocks_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the AJV_Blocks_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function set_locale() {

		$plugin_i18n = new AJV_Blocks_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new AJV_Blocks_Admin( $this->get_plugin_name(), $this->get_version() );

		// Add menu item.
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_admin_menu' );

		// Add settings link to the plugin.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_name . '.php' );
		$this->loader->add_filter( 'plugin_action_links_' . $plugin_basename, $plugin_admin, 'add_action_links' );

		// Save/Update the plugin options.
		$this->loader->add_action( 'admin_init', $plugin_admin, 'options_update' );

		// Enqueue styles.
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );

	}

	/**
	 * Register all of the hooks related to the block library functionality
	 * of the plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function define_library_hooks() {

		$plugin_library = new AJV_Blocks_Library( $this->get_plugin_name(), $this->get_version() );

		// Add custom block category.
		$this->loader->add_filter( 'block_categories_all', $plugin_library, 'custom_block_category' );

		// Conditionally register the blocks.
		$this->loader->add_action( 'init', $plugin_library, 'register_blocks' );

		// Inject inline block styles in the front-end when rendering a block.
		$this->loader->add_filter( 'render_block', $plugin_library, 'render_block_styles', 10, 2 );
	}

	/**
	 * Register all of the hooks related to the core block functionality
	 * of the plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function define_core_block_hooks() {

		$plugin_core = new AJV_Blocks_Core( $this->get_plugin_name(), $this->get_version() );

		// Remove core block styles in the front-end.
		$this->loader->add_action( 'init', $plugin_core, 'remove_core_block_styles' );

		// Enqueue assets in both the admin editor and frontend of the site.
		$this->loader->add_action( 'enqueue_block_assets', $plugin_core, 'block_assets' );

		// Enqueue assets in the admin editor only.
		$this->loader->add_action( 'enqueue_block_editor_assets', $plugin_core, 'block_editor_assets' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since  1.0.0
	 * @return string The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since  1.0.0
	 * @return AJV_Blocks_Loader Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since  1.0.0
	 * @return string The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
