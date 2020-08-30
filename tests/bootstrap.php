<?php
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['SERVER_NAME']     = '';
$PHP_SELF                   = $GLOBALS['PHP_SELF'] = $_SERVER['PHP_SELF'] = '/index.php';

$wp_tests_dir = getenv( 'WP_TESTS_DIR' ) ? getenv( 'WP_TESTS_DIR' ) : sys_get_temp_dir() . '/wordpress-tests-lib';
$tests_dir    = dirname( __FILE__ );
$plugin_dir   = dirname( $tests_dir );

require_once $wp_tests_dir . '/includes/functions.php';
function _manually_load_plugin() {
	require dirname( __FILE__ ) . '/../wp-ever-accounting.php';
}

tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $wp_tests_dir . '/includes/bootstrap.php';

activate_plugin( 'wp-ever-accounting/wp-ever-accounting.php' );

echo "Installing WP Ever Accounting...\n";

// Install Easy Digital Downloads
EAccounting_Install::install();

global $current_user, $edd_options;

$edd_options = get_option( 'edd_settings' );

$current_user = new WP_User( 1 );
$current_user->set_role( 'administrator' );
wp_update_user( array( 'ID' => 1, 'first_name' => 'Admin', 'last_name' => 'User' ) );

function _disable_reqs( $status = false, $args = array(), $url = '' ) {
	return new WP_Error( 'no_reqs_in_unit_tests', __( 'HTTP Requests disabled for unit tests', 'wp-ever-accounting' ) );
}

add_filter( 'pre_http_request', '_disable_reqs' );

// Include helpers
require_once 'framework/helpers/class-ea-helper-account.php';
require_once 'framework/helpers/class-ea-helper-contact.php';
require_once 'framework/helpers/class-ea-helper-category.php';
require_once 'framework/helpers/class-ea-helper-currency.php';
require_once 'framework/helpers/class-ea-helper-revenue.php';
require_once 'framework/helpers/class-ea-helper-transfer.php';
require_once 'framework/class-ea-unit-test-case.php';
