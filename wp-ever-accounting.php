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

/**
 * Autoload function.
 *
 * @param string $class_name Class name.
 *
 * @since 1.1.6
 * @return void
 */
function eac_autoloader( $class_name ) {
	// Bail out if the class name doesn't start with our prefix.
	if ( strpos( $class_name, 'EverAccounting\\' ) !== 0 ) {
		return;
	}

	// Remove the prefix from the class name.
	$class_name = substr( $class_name, strlen( 'EverAccounting\\' ) );

	// Replace the namespace separator with the directory separator.
	$class_name = str_replace( '\\', DIRECTORY_SEPARATOR, $class_name );

	// Add the .php extension.
	$class_name = $class_name . '.php';

	$file_paths = array(
		__DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $class_name,
		__DIR__ . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . $class_name,
	);

	foreach ( $file_paths as $file_path ) {
		if ( file_exists( $file_path ) ) {
			require_once $file_path;
			break;
		}
	}
}

spl_autoload_register( 'eac_autoloader' );


/**
 * Main EverAccounting Instance.
 *
 * Ensures only one instance of EverAccounting is loaded or can be loaded.
 *
 * @since 1.0.0
 * @since 1.1.6 renamed from eaccounting() to EverAccounting().
 * @return EverAccounting\Plugin
 */
function EAC() {
	return \EverAccounting\Plugin::create( __FILE__ );
}

EAC();
