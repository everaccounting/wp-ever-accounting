<?php

defined( 'ABSPATH' ) || exit();

/**
 * Insert Account
 *
 * @param $args
 *
 * @return int|WP_Error
 * @since 1.0.0
 */
function eaccounting_insert_product( $args ) {
	global $wpdb;
	$update = false;
	$id     = null;
	$args   = (array) apply_filters( 'eaccounting_create_product', $args );

	if ( isset( $args['id'] ) && ! empty( trim( $args['id'] ) ) ) {
		$id          = absint( $args['id'] );
		$update      = true;
		$item_before = (array) eaccounting_get_product( $id );
		if ( is_null( $item_before ) ) {
			return new \WP_Error( 'invalid_action', __( 'Could not find the item to  update', 'wp-ever-accounting' ) );
		}

		$args = array_merge( $item_before, $args );
	}

	$data = array(
		'id'             => $id,
		'name'           => ! isset( $args['name'] ) ? '' : sanitize_text_field( $args['name'] ),
		'sku'            => ! isset( $args['sku'] ) ? '' : sanitize_text_field( $args['sku'] ),
		'description'    => ! isset( $args['description'] ) ? '' : sanitize_textarea_field( $args['description'] ),
		'sale_price'     => ! isset( $args['sale_price'] ) ? '' : eaccounting_sanitize_price($args['sale_price']),
		'purchase_price' => ! isset( $args['purchase_price'] ) ? '0.00' : eaccounting_sanitize_price($args['purchase_price']),
		'quantity'       => ! isset( $args['quantity'] ) ? '0' : absint( $args['quantity'] ),
		'category_id'    => ! isset( $args['category_id'] ) ? '' : absint( $args['category_id'] ),
		'status'         => eaccounting_sanitize_status( $args['status'] ),
		'updated_at'     => current_time( 'Y-m-d H:i:s' ),
		'created_at'     => empty( $args['created_at'] ) ? current_time( 'Y-m-d H:i:s' ) : $args['created_at'],
	);


	if ( empty( $data['name'] ) ) {
		return new WP_Error( 'empty_content', __( 'Product name is required', 'wp-ever-accounting' ) );
	}

	if ( !isset( $data['sale_price'] ) ) {
		return new WP_Error( 'empty_content', __( 'Sale Price is required', 'wp-ever-accounting' ) );
	}

	if ( !isset( $data['purchase_price'] ) ) {
		return new WP_Error( 'empty_content', __( 'Purchase Price is required', 'wp-ever-accounting' ) );
	}

	if ( ! isset( $data['quantity'] ) ) {
		return new WP_Error( 'empty_content', __( 'Quantity is required', 'wp-ever-accounting' ) );
	}

	if ( ! empty( $data['sku'] ) && ! eaccounting_is_sku_available( $data['sku'], $id ) ) {
		return new WP_Error( 'invalid_content', __( 'SKU is taken by other product', 'wp-ever-accounting' ) );
	}

	$where = array( 'id' => $id );
	$data  = wp_unslash( $data );

	if ( $update ) {
		do_action( 'eaccounting_pre_product_update', $id, $data );
		if ( false === $wpdb->update( $wpdb->ea_products, $data, $where ) ) {
			return new WP_Error( 'db_update_error', __( 'Could not update product in the database', 'wp-ever-accounting' ), $wpdb->last_error );
		}
		do_action( 'eaccounting_product_update', $id, $data, $item_before );
	} else {
		do_action( 'eaccounting_pre_product_insert', $id, $data );
		if ( false === $wpdb->insert( $wpdb->ea_products, $data ) ) {
			return new WP_Error( 'db_insert_error', __( 'Could not insert product into the database', 'wp-ever-accounting' ), $wpdb->last_error );
		}
		$id = (int) $wpdb->insert_id;
		do_action( 'eaccounting_product_insert', $id, $data );
	}

	return $id;
}

/**
 * get products by sku
 *
 * @param $sku
 *
 * @return array|object|string|null
 * @since 1.0.0
 */
function eaccounting_get_products_by_sku( $sku ) {
	return eaccounting_get_products( array( 'sku' => $sku, 'fields' => 'id' ) );
}


/**
 * Get account
 *
 * @param $id
 *
 * @return object|null
 * @since 1.0.0
 */
function eaccounting_get_product( $id, $return = OBJECT ) {
	global $wpdb;

	return $wpdb->get_row( $wpdb->prepare( "select * from {$wpdb->ea_products} where id=%s", $id ) );
}

/**
 * Check if product sku is avilable
 *
 * @param $sku
 * @param null $product_id
 *
 * @return bool
 * @since 1.0.0
 */
function eaccounting_is_sku_available( $sku, $product_id = null ) {
	global $wpdb;
	$exist = $wpdb->get_var( $wpdb->prepare( "SELECT id from $wpdb->ea_products WHERE sku=%s", sanitize_text_field( $sku ) ) );

	return ! $exist || $exist == $product_id;
}

/**
 * Delete account
 *
 * @param $id
 *
 * @return bool
 * @since 1.0.0
 */
function eaccounting_delete_product( $id ) {
	global $wpdb;
	$id = absint( $id );

	$account = eaccounting_get_product( $id );
	if ( is_null( $account ) ) {
		return false;
	}

	do_action( 'eaccounting_pre_product_delete', $id, $account );
	if ( false == $wpdb->delete( $wpdb->ea_products, array( 'id' => $id ), array( '%d' ) ) ) {
		return false;
	}
	do_action( 'eaccounting_product_delete', $id, $account );

	return true;
}

/**
 * Get products
 *
 * since 1.0.0
 * @param array $args
 * @param bool $count
 *
 * @return array|object|string|null
 */
function eaccounting_get_products( $args = array(), $count = false ) {
	global $wpdb;
	$query_fields  = '';
	$query_from    = '';
	$query_where   = '';
	$query_orderby = '';
	$query_limit   = '';

	$default = array(
		'include'        => array(),
		'exclude'        => array(),
		'status'         => '',
		'search'         => '',
		'orderby'        => 'id',
		'order'          => 'DESC',
		'fields'         => 'all',
		'search_columns' => array( 'name', 'sku' ),
		'per_page'       => 20,
		'page'           => 1,
		'offset'         => 0,
	);

	$args        = wp_parse_args( $args, $default );
	$query_from  = "FROM $wpdb->ea_products";
	$query_where = 'WHERE 1=1';

	//enabled
	if ( ! empty( $args['status'] ) ) {
		$query_where .= $wpdb->prepare( " AND $wpdb->ea_products.status= %s", sanitize_key( $args['status'] ) );
	}

	//fields
	if ( is_array( $args['fields'] ) ) {
		$args['fields'] = array_unique( $args['fields'] );

		$query_fields = array();
		foreach ( $args['fields'] as $field ) {
			$field          = 'id' === $field ? 'id' : sanitize_key( $field );
			$query_fields[] = "$wpdb->ea_products.$field";
		}
		$query_fields = implode( ',', $query_fields );
	} elseif ( 'all' == $args['fields'] ) {
		$query_fields = "$wpdb->ea_products.*";
	} else {
		$query_fields = "$wpdb->ea_products.id";
	}

	//include
	$include = false;
	if ( ! empty( $args['include'] ) ) {
		$include = wp_parse_id_list( $args['include'] );
	}

	if ( ! empty( $include ) ) {
		// Sanitized earlier.
		$ids         = implode( ',', $include );
		$query_where .= " AND $wpdb->ea_products.id IN ($ids)";
	} elseif ( ! empty( $args['exclude'] ) ) {
		$ids         = implode( ',', wp_parse_id_list( $args['exclude'] ) );
		$query_where .= " AND $wpdb->ea_products.id NOT IN ($ids)";
	}

	//search
	$search = '';
	if ( isset( $args['search'] ) ) {
		$search = trim( $args['search'] );
	}
	if ( $search ) {
		$searches = array();
		$cols     = array_map( 'sanitize_key', $args['search_columns'] );
		$like     = '%' . $wpdb->esc_like( $search ) . '%';
		foreach ( $cols as $col ) {
			$searches[] = $wpdb->prepare( "$col LIKE %s", $like );
		}

		$query_where .= ' AND (' . implode( ' OR ', $searches ) . ')';
	}


	//ordering
	$order         = isset( $args['order'] ) ? esc_sql( strtoupper( $args['order'] ) ) : 'ASC';
	$order_by      = esc_sql( $args['orderby'] );
	$query_orderby = sprintf( " ORDER BY %s %s ", $order_by, $order );

	// limit
	if ( isset( $args['per_page'] ) && $args['per_page'] > 0 ) {
		if ( $args['offset'] ) {
			$query_limit = $wpdb->prepare( 'LIMIT %d, %d', $args['offset'], $args['per_page'] );
		} else {
			$query_limit = $wpdb->prepare( 'LIMIT %d, %d', $args['per_page'] * ( $args['page'] - 1 ), $args['per_page'] );
		}
	}

	if ( $count ) {
		return $wpdb->get_var( "SELECT count($wpdb->ea_products.id) $query_from $query_where" );
	}


	$request = "SELECT $query_fields $query_from $query_where $query_orderby $query_limit";

	if ( is_array( $args['fields'] ) || 'all' == $args['fields'] ) {
		return $wpdb->get_results( $request );
	}

	return $wpdb->get_col( $request );
}




