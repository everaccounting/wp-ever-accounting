<?php
/**
 * EverAccounting Item Functions.
 *
 * All item related function of the plugin.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

use EverAccounting\Item;

defined( 'ABSPATH' ) || exit;

/**
 * Retrieves item data given a item id or item object.
 *
 * @param int|object|Item $item item to retrieve
 * @param string $output The required return type. One of OBJECT, ARRAY_A, or ARRAY_N. Default OBJECT.
 *
 * @return Item|array|null
 * @since 1.1.0
 */
function eaccounting_get_item( $item, $output = OBJECT ) {
	if ( empty( $item ) ) {
		return null;
	}

	if ( $item instanceof Item ) {
		$_item = $item;
	} else {
		$_item = new Item( $item );
	}

	if ( !$_item->exists() ) {
		return null;
	}

	if ( ARRAY_A === $output ) {
		return $_item->to_array();
	}

	if ( ARRAY_N === $output ) {
		return array_values( $_item->to_array() );
	}

	return $_item;
}

/**
 *  Insert or update a item.
 *
 * @param array|object|Item $data An array, object, or item object of data arguments.
 *
 * @return Item|WP_Error The item object or WP_Error otherwise.
 * @global wpdb $wpdb WordPress database abstraction object.
 * @since 1.1.0
 */
function eaccounting_insert_item( $data ) {
	if ( $data instanceof Item ) {
		$data = $data->to_array();
	} elseif ( is_object( $data ) ) {
		$data = get_object_vars( $data );
	}

	if ( empty( $data ) || ! is_array( $data ) ) {
		return new WP_Error( 'invalid_item_data', __( 'Item could not be saved.', 'wp-ever-accounting' ) );
	}

	$data = wp_parse_args( $data, array( 'id' => null ) );
	$item = new Item( (int) $data['id'] );
	$item->set_props( $data );
	$is_error = $item->save();
	if ( is_wp_error( $is_error ) ) {
		return $is_error;
	}

	return $item;
}

/**
 * Delete an item.
 *
 * @param int $item_id Item ID
 *
 * @return array|false Item array data on success, false on failure.
 * @since 1.1.0
 */
function eaccounting_delete_item( $item_id ) {
	if ( $item_id instanceof Item ) {
		$item_id = $item_id->get_id();
	}

	if ( empty( $item_id ) ) {
		return false;
	}

	$item = new Item( (int) $item_id );
	if ( ! $item->exists() ) {
		return false;
	}

	return $item->delete();
}

/**
 * Retrieves an array of the items matching the given criteria.
 *
 * @param array $args Arguments to retrieve items.
 *
 * @return Item[]|int Array of item objects or count.
 * @since 1.1.0
 *
 */
function eaccounting_get_items( $args = array() ) {
	$defaults = array(
		'number'        => 20,
		'orderby'       => 'name',
		'order'         => 'DESC',
		'include'       => array(),
		'exclude'       => array(),
		'no_found_rows' => false,
		'count_total'   => false,
	);

	$parsed_args = wp_parse_args( $args, $defaults );
	$query       = new \EverAccounting\Item_Query( $parsed_args );
	if ( true === $parsed_args['count_total'] ) {
		return $query->get_total();
	}


	return $query->get_results();
}
