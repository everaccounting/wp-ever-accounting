<?php

use EverAccounting\Models\Item;

defined( 'ABSPATH' ) || exit;

/**
 * Get item units
 *
 * @return array
 * @since 1.1.6
 */
function eac_get_item_units() {
	return apply_filters(
		'ever_accounting_item_units',
		array(
			'pc'    => __( 'Piece', 'wp-ever-accounting' ),
			'box'   => __( 'Box', 'wp-ever-accounting' ),
			'cm'    => __( 'Centimeter', 'wp-ever-accounting' ),
			'doz'   => __( 'Dozen', 'wp-ever-accounting' ),
			'ft'    => __( 'Feet', 'wp-ever-accounting' ),
			'gram'  => __( 'Gram', 'wp-ever-accounting' ),
			'inch'  => __( 'Inch', 'wp-ever-accounting' ),
			'kg'    => __( 'Kilogram', 'wp-ever-accounting' ),
			'km'    => __( 'Kilometer', 'wp-ever-accounting' ),
			'lb'    => __( 'Pound', 'wp-ever-accounting' ),
			'liter' => __( 'Liter', 'wp-ever-accounting' ),
			'meter' => __( 'Meter', 'wp-ever-accounting' ),
			'mg'    => __( 'Milligram', 'wp-ever-accounting' ),
			'mile'  => __( 'Mile', 'wp-ever-accounting' ),
			'mm'    => __( 'Millimeter', 'wp-ever-accounting' ),
			'oz'    => __( 'Ounce', 'wp-ever-accounting' ),
		)
	);
}


/**
 * Main function for returning item.
 *
 * @param mixed  $item Item object.
 * @param string $column Optional. Column to get. Default null.
 * @param array  $args Optional. Additional arguments. Default empty array.
 * @return Item|null
 * @since 1.1.0
 */
function eac_get_item( $item, $column = null, $args = array() ) {
	return Item::get( $item, $column, $args );
}

/**
 *  Create new item programmatically.
 *
 *  Returns a new item object on success.
 *
 * @param array $data Item data.
 * @param bool  $wp_error Optional. Whether to return a WP_Error on failure. Default false.
 *
 * @return Item|WP_Error|bool
 * @since 1.1.0
 */
function eac_insert_item( $data, $wp_error = true ) {
	return Item::insert( $data, $wp_error );
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
	if ( ! $item ) {
		return false;
	}

	return $item->delete();
}

/**
 * Get items.
 *
 * @param array $args Query arguments.
 * @param bool  $count Return only the total found items.
 *
 * @return array|int|Item[]
 * @since 1.1.0
 */
function eac_get_items( $args = array(), $count = false ) {
	$defaults = array(
		'limit'   => 20,
		'offset'  => 0,
		'orderby' => 'id',
		'order'   => 'DESC',
		'fields'  => 'all',
	);

	$args = wp_parse_args( $args, $defaults );

	if ( $count ) {
		return Item::count( $args );
	}

	return Item::query( $args );
}
