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
 * @since 1.1.0
 * @return array
 */
function eaccounting_get_currency_codes() {
	return eaccounting_get_data( 'currencies' );
}

/**
 * Check if currency code is a valid one.
 *
 * @param $code
 * @since 1.1.0
 *
 * @return string
 */
function eaccounting_sanitize_currency_code( $code ) {
	$codes = eaccounting_get_currency_codes();
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
 * @param object|string|int $currency
 *
 * @return EverAccounting\Models\Currency|null
 * @since 1.1.0
 *
 */
function eaccounting_get_currency( $currency ) {
	if ( empty( $currency ) ) {
		return null;
	}
	try {
		$result = new EverAccounting\Models\Currency( $currency );
		return $result;
	} catch ( \Exception $e ) {
		return null;
	}
}

/**
 * @param $currency
 * @since 1.1.0
 *
 * @return mixed|null
 */
function eaccount_get_currency_rate( $currency ) {
	$exist = eaccounting_get_currency( $currency );
	if ( $exist ) {
		return $exist->get_rate();
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
 * @type int $id The currency ID. If equal to something other than 0,
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
 * @type int $enabled The status of the currency. Default 1.
 *
 * @type string $date_created The date when the currency is created. Default is current time.
 *
 *
 * }
 *
 * @return EverAccounting\Models\Currency|\WP_Error|bool
 * @since 1.1.0
 *
 */
function eaccounting_insert_currency( $args, $wp_error = true ) {
	// Ensure that we have data.
	if ( empty( $args ) ) {
		return false;
	}
	try {
		// The  id will be provided when updating an item.
		$args = wp_parse_args(
			$args,
			array(
				'id'   => null,
				'code' => null,
			)
		);
		$data = ! empty( $args['id'] ) ? $args['id'] : $args['code'];
		// Retrieve the currency.
		$item = new \EverAccounting\Models\Currency( $data );

		// Load new data.
		$item->set_props( $args );

		// Save the item
		$item->save();

		return $item;
	} catch ( \Exception $e ) {
		return $wp_error ? new WP_Error( 'insert_currency', $e->getMessage(), array( 'status' => $e->getCode() ) ) : 0;
	}
}
/**
 * Delete a currency.
 *
 * @param $currency_id
 *
 * @return bool
 * @since 1.1.0
 *
 */
function eaccounting_delete_currency( $currency_id ) {
	try {
		$currency = new EverAccounting\Models\Currency( $currency_id );
		return $currency->exists() ? $currency->delete() : false;
	} catch ( \Exception $e ) {
		return false;
	}
}
/**
 * Get currency items.
 *
 * @param array $args
 *
 * @return array|int|null
 * @since 1.1.0
 *
 *
 */
function eaccounting_get_currencies( $args = array() ) {
	$repository = \EverAccounting\Core\Repositories::load( 'currencies' );
	return $repository->get_currencies( $args );
}
