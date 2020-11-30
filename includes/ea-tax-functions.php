<?php
/**
 * EverAccounting Tax functions.
 *
 * Functions related to taxes.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get tax types.
 *
 * @return array
 * @since 1.1.0
 */
function eaccounting_get_tax_types() {
	$types = array(
		'fixed'     => __( 'Fixed', 'wp-ever-accounting' ),
		'normal'    => __( 'Normal', 'wp-ever-accounting' ),
		'inclusive' => __( 'Inclusive', 'wp-ever-accounting' ),
		'compound'  => __( 'Compound', 'wp-ever-accounting' ),
	);

	return apply_filters( 'eaccounting_tax_types', $types );
}

/**
 * Main function for returning tax.
 *
 * @param $tax
 *
 * @return \EverAccounting\Models\Tax|null
 * @since 1.1.0
 *
 */
function eaccounting_get_tax( $tax ) {
	if ( empty( $tax ) ) {
		return null;
	}
	try {
		$result = new EverAccounting\Models\Tax( $tax );

		return $result->exists() ? $result : null;
	} catch ( \EverAccounting\Core\Exception $e ) {
		return null;
	}
}

/**
 *  Create new tax programmatically.
 *
 *  Returns a new tax object on success.
 *
 * @param array $args {
 *                       An array of elements that make up an invoice to update or insert.
 *
 * @type int $id The tax ID. If equal to something other than 0,
 *                                         the tax with that id will be updated. Default 0.
 *
 * @type string $name The name of the tax.
 * @type double $rate The rate of the tax.
 * @type string $type The type for the tax.
 * @type int $enabled Status of the tax
 * }
 *
 * @return \EverAccounting\Models\Tax|WP_Error|bool
 * @since 1.1.0
 *
 */
function eaccounting_insert_tax( $args, $wp_error = true ) {
	// Ensure that we have data.
	if ( empty( $args ) ) {
		return false;
	}
	try {
		// The  id will be provided when updating an item.
		$args = wp_parse_args( $args, array( 'id' => null ) );

		// Retrieve the category.
		$item = new \EverAccounting\Models\Tax( $args['id'] );

		// Load new data.
		$item->set_props( $args );

		// Save the item
		$item->save();

		return $item;
	} catch ( \EverAccounting\Core\Exception $e ) {
		return $wp_error ? new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) ) : 0;
	}
}

/**
 * Delete an tax.
 *
 * @param $tax_id
 *
 * @return bool
 * @since 1.1.0
 *
 */
function eaccounting_delete_tax( $tax_id ) {
	try {
		$tax = new EverAccounting\Models\Tax( $tax_id );
		return $tax->exists() ? $tax->delete() : false;
	} catch ( \EverAccounting\Core\Exception $e ) {
		return false;
	}
}

/**
 * Get taxes.
 *
 * @param array $args {
 *
 * @type string $name The name of the tax.
 * @type double $rate The rate of the tax.
 * @type string $type The type for the tax.
 * @type int $enabled Status of the tax.
 * }
 *
 *
 * @return array|int
 * @since 1.1.0
 *
 */
function eaccounting_get_taxes( $args = array() ) {
	global $wpdb;
	$search_cols  = array( 'name', 'rate', 'type' );
	$orderby_cols = array( 'name', 'rate', 'type', 'enabled', 'date_created' );
	// Prepare args.
	$args = wp_parse_args(
		$args,
		array(
			'status'       => 'all',
			'type'         => '',
			'include'      => '',
			'search'       => '',
			'search_cols'  => $search_cols,
			'orderby_cols' => $orderby_cols,
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

	$qv    = apply_filters( 'eaccounting_get_taxes_args', $args );
	$table = 'ea_taxes';

	$query_fields  = eaccounting_prepare_query_fields( $qv, $table );
	$query_from    = eaccounting_prepare_query_from( $table );
	$query_where   = 'WHERE 1=1';
	$query_where   .= eaccounting_prepare_query_where( $qv, $table );
	$query_orderby = eaccounting_prepare_query_orderby( $qv, $table );
	$query_limit   = eaccounting_prepare_query_limit( $qv );
	$count_total   = true === $qv['count_total'];
	$cache_key     = md5( serialize( $qv ) );
	$results       = wp_cache_get( $cache_key, 'eaccounting_tax' );
	$request       = "SELECT $query_fields $query_from $query_where $query_orderby $query_limit";

	if ( false === $results ) {
		if ( $count_total ) {
			$results = (int) $wpdb->get_var( $request );
			wp_cache_set( $cache_key, $results, 'eaccounting_tax' );
		} else {
			$results = $wpdb->get_results( $request );
			if ( in_array( $qv['fields'], array( 'all', '*' ), true ) ) {
				foreach ( $results as $key => $item ) {
					wp_cache_set( $item->id, $item, 'eaccounting_tax' );
					wp_cache_set( $item->name . '-' . $item->type, $item, 'eaccounting_tax' );
				}
			}
			wp_cache_set( $cache_key, $results, 'eaccounting_tax' );
		}
	}

	if ( 'objects' === $qv['return'] && true !== $qv['count_total'] ) {
		$results = array_map( 'eaccounting_get_tax', $results );
	}

	return $results;
}
