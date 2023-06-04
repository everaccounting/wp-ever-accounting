<?php

use EverAccounting\Models\Product;

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
 * Main function for returning item.
 *
 * @param mixed $item Product object.
 * @return Product|null
 * @since 1.1.0
 */
function eac_get_product( $item ) {
	return Product::get( $item );
}

/**
 *  Create new item programmatically.
 *
 *  Returns a new item object on success.
 *
 * @param array $data Product data.
 * @param bool  $wp_error Optional. Whether to return a WP_Error on failure. Default false.
 *
 * @return Product|WP_Error|bool
 * @since 1.1.0
 */
function eac_insert_product( $data, $wp_error = true ) {
	return Product::insert( $data, $wp_error );
}

/**
 * Delete an item.
 *
 * @param int $item_id Product ID.
 *
 * @return bool
 * @since 1.1.0
 */
function eac_delete_product( $item_id ) {
	$item = eac_get_product( $item_id );
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
 * @return array|int|Product[]
 * @since 1.1.0
 */
function eac_get_products( $args = array(), $count = false ) {
	$defaults = array(
		'limit'   => 20,
		'offset'  => 0,
		'orderby' => 'id',
		'order'   => 'DESC',
		'fields'  => 'all',
	);

	$args = wp_parse_args( $args, $defaults );

	if ( $count ) {
		return Product::count( $args );
	}

	return Product::query( $args );
}
