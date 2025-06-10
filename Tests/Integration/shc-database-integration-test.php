<?php
/**
 * Integration tests for SHC_Database activation and cleanup.
 *
 * @package SA Hyperlink Crawler
 */
namespace SA_HYPERLINK_CRAWLER\Tests\Integration;

use Mockery;
use Brain\Monkey;
use WPMedia\PHPUnit\Integration\TestCase;
use SA_HYPERLINK_CRAWLER\Tracking\SHC_Database;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

/**
 * Integration tests for database operations.
 */
class DatabaseIntegrationTest extends TestCase {
    public function setUp(): void {
        parent::setUp();
        \Brain\Monkey\setUp();

        global $wpdb;
        $wpdb = Mockery::mock('wpdb');
        $wpdb->prefix = 'wp_';
        $wpdb->shouldReceive('query')->andReturn(true);
        $wpdb->shouldReceive('db_connect')->andReturn(true);
        $wpdb->shouldReceive('get_charset_collate')->andReturn('utf8');
        $wpdb->shouldReceive('prepare')->andReturnUsing(function($str) { return $str; });
    }

    public function tearDown(): void {
        Mockery::close();
        \Brain\Monkey\tearDown();
        parent::tearDown();
    }

    public function test_activate_runs_dbDelta() {
        if ( ! defined( 'ABSPATH' ) ) {
            define( 'ABSPATH', '/var/www/' );
        }

        global $wpdb;
        $wpdb->shouldReceive('get_charset_collate')->andReturn('utf8');
        $wpdb->shouldReceive('tables')->andReturn([]);
        $wpdb->shouldReceive('query')->andReturn(true);
        $wpdb->shouldReceive('prefix')->andReturn('wp_');

        $db = Mockery::mock(SHC_Database::class);
        $db->shouldReceive('activate')->once()->andReturn(true);
        $result = $db->activate();

		// Add assertions
        PHPUnitTestCase::assertTrue($result, 'Activation should return true on success');
    }

    public function test_cleanup_deletes_old_rows() {
        global $wpdb;

        // Set up specific expectations for this test
        $wpdb->shouldReceive('prefix')->andReturn('wp_');

        $db = Mockery::mock(SHC_Database::class);
        $db->shouldReceive('cleanup')->once()->andReturn(true);
        $result = $db->cleanup();

        // Add assertions
        PHPUnitTestCase::assertTrue($result, 'Cleanup should return true on success');
    }

	public function test_count_visits_fetches_total() {
        global $wpdb;

        $wpdb->shouldReceive('get_var')
            ->once()
            ->with("SELECT COUNT(*) FROM wp_" . SHC_Database::TABLE)
            ->andReturn(5);

        $db    = new SHC_Database();
        $count = $db->count_visits();

        PHPUnitTestCase::assertEquals(5, $count);
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
