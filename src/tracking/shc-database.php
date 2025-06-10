<?php
/**
 * Database handler class
 *
 * @package     SA Hyperlink Crawler
 * @since       1.0.0
 * @author      Stephen Akinola
 * @license     GPL-2.0-or-later
 */

namespace SA_HYPERLINK_CRAWLER\Tracking;

/**
 * Handle database operations for the plugin.
 */
class SHC_Database {
	/**
	 * Create required tables on plugin activation.
	 *
	 * @return void
	 */
	public function activate() {
		// TODO: create custom table for storing visits.
	}

	/**
	 * Remove custom tables on uninstall.
	 *
	 * @return void
	 */
	public function uninstall() {
		// TODO: drop custom table when uninstalling the plugin.
	}

	/**
	 * Insert a visit record.
	 *
	 * @param array $data Visit data from REST endpoint.
	 * @return void
	 */
	public function insert_visit( array $data ) {
		// TODO: insert visit data into custom table.
	}

	/**
	 * Retrieve visit records.
	 *
	 * @param int $paged Page number for pagination.
	 * @return array
	 */
	public function get_visits( $paged = 1 ) {
		// TODO: fetch visits for admin page display.
		return array( $paged );
	}

	/**
	 * Delete visits older than seven days.
	 *
	 * @return void
	 */
	public function cleanup() {
		// TODO: remove outdated visit data.
	}
}
