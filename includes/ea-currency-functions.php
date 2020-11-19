<?php
/**
 * EverAccounting Currency Functions.
 *
 * Currency related functions.
 *
 * @since   1.1.0
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
 * @since 1.1.0
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
 * @since 1.1.0
 *
 * @param array $args               {
 *                                  An array of elements that make up a currency to update or insert.
 *
 * @type int    $id                 The currency ID. If equal to something other than 0,
 *                                         the currency with that id will be updated. Default 0.
 *
 * @type string $name               The name of the currency . Default empty.
 *
 * @type string $code               The code of currency. Default empty.
 *
 * @type double $rate               The rate for the currency.Default is 1.
 *
 * @type double $precision          The precision for the currency. Default 0.
 *
 * @type string $symbol             The symbol for the currency. Default empty.
 *
 * @type string $position           The position where the currency code will be set in amount. Default before.
 *
 * @type string $decimal_separator  The decimal_separator for the currency code. Default ..
 *
 * @type string $thousand_separator The thousand_separator for the currency code. Default ,.
 *
 * @type int    $enabled            The status of the currency. Default 1.
 *
 * @type string $date_created       The date when the currency is created. Default is current time.
 *
 *
 * }
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
 * @since 1.1.0
 *
 * @param $currency_id
 *
 * @return bool
 */
function eaccounting_delete_currency( $currency_id ) {
	$currency = new EverAccounting\Models\Currency( $currency_id );
	if ( ! $currency ) {
		return false;
	}

	return \EverAccounting\Repositories\Currencies::instance()->delete( $currency->get_id() );
}

/**
 * Get currency items.
 *
 * @since 1.1.0
 *
 * @param bool  $callback
 *
 * @param array $args               {
 *                                  Optional. Arguments to retrieve currencies.
 *
 * @type string $name               The name of the currency .
 *
 * @type string $code               The code of currency.
 *
 * @type double $rate               The rate for the currency.
 *
 * @type double $precision          The precision for the currency.
 *
 * @type string $symbol             The symbol for the currency.
 *
 * @type string $position           The position where the currency code will be set in amount.
 *
 * @type string $decimal_separator  The decimal_separator for the currency code.
 *
 * @type string $thousand_separator The thousand_separator for the currency code.
 *
 * @type int    $enabled            The status of the currency. Default 1.
 *
 * }
 *
 * @return array|int
 */
function eaccounting_get_currencies( $args = array(), $callback = true ) {
	return \EverAccounting\Repositories\Currencies::instance()->get_items(
		$args,
		function ( $item ) use ( $callback ) {
			if ( $callback ) {
				$currency = new \EverAccounting\Models\Currency();
				$currency->set_props( $item );
				$currency->set_object_read( true );

				return $currency;
			}

			return $item;
		}
	);
}


function eaccounting_get_currency_by_code( $code ) {
	$key        = 'eaccounting_cache_currencies';
	$cached     = wp_cache_get( $key );
	$currencies = $cached ? $cached : get_transient( $key );
	if ( false === $currencies ) {
		$currencies = array();
		$items      = eaccounting_get_currencies( array( 'number' => - 1 ), false );
		foreach ( $items as $item ) {
			$currencies[ $item->code ] = $item;
		}
		set_transient( $key, $currencies, HOUR_IN_SECONDS );
		wp_cache_set( $key, $currencies, 'currencies' );
	}

	if ( array_key_exists( $code, $currencies ) ) {
		return $currencies[ $code ];
	}

	return null;
}
