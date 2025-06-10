<?php
/**
 * Integration tests for SHC_RestEndpoint registration.
 *
 * @package SA Hyperlink Crawler
 */
namespace SA_HYPERLINK_CRAWLER\Tests\Integration;

use Mockery;
use Brain\Monkey\Functions;
use WPMedia\PHPUnit\Integration\TestCase;
use SA_HYPERLINK_CRAWLER\Tracking\SHC_RestEndpoint;
use SA_HYPERLINK_CRAWLER\Tracking\SHC_Database;

/**
 * Integration tests for REST endpoint hook registration.
 */
class RestEndpointIntegrationTest extends TestCase {

    public function setUp(): void {
        parent::setUp();
        \Brain\Monkey\setUp();
    }

    public function tearDown(): void {
        Mockery::close();
        \Brain\Monkey\tearDown();
        parent::tearDown();
    }

    public function testRegistersRouteOnRestApiInit() {
        global $wpdb;
        $wpdb = Mockery::mock('wpdb');
        $wpdb->prefix = 'wp_';
        $wpdb->shouldReceive('suppress_errors')->andReturn(true);
        $wpdb->shouldReceive('db_connect')->andReturn(true);
        $wpdb->shouldReceive('_escape')->andReturnUsing(function($str) { return $str; });
        $wpdb->shouldReceive('get_results')->andReturn([]);
        $wpdb->shouldReceive('prepare')->andReturnUsing(function($str) { return $str; });
        $wpdb->shouldReceive('get_row')->andReturn(null);
        $wpdb->shouldReceive('query')->andReturn(true);

        $db = Mockery::mock(SHC_Database::class);

        $endpoint = new SHC_RestEndpoint($db);

        Functions\expect( 'register_rest_route' )
            ->once()
            ->with( 'sa-hyperlink-crawler/v1', '/visit', Mockery::type('array') );

        $endpoint->register();
        do_action( 'rest_api_init' );
    }

	public static function tearDownAfterClass(): void {
		global $wpdb;
		if (isset($wpdb) && $wpdb instanceof \Mockery\MockInterface) {
			$wpdb->shouldReceive('query')->andReturn(true);
			$wpdb->shouldReceive('db_connect')->andReturn(true);
		}
		parent::tearDownAfterClass();
	}
}