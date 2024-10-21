<?php
/**
 * EAccounting Currency Functions.
 *
 * Currency related functions.
 *
 * @since   1.1.0
 * @package EAccounting
 */

use EAccounting\Models\Currency;

defined( 'ABSPATH' ) || exit();

/**
 * Return all available currency codes.
 *
 * @since 1.1.0
 * @return array
 */
function eaccounting_get_currency_codes() {
	return eaccounting_get_data( 'currencies' );
}

/**
 * Check if currency code is a valid one.
 *
 * @param string $code Currency code.
 *
 * @since 1.1.0
 * @return string
 */
function eaccounting_sanitize_currency_code( $code ) {
	$codes = eaccounting_get_currency_codes();
	$code  = strtoupper( $code );
	if ( empty( $code ) || ! array_key_exists( $code, $codes ) ) {
		return '';
	}

	return $code;
}

/**
 * Main function for returning currency.
 *
 * This function is little different from rest
 * Even if the currency in the database doest not
 * exist it will it populate with default data.
 *
 * Whenever need to check existence of the object
 * in database must check $currency->exist()
 *
 * @param object|string|int $currency Currency object, code or ID.
 *
 * @since 1.1.0
 * @return EverAccounting\Models\Currency|null
 */
function eaccounting_get_currency( $currency ) {
	return eac_get_currency( $currency );
}

/**
 * Get currency rate.
 *
 * @param string $currency Currency code.
 *
 * @since 1.1.0
 * @return mixed|null
 */
function eaccounting_get_currency_rate( $currency ) {
	$exist = eaccounting_get_currency( $currency );
	if ( $exist ) {
		return $exist->rate;
	}

	return 1;
}


/**
 *  Create new currency programmatically.
 *
 *  Returns a new currency object on success.
 *
 * @param array $args {
 *                                  An array of elements that make up a currency to update or insert.
 *
 * @type int    $id The currency ID. If equal to something other than 0,
 *                                         the currency with that id will be updated. Default 0.
 *
 * @type string $name The name of the currency . Default empty.
 *
 * @type string $code The code of currency. Default empty.
 *
 * @type double $rate The rate for the currency.Default is 1.
 *
 * @type double $precision The precision for the currency. Default 0.
 *
 * @type string $symbol The symbol for the currency. Default empty.
 *
 * @type string $position The position where the currency code will be set in amount. Default before.
 *
 * @type string $decimal_separator The decimal_separator for the currency code. Default ..
 *
 * @type string $thousand_separator The thousand_separator for the currency code. Default ,.
 *
 * @type int    $enabled The status of the currency. Default 1.
 *
 * @type string $date_created The date when the currency is created. Default is current time.
 *
 * }
 * @param bool  $wp_error Optional. Whether to return a WP_Error on failure. Default false.
 *
 * @since 1.1.0
 * @return EAccounting\Models\Currency|\WP_Error|bool
 */
function eaccounting_insert_currency( $args, $wp_error = true ) {
	return eac_insert_currency( $args, $wp_error );
}

/**
 * Delete a currency.
 *
 * @param string $code Currency code.
 *
 * @since 1.1.0
 * @return bool
 */
function eaccounting_delete_currency( $code ) {
	$currency = eac_get_currency( $code );

	return $currency ? $currency->delete() : false;
}

/**
 * Get currency items.
 *
 * @param array $args Query arguments.
 *
 * @since 1.1.0
 * @return array|int|null
 */
function eaccounting_get_currencies( $args = array() ) {
	if ( ! empty( $args['count_total'] ) ) {
		$args['count'] = true;
		unset( $args['count_total'] );
	}

	return eac_get_currencies( $args );
}
