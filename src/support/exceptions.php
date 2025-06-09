<?php
/**
 *  Exception handling
 *
 * @package     SA Hyperlink Crawler
 * @since       1.0.0
 * @author      Stephen Akinola
 * @license     GPL-2.0-or-later
 */

namespace SA_HYPERLINK_CRAWLER;

// Automatically added when writing the WHOOPS section below to find the whoops functions.
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

// Register WHOOPS - See WHOOPS's github + KTC tutorial.
add_action( 'init', __NAMESPACE__ . '\load_whoops' );
/**
 * Initialize WHOOPS and registers the handler with a custom editor style
 *
 * @since 1.0.0
 *
 * @return void
 */
function load_whoops() {
	$whoops     = new Run();
	$error_page = new PrettyPageHandler();
	$error_page->setEditor( 'sublime' ); // Set a specific style to the code display.
	$whoops->pushHandler( $error_page );
	$whoops->register();
}
