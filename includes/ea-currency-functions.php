<?php
/**
 * EverAccounting Currency Functions.
 *
 * Currency related functions.
 *
 * @package EverAccounting
 * @since 1.0.2
 */

defined( 'ABSPATH' ) || exit();

/**
 * Main function for returning currency.
 *
 * @param $currency
 *
 * @return \EAccounting\Currency|false
 * @since 1.0.2
 *
 */
function eaccounting_get_currency( $currency ) {
	if ( empty( $currency ) ) {
		return false;
	}

	try {
		if ( $currency instanceof \EAccounting\Currency ) {
			$_currency = $currency;
		} elseif ( is_object( $currency ) && ! empty( $currency->id ) ) {
			$_currency = new \EAccounting\Currency( null );
			$_currency->populate( $currency );
		} else {
			$_currency = new \EAccounting\Currency( absint( $currency ) );
		}

		if ( ! $_currency->exists() ) {
			throw new \EverAccounting\Exception( 'invalid-id', __( 'Invalid account.', 'wp-ever-accounting' ) );
		}

		return $_currency;
	} catch ( Exception $exception ) {
		return null;
	}
}

/**
 * Get currency by code
 *
 * @param string $code
 *
 * @return \EAccounting\Currency|false
 * @since 1.0.2
 *
 */
function eaccounting_get_currency_by_code( $code ) {
	if ( empty( $code ) ) {
		return null;
	}
	try {
		global $wpdb;
		$_currency = $wpdb->get_row( $wpdb->prepare( "SELECT * from {$wpdb->prefix}ea_currencies where code=%s", sanitize_key( $code ) ) );

		return eaccounting_get_currency( $_currency );
	} catch ( Exception $exception ) {
		return null;
	}
}

/**
 *  Create new currency programmatically.
 *
 *  Returns a new currency object on success.
 *
 * @param array $args currency arguments.
 *
 * @return \EAccounting\Currency|WP_Error
 * @since 1.0.2
 *
 */
function eaccounting_insert_currency( $args ) {
	try {
		$default_args = array(
			'id' => null,
		);
		$args         = (array) wp_parse_args( $args, $default_args );
		$currency     = new \EAccounting\Currency( $args['id'] );
		$currency->set_props( $args );
		$currency->save();

	} catch ( \EverAccounting\Exception $e ) {
		return new \WP_Error( $e->getErrorCode(), $e->getMessage() );
	}

	return $currency;
}

/**
 * Delete a currency.
 *
 * @param $currency_id
 *
 * @return bool
 * @since 1.0.2
 *
 */
function eaccounting_delete_currency( $currency_id ) {
	try {
		$currency = new \EAccounting\Currency( $currency_id );
		if ( ! $currency->exists() ) {
			throw new Exception( __( 'Invalid currency.', 'wp-ever-accounting' ) );
		}

		$currency->delete();

		return empty( $currency->get_id() );

	} catch ( Exception $exception ) {
		return false;
	}
}
