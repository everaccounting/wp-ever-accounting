<?php
/**
 * PHPUnit bootstrap file.
 *
 * @package Ever_Accounting
 */

ini_set( 'display_errors', 'on' );
error_reporting( E_ALL );

$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['SERVER_NAME']     = '';
$PHP_SELF                   = $GLOBALS['PHP_SELF'] = $_SERVER['PHP_SELF'] = '/index.php';

$wp_tests_dir = getenv( 'WP_TESTS_DIR' );
$tests_dir    = __DIR__;
$plugin_dir   = dirname( $tests_dir, 2 );
$plugins_dir  = dirname( $plugin_dir );

if ( ! $wp_tests_dir ) {
	$wp_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! file_exists( "{$wp_tests_dir}/includes/functions.php" ) ) {
	echo "Could not find {$wp_tests_dir}/includes/functions.php, have you run bin/install-wp-tests.sh ?" . PHP_EOL; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	exit( 1 );
}

// Give access to tests_add_filter() function.
require_once "{$wp_tests_dir}/includes/functions.php";

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {
	require dirname( __DIR__, 2 ) . '/wp-ever-accounting.php';
}

tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Setup plugin.
tests_add_filter( 'setup_theme', function () use ( $plugin_dir, $plugins_dir ) {
	define( 'WP_UNINSTALL_PLUGIN', true );
	include $plugin_dir . '/uninstall.php';
	\Ever_Accounting\Lifecycle::uninstall();
	\Ever_Accounting\Lifecycle::install();
	// Reload capabilities after install, see https://core.trac.wordpress.org/ticket/28374.
	if ( version_compare( $GLOBALS['wp_version'], '4.7', '<' ) ) {
		$GLOBALS['wp_roles']->reinit();
	} else {
		$GLOBALS['wp_roles'] = null; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		wp_roles();
	}
	echo esc_html( 'Installing Plugin ...' . PHP_EOL );
	echo esc_html( 'Initiating Tests ...' . PHP_EOL );
} );

// Start up the WP testing environment.
require "{$wp_tests_dir}/includes/bootstrap.php";
