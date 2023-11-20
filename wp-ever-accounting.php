<?php
/**
 * Plugin Name: Ever Accounting
 * Plugin URI: https://wpeveraccounting.com/
 * Description: Manage your business finances right from your WordPress dashboard.
 * Version: 1.1.6
 * Author: everaccounting
 * Author URI: https://wpeveraccounting.com/
 * Requires at least: 4.7.0
 * Tested up to: 6.1
 * Text Domain: wp-ever-accounting
 * Domain Path: /languages/
 * License: GPL2+
 *
 * @package wp-ever-accounting
 */

defined( 'ABSPATH' ) || exit();

// Autoload function.
spl_autoload_register( function ( $class ) {
	$prefix = 'EverAccounting\\';
	$len    = strlen( $prefix );

	// Bail out if the class name doesn't start with our prefix.
	if ( strncmp( $prefix, $class, $len ) !== 0 ) {
		return;
	}

	// Remove the prefix from the class name.
	$relative_class = substr( $class, $len );
	// Replace the namespace separator with the directory separator.
	$file = str_replace( '\\', DIRECTORY_SEPARATOR, $relative_class ) . '.php';

	// Look for the file in the src and lib directories.
	$file_paths = array(
		__DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $file,
		__DIR__ . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . $file,
	);

	foreach ( $file_paths as $file_path ) {
		if ( file_exists( $file_path ) ) {
			require_once $file_path;
			break;
		}
	}
} );


/**
 * Main EverAccounting Instance.
 *
 * Ensures only one instance of EverAccounting is loaded or can be loaded.
 *
 * @since 1.0.0
 * @since 1.1.6 renamed from eaccounting() to EAC().
 * @return EverAccounting\Plugin
 */
function EAC() {
	return \EverAccounting\Plugin::create( __FILE__ );
}

EAC();
