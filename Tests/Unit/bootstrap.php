<?php

namespace WPMedia\PHPUnit;

use function WPMedia\PHPUnit\init_test_suite;

require_once dirname(dirname(__DIR__)).'/vendor/wp-media/phpunit/BootstrapManager.php';
BootstrapManager::setupConstants( $_SERVER['argv'][1] );

require_once WPMEDIA_PHPUNIT_ROOT_DIR . '/vendor/yoast/wp-test-utils/src/BrainMonkey/bootstrap.php';
require_once dirname(dirname(__DIR__)).'/vendor/wp-media/phpunit/bootstrap-functions.php';
init_test_suite();

// Bootstrap the wp-media/phpunit-{add-on}.
if (
	defined( 'WPMEDIA_PHPUNIT_ADDON_ROOT_TEST_DIR' )
	&&
	is_readable( WPMEDIA_PHPUNIT_ADDON_ROOT_TEST_DIR . '/bootstrap.php' )
) {
	require_once WPMEDIA_PHPUNIT_ADDON_ROOT_TEST_DIR . '/bootstrap.php';
}

// Bootstrap the plugin.
if ( is_readable( WPMEDIA_PHPUNIT_ROOT_TEST_DIR . '/bootstrap.php' ) ) {
	require_once WPMEDIA_PHPUNIT_ROOT_TEST_DIR . '/bootstrap.php';
}

// Load plugin classes
require_once dirname(dirname(__DIR__)) . '/src/tracking/shc-crawler.php';
require_once dirname(dirname(__DIR__)) . '/src/tracking/shc-database.php';
require_once dirname(dirname(__DIR__)) . '/src/tracking/shc-restendpoint.php';
require_once dirname(dirname(__DIR__)) . '/src/tracking/shc-adminpage.php';
require_once dirname(dirname(__DIR__)) . '/src/tracking/shc-cron.php';
require_once dirname(dirname(__DIR__)) . '/src/shc-plugin-class.php';

// Define plugin constants if not already defined
if ( ! defined( 'SA_HYPERLINK_CRAWLER_PLUGIN_FILENAME' ) ) {
    define( 'SA_HYPERLINK_CRAWLER_PLUGIN_FILENAME', dirname(dirname(__DIR__)) . '/sa-hyperlink-crawler.php' );
}

if ( ! defined( 'SA_HYPERLINK_CRAWLER_VERSION' ) ) {
    define( 'SA_HYPERLINK_CRAWLER_VERSION', '1.0.0' );
}
