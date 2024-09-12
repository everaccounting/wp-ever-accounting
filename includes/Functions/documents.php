<?php
/**
 * EverAccounting Document Functions.
 *
 * All document related function of the plugin.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

use EverAccounting\Models\Bill;
use EverAccounting\Models\Invoice;

defined( 'ABSPATH' ) || exit;


/**
 * Main function for returning invoice.
 *
 * @param mixed $invoice Invoice ID or post object.
 *
 * @since 1.1.0
 *
 * @return EverAccounting\Models\Invoice|null
 */
function eac_get_invoice( $invoice ) {
	return Invoice::find( $invoice );
}

/**
 *  Create new invoice programmatically.
 *  Returns a new invoice object on success.
 *
 * @param array $args Invoice arguments.
 * @param bool  $wp_error Whether to return a WP_Error on failure.
 *
 * @since 1.1.0
 * @return Invoice|false|int|WP_Error
 */
function eac_insert_invoice( $args, $wp_error = true ) {
	return Invoice::insert( $args, $wp_error );
}

/**
 * Delete an invoice.
 *
 * @param int $invoice_id Invoice ID.
 *
 * @since 1.1.0
 *
 * @return bool
 */
function eac_delete_invoice( $invoice_id ) {
	$invoice = eac_get_invoice( $invoice_id );

	return $invoice && $invoice->delete();
}

/**
 * Get invoices.
 *
 * @param array $args Query arguments.
 * @param bool  $count Whether to return count or items.
 *
 * @since 1.1.0
 *
 * @return array|Invoice[]|int|
 */
function eac_get_invoices( $args = array(), $count = false ) {
	if ( $count ) {
		return Invoice::count( $args );
	}

	return Invoice::results( $args );
}


/**
 * Main function for returning bill.
 *
 * @param mixed $bill Bill ID or object.
 *
 * @since 1.1.0
 *
 * @return Bill|null
 */
function eac_get_bill( $bill ) {
	return Bill::find( $bill );
}

/**
 *  Create new bill programmatically.
 *  Returns a new bill object on success.
 *
 * @param array $args Bill data.
 * @param bool  $wp_error Optional. Whether to return a WP_Error on failure.
 *
 * @since 1.1.0
 * @return Bill|false|int|WP_Error
 */
function eac_insert_bill( $args, $wp_error = true ) {
	return Bill::insert( $args, $wp_error );
}

/**
 * Delete an bill.
 *
 * @param int $bill_id Bill ID.
 *
 * @since 1.1.0
 *
 * @return bool
 */
function eac_delete_bill( $bill_id ) {
	$bill = eac_get_bill( $bill_id );

	return $bill && $bill->delete();
}

/**
 * Get bills.
 *
 * @param array $args Query arguments.
 * @param bool  $count Whether to return count or items.
 *
 * @since 1.1.0
 *
 * @return array|Bill[]|int|
 */
function eac_get_bills( $args = array(), $count = false ) {
	if ( $count ) {
		return Bill::count( $args );
	}

	return Bill::results( $args );
}

/**
 * Get bill statuses.
 *
 * @return mixed|void
 */
function eac_get_bill_statuses() {
	$statuses = array(
		'draft'     => esc_html__( 'Draft', 'wp-ever-accounting' ),
		'received'  => esc_html__( 'Received', 'wp-ever-accounting' ),
		'partial'   => esc_html__( 'Partial', 'wp-ever-accounting' ),
		'paid'      => esc_html__( 'Paid', 'wp-ever-accounting' ),
		'overdue'   => esc_html__( 'Overdue', 'wp-ever-accounting' ),
		'cancelled' => esc_html__( 'Cancelled', 'wp-ever-accounting' ),
	);

	return apply_filters( 'ever_accounting_bill_statuses', $statuses );
}

/**
 * Get invoice statuses.
 *
 * @return mixed|void
 */
function eac_get_invoice_statuses() {
	$statuses = array(
		'draft'     => esc_html__( 'Draft', 'wp-ever-accounting' ),
		'sent'      => esc_html__( 'Sent', 'wp-ever-accounting' ),
		'partial'   => esc_html__( 'Partial', 'wp-ever-accounting' ),
		'paid'      => esc_html__( 'Paid', 'wp-ever-accounting' ),
		'overdue'   => esc_html__( 'Overdue', 'wp-ever-accounting' ),
		'cancelled' => esc_html__( 'Cancelled', 'wp-ever-accounting' ),
	);

	return apply_filters( 'ever_accounting_invoice_statuses', $statuses );
}


/**
 * Get invoice columns.
 *
 * @since 1.1.0
 * @return array
 */
function eac_get_invoice_columns() {
	$columns = array(
		'item'     => get_option( 'eac_invoice_col_item_label', esc_html__( 'Item', 'wp-ever-accounting' ) ),
		'price'    => get_option( 'eac_invoice_col_price_label', esc_html__( 'Price', 'wp-ever-accounting' ) ),
		'quantity' => get_option( 'eac_invoice_col_quantity_label', esc_html__( 'Quantity', 'wp-ever-accounting' ) ),
		'tax'      => get_option( 'eac_invoice_col_tax_label', esc_html__( 'Tax', 'wp-ever-accounting' ) ),
		'subtotal' => get_option( 'eac_invoice_col_subtotal_label', esc_html__( 'Subtotal', 'wp-ever-accounting' ) ),
	);

	return apply_filters( 'ever_accounting_invoice_columns', $columns );
}

/**
 * Get bill columns.
 *
 * @since 1.1.0
 * @return array
 */
function eac_get_bill_columns() {
	$columns = array(
		'item'         => esc_html__( 'Item', 'wp-ever-accounting' ),
		'price'        => esc_html__( 'Price', 'wp-ever-accounting' ),
		'quantity'     => esc_html__( 'Quantity', 'wp-ever-accounting' ),
		'subtotal_tax' => esc_html__( 'Tax', 'wp-ever-accounting' ),
		'subtotal'     => esc_html__( 'Subtotal', 'wp-ever-accounting' ),
	);

	return apply_filters( 'ever_accounting_bill_columns', $columns );
}
