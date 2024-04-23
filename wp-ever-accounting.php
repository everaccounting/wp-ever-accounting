<?php
/**
 * Plugin Name: Ever Accounting
 * Plugin URI: https://wpeveraccounting.com/
 * Description: Manage your business finances right from your WordPress dashboard.
 * Version: 1.2.1.4
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

define( 'EACCOUNTING_BASENAME', plugin_basename( __FILE__ ) );
define( 'EACCOUNTING_PLUGIN_FILE', __FILE__ );

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/class-wp-ever-accounting.php';
require_once __DIR__ . '/vendor/woocommerce/action-scheduler/action-scheduler.php';

// migrate plugin version.
//if ( get_option( 'eaccounting_version' ) && ! get_option( 'eac_version' ) ) {
//	update_option( 'eac_version', get_option( 'eaccounting_version' ) );
//}

/**
 * Returns the main instance of Plugin.
 *
 * @since  1.0.0
 * @return EverAccounting
 */
function eaccounting() {
	return EverAccounting::instance();
}

eaccounting();


/**
 * Main instance of EverAccounting.
 *
 * Returns the main instance of EverAccounting to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return EverAccounting\Plugin
 */
function EAC() {
	return EverAccounting\Plugin::create( __FILE__ );
}

// Instantiate the plugin.
EAC();
