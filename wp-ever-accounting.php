<?php
/**
 * Plugin Name:  WP Ever Accounting
 * Description:  Manage your business finances right from your WordPress dashboard.
 * Version: 1.1.2
 * Plugin URI:   https://pluginever.com/plugins/wp-ever-accounting/
 * Author:       pluginever
 * Author URI:   https://pluginever.com/
 * Text Domain:  wp-ever-accounting
 * Domain Path: /i18n/languages/
 * Requires PHP: 7.0.0
 *
 * @package     Ever_Accounting
 * @author      everaccounting
 * @link        https://pluginever.com/plugins/wp-ever-accounting/
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

defined( 'ABSPATH' ) || exit;

// Define plugin constants.
define( 'EACCOUNTING_FILE', __FILE__ );

// Include the main plugin class.
require_once __DIR__ . '/includes/class-plugin.php';

/**
 * Returns the main instance of plugin.
 *
 * @since  1.1.3
 * @return Ever_Accounting\Plugin
 */
function eaccounting() {
	return Ever_Accounting\Plugin::instance();
}

// Kick off the plugin.
$GLOBALS['eaccounting'] = eaccounting();
