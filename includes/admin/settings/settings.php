<?php
/**
 * Admin Settings Page.
 *
 * @since       1.0.2
 * @subpackage  Admin/Settings
 * @package     EverAccounting
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit();

require_once dirname( __FILE__ ) . '/currencies/currencies.php';
require_once dirname( __FILE__ ) . '/categories/categories.php';
require_once dirname( __FILE__ ) . '/taxes/taxes.php';

function eaccounting_admin_settings_page() {
	\EverAccounting\Admin\Settings::output();
}
