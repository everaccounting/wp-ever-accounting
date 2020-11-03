<?php

/**
 * EverAccounting Bill Item Functions.
 *
 * All bill item related function of the plugin.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

use EverAccounting\Exception;
use EverAccounting\Bill_Item;

defined( 'ABSPATH' ) || exit;

/**
 * Main function for returning bill_item.
 *
 * @param $bill_item
 *
 * @return Bill_Item|null
 * @since 1.1.0
 *
 */

function eaccounting_get_bill_item( $bill_item ) {
	if ( empty( $bill_item ) ) {
		return null;
	}

	try {
		if ( $bill_item instanceof Bill_Item ) {
			$_bill_item = $bill_item;
		} elseif ( is_object( $bill_item ) && ! empty( $bill_item->id ) ) {
			$_bill_item = new Bill_Item( null );
			$_bill_item->populate( $bill_item );
		} else {
			$_bill_item = new Bill_Item( absint( $bill_item ) );
		}

		if ( ! $_bill_item->exists() ) {
			throw new Exception( 'invalid_id', __( 'Invalid bill item.', 'wp-ever-accounting' ) );
		}

		return $_bill_item;
	} catch ( Exception $exception ) {
		return null;
	}
}

/**
 *  Create new bill item programmatically.
 *
 *  Returns a new bill_item object on success.
 *
 * @param array $args Bill_Item arguments.
 *
 * @return Bill_Item|WP_Error
 * @since 1.1.0
 *
 */
function eaccounting_insert_bill_item( $args ) {
	try {
		$default_args = array(
			'id' => null,
		);
		$args         = (array) wp_parse_args( $args, $default_args );
		$bill_item = new bill_item( $args['id'] );
		$bill_item->set_props( $args );

		//validation
		if ( ! $bill_item->get_date_created() ) {
			$bill_item->set_date_created( time() );
		}

		if ( empty( $bill_item->get_bill_id() ) ) {
			throw new Exception( 'empty_props', __( 'Bill id is required', 'wp-ever-accounting' ) );
		}

		$bill = eaccounting_get_bill( $bill_item->get_bill_id() );
		if ( ! $bill->exists() ) {
			throw new Exception( 'invalid_invoice', __( 'Bill not found.', 'wp-ever-accounting' ) );
		}

		if ( empty( $bill_item->get_name( 'edit' ) ) ) {
			throw new Exception( 'empty_props', __( 'Name is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $bill_item->get_quantity( 'edit' ) ) ) {
			throw new Exception( 'empty_props', __( 'Quantity is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $bill_item->get_price( 'edit' ) ) ) {
			throw new Exception( 'empty_props', __( 'Price is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $bill_item->get_total( 'edit' ) ) ) {
			throw new Exception( 'empty_props', __( 'Total is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $bill_item->get_tax( 'edit' ) ) ) {
			throw new Exception( 'empty_props', __( 'Tax is required', 'wp-ever-accounting' ) );
		}

		$bill_item->save();

	} catch ( Exception $e ) {
		return new WP_Error( $e->getErrorCode(), $e->getMessage() );
	}

	return $bill_item;
}

/**
 * Delete an bill_item.
 *
 * @param $bill_item_id
 *
 * @return bool
 * @since 1.1.0
 *
 */
function eaccounting_delete_bill_item( $bill_item_id ) {
	try {
		$bill_item = new Bill_Item( $bill_item_id );
		if ( ! $bill_item->exists() ) {
			throw new Exception( 'invalid_id', __( 'Invalid bill item.', 'wp-ever-accounting' ) );
		}

		$bill_item->delete();

		return empty( $bill_item->get_id() );

	} catch ( Exception $exception ) {
		return false;
	}
}
