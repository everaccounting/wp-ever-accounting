<?php

use EverAccounting\Models\Category;

defined( 'ABSPATH' ) || exit;

/**
 * Get all the available type of category the plugin support.
 *
 * @since 1.1.0
 * @return array
 */
function eac_get_category_types() {
	$types = array(
		'expense' => esc_html__( 'Expense', 'wp-ever-accounting' ),
		'income'  => esc_html__( 'Income', 'wp-ever-accounting' ),
		'item'    => esc_html__( 'Item', 'wp-ever-accounting' ),
	);

	return apply_filters( 'ever_accounting_category_types', $types );
}

/**
 * Get category.
 *
 * @param mixed $category Category ID or object.
 *
 * @since 1.1.0
 */
function eac_get_category( $category ) {
	return Category::find( $category );
}

/**
 * Insert a category.
 *
 * @param array $data Category data.
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
 * @param int $id Category ID.
 *
 * @return bool
 * @since 1.1.0
 */
function eac_delete_category( $id ) {
	$category = eac_get_category( $id );

	return $category ? $category->delete() : false;
}

/**
 * Get category items.
 *
 * @param array $args Query arguments.
 * @param bool  $count Whether to return the count of items.
 *
 * @since 1.1.0
 * @return int|array|Category[]
 */
function eac_get_categories( $args = array(), $count = false ) {
	if ( $count ) {
		return Category::count( $args );
	}

	return Category::query( $args );
}
