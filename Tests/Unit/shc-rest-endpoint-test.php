<?php
/**
 * Unit tests for SHC_RestEndpoint class.
 *
 * @package SA Hyperlink Crawler
 */
namespace SA_HYPERLINK_CRAWLER;

use Mockery;
use Brain\Monkey\Functions;
use WPMedia\PHPUnit\Unit\TestCase;
use SA_HYPERLINK_CRAWLER\Tracking\SHC_RestEndpoint;
use SA_HYPERLINK_CRAWLER\Tracking\SHC_Database;

/**
 * Unit test suite for SHC_RestEndpoint.
 */
class SHC_RestEndpoint_Test extends TestCase {

    protected function setUp() : void {
        parent::setUp();
        \Brain\Monkey\setUp();

        if ( ! defined( 'SA_HYPERLINK_CRAWLER_PLUGIN_FILENAME' ) ) {
            define( 'SA_HYPERLINK_CRAWLER_PLUGIN_FILENAME', __FILE__ );
        }
        if ( ! defined( 'SA_HYPERLINK_CRAWLER_VERSION' ) ) {
            define( 'SA_HYPERLINK_CRAWLER_VERSION', '1.0.0' );
        }

        if ( ! defined( 'WP_DEBUG' ) ) {
            define( 'WP_DEBUG', false );
        }

        // Mock WordPress translation functions
        Functions\when( '__' )->returnArg( 1 );
        Functions\when( '_e' )->returnArg( 1 );
        Functions\when( 'esc_html__' )->returnArg( 1 );
        Functions\when( 'esc_html_e' )->returnArg( 1 );

        // Manually load the class under test as Composer autoloading does not handle the lowercase filename used in the plugin sources.
        require_once dirname( dirname( __DIR__ ) ) . '/src/tracking/shc-restendpoint.php';
    }

    public function tearDown(): void {
        Mockery::close();
        parent::tearDown();
    }

    public function testPermissionsCheckRejectsInvalidNonce() {
        $request = \Mockery::mock( 'WP_REST_Request' );
        $request->shouldReceive( 'get_param' )
            ->with( 'nonce' )
            ->andReturn( 'invalid-nonce' );

        Functions\expect( 'wp_verify_nonce' )
            ->once()
            ->with( 'invalid-nonce', 'shc-rest' )
            ->andReturn( false );

        $db = \Mockery::mock( 'SA_HYPERLINK_CRAWLER\Tracking\SHC_Database' );
        $endpoint = new SHC_RestEndpoint( $db );

        $result = $endpoint->permissions_check( $request );

        $this->assertInstanceOf( \WP_Error::class, $result );
        $this->assertEquals( 'rest_forbidden', $result->code );
    }

    public function testPermissionsCheckAcceptsValidNonce() {
        $request = \Mockery::mock( 'WP_REST_Request' );
        $request->shouldReceive( 'get_param' )
            ->with( 'nonce' )
            ->andReturn( 'valid-nonce' );

        Functions\expect( 'wp_verify_nonce' )
            ->once()
            ->with( 'valid-nonce', 'shc-rest' )
            ->andReturn( true );

        $db = \Mockery::mock( 'SA_HYPERLINK_CRAWLER\Tracking\SHC_Database' );
        $endpoint = new SHC_RestEndpoint( $db );

        $result = $endpoint->permissions_check( $request );

        $this->assertTrue( $result );
    }

    public function testHandleVisitPersistsData() {
        Functions\when( 'current_time' )->justReturn( '2025-06-10 12:00:00' );

        $db = Mockery::mock(SHC_Database::class);
        $db->shouldReceive( 'insert_visit' )->once()->with( [
            'links'  => ['a','b'],
            'width'  => 800,
            'height' => 600,
            'time'   => '2025-06-10 12:00:00',
        ] );

        $endpoint = new SHC_RestEndpoint( $db );

        $request = Mockery::mock('WP_REST_Request');
        $request->shouldReceive('get_param')->with('links')->andReturn(['a','b']);
        $request->shouldReceive('get_param')->with('width')->andReturn(800);
        $request->shouldReceive('get_param')->with('height')->andReturn(600);

        $response = $endpoint->handle_visit( $request );
        $this->assertInstanceOf( \WP_REST_Response::class, $response );
        $this->assertEquals(201, $response->get_status());
        $this->assertEquals(['success' => true], $response->get_data());
    }
}
