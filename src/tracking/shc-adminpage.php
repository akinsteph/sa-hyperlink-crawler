<?php
/**
 * Admin page class
 *
 * @package     SA Hyperlink Crawler
 * @since       1.0.0
 * @author      Stephen Akinola
 * @license     GPL-2.0-or-later
 */

namespace SA_HYPERLINK_CRAWLER\Tracking;

use SA_HYPERLINK_CRAWLER\Tracking\SHC_Database;
/**
 * Provide an admin page displaying visit data.
 */
class SHC_AdminPage {
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
	 * Add hooks for admin menu and screen rendering.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Enqueue admin styles only on our page.
	 *
	 * @param string $hook Current admin page hook.
	 * @return void
	 */
	public function enqueue( $hook ) {
		if ( 'toplevel_page_sa-hyperlink-crawler' !== $hook ) {
				return;
		}

		$src = plugin_dir_url( SA_HYPERLINK_CRAWLER_PLUGIN_FILENAME ) . 'assets/css/admin.css';
		wp_enqueue_style( 'shc-admin', $src, array(), SA_HYPERLINK_CRAWLER_VERSION );
	}

	/**
	 * Register the admin menu page.
	 *
	 * @return void
	 */
	public function add_menu() {
		add_menu_page(
			__( 'Hyperlink Visits', 'sa_hyperlink_crawler' ),
			__( 'Hyperlink Crawler', 'sa_hyperlink_crawler' ),
			'manage_options',
			'sa-hyperlink-crawler',
			array( $this, 'render' ),
			'dashicons-admin-links',
			26
		);
	}

	/**
	 * Render the admin page contents.
	 *
	 * @return void
	 */
	public function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
				return;
		}

		$paged    = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Display page
		$per_page = SHC_Database::PER_PAGE;
		$visits   = $this->db->get_visits( $paged, $per_page );

		echo '<div class="wrap">';
		echo '<h1 class="shc-page-title">' . esc_html__( 'Recent Hyperlink Visits', 'sa_hyperlink_crawler' ) . '</h1>';
		echo '<div class="shc-admin-container">';
		echo '<table class="widefat shc-admin-table">';
		echo '<thead><tr>';
		echo '<th>' . esc_html__( 'Time', 'sa_hyperlink_crawler' ) . '</th>';
		echo '<th>' . esc_html__( 'Screen Size', 'sa_hyperlink_crawler' ) . '</th>';
		echo '<th>' . esc_html__( 'Links', 'sa_hyperlink_crawler' ) . '</th>';
		echo '</tr></thead><tbody>';

		if ( empty( $visits ) ) {
				echo '<tr><td colspan="3">' . esc_html__( 'No visits recorded.', 'sa_hyperlink_crawler' ) . '</td></tr>';
		} else {
			foreach ( $visits as $visit ) {
					echo '<tr>';
					echo '<td>' . esc_html( $visit['visit_time'] ) . '</td>';
					echo '<td>' . intval( $visit['screen_width'] ) . 'x' . intval( $visit['screen_height'] ) . '</td>';
					echo '<td>';
				foreach ( $visit['links'] as $link ) {
						$url  = esc_url( $link['url'] );
						$text = esc_html( $link['text'] );
						echo '<div><a href="' . esc_attr( $url ) . '" target="_blank">' . esc_html( $text ) . '</a></div>';
				}
					echo '</td>';
					echo '</tr>';
			}
		}
		echo '</tbody></table>';

		// Pagination.
		$per_page    = SHC_Database::PER_PAGE;
		$total       = $this->db->count_visits();
		$total_pages = (int) ceil( $total / $per_page );

		$prev = $paged > 1 ? $paged - 1 : 0;
		$next = $paged < $total_pages ? $paged + 1 : 0;
		echo '<div class="shc-pagination">';
		if ( $prev ) {
				echo '<a href="' . esc_url( add_query_arg( 'paged', $prev ) ) . '">' . esc_html__( 'Previous', 'sa_hyperlink_crawler' ) . '</a>';
		}
		if ( $next ) {
				echo '<a href="' . esc_url( add_query_arg( 'paged', $next ) ) . '">' . esc_html__( 'Next', 'sa_hyperlink_crawler' ) . '</a>';
		}
		echo '</div>'; // End pagination.

		echo '</div>'; // End admin container.
		echo '</div>'; // End wrap.
	}
}
