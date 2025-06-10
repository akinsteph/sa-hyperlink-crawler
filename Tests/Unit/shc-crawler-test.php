<?php
/**
 * Unit tests for the SHC_Crawler class.
 *
 * @package SA Hyperlink Crawler
 */
namespace SA_HYPERLINK_CRAWLER;

use SA_HYPERLINK_CRAWLER\Tracking\SHC_Crawler;
use WPMedia\PHPUnit\Unit\TestCase;
use Brain\Monkey\Functions;

/**
 * @covers \SA_HYPERLINK_CRAWLER\Tracking\SHC_Crawler
 */
class SHC_Crawler_Test extends TestCase {

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

        // Manually load the class under test as Composer autoloading does not handle the lowercase filename used in the plugin sources.
        require_once dirname( dirname( __DIR__ ) ) . '/src/tracking/shc-crawler.php';
    }

    protected function tearDown() : void {
        \Brain\Monkey\tearDown();
        parent::tearDown();
    }

    public function test_register_adds_enqueue_action() {
        $crawler = new SHC_Crawler();

        Functions\expect( 'add_action' )
            ->once()
            ->with( 'wp_enqueue_scripts', array( $crawler, 'enqueue' ) );

        $crawler->register();
    }

    public function test_enqueue_does_nothing_if_not_front_page() {
        Functions\when( 'is_front_page' )->justReturn( false );
        Functions\expect( 'wp_enqueue_script' )->never();
        Functions\expect( 'wp_localize_script' )->never();

        $crawler = new SHC_Crawler();
        $crawler->enqueue();
    }

    public function test_enqueue_adds_script_and_localizes_data() {
        Functions\when( 'is_front_page' )->justReturn( true );
        Functions\when( 'plugin_dir_url' )->justReturn( 'http://example.com/' );
        Functions\when( 'rest_url' )->justReturn( 'http://example.com/wp-json/sa-hyperlink-crawler/v1/visit' );
        Functions\when( 'wp_create_nonce' )->justReturn( 'test-nonce' );

        // Mock WP_DEBUG constant
        if (!defined('WP_DEBUG')) {
            define('WP_DEBUG', false);
        }

        // Set up expectations before calling the method
        Functions\expect( 'wp_enqueue_script' )
            ->once()
            ->with(
                'shc-script',
                'http://example.com/assets/js/shc-crawler.js',
                array( 'jquery' ),
                '1.0.0',
                true
            );

        Functions\expect( 'wp_localize_script' )
            ->once()
            ->with(
                'shc-script',
                'shcData',
                array(
                    'endpoint' => 'http://example.com/wp-json/sa-hyperlink-crawler/v1/visit',
                    'nonce' => 'test-nonce',
                    'debug' => false
                )
            );

        $crawler = new SHC_Crawler();
        $crawler->enqueue();
    }

    public function test_get_script_data() {
        $crawler = new SHC_Crawler();

        Functions\when( 'rest_url' )->justReturn( '/endpoint' );
        Functions\when( 'wp_create_nonce' )->justReturn( 'abc' );


        $data = $crawler->get_script_data();

        $this->assertSame( '/endpoint', $data['endpoint'] );
        $this->assertSame( 'abc', $data['nonce'] );
        $this->assertFalse( $data['debug'] );
    }
}
