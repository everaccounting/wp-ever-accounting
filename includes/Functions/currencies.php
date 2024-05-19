<?php

use EverAccounting\Models\Currency;

defined( 'ABSPATH' ) || exit;

/**
 * Get currency symbol.
 *
 * @param mixed $currency Currency ID or object.
 *
 * @return string
 * @since 1.1.0
 */
function eac_get_currency_symbol( $currency = null ) {
	$currency = eac_get_currency( $currency );

	return $currency && $currency->symbol ? $currency->symbol : '$';
}

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

	return Currency::find( $currency );
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
 * Get currency items.
 *
 * @param array $args Query arguments.
 * @param bool  $count Whether to return the count of items.
 *
 * @return int|array|Currency[]
 * @since 1.1.0
 */
function eac_get_currencies( $args = array(), $count = false ) {
	if ( $count ) {
		return Currency::count( $args );
	}

	return Currency::results( $args );
}
