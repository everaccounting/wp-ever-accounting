<?php

/**
 * EverAccounting Invoice Functions.
 *
 * All item related function of the plugin.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

use EverAccounting\Exception;
use EverAccounting\Invoice;
use EverAccounting\Invoice_Item;
use EverAccounting\Invoice_History;

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
		if ( $invoice_history instanceof Invoice_Item ) {
			$_invoice_history = $invoice_history;
		} elseif ( is_object( $invoice_history ) && ! empty( $invoice_history->id ) ) {
			$_invoice_history = new Invoice_History( null );
			$_invoice_history->populate( $invoice_history );
		} else {
			$_invoice_history = new Invoice_Item( absint( $invoice_history ) );
		}

		if ( ! $_invoice_history->exists() ) {
			throw new Exception( 'invalid_id', __( 'Invalid invoice item.', 'wp-ever-accounting' ) );
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
		$default_args = array(
			'id' => null,
		);
		$args         = (array) wp_parse_args( $args, $default_args );
		$invoice_history = new invoice_history( $args['id'] );
		$invoice_history->set_props( $args );

		//validation
		if ( ! $invoice_history->get_date_created() ) {
			$invoice_history->set_date_created( time() );
		}

		if ( empty( $invoice_history->get_invoice_id() ) ) {
			throw new Exception( 'empty_props', __( 'Invoice id is required', 'wp-ever-accounting' ) );
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
function eaccounting_delete_invoice_hsitory( $invoice_history_id ) {
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
