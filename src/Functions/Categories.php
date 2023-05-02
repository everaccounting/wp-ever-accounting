<?php

use EverAccounting\Models\Category;

defined( 'ABSPATH' ) || exit;


/**
 * Get all the available type of category the plugin support.
 *
 * @return array
 * @since 1.1.0
 */
function eac_get_category_types() {
	$types = array(
		'expense' => esc_html__( 'Expense', 'wp-ever-accounting' ),
		'payment' => esc_html__( 'Payment', 'wp-ever-accounting' ),
		'other'   => esc_html__( 'Other', 'wp-ever-accounting' ),
		'item'    => esc_html__( 'Item', 'wp-ever-accounting' ),
	);

	return apply_filters( 'ever_accounting_category_types', $types );
}

/**
 * Get category.
 *
 * @param mixed  $category Category ID or object.
 * @param string $column Optional. Column to get. Default null.
 * @param array  $args Optional. Additional arguments. Default empty array.
 * @return null|EverAccounting\Models\Category
 * @since 1.1.0
 */
function eac_get_category( $category, $column = null, $args = array() ) {
	return Category::get( $category, $column, $args );
}

/**
 * Insert a category.
 *
 * @param array $data   Category data.
 * @param bool  $wp_error Whether to return false or WP_Error on failure.
 *
 * @since 1.1.0
 * @return int|\WP_Error|Category|bool The value 0 or WP_Error on failure. The Category object on success.
 */
function eac_insert_category( $data = array(), $wp_error = true ) {
	return Category::insert( $data, $wp_error );
}

/**
 * Delete a category.
 *
 * @param int $category_id Category ID.
 *
 * @return bool
 * @since 1.1.0
 */
function eac_delete_category( $category_id ) {
	$category = eac_get_category( $category_id );
	if ( ! $category ) {
		return false;
	}

	return $category->delete();
}

/**
 * Get category items.
 *
 * @param array $args Query arguments.
 * @param bool  $count Whether to return the count of items.
 *
 * @return int|array|Category[]
 * @since 1.1.0
 */
function eac_get_categories( $args = array(), $count = false ) {
	$defaults = array(
		'limit'   => 20,
		'offset'  => 0,
		'orderby' => 'id',
		'order'   => 'DESC',
		'fields'  => 'all',
	);

	$args = wp_parse_args( $args, $defaults );

	if ( $count ) {
		return Category::count( $args );
	}

	return Category::query( $args );
}
