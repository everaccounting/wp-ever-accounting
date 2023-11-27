<?php
/**
 * EverAccounting Currency Functions.
 *
 * Currency related functions.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

use EverAccounting\Models\Currency;

defined( 'ABSPATH' ) || exit();

/**
 * Return all available currency codes.
 *
 * @return array
 * @since 1.1.0
 */
//function eaccounting_get_currency_codes() {
//	return eaccounting_get_data( 'currencies' );
//}

/**
 * Check if currency code is a valid one.
 *
 * @param string $code Currency code.
 *
 * @return string
 * @since 1.1.0
 */
//function eaccounting_sanitize_currency_code( $code ) {
//	$codes = eaccounting_get_currency_codes();
//	$code  = strtoupper( $code );
//	if ( empty( $code ) || ! array_key_exists( $code, $codes ) ) {
//		return '';
//	}
//
//	return $code;
//}

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
 *@return EverAccounting\Models\Currency|null
 */
function eaccounting_get_currency( $currency ) {
	if ( empty( $currency ) ) {
		return null;
	}
	try {
		return new EverAccounting\Models\Currency( $currency );
	} catch ( \Exception $e ) {
		return null;
	}
}

/**
 * Get currency rate.
 *
 * @param string $currency Currency code.
 *
 * @return mixed|null
 * @since 1.1.0
 */
function eaccounting_get_currency_rate( $currency ) {
	$exist = eaccounting_get_currency( $currency );
	if ( $exist ) {
		return $exist->get_rate();
	}

	return 1;
}
