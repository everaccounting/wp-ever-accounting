<?php
/**
 * Admin Payments Page.
 *
 * @package     EverAccounting
 * @subpackage  Admin/Expenses/Payments
 * @since       1.0.2
 */
defined( 'ABSPATH' ) || exit();

function eaccounting_renders_payments_tab() {
	$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
	if ( in_array( $requested_view, array( 'view' ), true ) && ! empty( $_GET['payment_id'] ) ) {
		$payment_id = isset( $_GET['payment_id'] ) ? absint( $_GET['payment_id'] ) : null;
		include dirname( __FILE__ ) . '/view-payment.php';
	} elseif ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
		$payment_id = isset( $_GET['payment_id'] ) ? absint( $_GET['payment_id'] ) : null;
		include dirname( __FILE__ ) . '/edit-payment.php';
	} else {
		include dirname( __FILE__ ) . '/list-payment.php';
	}

}

add_action( 'eaccounting_expenses_tab_payments', 'eaccounting_renders_payments_tab' );
