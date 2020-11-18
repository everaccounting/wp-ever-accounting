<?php
/**
 * EverAccounting Currency Functions.
 *
 * Currency related functions.
 *
 * @since   1.0.2
 * @package EverAccounting
 */


defined( 'ABSPATH' ) || exit();

/**
 * Checks if the currency code is valid.
 *
 * @since 1.1.0
 *
 * @param $code
 *
 * @return bool
 */
function eaccounting_get_currency_code( $code ) {
	$codes = eaccounting_get_global_currencies();

	return array_key_exists( strtoupper( $code ), $codes ) ? $codes[ $code ] : false;
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
 *
 * @since 1.0.2
 *
 * @param object|string|int $currency
 *
 * @return EverAccounting\Models\Currency|null
 */
function eaccounting_get_currency( $currency ) {
	if ( empty( $currency ) ) {
		return null;
	}

	$result = new EverAccounting\Models\Currency( $currency );

	return $result->exists() ? $result : null;
}

/**
 *  Create new currency programmatically.
 *
 *  Returns a new currency object on success.
 *
 * @since 1.0.2
 *
 * @param array $args currency arguments.
 *
 * @return EverAccounting\Models\Currency|\WP_Error
 */
function eaccounting_insert_currency( $args ) {
	$args = wp_parse_args( $args, array( 'code' => '' ) );
	$code = eaccounting_get_currency_code( $args['code'] );
	if ( $code ) {
		$args = array_merge( $code, $args );
	}
	$currency = new EverAccounting\Models\Currency( $args );

	return $currency->save();
}

/**
 * Delete a currency.
 *
 * @since 1.0.2
 *
 * @param $currency_id
 *
 * @return bool
 */
function eaccounting_delete_currency( $currency_id ) {
	$currency = new EverAccounting\Models\Account( $currency_id );
	if ( ! $currency->exists() ) {
		return false;
	}

	return \EverAccounting\Repositories\Currencies::instance()->delete( $currency->get_id() );
}

/**
 * Get currency items.
 *
 * @since 1.1.0
 *
 * @param array $args
 *
 * @return array|int
 */
function eaccounting_get_currencies( $args = array() ) {
	return \EverAccounting\Repositories\Currencies::instance()->get_items( $args );
}
