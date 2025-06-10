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
	 * Register WordPress cron events.
	 *
	 * @return void
	 */
	public function register() {
		// TODO: schedule daily cleanup event.
	}

	/**
	 * Callback to purge old visit data.
	 *
	 * @return void
	 */
	public function cleanup() {
		// TODO: delete visits older than seven days via Database class.
	}
}
