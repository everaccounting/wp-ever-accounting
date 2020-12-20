<?php
/**
 * Admin Revenues Page.
 *
 * @since       1.0.2
 * @subpackage  Admin/Sales/Revenues
 * @package     EverAccounting
 */
defined( 'ABSPATH' ) || exit();


function eaccounting_render_customers_tab() {
	$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
	if ( in_array( $requested_view, array( 'view' ), true ) && ! empty( $_GET['customer_id'] ) ) {
		$customer_id = isset( $_GET['customer_id'] ) ? absint( $_GET['customer_id'] ) : null;
		include dirname( __FILE__ ) . '/view/html-customer.php';
	} elseif ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
		$customer_id = isset( $_GET['customer_id'] ) ? absint( $_GET['customer_id'] ) : null;
		include dirname( __FILE__ ) . '/view/html-customer-edit.php';
	} else {
		include dirname( __FILE__ ) . '/view/html-customer-list.php';
	}
}

add_action( 'eaccounting_sales_tab_customers', 'eaccounting_render_customers_tab' );
