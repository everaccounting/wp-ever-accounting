<?php
/**
 * EverAccounting Item Functions.
 *
 * All item related function of the plugin.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

defined( 'ABSPATH' ) || exit;

/**
 * Main function for returning item.
 *
 * @param $item
 *
 * @return EverAccounting\Models\Item|null
 * @since 1.1.0
 *
 */
function eaccounting_get_item( $item ) {
	if ( empty( $item ) ) {
		return null;
	}
	try {
		$result = new EverAccounting\Models\Item( $item );

		return $result->exists() ? $result : null;
	} catch ( \EverAccounting\Core\Exception $e ) {
		return null;
	}
}


/**
 *  Create new item programmatically.
 *
 *  Returns a new item object on success.
 *
 * @param array $args {
 *                              An array of elements that make up an invoice to update or insert.
 *
 * @type int $id The item ID. If equal to something other than 0,
 *                                         the item with that id will be updated. Default 0.
 * @type string $name The name of the item.
 * @type string $sku The sku of the item.
 * @type int $image_id The image_id for the item.
 * @type string $description The description of the item.
 * @type double $sale_price The sale_price of the item.
 * @type double $purchase_price The purchase_price for the item.
 * @type int $quantity The quantity of the item.
 * @type int $category_id The category_id of the item.
 * @type int $tax_id The tax_id of the item.
 * @type int $enabled The enabled of the item.
 * }
 *
 * @return EverAccounting\Models\Item|WP_Error|bool
 * @since 1.1.0
 *
 */
function eaccounting_insert_item( $args, $wp_error = true ) {
	// Ensure that we have data.
	if ( empty( $args ) ) {
		return false;
	}

	try {
		// The  id will be provided when updating an item.
		$args = wp_parse_args( $args, array( 'id' => null ) );

		// Retrieve the item.
		$item = new \EverAccounting\Models\Item( $args['id'] );

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
 * Delete an item.
 *
 * @param $item_id
 *
 * @return bool
 * @since 1.1.0
 *
 */
function eaccounting_delete_item( $item_id ) {
	try {
		$item = new EverAccounting\Models\Item( $item_id );

		return $item->exists() ? $item->delete() : false;
	} catch ( \EverAccounting\Core\Exception $e ) {
		return false;
	}
}

/**
 * Get items.
 *
 * @param array $args {
 *
 * @type string $name The name of the item.
 * @type string $sku The sku of the item.
 * @type int $image_id The image_id for the item.
 * @type string $description The description of the item.
 * @type double $sale_price The sale_price of the item.
 * @type double $purchase_price The purchase_price for the item.
 * @type int $quantity The quantity of the item.
 * @type int $category_id The category_id of the item.
 * @type int $tax_id The tax_id of the item.
 * @type int $enabled The enabled of the item.
 * }
 *
 * @return array|int
 * @since 1.1.0
 *
 *
 */
function eaccounting_get_items( $args = array() ) {
	global $wpdb;
	$search_cols  = array( 'name', 'sku', 'description' );
	$orderby_cols = array( 'name', 'sku', 'description', 'sale_price', 'purchase_price', 'quantity', 'enabled', 'date_created' );
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

	$qv    = apply_filters( 'eaccounting_get_items_args', $args );
	$table = 'ea_items';

	$query_fields  = eaccounting_prepare_query_fields( $qv, $table );
	$query_from    = eaccounting_prepare_query_from( $table );
	$query_where   = 'WHERE 1=1';
	$query_where   .= eaccounting_prepare_query_where( $qv, $table );
	$query_orderby = eaccounting_prepare_query_orderby( $qv, $table );
	$query_limit   = eaccounting_prepare_query_limit( $qv );
	$count_total   = true === $qv['count_total'];
	$cache_key     = md5( serialize( $qv ) );
	$results       = wp_cache_get( $cache_key, 'eaccounting_item' );
	$request       = "SELECT $query_fields $query_from $query_where $query_orderby $query_limit";


	if ( false === $results ) {
		if ( $count_total ) {
			$results = (int) $wpdb->get_var( $request );
			wp_cache_set( $cache_key, $results, 'eaccounting_item' );
		} else {
			$results = $wpdb->get_results( $request );
			if ( in_array( $qv['fields'], array( 'all', '*' ), true ) ) {
				foreach ( $results as $key => $item ) {
					wp_cache_set( $item->id, $item, 'eaccounting_item' );
					wp_cache_set( $item->name . '-' . $item->type, $item, 'eaccounting_item' );
				}
			}
			wp_cache_set( $cache_key, $results, 'eaccounting_item' );
		}
	}

	if ( 'objects' === $qv['return'] && true !== $qv['count_total'] ) {
		$results = array_map( 'eaccounting_get_item', $results );
	}

	return $results;
}
