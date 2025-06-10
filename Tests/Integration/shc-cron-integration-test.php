<?php
/**
 * Integration tests for SHC_Cron functionality.
 */
namespace SA_HYPERLINK_CRAWLER\Tests\Integration;

use WPMedia\PHPUnit\Integration\TestCase;
use SA_HYPERLINK_CRAWLER\Tracking\SHC_Cron;
use SA_HYPERLINK_CRAWLER\Tracking\SHC_Database;
use Brain\Monkey\Functions;
use Mockery;

class CronIntegrationTest extends TestCase {
    public function setUp(): void {
        parent::setUp();
        \Brain\Monkey\setUp();
    }

    public function tearDown(): void {
        Mockery::close();
        \Brain\Monkey\tearDown();
        parent::tearDown();
    }

    public function test_register_adds_hook() {
        $db = Mockery::mock(SHC_Database::class);
        $cron = new SHC_Cron($db);

        Functions\expect('add_action')
            ->once()
            ->with(SHC_Cron::HOOK, array($cron, 'cleanup'));

        $cron->register();
    }

    public function test_schedule_creates_daily_event() {
        $db = Mockery::mock(SHC_Database::class);
        $cron = new SHC_Cron($db);

        Functions\when('wp_next_scheduled')->justReturn(false);
        Functions\when('time')->justReturn(100);
        Functions\expect('wp_schedule_event')
            ->once()
            ->with(100, 'daily', SHC_Cron::HOOK);

        $cron->schedule();
    }
}