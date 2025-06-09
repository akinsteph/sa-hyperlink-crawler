<?php
/**
 * Plugin main class
 *
 * @package     SA Hyperlink Crawler
 * @since       1.0.0
 * @author      Stephen Akinola
 * @license     GPL-2.0-or-later
 */

namespace SA_HYPERLINK_CRAWLER;

/**
 * Main plugin class. It manages initialization, install, and activations.
 */
class SHC_Plugin_Class {
	/**
	 * Manages plugin initialization
	 *
	 * @return void
	 */
	public function __construct() {

		// Register plugin lifecycle hooks.
		register_deactivation_hook( SA_HYPERLINK_CRAWLER_PLUGIN_FILENAME, array( $this, 'sa_hyperlink_crawler_deactivate' ) );
	}

	/**
	 * Handles plugin activation:
	 *
	 * @return void
	 */
	public static function sa_hyperlink_crawler_activate() {
		// Security checks.
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		$plugin = isset( $_REQUEST['plugin'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['plugin'] ) ) : '';
		check_admin_referer( "activate-plugin_{$plugin}" );
	}

	/**
	 * Handles plugin deactivation
	 *
	 * @return void
	 */
	public function sa_hyperlink_crawler_deactivate() {
		// Security checks.
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		$plugin = isset( $_REQUEST['plugin'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['plugin'] ) ) : '';
		check_admin_referer( "deactivate-plugin_{$plugin}" );
	}

	/**
	 * Handles plugin uninstall
	 *
	 * @return void
	 */
	public static function sa_hyperlink_crawler_uninstall() {

		// Security checks.
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
	}
}
