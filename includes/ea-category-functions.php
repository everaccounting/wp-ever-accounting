<?php
/**
 * EverAccounting category Functions.
 *
 * All category related function of the plugin.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

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
	);

	return apply_filters( 'eaccounting_category_types', $types );
}

/**
 * Get the category type label of a specific type.
 *
 * @param $type
 *
 * @return string
 * @since 1.1.0
 *
 */
function eaccounting_get_category_type( $type ) {
	$types = eaccounting_get_category_types();

	return array_key_exists( $type, $types ) ? $types[ $type ] : null;
}

/**
 * Get category.
 *
 * @param $category
 *
 * @return null|EverAccounting\Models\Category
 * @since 1.1.0
 *
 */
function eaccounting_get_category( $category ) {
	if ( empty( $category ) ) {
		return null;
	}
	try {
		$result = new EverAccounting\Models\Category( $category );

		return $result->exists() ? $result : null;
	} catch ( \EverAccounting\Core\Exception $e ) {
		return null;
	}
}

/**
 * Insert a category.
 *
 * @param array $data {
 *                            An array of elements that make up an category to update or insert.
 *
 * @type int $id The category ID. If equal to something other than 0, the category with that ID will be updated. Default 0.
 *
 * @type string $name Unique name of the category.
 *
 * @type string $type Category type.
 *
 * @type string $color Color of the category.
 *
 * @type int $enabled The status of the category. Default 1.
 *
 * @type string $date_created The date when the category is created. Default is current current time.
 *
 * }
 *
 * @param bool $wp_error Whether to return false or WP_Error on failure.
 *
 * @return int|\WP_Error|\EverAccounting\Models\Category|bool The value 0 or WP_Error on failure. The Category object on success.
 * @since 1.1.0
 *
 */
function eaccounting_insert_category( $data = array(), $wp_error = false ) {
	// Ensure that we have data.
	if ( empty( $data ) ) {
		return false;
	}
	try {
		// The  id will be provided when updating an item.
		$data = wp_parse_args( $data, array( 'id' => null ) );

		// Retrieve the category.
		$item = new \EverAccounting\Models\Category( $data['id'] );

		// Load new data.
		$item->set_props( $data );

		$item->save();

		return $item;
	} catch ( \EverAccounting\Core\Exception $e ) {
		return $wp_error ? new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) ) : 0;
	}
}

/**
 * Delete a category.
 *
 * @param $category_id
 *
 * @return bool
 * @since 1.1.0
 *
 */
function eaccounting_delete_category( $category_id ) {
	try {
		$category = new EverAccounting\Models\Category( $category_id );

		return $category->exists() ? $category->delete() : false;
	} catch ( \EverAccounting\Core\Exception $e ) {
		return false;
	}
}

/**
 * Get category items.
 *
 * @param array $args
 *
 * @return int|array|null
 * @since 1.1.0
 *
 */
function eaccounting_get_categories( $args = array() ) {
	global $wpdb;
	// Prepare args.
	$args = wp_parse_args(
		$args,
		array(
			'status'       => 'all',
			'type'         => '',
			'include'      => '',
			'search'       => '',
			'search_cols'  => array( 'name', 'type' ),
			'orderby_cols' => array( 'name', 'type', 'color', 'enabled', 'date_created' ),
			'fields'       => '*',
			'orderby'      => 'id',
			'order'        => 'ASC',
			'number'       => 20,
			'offset'       => 0,
			'paged'        => 1,
			'return'       => 'objects',
			'count_total'  => false,
		)
	);

	$qv    = apply_filters( 'eaccounting_get_categories_args', $args );
	$table = 'ea_categories';

	$query_fields  = eaccounting_prepare_query_fields( $qv, $table );
	$query_from    = eaccounting_prepare_query_from( $table );
	$query_where   = 'WHERE 1=1';
	$query_where   .= eaccounting_prepare_query_where( $qv, $table );
	$query_orderby = eaccounting_prepare_query_orderby( $qv, $table );
	$query_limit   = eaccounting_prepare_query_limit( $qv );
	$count_total   = true === $qv['count_total'];
	$cache_key     = md5( serialize( $qv ) );
	$results       = wp_cache_get( $cache_key, 'eaccounting_category' );
	$request       = "SELECT $query_fields $query_from $query_where $query_orderby $query_limit";


	if ( false === $results ) {
		if ( $count_total ) {
			$results = (int) $wpdb->get_var( $request );
			wp_cache_set( $cache_key, $results, 'eaccounting_category' );
		} else {
			$results = $wpdb->get_results( $request );
			if ( in_array( $qv['fields'], array( 'all', '*' ), true ) ) {
				foreach ( $results as $key => $item ) {
					wp_cache_set( $item->id, $item, 'eaccounting_category' );
					wp_cache_set( $item->name . '-' . $item->type, $item, 'eaccounting_category' );
				}
			}
			wp_cache_set( $cache_key, $results, 'eaccounting_category' );
		}
	}

	if ( 'objects' === $qv['return'] && true !== $qv['count_total'] ) {
		$results = array_map( 'eaccounting_get_category', $results );
	}

	return $results;
}
