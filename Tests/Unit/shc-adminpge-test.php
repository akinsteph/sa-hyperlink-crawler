<?php
/**
 * Unit tests for SHC_AdminPage class.
 */
namespace SA_HYPERLINK_CRAWLER;

use SA_HYPERLINK_CRAWLER\Tracking\SHC_AdminPage;
use SA_HYPERLINK_CRAWLER\Tracking\SHC_Database;
use WPMedia\PHPUnit\Unit\TestCase;
use Mockery;
use Brain\Monkey\Functions;

class SHC_AdminPage_Test extends TestCase {
    protected function setUp() : void {
        parent::setUp();
        \Brain\Monkey\setUp();

        Functions\when( '__' )->returnArg(1);
        Functions\when( 'esc_html__' )->returnArg(1);
        Functions\when( 'esc_url' )->returnArg(1);
        Functions\when( 'add_query_arg' )->returnArg(1);
        Functions\when( 'current_user_can' )->justReturn(true);
    }

    protected function tearDown() : void {
        \Brain\Monkey\tearDown();
        Mockery::close();
        parent::tearDown();
    }

    public function test_render_fetches_visits_with_pagination() {
        $db = Mockery::mock(SHC_Database::class);
        $db->shouldReceive('get_visits')->once()->with(1, SHC_Database::PER_PAGE)->andReturn([]);
        $db->shouldReceive('count_visits')->once()->andReturn(0);

        $page = new SHC_AdminPage($db);

        ob_start();
        $page->render();
        $output = ob_get_clean();

        $this->assertStringContainsString('Recent Hyperlink Visits', $output);
    }
}