<?php
/**
 * REST endpoint class
 *
 * @package     SA Hyperlink Crawler
 * @since       1.0.0
 * @author      Stephen Akinola
 * @license     GPL-2.0-or-later
 */

namespace SA_HYPERLINK_CRAWLER\Tracking;

use \WP_REST_Request;
use \WP_REST_Response;

/**
 * Register REST endpoint for recording visits.
 */
class SHC_RestEndpoint {
	/**
     * Register REST routes.
     *
     * @return void
     */
    public function register_routes() {
        // TODO: register REST route for POST requests.
    }

    /**
     * Handle POST request to store visit data.
     *
     * @param WP_REST_Request $request Incoming request.
     * @return WP_REST_Response
     */
    public function handle_visit( WP_REST_Request $request ) {
        // TODO: validate and save visit data via Database class.
        return new WP_REST_Response();
    }
}