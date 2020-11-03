<?php
/**
 * EverAccounting Invoice Item Functions.
 *
 * All invoice item related function of the plugin.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

use EverAccounting\Exception;
use EverAccounting\Invoice;
use EverAccounting\Invoice_Item;

/**
 * Main function for returning invoice_item.
 *
 * @param $invoice_item
 *
 * @return Invoice_Item|null
 * @since 1.1.0
 *
 */
function eaccounting_get_invoice_item( $invoice_item ) {
	if ( empty( $invoice_item ) ) {
		return null;
	}

	try {
		if ( $invoice_item instanceof Invoice_Item ) {
			$_invoice_item = $invoice_item;
		} elseif ( is_object( $invoice_item ) && ! empty( $invoice_item->id ) ) {
			$_invoice_item = new Invoice_Item( null );
			$_invoice_item->populate( $invoice_item );
		} else {
			$_invoice_item = new Invoice_Item( absint( $invoice_item ) );
		}

		if ( ! $_invoice_item->exists() ) {
			throw new Exception( 'invalid_id', __( 'Invalid invoice item.', 'wp-ever-accounting' ) );
		}

		return $_invoice_item;
	} catch ( Exception $exception ) {
		return null;
	}
}

/**
 *  Create new invoice item programmatically.
 *
 *  Returns a new invoice_item object on success.
 *
 * @param array $args Invoice_Item arguments.
 *
 * @return Invoice_Item|WP_Error
 * @since 1.1.0
 *
 */
function eaccounting_insert_invoice_item( $args ) {
	try {
		$default_args = array(
			'id' => null,
		);
		$args         = (array) wp_parse_args( $args, $default_args );
		$invoice_item = new invoice_item( $args['id'] );
		$invoice_item->set_props( $args );

		//validation
		if ( ! $invoice_item->get_date_created() ) {
			$invoice_item->set_date_created( time() );
		}

		if ( empty( $invoice_item->get_invoice_id() ) ) {
			throw new Exception( 'empty_props', __( 'Invoice id is required', 'wp-ever-accounting' ) );
		}

		$invoice = eaccounting_get_invoice( $invoice_item->get_invoice_id() );
		if ( ! $invoice->exists() ) {
			throw new Exception( 'invalid_invoice', __( 'Invoice not found.', 'wp-ever-accounting' ) );
		}

		if ( empty( $invoice_item->get_name( 'edit' ) ) ) {
			throw new Exception( 'empty_props', __( 'Name is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $invoice_item->get_quantity( 'edit' ) ) ) {
			throw new Exception( 'empty_props', __( 'Quantity is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $invoice_item->get_price( 'edit' ) ) ) {
			throw new Exception( 'empty_props', __( 'Price is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $invoice_item->get_total( 'edit' ) ) ) {
			throw new Exception( 'empty_props', __( 'Total is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $invoice_item->get_tax( 'edit' ) ) ) {
			throw new Exception( 'empty_props', __( 'Tax is required', 'wp-ever-accounting' ) );
		}

		$invoice_item->save();

	} catch ( Exception $e ) {
		return new WP_Error( $e->getErrorCode(), $e->getMessage() );
	}

	return $invoice_item;
}

/**
 * Delete an invoice_item.
 *
 * @param $invoice_item_id
 *
 * @return bool
 * @since 1.1.0
 *
 */
function eaccounting_delete_invoice_item( $invoice_item_id ) {
	try {
		$invoice_item = new Invoice_Item( $invoice_item_id );
		if ( ! $invoice_item->exists() ) {
			throw new Exception( 'invalid_id', __( 'Invalid invoice item.', 'wp-ever-accounting' ) );
		}

		$invoice_item->delete();

		return empty( $invoice_item->get_id() );

	} catch ( Exception $exception ) {
		return false;
	}
}
