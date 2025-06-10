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
	* Number of visits displayed per page.
	*
	* @var int
	*/
	const PER_PAGE = 20;

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

		$sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
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
		$sql        = $wpdb->prepare( 'DROP TABLE IF EXISTS %i', $table_name );
		$wpdb->query( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- query is prepared above and table name is static
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
	 * @param int $per_page Number of visits per page.
	 * @return array
	 */
	public function get_visits( $paged = 1, $per_page = self::PER_PAGE ) {
		global $wpdb;

		$per_page = $per_page > 0 ? intval( $per_page ) : self::PER_PAGE;
		$paged    = max( 1, intval( $paged ) );
		$offset   = ( $paged - 1 ) * $per_page;
		$table    = $this->get_table();

		$sql = $wpdb->prepare(
			'SELECT id, visit_time, screen_width, screen_height, links
			FROM %i
			ORDER BY visit_time DESC
			LIMIT %d OFFSET %d',
			$table,
			$per_page,
			$offset
		);

		$results = $wpdb->get_results( $sql, ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- query is prepared above and table name is static

		if ( ! empty( $results ) ) {
			foreach ( $results as &$row ) {
				$row['links'] = json_decode( $row['links'], true );
			}
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
		$table     = $this->get_table();
		$sql       = $wpdb->prepare(
			'DELETE FROM %i WHERE visit_time < %s',
			$table,
			$threshold
		);
		$wpdb->query( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- query is prepared above and table name is static
	}

	/**
	 * Count total visits stored.
	 *
	 * @return int
	 */
	public function count_visits() {
		global $wpdb;

		$table = $this->get_table();
		$sql   = "SELECT COUNT(*) FROM {$table}";

		return (int) $wpdb->get_var( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- table name is static
	}
}
