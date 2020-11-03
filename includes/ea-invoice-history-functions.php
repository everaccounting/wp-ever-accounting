<?php

/**
 * EverAccounting Invoice History Functions.
 *
 * All invoice history related function of the plugin.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

use EverAccounting\Exception;
use EverAccounting\Invoice;
use EverAccounting\Invoice_History;

defined( 'ABSPATH' ) || exit;

/**
 * Main function for returning invoice_history.
 *
 * @param $invoice_history
 *
 * @return Invoice_History|null
 * @since 1.1.0
 *
 */
function eaccounting_get_invoice_history( $invoice_history ) {
	if ( empty( $invoice_history ) ) {
		return null;
	}

	try {
		if ( $invoice_history instanceof Invoice_History ) {
			$_invoice_history = $invoice_history;
		} elseif ( is_object( $invoice_history ) && ! empty( $invoice_history->id ) ) {
			$_invoice_history = new Invoice( null );
			$_invoice_history->populate( $invoice_history );
		} else {
			$_invoice_history = new Invoice_History( absint( $invoice_history ) );
		}

		if ( ! $_invoice_history->exists() ) {
			throw new Exception( 'invalid_id', __( 'Invalid invoice.', 'wp-ever-accounting' ) );
		}

		return $_invoice_history;
	} catch ( Exception $exception ) {
		return null;
	}
}


/**
 *  Create new invoice history programmatically.
 *
 *  Returns a new invoice_history object on success.
 *
 * @param array $args Invoice_History arguments.
 *
 * @return Invoice_History|WP_Error
 * @since 1.1.0
 *
 */
function eaccounting_insert_invoice_history( $args ) {
	try {
		$default_args            = array(
			'id' => null,
		);
		$args                    = (array) wp_parse_args( $args, $default_args );
		$invoice_history = new invoice_history( $args['id'] );
		$invoice_history->set_props( $args );

		//validation
		if ( ! $invoice_history->get_date_created() ) {
			$invoice_history->set_date_created( time() );
		}

		if ( empty( $invoice_history->get_invoice_id() ) ) {
			throw new Exception( 'empty_props', __( 'Invoice id is required', 'wp-ever-accounting' ) );
		}

		$invoice = eaccounting_get_invoice( $invoice_history->get_invoice_id() );
		if ( ! $invoice->exists() ) {
			throw new Exception( 'invalid_invoice', __( 'Invoice not found.', 'wp-ever-accounting' ) );
		}

		if ( empty( $invoice_history->get_status( 'edit' ) ) ) {
			throw new Exception( 'empty_props', __( 'Status is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $invoice_history->get_notify( 'edit' ) ) ) {
			throw new Exception( 'empty_props', __( 'Notify is required', 'wp-ever-accounting' ) );
		}


		$invoice_history->save();

	} catch ( Exception $e ) {
		return new WP_Error( $e->getErrorCode(), $e->getMessage() );
	}

	return $invoice_history;
}

/**
 * Delete an invoice_history.
 *
 * @param $invoice_history_id
 *
 * @return bool
 * @since 1.1.0
 *
 */
function eaccounting_delete_invoice_history( $invoice_history_id ) {
	try {
		$invoice_history = new Invoice_History( $invoice_history_id );
		if ( ! $invoice_history->exists() ) {
			throw new Exception( 'invalid_id', __( 'Invalid invoice history.', 'wp-ever-accounting' ) );
		}

		$invoice_history->delete();

		return empty( $invoice_history->get_id() );

	} catch ( Exception $exception ) {
		return false;
	}
}
