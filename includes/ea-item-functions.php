<?php

/**
 * EverAccounting Item Functions.
 *
 * All item related function of the plugin.
 *
 * @since   1.0.4
 * @package EverAccounting
 */
use EverAccounting\Exception;
use EverAccounting\Item;

defined( 'ABSPATH' ) || exit;

/**
 * Main function for returning item.
 *
 * @since 1.0.4
 *
 * @param $item
 *
 * @return Item|null
 */
function eaccounting_get_item( $item ) {
	if ( empty( $item ) ) {
		return null;
	}

	try {
		if ( $item instanceof Item ) {
			$_item = $item;
		} elseif ( is_object( $item ) && ! empty( $item->id ) ) {
			$_item = new Item( null );
			$_item->populate( $item );
		} else {
			$_item = new Item( absint( $item ) );
		}



		if ( ! $_item->exists() ) {
			throw new Exception( 'invalid_id', __( 'Invalid item.', 'wp-ever-accounting' ) );
		}

		return $_item;
	} catch ( Exception $exception ) {
		return null;
	}
}



/**
 *  Create new item programmatically.
 *
 *  Returns a new item object on success.
 *
 * @since 1.0.4
 *
 * @param array $args Item arguments.
 *
 * @return Item|WP_Error
 */
function eaccounting_insert_item( $args ) {
	try {
		$default_args = array(
			'id' => null,
		);
		$args         = (array) wp_parse_args( $args, $default_args );
		$item      = new item( $args['id'] );
		$item->set_props( $args );

		//validation
		if ( ! $item->get_date_created() ) {
			$item->set_date_created( time() );
		}
		if ( ! $item->get_creator_id() ) {
			$item->set_creator_id();
		}

		if ( empty( $item->get_name() ) ) {
			throw new Exception( 'empty_props', __( 'Item Name is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $item->get_sale_price( 'edit' ) ) ) {
			throw new Exception( 'empty_props', __( 'Sale price is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $item->get_purchase_price( 'edit' ) ) ) {
			throw new Exception( 'empty_props', __( 'Purchase price is required', 'wp-ever-accounting' ) );
		}

		$item->save();

	} catch ( Exception $e ) {
		return new WP_Error( $e->getErrorCode(), $e->getMessage() );
	}

	return $item;
}

/**
 * Delete an item.
 *
 * @since 1.0.4
 *
 * @param $item_id
 *
 * @return bool
 */
function eaccounting_delete_item( $item_id ) {
	try {
		$item = new Item( $item_id );
		if ( ! $item->exists() ) {
			throw new Exception( 'invalid_id', __( 'Invalid account.', 'wp-ever-accounting' ) );
		}

		$item->delete();

		return empty( $item->get_id() );

	} catch ( Exception $exception ) {
		return false;
	}
}
