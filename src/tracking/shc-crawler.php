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
	* Script handle used when enqueuing.
	*
	* @var string
	*/
	const HANDLE = 'shc-script';

	/**
	 * Hook into WordPress to enqueue the tracking script.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Enqueue the tracking script on the homepage only.
	 *
	 * @return void
	 */
	public function enqueue() {
		if ( ! is_front_page() ) {
			return;
		}

		$src = plugin_dir_url( SA_HYPERLINK_CRAWLER_PLUGIN_FILENAME ) . 'assets/js/shc-crawler.js';

		wp_enqueue_script( self::HANDLE, $src, array(), SA_HYPERLINK_CRAWLER_VERSION, true );

		wp_localize_script( self::HANDLE, 'shcData', $this->get_script_data() );
	}

	/**
	 * Render inline data for the script.
	 *
	 * @return array
	 */
	public function get_script_data() {
		return array(
			'endpoint' => rest_url( 'sa-hyperlink-crawler/v1/visit' ),
			'nonce'    => wp_create_nonce( 'wp_rest' ),
			'debug'    => defined( 'WP_DEBUG' ) && WP_DEBUG,
		);

		// TODO: provide other info.
	}
}
