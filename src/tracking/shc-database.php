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
	 * Table name without prefix.
	 *
	 * @var string
	 */
	const TABLE = 'shc_visits';

	/**
	* Return the full table name including WordPress prefix.
	*
	* @return string
	*/
	public function get_table() {
		global $wpdb;

		return $wpdb->prefix . self::TABLE;
	}

	/**
	 * Create required tables on plugin activation.
	 *
	 * @return void
	 */
	public function activate() {
		global $wpdb;

		$table_name      = $this->get_table();
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table_name} (
				id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				visit_time DATETIME NOT NULL,
				screen_width INT(11) NOT NULL,
				screen_height INT(11) NOT NULL,
				links LONGTEXT NOT NULL,
				PRIMARY KEY  (id),
				KEY visit_time (visit_time)
		) {$charset_collate};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Remove custom tables on uninstall.
	 *
	 * @return void
	 */
	public function uninstall() {
		global $wpdb;

		$table_name = $this->get_table();
		$wpdb->query( "DROP TABLE IF EXISTS {$table_name}" );
	}

	/**
	 * Insert a visit record.
	 *
	 * @param array $data Visit data from REST endpoint.
	 * @return void
	 */
	public function insert_visit( array $data ) {
		global $wpdb;

		$wpdb->insert(
				$this->get_table(),
				array(
						'visit_time'    => $data['time'],
						'screen_width'  => $data['width'],
						'screen_height' => $data['height'],
						'links'         => wp_json_encode( $data['links'] ),
				),
				array( '%s', '%d', '%d', '%s' )
		);
	}

	/**
	 * Retrieve visit records.
	 *
	 * @param int $paged Page number for pagination.
	 * @return array
	 */
	public function get_visits( $paged = 1 ) {
		global $wpdb;

		$per_page = 20;
		$paged    = max( 1, intval( $paged ) );
		$offset   = ( $paged - 1 ) * $per_page;

		$sql = $wpdb->prepare(
				"SELECT id, visit_time, screen_width, screen_height, links
				 FROM {$this->get_table()} ORDER BY visit_time DESC
				 LIMIT %d OFFSET %d",
				$per_page,
				$offset
		);

		$results = $wpdb->get_results( $sql, ARRAY_A );

		foreach ( $results as &$row ) {
				$row['links'] = json_decode( $row['links'], true );
		}

		return $results;
	}

	/**
	 * Delete visits older than seven days.
	 *
	 * @return void
	 */
	public function cleanup() {
		global $wpdb;

		$threshold = gmdate( 'Y-m-d H:i:s', strtotime( '-7 days' ) );
		$sql       = $wpdb->prepare(
				"DELETE FROM {$this->get_table()} WHERE visit_time < %s",
				$threshold
		);
		$wpdb->query( $sql );
	}
}
