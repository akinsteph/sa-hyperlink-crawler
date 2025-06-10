<?php
/**
 * Unit tests for SHC_Database class.
 *
 * @package SA Hyperlink Crawler
 */
namespace SA_HYPERLINK_CRAWLER;

use SA_HYPERLINK_CRAWLER\Tracking\SHC_Database;
use WPMedia\PHPUnit\Unit\TestCase;
use Mockery;
use Brain\Monkey\Functions;

class SHC_Database_Test extends TestCase {
    protected $wpdb;

    protected function setUp() : void {
        parent::setUp();
        \Brain\Monkey\setUp();

        // Mock global $wpdb.
        $this->wpdb = Mockery::mock();
        $this->wpdb->prefix = 'wp_';
        global $wpdb;
        $wpdb = $this->wpdb;

        require_once dirname( dirname( __DIR__ ) ) . '/src/tracking/shc-database.php';

        // Provide a fallback for wp_json_encode when WordPress is not loaded.
        Functions\when( 'wp_json_encode' )->alias( 'json_encode' );

        if ( ! defined( 'ARRAY_A' ) ) {
            define( 'ARRAY_A', 'ARRAY_A' );
        }
    }

    protected function tearDown() : void {
        Mockery::close();
        \Brain\Monkey\tearDown();
        parent::tearDown();
    }

    public function test_insert_visit_calls_wpdb_insert() {
        $db = new SHC_Database();
        $data = [
            'links'  => ['a', 'b'],
            'width'  => 1024,
            'height' => 768,
            'time'   => '2025-06-10 12:00:00',
        ];

        $this->wpdb->shouldReceive('insert')->once()->with(
            'wp_' . SHC_Database::TABLE,
            [
                'visit_time'    => $data['time'],
                'screen_width'  => $data['width'],
                'screen_height' => $data['height'],
                'links'         => json_encode($data['links']),
            ],
            ['%s','%d','%d','%s']
        );

        $db->insert_visit($data);
    }

    public function test_get_visits_decodes_links() {
        $db = new SHC_Database();

        $this->wpdb->shouldReceive('prepare')
            ->once()
            ->with(
                Mockery::type('string'),
                'wp_' . SHC_Database::TABLE,
                Mockery::type('int'),
                Mockery::type('int')
            )
            ->andReturn('sql');
        $this->wpdb->shouldReceive('get_results')->once()->with('sql', \ARRAY_A)->andReturn([
            [
                'id' => 1,
                'visit_time' => '2025-06-10 12:00:00',
                'screen_width' => 800,
                'screen_height' => 600,
                'links' => json_encode(['x','y']),
            ],
        ]);

        $result = $db->get_visits(1);

        $this->assertIsArray($result[0]['links']);
        $this->assertSame('x', $result[0]['links'][0]);
    }

    public function test_cleanup_executes_delete_query() {
        $db = new SHC_Database();

        $this->wpdb->shouldReceive('prepare')
            ->once()
            ->with(
                Mockery::on(function($sql){return strpos($sql,'DELETE FROM')===0;}),
                'wp_' . SHC_Database::TABLE,
                Mockery::type('string')
            )
            ->andReturn('del');
        $this->wpdb->shouldReceive('query')->once()->with('del');

        $db->cleanup();
    }

    public function test_count_visits_executes_query_and_returns_int() {
        $db = new SHC_Database();

        $this->wpdb->shouldReceive('get_var')
            ->once()
            ->with("SELECT COUNT(*) FROM wp_" . SHC_Database::TABLE)
            ->andReturn(7);

        $this->assertSame(7, $db->count_visits());
    }
}