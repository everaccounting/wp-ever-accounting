<?php

/**
 * EverAccounting Invoice Functions.
 *
 * All invoice related function of the plugin.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

use EverAccounting\Exception;
use EverAccounting\Invoice;

defined( 'ABSPATH' ) || exit;

/**
 * Main function for returning invoice.
 *
 * @param $invoice
 *
 * @return Invoice|null
 * @since 1.1.0
 *
 */
function eaccounting_get_invoice( $invoice ) {
	if ( empty( $invoice ) ) {
		return null;
	}

	try {
		if ( $invoice instanceof Invoice ) {
			$_invoice = $invoice;
		} elseif ( is_object( $invoice ) && ! empty( $invoice->id ) ) {
			$_invoice = new Invoice( null );
			$_invoice->populate( $invoice );
		} else {
			$_invoice = new Invoice( absint( $invoice ) );
		}

		if ( ! $_invoice->exists() ) {
			throw new Exception( 'invalid_id', __( 'Invalid invoice.', 'wp-ever-accounting' ) );
		}

		return $_invoice;
	} catch ( Exception $exception ) {
		return null;
	}
}

/**
 *  Create new invoice programmatically.
 *
 *  Returns a new invoice object on success.
 *
 * @param array $args Invoice arguments.
 *
 * @return Invoice|WP_Error
 * @since 1.1.0
 *
 */
function eaccounting_insert_invoice( $args ) {
	try {
		$default_args = array(
			'id' => null,
		);
		$args         = (array) wp_parse_args( $args, $default_args );
		error_log( print_r( $args, true ) );
		$invoice = new invoice( $args['id'] );
		$invoice->set_props( $args );

		//validation
		if ( ! $invoice->get_date_created() ) {
			$invoice->set_date_created( time() );
		}
		if ( ! $invoice->get_creator_id() ) {
			$invoice->set_creator_id();
		}

		if ( empty( $invoice->get_invoice_number() ) ) {
			throw new Exception( 'empty_props', __( 'Invoice number is required', 'wp-ever-accounting' ) );
		}

//		if ( empty( $invoice->get_status() ) ) {
//			throw new Exception( 'empty_props', __( 'Status is required', 'wp-ever-accounting' ) );
//		}

		if ( empty( $invoice->get_invoiced_at( 'edit' ) ) ) {
			throw new Exception( 'empty_props', __( 'Invoiced at is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $invoice->get_total( 'edit' ) ) ) {
			throw new Exception( 'empty_props', __( 'Invoice total is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $invoice->get_currency_code( 'edit' ) ) ) {
			throw new Exception( 'empty_props', __( 'Currency code is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $invoice->get_currency_rate( 'edit' ) ) ) {
			throw new Exception( 'empty_props', __( 'Currency rate is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $invoice->get_contact_name( 'edit' ) ) ) {
			throw new Exception( 'empty_props', __( 'Contact name is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $invoice->get_category_id( 'edit' ) ) ) {
			throw new Exception( 'empty_props', __( 'Category id is required', 'wp-ever-accounting' ) );
		}

		$invoice->save();

	} catch ( Exception $e ) {
		return new WP_Error( $e->getErrorCode(), $e->getMessage() );
	}

	return $invoice;
}

/**
 * Delete an invoice.
 *
 * @param $invoice_id
 *
 * @return bool
 * @since 1.1.0
 *
 */
function eaccounting_delete_invoice( $invoice_id ) {
	try {
		$invoice = new Invoice( $invoice_id );
		if ( ! $invoice->exists() ) {
			throw new Exception( 'invalid_id', __( 'Invalid invoice.', 'wp-ever-accounting' ) );
		}

		$invoice->delete();

		return empty( $invoice->get_id() );

	} catch ( Exception $exception ) {
		return false;
	}
}
