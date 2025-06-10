<?php
/**
 * Unit tests for SHC_Cron class.
 */
namespace SA_HYPERLINK_CRAWLER;

use SA_HYPERLINK_CRAWLER\Tracking\SHC_Cron;
use SA_HYPERLINK_CRAWLER\Tracking\SHC_Database;
use WPMedia\PHPUnit\Unit\TestCase;
use Brain\Monkey\Functions;
use Mockery;

class SHC_Cron_Test extends TestCase {
    protected function setUp() : void {
        parent::setUp();
        \Brain\Monkey\setUp();
    }

    protected function tearDown() : void {
        Mockery::close();
        \Brain\Monkey\tearDown();
        parent::tearDown();
    }

    public function test_register_adds_action() {
        $db = Mockery::mock(SHC_Database::class);
        $cron = new SHC_Cron($db);

        Functions\expect('add_action')
            ->once()
            ->with(SHC_Cron::HOOK, array($cron, 'cleanup'));

        $cron->register();
    }

    public function test_schedule_registers_event_when_missing() {
        $db = Mockery::mock(SHC_Database::class);
        $cron = new SHC_Cron($db);

        Functions\when('wp_next_scheduled')->justReturn(false);
        Functions\expect('wp_schedule_event')
            ->once()
            ->with(Mockery::type('int'), 'daily', SHC_Cron::HOOK);

        $cron->schedule();
    }

    public function test_schedule_does_nothing_if_already_scheduled() {
        $db = Mockery::mock(SHC_Database::class);
        $cron = new SHC_Cron($db);

        Functions\when('wp_next_scheduled')->justReturn(123);
        Functions\expect('wp_schedule_event')->never();

        $cron->schedule();
    }

    public function test_unschedule_removes_existing_event() {
        $db = Mockery::mock(SHC_Database::class);
        $cron = new SHC_Cron($db);

        Functions\when('wp_next_scheduled')->justReturn(123);
        Functions\expect('wp_unschedule_event')
            ->once()
            ->with(123, SHC_Cron::HOOK);

        $cron->unschedule();
    }

    public function test_cleanup_calls_database_cleanup() {
        $db = Mockery::mock(SHC_Database::class);
        $db->shouldReceive('cleanup')->once();

        $cron = new SHC_Cron($db);
        $cron->cleanup();
    }
}