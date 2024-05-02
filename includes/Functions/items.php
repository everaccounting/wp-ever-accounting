<?php
/**
 * EverAccounting Item Functions.
 *
 * All item related function of the plugin.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

use EverAccounting\Models\Item;

defined( 'ABSPATH' ) || exit;

/**
 * Get item units
 *
 * @return array
 * @since 1.1.6
 */
function eac_get_unit_types() {
	return apply_filters(
		'ever_accounting_unit_types',
		array(
			'box'   => __( 'Box', 'wp-ever-accounting' ),
			'cm'    => __( 'Centimeter', 'wp-ever-accounting' ),
			'day'   => __( 'Day', 'wp-ever-accounting' ),
			'doz'   => __( 'Dozen', 'wp-ever-accounting' ),
			'ft'    => __( 'Feet', 'wp-ever-accounting' ),
			'gm'    => __( 'Gram', 'wp-ever-accounting' ),
			'hr'    => __( 'Hour', 'wp-ever-accounting' ),
			'inch'  => __( 'Inch', 'wp-ever-accounting' ),
			'kg'    => __( 'Kilogram', 'wp-ever-accounting' ),
			'km'    => __( 'Kilometer', 'wp-ever-accounting' ),
			'l'     => __( 'Liter', 'wp-ever-accounting' ),
			'lb'    => __( 'Pound', 'wp-ever-accounting' ),
			'm'     => __( 'Meter', 'wp-ever-accounting' ),
			'mg'    => __( 'Milligram', 'wp-ever-accounting' ),
			'mile'  => __( 'Mile', 'wp-ever-accounting' ),
			'min'   => __( 'Minute', 'wp-ever-accounting' ),
			'mm'    => __( 'Millimeter', 'wp-ever-accounting' ),
			'month' => __( 'Month', 'wp-ever-accounting' ),
			'oz'    => __( 'Ounce', 'wp-ever-accounting' ),
			'pc'    => __( 'Piece', 'wp-ever-accounting' ),
			'sec'   => __( 'Second', 'wp-ever-accounting' ),
			'unit'  => __( 'Unit', 'wp-ever-accounting' ),
			'week'  => __( 'Week', 'wp-ever-accounting' ),
			'year'  => __( 'Year', 'wp-ever-accounting' ),
		)
	);
}

/**
 * Get item types.
 *
 * @return array
 * @since 1.1.6
 */
function eac_get_item_types() {
	return apply_filters(
		'ever_accounting_item_types',
		array(
			'standard' => __( 'Standard Item', 'wp-ever-accounting' ),
			'shipping' => __( 'Shipping Fee', 'wp-ever-accounting' ),
			'fee'      => __( 'Fee Item', 'wp-ever-accounting' ),
		)
	);
}


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
 * @return Item[]|int
 * @since 1.1.0
 */
function eac_get_items( $args = array(), $count = false ) {
	if ( $count ) {
		return Item::count( $args );
	}

	return Item::query( $args );
}
