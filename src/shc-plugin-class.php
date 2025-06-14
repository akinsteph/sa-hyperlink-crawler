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

use SA_HYPERLINK_CRAWLER\Tracking\SHC_Database;
use SA_HYPERLINK_CRAWLER\Tracking\SHC_Crawler;
use SA_HYPERLINK_CRAWLER\Tracking\SHC_RestEndpoint;
use SA_HYPERLINK_CRAWLER\Tracking\SHC_AdminPage;
use SA_HYPERLINK_CRAWLER\Tracking\SHC_Cron;

/**
 * Main plugin class. It manages initialization, install, and activations.
 */
class SHC_Plugin_Class {
	/**
	 * Database handler instance.
	 *
	 * @var SHC_Database
	 */
	protected $db;

	/**
	 * Script handler instance.
	 *
	 * @var SHC_Crawler
	 */
	protected $script;

	/**
	 * REST endpoint handler instance.
	 *
	 * @var SHC_RestEndpoint
	 */
	protected $endpoint;

	/**
	 * Admin page handler instance.
	 *
	 * @var SHC_AdminPage
	 */
	protected $admin_page;

	/**
	 * Cron handler instance.
	 *
	 * @var SHC_Cron
	 */
	protected $cron;

	/**
	 * Manages plugin initialization
	 *
	 * @return void
	 */
	public function __construct() {
		$this->db         = new SHC_Database();
		$this->script     = new SHC_Crawler();
		$this->endpoint   = new SHC_RestEndpoint( $this->db );
		$this->admin_page = new SHC_AdminPage( $this->db );
		$this->cron       = new SHC_Cron( $this->db );

		// Register hooks.
		$this->register_hooks();

		// Register plugin lifecycle hooks.
		register_deactivation_hook( SA_HYPERLINK_CRAWLER_PLUGIN_FILENAME, array( $this, 'sa_hyperlink_crawler_deactivate' ) );
	}

	/**
	 * Register hooks with WordPress.
	 *
	 * @return void
	 */
	protected function register_hooks() {
		$this->script->register();
		$this->endpoint->register();
		$this->admin_page->register();
		$this->cron->register();
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

		$db = new SHC_Database();
		$db->activate();

		$cron = new SHC_Cron( $db );
		$cron->schedule();
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

		$this->cron->unschedule();
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

		$db = new SHC_Database();
		$db->uninstall();
	}
}
