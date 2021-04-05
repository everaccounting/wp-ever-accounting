<?php
/**
 * PHPUnit bootstrap file
 *
 * @package EverAccounting
 */

ini_set( 'display_errors', 'on' );
error_reporting( E_ALL );

$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['SERVER_NAME']     = '';
$PHP_SELF                   = $GLOBALS['PHP_SELF'] = $_SERVER['PHP_SELF'] = '/index.php';

$wp_tests_dir = getenv( 'WP_TESTS_DIR' ) ? getenv( 'WP_TESTS_DIR' ) : sys_get_temp_dir() . '/wordpress-tests-lib';
$tests_dir    = dirname( __FILE__ );
$plugin_dir   = dirname( $tests_dir );
$plugins_dir  = dirname( $plugin_dir );

// load test function so tests_add_filter() is available.
require_once $wp_tests_dir . '/includes/functions.php';
// load dependencies.
tests_add_filter( 'muplugins_loaded', function () use ( $plugin_dir, $plugins_dir ) {
	require_once $plugins_dir . '/wp-ever-accounting/wp-ever-accounting.php';
} );

// Setup plugin.
tests_add_filter( 'setup_theme', function () use ( $plugin_dir, $plugins_dir ) {
	define( 'WP_UNINSTALL_PLUGIN', true );
	define( 'EACCOUNTING_REMOVE_ALL_DATA', true );
	include $plugin_dir . '/uninstall.php';
	echo esc_html( 'Installing EverAccounting ...' . PHP_EOL );
	EverAccounting\Install::install();

	// Reload capabilities after install, see https://core.trac.wordpress.org/ticket/28374.
	if ( version_compare( $GLOBALS['wp_version'], '4.7', '<' ) ) {
		$GLOBALS['wp_roles']->reinit();
	} else {
		$GLOBALS['wp_roles'] = null; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		wp_roles();
	}

	echo esc_html( 'Initiating Tests ...' . PHP_EOL );
} );


// load the WP testing environment.
require_once $wp_tests_dir . '/includes/bootstrap.php';

// Includes core files.

require_once $tests_dir . '/framework/helpers/account-helper.php';
require_once $tests_dir . '/framework/helpers/currency-helper.php';
require_once $tests_dir . '/framework/helpers/category-helper.php';
require_once $tests_dir . '/framework/helpers/customer-helper.php';
require_once $tests_dir . '/framework/helpers/vendor-helper.php';

require_once $tests_dir . '/framework/class-unit-test-factory.php';
require_once $tests_dir . '/framework/class-unittestcase.php';
require_once $tests_dir . '/framework/class-rest-unittestcase.php';


