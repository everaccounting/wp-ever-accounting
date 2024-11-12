<?php
/**
 * Plugin Name: Ever Accounting
 * Plugin URI: https://wpeveraccounting.com/
 * Description: Manage your business finances right from your WordPress dashboard.
 * Version: 2.0.0
 * Author: EAccounting
 * Author URI: https://wpeveraccounting.com/
 * Requires at least: 4.7.0
 * Tested up to: 6.5
 * Text Domain: wp-ever-accounting
 * Domain Path: /languages/
 * License: GPL2+
 *
 * @package wp-ever-accounting
 */

defined( 'ABSPATH' ) || exit();

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/vendor/namespaced/autoload.php';

// Migrate legacy version.
if ( get_option( 'eaccounting_version' ) ) {
	update_option( 'eac_version', get_option( 'eaccounting_version' ) );
	update_option( 'eac_install_date', get_option( 'eaccounting_install_date', wp_date( 'U' ) ) );
	delete_option( 'eaccounting_version' );
}

/**
 * Main instance of EverAccounting.
 *
 * @since  1.0.0
 * @return EverAccounting\Plugin
 */
function EAC() { // phpcs:ignore
	return EverAccounting\Plugin::create( __FILE__ );
}

// Instantiate the plugin.
EAC();
