<?php
/**
 * EverAccounting Currency Functions.
 *
 * Currency related functions.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

use EverAccounting\Currency;

defined( 'ABSPATH' ) || exit();

/**
 * Retrieves currency data given a currency id or currency object.
 *
 * @param int|object|Currency $currency currency to retrieve
 * @param string $output The required return type. One of OBJECT, ARRAY_A, or ARRAY_N. Default OBJECT.
 *
 * @return Currency|array|null
 * @since 1.1.0
 */
function eaccounting_get_currency( $currency, $output = OBJECT ) {
	if ( empty( $currency ) ) {
		return null;
	}

	if ( $currency instanceof Currency ) {
		$_currency = $currency;
	} else {
		$_currency = new Currency( $currency );
	}

	if ( $_currency->exists() ) {
		return null;
	}

	if ( ARRAY_A === $output ) {
		return $_currency->to_array();
	}

	if ( ARRAY_N === $output ) {
		return array_values( $_currency->to_array() );
	}

	return $_currency;
}

/**
 *  Insert or update a currency.
 *
 * @param array|object|Currency $data An array, object, or currency object of data arguments.
 *
 * @return Currency|WP_Error The currency object or WP_Error otherwise.
 * @global wpdb $wpdb WordPress database abstraction object.
 * @since 1.1.0
 */
function eaccounting_insert_currency( $data ) {
	if ( $data instanceof Currency ) {
		$data = $data->to_array();
	} elseif ( is_object( $data ) ) {
		$data = get_object_vars( $data );
	}

	if ( empty( $data ) || ! is_array( $data ) ) {
		return new WP_Error( 'invalid_currency_data', __( 'Currency could not be saved.', 'wp-ever-accounting' ) );
	}

	$data = wp_parse_args( $data, array( 'id' => null ) );
	$currency = new Currency( (int) $data['id'] );
	$currency->set_props( $data );
	$is_error = $currency->save();
	if ( is_wp_error( $is_error ) ) {
		return $is_error;
	}

	return $currency;
}

/**
 * Delete an currency.
 *
 * @param int $currency_id Currency ID
 *
 * @return array|false Currency array data on success, false on failure.
 * @since 1.1.0
 */
function eaccounting_delete_currency( $currency_id ) {
	if ( $currency_id instanceof Currency ) {
		$currency_id = $currency_id->get_id();
	}

	if ( empty( $currency_id ) ) {
		return false;
	}

	$currency = new Currency( (int) $currency_id );
	if ( ! $currency->exists() ) {
		return false;
	}

	return $currency->delete();
}

/**
 * Retrieves an array of the currencies matching the given criteria.
 *
 * @param array $args Arguments to retrieve currencies.
 *
 * @return Currency[]|int Array of currency objects or count.
 * @since 1.1.0
 *
 */
function eaccounting_get_currencies( $args = array() ) {
	$defaults = array(
		'number'        => 20,
		'orderby'       => 'name',
		'order'         => 'DESC',
		'include'       => array(),
		'exclude'       => array(),
		'no_found_rows' => false,
		'count_total'   => false,
	);

	$parsed_args = wp_parse_args( $args, $defaults );
	$query       = new \EverAccounting\Currency_Query( $parsed_args );
	if ( true === $parsed_args['count_total'] ) {
		return $query->get_total();
	}


	return $query->get_results();
}

/**
 * Check if currency code is a valid one.
 *
 * @param $code
 *
 * @return string
 * @since 1.1.0
 *
 */
function eaccounting_sanitize_currency_code( $code ) {
	$codes = eaccounting_get_data( 'currencies' );
	$code  = strtoupper( $code );
	if ( empty( $code ) || ! array_key_exists( $code, $codes ) ) {
		return '';
	}

	return $code;
}
