<?php

use EverAccounting\Models\Currency;

defined( 'ABSPATH' ) || exit;

/**
 * Get currency.
 *
 * @param mixed $currency Currency ID or object.
 *
 * @since 1.1.0
 */
function eac_get_currency( $currency = null ) {
	if ( empty( $currency ) ) {
		$currency = eac_get_base_currency();
	}

	return Currency::get( $currency );
}

/**
 * Insert a currency.
 *
 * @param array $data Currency data.
 * @param bool  $wp_error Whether to return false or WP_Error on failure.
 *
 * @return int|\WP_Error|Currency|bool The value 0 or WP_Error on failure. The Currency object on success.
 * @since 1.1.0
 */
function eac_insert_currency( $data = array(), $wp_error = true ) {
	return Currency::insert( $data, $wp_error );
}

/**
 * Delete a currency.
 *
 * @param int $currency Currency ID.
 *
 * @return bool
 * @since 1.1.0
 */
function eac_delete_currency( $currency ) {
	$currency = eac_get_currency( $currency );
	if ( ! $currency ) {
		return false;
	}

	return $currency->delete();
}

/**
 * Get currency items.
 *
 * @param array $args Query arguments.
 * @param bool  $count Whether to return the count of items.
 *
 * @return int|array|Currency[]
 * @since 1.1.0
 */
function eac_get_currencies( $args = array(), $count = false ) {
	$defaults = array(
		'limit'   => 20,
		'offset'  => 0,
		'orderby' => 'id',
		'order'   => 'DESC',
		'fields'  => 'all',
	);

	$args = wp_parse_args( $args, $defaults );

	if ( $count ) {
		return Currency::count( $args );
	}

	return Currency::query( $args );
}