<?php
/**
 * EverAccounting Currency Functions
 *
 * Currency related functions.
 *
 * @package EverAccounting\Functions
 * @version 1.0.2
 */

defined( 'ABSPATH' ) || exit();

/**
 * Main function for returning currency.
 *
 * @param $currency
 *
 * @return EAccounting_Currency|false
 * @since 1.0.0
 *
 */
function eaccounting_get_currency( $currency ) {
	if ( empty( $currency ) ) {
		return false;
	}

	try {
		$currency= new EAccounting_Currency( $currency );
		if ( ! $currency->exists() ) {
			throw new Exception( __( 'Invalid currency.', 'wp-ever-accounting' ) );
		}

		return $currency;
	} catch ( Exception $exception ) {
		return false;
	}
}

/**
 *  Create new currency programmatically.
 *
 *  Returns a new currency object on success.
 *
 * @param array $args currency arguments.
 *
 * @return EAccounting_Currency|WP_Error
 * @since 1.0.0
 *
 */
function eaccounting_insert_currency( $args ) {
	try {
		$default_args = array(
			'id' => null,
		);
		$args    = (array) wp_parse_args( $args, $default_args );
		$currency= new EAccounting_Currency( $args['id'] );
		$currency->set_props( $args );
		$currency->save();

	} catch ( Exception $e ) {
		return new WP_Error( 'error', $e->getMessage() );
	}

	return $currency;
}

/**
 * Delete a currency.
 *
 * @param $currency_id
 *
 * @return bool
 * @since 1.0.0
 *
 */
function eaccounting_delete_currency( $currency_id ) {
	try {
		$currency= new EAccounting_Currency( $currency_id );
		if ( ! $currency->exists() ) {
			throw new Exception( __( 'Invalid currency.', 'wp-ever-accounting' ) );
		}

		$currency->delete();

		return empty( $currency->get_id() );

	} catch ( Exception $exception ) {
		return false;
	}
}
