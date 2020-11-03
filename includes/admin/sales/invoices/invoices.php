<?php
/**
 * Admin Invoices Page.
 *
 * @package     EverAccounting
 * @subpackage  Admin/Sales/Invoices
 * @since       1.0.2
 */
defined( 'ABSPATH' ) || exit();

function eaccounting_sales_tab_invoices() {
	echo '<div id="eaccounting-app"></div>';
}
add_action( 'eaccounting_sales_tab_invoices', 'eaccounting_sales_tab_invoices' );
