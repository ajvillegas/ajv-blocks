<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.alexisvillegas.com
 * @since             1.0.0
 * @package           AJV_Blocks
 *
 * @wordpress-plugin
 * Plugin Name:       AJV Block Library
 * Plugin URI:        https://github.com/ajvillegas/ajv-blocks
 * Description:       A collection of custom blocks and core block enhancements.
 * Version:           1.0.0
 * Author:            Alexis J. Villegas
 * Author URI:        https://www.alexisvillegas.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ajv-blocks
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'AJV_BLOCKS_VERSION', '1.0.0' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ajv-blocks.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since 1.0.0
 */
function run_ajv_blocks() {

	$plugin = new AJV_Blocks();
	$plugin->run();

}

run_ajv_blocks();
