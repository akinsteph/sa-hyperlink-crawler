<?php
/**
 *  Implements the Integration test set for the plugin management.
 *
 * @package     SA Hyperlink Crawler
 * @since       1.0.0
 * @author      Stephen Akinola
 * @license     GPL-2.0-or-later
 */

namespace SA_HYPERLINK_CRAWLER;

require_once dirname(dirname(__DIR__)) . "/sa-hyperlink-crawler.php";

use WPMedia\PHPUnit\Integration\TestCase;
use Brain\Monkey\Functions;

/**
 * Integration test set for the Webplan Updater Cron Class.
 */
class SA_Hyperlink_Crawler_Plugin_Integration_Test extends TestCase {

	/**
     * Checks the call to plugin init function on plugin_loaded.
     */
    public function testShouldLoadPlugin() {
		  Functions\expect(__NAMESPACE__ . '\sa_hyperlink_crawler_plugin_init')->once();
		  do_action('plugins_loaded');
    }
}
