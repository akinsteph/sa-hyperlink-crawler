<?php
/**
 * Crawler class
 *
 * @package     SA Hyperlink Crawler
 * @since       1.0.0
 * @author      Stephen Akinola
 * @license     GPL-2.0-or-later
 */

namespace SA_HYPERLINK_CRAWLER\Tracking;

/**
 * Manage the tracking script injection.
 */
class SHC_Crawler {
    /**
     * Hook into WordPress to enqueue the tracking script.
     *
     * @return void
     */
    public function register() {
        // TODO: enqueue JavaScript on the homepage.
    }

    /**
     * Render inline data for the script.
     *
     * @return array
     */
    public function get_script_data() {
        // TODO: provide REST endpoint URL and other info.
        return array();
    }
}