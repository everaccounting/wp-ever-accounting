<?php
/**
 * Admin Invoices Page.
 *
 * @since       1.1.0
 * @subpackage  Admin/Sales/Invoices
 * @package     EverAccounting
 */
defined( 'ABSPATH' ) || exit();

function eaccounting_renders_invoices_tab() {
	$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
	if ( in_array( $requested_view, array( 'view' ), true ) && ! empty( $_GET['invoice_id'] ) ) {
		$invoice_id = isset( $_GET['invoice_id'] ) ? absint( $_GET['invoice_id'] ) : null;
		include dirname( __FILE__ ) . '/view-invoice.php';
	} elseif ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
		$invoice_id = isset( $_GET['invoice_id'] ) ? absint( $_GET['invoice_id'] ) : null;
		include dirname( __FILE__ ) . '/edit-invoice.php';
	} else {
		include dirname( __FILE__ ) . '/list-invoice.php';
	}
}

add_action( 'eaccounting_sales_tab_invoices', 'eaccounting_renders_invoices_tab' );

/**
 * Handle invoice actions.
 *
 * @since 1.1.0
 */
function eaccounting_handle_invoice_action() {
	$action     = eaccounting_clean( wp_unslash( $_POST['invoice_action'] ) );
	$invoice_id = absint( wp_unslash( $_POST['invoice_id'] ) );
	$invoice    = eaccounting_get_invoice( $invoice_id );

	if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'ea_invoice_action' ) || ! current_user_can( 'ea_manage_invoice' ) || ! $invoice->exists() ) {
		wp_die( 'no cheatin!' );
	}

	if ( ! did_action( 'eaccounting_invoice_action_' . sanitize_title( $action ) ) ) {
		do_action( 'eaccounting_invoice_action_' . sanitize_title( $action ), $invoice );
	}

	wp_redirect( add_query_arg( array( 'action' => 'view' ), eaccounting_clean( $_REQUEST['_wp_http_referer'] ) ) );
}

add_action( 'admin_post_eaccounting_invoice_action', 'eaccounting_handle_invoice_action' );
