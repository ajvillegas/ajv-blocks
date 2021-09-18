<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @link  https://www.alexisvillegas.com
 * @since 1.0.0
 *
 * @package AJV_Blocks
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// If user doesn't have the right permissions, then exit.
if ( ! current_user_can( 'activate_plugins' ) ) {
	return;
}

// If action didn't originate on the proper page, then exit.
if ( __FILE__ !== WP_UNINSTALL_PLUGIN ) {
	return;
}

// Delete plugin options from database.
if ( is_multisite() ) {

	delete_option( 'ajv-blocks' );

	if ( function_exists( 'get_sites' ) && class_exists( 'WP_Site_Query' ) ) {
		$sites = get_sites();

		if ( $sites ) {
			foreach ( $sites as $site ) {
				switch_to_blog( $site->blog_id );
					delete_option( 'ajv-blocks' );
				restore_current_blog();
			}
		}

		return;
	}
} else {

	delete_option( 'ajv-blocks' );

}
