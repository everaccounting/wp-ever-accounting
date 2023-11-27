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
 * @since 1.1.0
 *
 * @param mixed $invoice Invoice ID or post object.
 * @return EverAccounting\Models\Invoice|null
 */
function eac_get_invoice( $invoice ) {
	return Invoice::get( $invoice );
}

/**
 *  Create new invoice programmatically.
 *  Returns a new invoice object on success.
 *
 * @since 1.1.0
 * @param  array $args   Invoice arguments.
 * @param bool  $wp_error Whether to return a WP_Error on failure.
 *
 * @return Invoice|false|int|WP_Error
 */
function eac_insert_invoice( $args, $wp_error = true ) {
	return Invoice::insert( $args, $wp_error );
}

/**
 * Delete an invoice.
 *
 * @since 1.1.0
 *
 * @param int $invoice_id Invoice ID.
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
 * @since 1.1.0
 *
 * @param array $args Query arguments.
 * @param bool  $count Whether to return count or items.
 * @return array|Invoice[]|int|
 */
function eac_get_invoices( $args = array(), $count = false ) {
	$defaults = array(
		'limit'   => 20,
		'offset'  => 0,
		'orderby' => 'id',
		'order'   => 'DESC',
		'fields'  => 'all',
	);

	$args = wp_parse_args( $args, $defaults );

	if ( $count ) {
		return Invoice::count( $args );
	}

	return Invoice::query( $args );
}


/**
 * Main function for returning bill.
 *
 * @since 1.1.0
 *
 * @param mixed $bill Bill ID or object.
 * @return Bill|null
 */
function eac_get_bill( $bill ) {
	return Bill::get( $bill );
}

/**
 *  Create new bill programmatically.
 *  Returns a new bill object on success.
 *
 * @since 1.1.0
 * @param  array $args   Bill data.
 * @param bool  $wp_error Optional. Whether to return a WP_Error on failure.
 *
 * @return Bill|false|int|WP_Error
 */
function eac_insert_bill( $args, $wp_error = true ) {
	return Bill::insert( $args, $wp_error );
}

/**
 * Delete an bill.
 *
 * @since 1.1.0
 *
 * @param int $bill_id Bill ID.
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
 * @since 1.1.0
 *
 * @param array $args Query arguments.
 * @param bool  $count Whether to return count or items.
 *
 * @return array|Bill[]|int|
 */
function eac_get_bills( $args = array(), $count = false ) {
	$defaults = array(
		'limit'   => 20,
		'offset'  => 0,
		'orderby' => 'id',
		'order'   => 'DESC',
		'fields'  => 'all',
	);

	$args = wp_parse_args( $args, $defaults );

	if ( $count ) {
		return Bill::count( $args );
	}

	return Bill::query( $args );
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
