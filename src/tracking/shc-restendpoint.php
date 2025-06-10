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

use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

/**
 * Register REST endpoint for recording visits.
 */
class SHC_RestEndpoint {

	/**
	 * Database handler.
	 *
	 * @var SHC_Database
	 */
	protected $db;

	/**
	 * Constructor.
	 *
	 * @param SHC_Database $db Database handler dependency.
	 */
	public function __construct( SHC_Database $db ) {
		$this->db = $db;
	}

	/**
	 * Register hooks with WordPress.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register REST routes.
	 *
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			'sa-hyperlink-crawler/v1',
			'/visit',
			array(
				'methods'             => 'POST',
				'permission_callback' => array( $this, 'permissions_check' ),
				'callback'            => array( $this, 'handle_visit' ),
			)
		);
	}

	/**
	 * Validate the request using nonce verification.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return true|WP_Error
	 */
	public function permissions_check( WP_REST_Request $request ) {
		$nonce = $request->get_header( 'X-WP-Nonce' );

		if ( ! wp_verify_nonce( $nonce, 'shc_rest' ) ) {
				return new \WP_Error( 'rest_forbidden', __( 'Invalid nonce', 'sa_hyperlink_crawler' ), array( 'status' => 403 ) );
		}

		return true;
	}

	/**
	 * Handle POST request to store visit data.
	 *
	 * @param WP_REST_Request $request Incoming request.
	 * @return WP_REST_Response
	 */
	public function handle_visit( WP_REST_Request $request ) {
		$data = array(
			'links'  => $request->get_param( 'links' ),
			'width'  => intval( $request->get_param( 'width' ) ),
			'height' => intval( $request->get_param( 'height' ) ),
			'time'   => current_time( 'mysql', true ),
		);

		if ( empty( $data['links'] ) || ! is_array( $data['links'] ) ) {
			return new \WP_REST_Response( array( 'error' => 'Invalid data' ), 400 );
		}

		// Persist the visit using database handler.
		$this->db->insert_visit( $data );

		return new \WP_REST_Response( array( 'success' => true ), 201 );
	}
}
