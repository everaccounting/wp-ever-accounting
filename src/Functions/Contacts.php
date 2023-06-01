<?php
/**
 * EverAccounting Contact Functions.
 *
 * Contact related functions.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

use EverAccounting\Models\Customer;
use EverAccounting\Models\Vendor;

defined( 'ABSPATH' ) || exit();

/**
 * Get contact types.
 *
 * @return array
 * @since 1.1.0
 */
function eac_get_contact_types() {
	return apply_filters(
		'ever_accounting_contact_types',
		array(
			'customer' => esc_html__( 'Customer', 'wp-ever-accounting' ),
			'vendor'   => esc_html__( 'Vendor', 'wp-ever-accounting' ),
		)
	);
}


/**
 * Get customer.
 *
 * @param mixed  $customer Customer ID or object.
 * @param string $column Optional. Column to get. Default null.
 * @param array  $args Optional. Additional arguments. Default empty array.
 * @return Customer|null
 * @since 1.1.0
 */
function eac_get_customer( $customer, $column = null, $args = array() ) {
	return Customer::get( $customer, $column, $args );
}

/**
 *  Create new customer programmatically.
 *
 *  Returns a new customer object on success.
 *
 * @param array $args   Customer data.
 * @param bool  $wp_error Optional. Whether to return a WP_Error on failure. Default false.
 *
 * @return EverAccounting\Models\Customer|\WP_Error|bool
 * @since 1.1.0
 */
function eac_insert_customer( $args, $wp_error = true ) {
	return Customer::insert( $args, $wp_error );
}

/**
 * Delete a customer.
 *
 * @param int $customer_id Customer ID.
 *
 * @return bool
 * @since 1.1.0
 */
function eac_delete_customer( $customer_id ) {
	$customer = eac_get_customer( $customer_id );

	if ( ! $customer ) {
		return false;
	}

	return $customer->delete();
}

/**
 * Get customers items.
 *
 * @param array $args Query args.
 * @param bool  $count Whether to return count or items.
 *
 * @return array|int|Customer[]
 * @since 1.1.0
 */
function eac_get_customers( $args = array(), $count = false ) {
	$defaults = array(
		'limit'   => 20,
		'offset'  => 0,
		'orderby' => 'id',
		'order'   => 'DESC',
		'fields'  => 'all',
	);

	$args = wp_parse_args( $args, $defaults );

	if ( $count ) {
		return Customer::count( $args );
	}

	return Customer::query( $args );
}

/**
 * Get vendor.
 *
 * @param mixed  $vendor Vendor ID or object.
 * @param string $column Optional. Column to get. Default null.
 * @param array  $args Optional. Additional arguments. Default empty array.
 * @return Vendor|null
 * @since 1.1.0
 */
function eac_get_vendor( $vendor, $column = null, $args = array() ) {
	return Vendor::get( $vendor, $column, $args );
}

/**
 *  Create new vendor programmatically.
 *
 *  Returns a new vendor object on success.
 *
 * @param array $args Vendor data.
 * @param bool  $wp_error Optional. Whether to return a WP_Error on failure. Default false.
 *
 * @return EverAccounting\Models\Vendor|\WP_Error|bool
 * @since 1.1.0
 */
function eac_insert_vendor( $args, $wp_error = true ) {
	return Vendor::insert( $args, $wp_error );
}

/**
 * Delete a vendor.
 *
 * @param int $vendor_id Vendor ID.
 *
 * @return bool
 * @since 1.1.0
 */
function eac_delete_vendor( $vendor_id ) {
	$vendor = eac_get_vendor( $vendor_id );

	if ( ! $vendor ) {
		return false;
	}

	return $vendor->delete();
}

/**
 * Get vendors items.
 *
 * @param array $args Query arguments.
 * @param bool  $count Whether to return a count (true) instead of results. Default false.
 *
 * @return array|int|Vendor[]
 * @since 1.1.0
 */
function eac_get_vendors( $args = array(), $count = false ) {
	$defaults = array(
		'limit'   => 20,
		'offset'  => 0,
		'orderby' => 'id',
		'order'   => 'DESC',
		'fields'  => 'all',
	);

	$args = wp_parse_args( $args, $defaults );

	if ( $count ) {
		return Vendor::count( $args );
	}

	return Vendor::query( $args );
}


/**
 * Get contact.
 *
 * @param mixed  $contact Contact ID or object.
 * @param string $column Optional. Column to get. Default null.
 * @param array  $args Optional. Additional arguments. Default empty array.
 */
function eac_get_contact( $contact, $column = null, $args = array() ) {
	return \EverAccounting\Models\Contact::get( $contact, $column, $args );
}
