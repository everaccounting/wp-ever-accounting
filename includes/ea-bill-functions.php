<?php

/**
 * EverAccounting Bill Functions.
 *
 * All bill related function of the plugin.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

use EverAccounting\Exception;
use EverAccounting\Bill;

defined( 'ABSPATH' ) || exit;

/**
 * Main function for returning bill.
 *
 * @param $bill
 *
 * @return Bill|null
 * @since 1.1.0
 *
 */
function eaccounting_get_bill( $bill ) {
	if ( empty( $bill ) ) {
		return null;
	}

	try {
		if ( $bill instanceof Bill ) {
			$_bill = $bill;
		} elseif ( is_object( $bill ) && ! empty( $bill->id ) ) {
			$_bill = new Bill( null );
			$_bill->populate( $bill );
		} else {
			$_bill = new Bill( absint( $bill ) );
		}

		if ( ! $_bill->exists() ) {
			throw new Exception( 'invalid_id', __( 'Invalid bill.', 'wp-ever-accounting' ) );
		}

		return $_bill;
	} catch ( Exception $exception ) {
		return null;
	}
}

/**
 *  Create new bill programmatically.
 *
 *  Returns a new bill object on success.
 *
 * @param array $args Bill arguments.
 *
 * @return Bill|WP_Error
 * @since 1.1.0
 *
 */
function eaccounting_insert_bill( $args ) {
	try {
		$default_args = array(
			'id' => null,
		);
		$args         = (array) wp_parse_args( $args, $default_args );
		$bill         = new bill( $args['id'] );
		$bill->set_props( $args );
		error_log( print_r( $bill, true ) );

		//validation
		if ( ! $bill->get_date_created() ) {
			$bill->set_date_created( time() );
		}
		if ( ! $bill->get_creator_id() ) {
			$bill->set_creator_id();
		}

		if ( empty( $bill->get_bill_number() ) ) {
			throw new Exception( 'empty_props', __( 'Bill number is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $bill->get_status() ) ) {
			throw new Exception( 'empty_props', __( 'Status is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $bill->get_bill_at( 'edit' ) ) ) {
			throw new Exception( 'empty_props', __( 'Bill at is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $bill->get_total( 'edit' ) ) ) {
			throw new Exception( 'empty_props', __( 'Bill total is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $bill->get_currency_code( 'edit' ) ) ) {
			throw new Exception( 'empty_props', __( 'Currency code is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $bill->get_currency_rate( 'edit' ) ) ) {
			throw new Exception( 'empty_props', __( 'Currency rate is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $bill->get_contact_name( 'edit' ) ) ) {
			throw new Exception( 'empty_props', __( 'Contact name is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $bill->get_category_id( 'edit' ) ) ) {
			throw new Exception( 'empty_props', __( 'Category id is required', 'wp-ever-accounting' ) );
		}

		$bill->save();

	} catch ( Exception $e ) {
		return new WP_Error( $e->getErrorCode(), $e->getMessage() );
	}

	return $bill;
}

/**
 * Delete an bill.
 *
 * @param $bill
 *
 * @return bool
 * @since 1.1.0
 *
 */
function eaccounting_delete_bill( $bill_id ) {
	try {
		$bill = new Bill( $bill_id );
		if ( ! $bill->exists() ) {
			throw new Exception( 'invalid_id', __( 'Invalid bill.', 'wp-ever-accounting' ) );
		}

		$bill->delete();

		return empty( $bill->get_id() );

	} catch ( Exception $exception ) {
		return false;
	}
}
