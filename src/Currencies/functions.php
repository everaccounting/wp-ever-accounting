<?php
/**
 * EverAccounting Currency Functions.
 *
 * Currency related functions.
 *
 * @since   1.0.2
 * @package EverAccounting
 */

namespace EverAccounting\Currencies;

use EverAccounting\Exception;

defined( 'ABSPATH' ) || exit();

require_once dirname( __FILE__ ) . '/hooks.php';
require_once dirname( __FILE__ ) . '/deprecated.php';

/**
 * Main function for querying currency.
 *
 * @since 1.1.0
 *
 * @param array $args
 *
 * @return \EverAccounting\Currencies\Query
 */
function query( $args = array() ) {
	return new Query( $args );
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
 * @param bool              $exist
 *
 * @param object|string|int $currency
 *
 * @return \EverAccounting\Currencies\Currency|null
 */
function get( $currency, $exist = false ) {
	if ( empty( $currency ) ) {
		return null;
	}

	try {
		if ( $currency instanceof Currency ) {
			$_currency = $currency;
		} elseif ( is_object( $currency ) && ! empty( $currency->id ) ) {
			$_currency = new Currency( null );
			$_currency->populate( $currency );
		} elseif ( is_string( $currency ) && ! empty( $currency ) ) {
			$_currency = new Currency( null );
			$_currency->populate_by_code( $currency );
		} else {
			$_currency = new Currency( absint( $currency ) );
		}

		if ( $exist && ! $_currency->exists() ) {
			throw new Exception( 'invalid_id', __( 'Invalid currency.', 'wp-ever-accounting' ) );
		}

		return $_currency;
	} catch ( Exception $e ) {
		return null;
	}
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
 * @return Currency|\WP_Error
 */
function insert( $args ) {
	try {
		$default_args = array(
			'id' => null,
		);
		$args         = (array) wp_parse_args( $args, $default_args );
		$currency     = new Currency( $args['id'] );
		$currency->set_props( $args );

		if ( null === $currency->get_date_created() ) {
			$currency->set_date_created( time() );
		}
		if ( empty( $currency->get_code() ) ) {
			throw new Exception( 'empty_prop', __( 'Currency code is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $currency->get_rate() ) ) {
			throw new Exception( 'empty_prop', __( 'Currency rate is required.', 'wp-ever-accounting' ) );
		}
		$exist = $currency->get_by_code( $currency->get_code() );
		if ( ! empty( $exist ) && absint( $exist->id ) !== absint( $currency->get_id() ) ) {
			throw new Exception( 'empty_prop', __( 'Duplicate currency code.', 'wp-ever-accounting' ) );
		}

		$attributes = $currency->global_currencies[ $currency->get_code() ];
		if ( empty( $currency->get_name( 'edit' ) ) ) {
			$currency->set_name( $attributes['name'] );
		}

		if ( empty( $currency->get_symbol( 'edit' ) ) ) {
			$currency->set_symbol( $attributes['symbol'] );
		}

		if ( empty( $currency->get_position( 'edit' ) ) ) {
			$currency->set_position( $attributes['position'] );
		}

		if ( empty( $currency->get_precision( 'edit' ) ) ) {
			$currency->set_precision( $attributes['precision'] );
		}

		if ( empty( $currency->get_decimal_separator( 'edit' ) ) ) {
			$currency->set_decimal_separator( $attributes['decimal_separator'] );
		}

		if ( empty( $currency->get_thousand_separator( 'edit' ) ) ) {
			$currency->set_thousand_separator( $attributes['thousand_separator'] );
		}

		$currency->save();

	} catch ( Exception $e ) {
		return new \WP_Error( $e->getErrorCode(), $e->getMessage() );
	}

	return $currency;
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
function delete( $currency_id ) {
	try {
		$currency = new Currency( $currency_id );
		if ( ! $currency->exists() ) {
			throw new Exception( 'invalid_id', __( 'Invalid currency.', 'wp-ever-accounting' ) );
		}

		$currency->delete();

		return empty( $currency->get_id() );

	} catch ( Exception $exception ) {
		return false;
	}
}
