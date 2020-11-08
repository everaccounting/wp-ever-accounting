<?php
defined( 'ABSPATH' ) || exit();

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
 * @param bool              $exist
 *
 * @param object|string|int $currency
 *
 * @return \EverAccounting\Currencies\Currency|int|object|string
 */
function eaccounting_get_currency( $currency, $exist = false ) {
	return \EverAccounting\Currencies\get( $currency, $exist );
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
 * @return EverAccounting\Currencies\Currency|\WP_Error
 */
function eaccounting_insert_currency( $args ) {
	return \EverAccounting\Currencies\insert( $args );
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
	return \EverAccounting\Currencies\delete( $currency_id );
}
