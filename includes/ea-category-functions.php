<?php
/**
 * EverAccounting category Functions.
 *
 * All category related function of the plugin.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

use EverAccounting\Category;

defined( 'ABSPATH' ) || exit();

/**
 * Get all the available type of category the plugin support.
 *
 * @return array
 * @since 1.1.0
 */
function eaccounting_get_category_types() {
	$types = array(
		'expense' => __( 'Expense', 'wp-ever-accounting' ),
		'income'  => __( 'Income', 'wp-ever-accounting' ),
		'other'   => __( 'Other', 'wp-ever-accounting' ),
		'item'    => __( 'Item', 'wp-ever-accounting' ),
	);

	return apply_filters( 'eaccounting_category_types', $types );
}

/**
 * Retrieves category data given a category id or category object.
 *
 * @param int|object|Category $category category to retrieve
 * @param string              $output The required return type. One of OBJECT, ARRAY_A, or ARRAY_N. Default OBJECT.
 *
 * @return Category|array|null
 * @since 1.1.0
 */
function eaccounting_get_category( $category, $output = OBJECT ) {
	if ( empty( $category ) ) {
		return null;
	}

	if ( $category instanceof Category ) {
		$_category = $category;
	} else {
		$_category = new Category( $category );
	}

	if ( ! $_category->exists() ) {
		return null;
	}

	if ( ARRAY_A === $output ) {
		return $_category->to_array();
	}

	if ( ARRAY_N === $output ) {
		return array_values( $_category->to_array() );
	}

	return $_category;
}

/**
 *  Insert or update a category.
 *
 * @param array|object|Category $data An array, object, or category object of data arguments.
 *
 * @return Category|WP_Error The category object or WP_Error otherwise.
 * @global wpdb $wpdb WordPress database abstraction object.
 * @since 1.1.0
 */
function eaccounting_insert_category( $data ) {
	if ( $data instanceof Category ) {
		$data = $data->to_array();
	} elseif ( is_object( $data ) ) {
		$data = get_object_vars( $data );
	}

	if ( empty( $data ) || ! is_array( $data ) ) {
		return new WP_Error( 'invalid_category_data', __( 'Category could not be saved.', 'wp-ever-accounting' ) );
	}

	$data     = wp_parse_args( $data, array( 'id' => null ) );
	$category = new Category( (int) $data['id'] );
	$category->set_props( $data );
	$is_error = $category->save();
	if ( is_wp_error( $is_error ) ) {
		return $is_error;
	}

	return $category;
}

/**
 * Delete an category.
 *
 * @param int $category_id Category ID
 *
 * @return array|false Category array data on success, false on failure.
 * @since 1.1.0
 */
function eaccounting_delete_category( $category_id ) {
	if ( $category_id instanceof Category ) {
		$category_id = $category_id->get_id();
	}

	if ( empty( $category_id ) ) {
		return false;
	}

	$category = new Category( (int) $category_id );
	if ( ! $category->exists() ) {
		return false;
	}

	return $category->delete();
}

/**
 * Retrieves an array of the categories matching the given criteria.
 *
 * @param array $args Arguments to retrieve categories.
 *
 * @return Category[]|int Array of category objects or count.
 * @since 1.1.0
 */
function eaccounting_get_categories( $args = array() ) {
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
	$query       = new \EverAccounting\Category_Query( $parsed_args );
	if ( true === $parsed_args['count_total'] ) {
		return $query->get_total();
	}

	return $query->get_results();
}
