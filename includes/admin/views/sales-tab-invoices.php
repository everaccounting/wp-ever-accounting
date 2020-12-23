<?php
/**
 * Page : Sales
 * Tab: Invoice
 *
 * @var array $tabs
 * @var string $current_tab
 */
defined( 'ABSPATH' ) || exit();


$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
if ( in_array( $requested_view, array( 'view' ), true ) && ! empty( $_GET['invoice_id'] ) ) {
	$invoice_id = isset( $_GET['invoice_id'] ) ? absint( $_GET['invoice_id'] ) : null;
	include dirname( __FILE__ ) . '/invoices/view-invoice.php';
} elseif ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
	$invoice_id = isset( $_GET['invoice_id'] ) ? absint( $_GET['invoice_id'] ) : null;
	include dirname( __FILE__ ) . '/invoices/edit-invoice.php';
} else {
	include dirname( __FILE__ ) . '/invoices/list-invoice.php';
}

