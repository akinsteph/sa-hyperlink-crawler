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

    public function tearDown(): void {
        Mockery::close();
        parent::tearDown();
    }

    public function testRegistersRouteOnRestApiInit() {
        $db = Mockery::mock(SHC_Database::class);
        $endpoint = new SHC_RestEndpoint( $db );

        Functions\expect( 'register_rest_route' )
            ->once()
            ->with( 'sa-hyperlink-crawler/v1', '/visit', Mockery::type('array') );

        $endpoint->register();
        do_action( 'rest_api_init' );
    }
}