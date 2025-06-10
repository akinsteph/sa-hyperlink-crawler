<?php
/**
 * Cron class
 *
 * @package     SA Hyperlink Crawler
 * @since       1.0.0
 * @author      Stephen Akinola
 * @license     GPL-2.0-or-later
 */

namespace SA_HYPERLINK_CRAWLER\Tracking;

/**
 * Manage scheduled cleanup tasks.
 */
class SHC_Cron {
	/**
	 * Cron hook identifier.
	 *
	 * @var string
	 */
	const HOOK = 'shc_daily_cleanup';

	/**
	 * Database handler instance.
	 *
	 * @var SHC_Database
	 */
	protected $db;

	/**
	 * Constructor.
	 *
	 * @param SHC_Database $db Database handler.
	 */
	public function __construct( SHC_Database $db ) {
			$this->db = $db;
	}

	/**
	 * Register WordPress cron events.
	 *
	 * @return void
	 */
	public function register() {
			add_action( self::HOOK, array( $this, 'cleanup' ) );
	}

	/**
	 * Schedule the cleanup event daily if not already scheduled.
	 *
	 * @return void
	 */
	public function schedule() {
			if ( ! wp_next_scheduled( self::HOOK ) ) {
					wp_schedule_event( time(), 'daily', self::HOOK );
			}
	}

	/**
	 * Unschedule the cleanup event.
	 *
	 * @return void
	 */
	public function unschedule() {
			$timestamp = wp_next_scheduled( self::HOOK );
			if ( $timestamp ) {
					wp_unschedule_event( $timestamp, self::HOOK );
			}
	}

	/**
	 * Callback to purge old visit data.
	 *
	 * @return void
	 */
	public function cleanup() {
			$this->db->cleanup();
	}
}
