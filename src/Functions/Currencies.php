<?php

use EverAccounting\Models\Currency;

defined( 'ABSPATH' ) || exit;

/**
 * Main function for returning currency.

 * @param mixed  $currency Currency object, code or ID.
 * @param string $column Optional. Column to get. Default null.
 * @param array  $args Optional. Additional arguments. Default empty array.
 * @since 1.1.0
 * @return Currency|null
 */
function eac_get_currency( $currency, $column = null, $args = array() ) {
	return Currency::get( $currency, $column, $args );
}

/**
 * Get currency rate.
 *
 * @param string $currency Currency code.
 *
 * @return mixed|null
 * @since 1.1.0
 */
function eac_get_currency_rate( $currency ) {
	$currency = eac_get_currency( $currency );
	if ( ! $currency ) {
		return 1;
	}

	return $currency->get_rate();
}


/**
 *  Create new currency programmatically.
 *
 *  Returns a new currency object on success.
 *
 * @param array $args   Currency arguments.
 * @param bool  $wp_error Optional. Whether to return a WP_Error on failure. Default false.
 *
 * @since 1.1.0
 * @return Currency|\WP_Error|bool
 */
function eac_insert_currency( $args, $wp_error = true ) {
	return Currency::insert( $args, $wp_error );
}

/**
 * Delete a currency.
 *
 * @param string $currency_code Currency code.
 *
 * @return bool
 * @since 1.1.0
 */
function eac_delete_currency( $currency_code ) {
	$currency = eac_get_currency( $currency_code );
	if ( ! $currency ) {
		return false;
	}

	return $currency->delete();
}

/**
 * Get currency items.
 *
 * @param array $args Query arguments.
 * @param bool  $count Whether to return count or items.
 *
 * @return Currency[]|int|null
 * @since 1.1.0
 */
function eac_get_currencies( $args = array(), $count = false ) {
	$args = wp_parse_args(
		$args,
		array(
			'limit'   => 20,
			'offset'  => 0,
			'orderby' => 'id',
			'order'   => 'DESC',
		)
	);

	if ( $count ) {
		return Currency::count( $args );
	}

	return Currency::query( $args );
}


/**
 * Get currency rates.
 *
 * @return array
 * @since 1.1.0
 */
function eac_get_currency_rates() {
	$rates = get_transient( 'eac_currency_rates' );
	if ( false === $rates ) {
		$currencies = eac_get_currencies( array( 'limit' => - 1 ) );
		$rates      = array();
		foreach ( $currencies as $currency ) {
			$rates[ $currency->get_code() ] = $currency->get_rate();
		}

		set_transient( 'eac_currency_rates', $rates, 24 * HOUR_IN_SECONDS );
	}

	return $rates;
}


/**
 * Flush currency rates.
 *
 * @since 1.1.0
 */
function eac_flush_currency_rates() {
	delete_transient( 'eac_currency_rates' );
}

add_action( 'ever_accounting_currency_saved', 'eac_flush_currency_rates' );
add_action( 'ever_accounting_currency_deleted', 'eac_flush_currency_rates' );
