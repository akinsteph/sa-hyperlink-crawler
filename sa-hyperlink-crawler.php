<?php
/**
 * Plugin Template
 *
 * @package     SA Hyperlink Crawler
 * @author      Stephen Akinola
 * @copyright   Stephen Akinola
 * @license     GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name: SA Hyperlink Crawler
 * Version:     1.0.0
 * Description: Tracks hyperlinks visible above the fold on the homepage.
 * Author:      Stephen Akinola
 */

// Define the namespace.
namespace SA_HYPERLINK_CRAWLER;

if ( ! defined( 'ABSPATH' ) ) { // If WordPress is not loaded.
	exit( 'WordPress not loaded. Can not load the plugin' );
}

define( 'SA_HYPERLINK_CRAWLER_PLUGIN_FILENAME', __FILE__ ); // Filename of the plugin, including the file.
define( 'SA_HYPERLINK_CRAWLER_VERSION', '1.0.0' ); // Version of the plugin.
define( 'SHC_DOMAIN', 'sa-hyperlink-crawler' ); // Domain of the plugin.

// Composer autoload.
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

require_once __DIR__ . '/src/support/exceptions.php';

// Load tracking classes.
require_once __DIR__ . '/src/tracking/shc-database.php';
require_once __DIR__ . '/src/tracking/shc-crawler.php';
require_once __DIR__ . '/src/tracking/shc-restendpoint.php';
require_once __DIR__ . '/src/tracking/shc-adminpage.php';
require_once __DIR__ . '/src/tracking/shc-cron.php';

// Load the plugin class.
require_once __DIR__ . '/src/shc-plugin-class.php';


/**
 * Creates the plugin object on plugins_loaded hook
 *
 * @return void
 */
function sa_hyperlink_crawler_plugin_init() {
	// Plugin initialization.
	$shc_plugin = new SHC_Plugin_Class();
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\sa_hyperlink_crawler_plugin_init' );

\register_activation_hook( __FILE__, __NAMESPACE__ . '\SHC_Plugin_Class::sa_hyperlink_crawler_activate' );
\register_uninstall_hook( __FILE__, __NAMESPACE__ . '\SHC_Plugin_Class::sa_hyperlink_crawler_uninstall' );
