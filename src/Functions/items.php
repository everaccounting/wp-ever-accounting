<?php
/**
 * EAccounting Item Functions.
 *
 * All item related function of the plugin.
 *
 * @since   1.1.0
 * @package EAccounting
 */

use EverAccounting\Models\Item;

defined( 'ABSPATH' ) || exit;

/**
 * Main function for returning item.
 *
 * @param mixed $item Item object.
 *
 * @return Item|null
 * @since 1.1.0
 */
function eac_get_item( $item ) {
	return Item::find( $item );
}

/**
 *  Create new item programmatically.
 *
 *  Returns a new item object on success.
 *
 * @param array $args An array of elements that make up an invoice to update or insert.
 * @param bool  $wp_error Optional. Whether to return a WP_Error on failure. Default false.
 *
 * @return Item|WP_Error|bool
 * @since 1.1.0
 */
function eac_insert_item( $args, $wp_error = true ) {
	return Item::insert( $args, $wp_error );
}

/**
 * Delete an item.
 *
 * @param int $item_id Item ID.
 *
 * @return bool
 * @since 1.1.0
 */
function eac_delete_item( $item_id ) {
	$item = eac_get_item( $item_id );

	return $item ? $item->delete() : false;
}

/**
 * Get items.
 *
 * @param array $args Optional. Arguments to retrieve items.
 * @param bool  $count Optional. Whether to return the count of items.
 *
 * @return array|int
 * @since 1.1.0
 */
function eac_get_items( $args = array(), $count = false ) {
	if ( $count ) {
		return Item::count( $args );
	}

	return Item::query( $args );

}
