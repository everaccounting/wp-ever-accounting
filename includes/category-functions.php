<?php

defined( 'ABSPATH' ) || exit();

/**
 * Category types
 *
 * @return array
 * @since 1.0.0
 */
function eaccounting_get_category_types() {
	$types = array(
		'expense' => __( 'Expense', 'wp-ever-accounting' ),
		'income'  => __( 'Income', 'wp-ever-accounting' ),
		'product' => __( 'Product', 'wp-ever-accounting' ),
		'other'   => __( 'Other', 'wp-ever-accounting' ),
	);

	return apply_filters( 'eaccounting_category_types', $types );
}

/**
 * Create category
 *
 * @param $args
 *
 * @return int|WP_Error|null
 * @since 1.0.0
 */
function eaccounting_insert_category( $args ) {
	global $wpdb;
	$update = false;
	$id     = null;
	$args   = (array) apply_filters( 'eaccounting_insert_category', $args );

	if ( isset( $args['id'] ) && ! empty( trim( $args['id'] ) ) ) {
		$id          = (int) $args['id'];
		$update      = true;
		$item_before = (array) eaccounting_get_category( $id );
		if ( is_null( $item_before ) ) {
			return new \WP_Error( 'invalid_action', __( 'Could not find the item to  pdate', 'wp-ever-accounting' ) );
		}

		$args = array_merge( $item_before, $args );
	}


	$data = array(
		'id'         => empty( $args['id'] ) ? null : absint( $args['id'] ),
		'name'       => ! isset( $args['name'] ) ? '' : sanitize_text_field( $args['name'] ),
		'type'       => ! isset( $args['type'] ) ? '' : sanitize_text_field( $args['type'] ),
		'color'      => ! isset( $args['color'] ) ? '' : sanitize_hex_color( $args['color'] ),
		'status'     => 'active' == $args['status'] ? 'active' : 'inactive',
		'updated_at' => date( 'Y-m-d H:i:s' ),
		'created_at' => empty( $args['created_at'] ) ? date( 'Y-m-d H:i:s' ) : $args['created_at'],
	);

	if ( empty( $data['name'] ) ) {
		return new WP_Error( 'empty_content', __( 'Category name is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data['type'] ) ) {
		return new WP_Error( 'empty_content', __( 'Category type is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data['color'] ) ) {
		return new WP_Error( 'empty_content', __( 'Category color is required', 'wp-ever-accounting' ) );
	}

	if ( ! array_key_exists( $data['type'], eaccounting_get_category_types() ) ) {
		return new WP_Error( 'invalid_content', __( 'Invalid category type', 'wp-ever-accounting' ) );
	}


	$where = array( 'id' => $id );
	$data  = wp_unslash( $data );

	if ( $update ) {
		do_action( 'eaccounting_pre_category_update', $id, $data );
		if ( false === $wpdb->update( $wpdb->ea_categories, $data, $where ) ) {
			return new WP_Error( 'db_update_error', sprintf( __( 'Could not update category in the database - (%s)', 'wp-ever-accounting' ), $wpdb->last_error ), $wpdb->last_error );
		}
		do_action( 'eaccounting_category_update', $id, $data, $item_before );
	} else {
		do_action( 'eaccounting_pre_category_insert', $id, $data );
		if ( false === $wpdb->insert( $wpdb->ea_categories, $data ) ) {

			return new WP_Error( 'db_insert_error', __( 'Could not insert category into the database', 'wp-ever-accounting' ), $wpdb->last_error );
		}
		$id = (int) $wpdb->insert_id;
		do_action( 'eaccounting_category_insert', $id, $data );
	}

	return $id;
}


/**
 * Get category
 *
 * @param $id
 *
 * @return object|null
 * @since 1.0.0
 */
function eaccounting_get_category( $id ) {
	global $wpdb;

	return $wpdb->get_row( $wpdb->prepare( "select * from {$wpdb->ea_categories} where id=%s", $id ) );
}


/**
 * Delete category
 *
 * @param $id
 *
 * @return bool
 * @since 1.0.0
 */
function eaccounting_delete_category( $id ) {
	global $wpdb;
	$id = absint( $id );

	$category = eaccounting_get_category( $id );
	if ( is_null( $category ) ) {
		return false;
	}

	do_action( 'eaccounting_pre_category_delete', $id, $category );
	if ( false == $wpdb->delete( $wpdb->ea_categories, array( 'id' => $id ), array( '%d' ) ) ) {
		return false;
	}
	do_action( 'eaccounting_category_delete', $id, $category );

	return true;
}


/**
 * Get categories
 *
 * @param array $args
 * @param bool $count
 *
 * @return array|null|int
 * @since 1.0.0
 */
function eaccounting_get_categories( $args = array(), $count = false ) {
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
		'type'           => '',
		'search'         => '',
		'orderby'        => 'id',
		'order'          => 'DESC',
		'fields'         => 'all',
		'search_columns' => array( 'name', 'type' ),
		'per_page'       => 20,
		'page'           => 1,
		'offset'         => 0,
	);

	$args        = wp_parse_args( $args, $default );
	$query_from  = "FROM $wpdb->ea_categories";
	$query_where = 'WHERE 1=1';

	//status
	if ( ! empty( $args['status'] ) ) {
		$query_where .= $wpdb->prepare( " AND $wpdb->ea_categories.status= %s", sanitize_key( $args['status'] ) );
	}

	//type
	if ( ! empty( $args['type'] ) ) {
		$query_where .= $wpdb->prepare( " AND $wpdb->ea_categories.type= %s", sanitize_key( $args['type'] ) );
	}

	//fields
	if ( is_array( $args['fields'] ) ) {
		$args['fields'] = array_unique( $args['fields'] );

		$query_fields = array();
		foreach ( $args['fields'] as $field ) {
			$field          = 'id' === $field ? 'id' : sanitize_key( $field );
			$query_fields[] = "$wpdb->ea_categories.$field";
		}
		$query_fields = implode( ',', $query_fields );
	} elseif ( 'all' == $args['fields'] ) {
		$query_fields = "$wpdb->ea_categories.*";
	} else {
		$query_fields = "$wpdb->ea_categories.id";
	}

	//include
	$include = false;
	if ( ! empty( $args['include'] ) ) {
		$include = wp_parse_id_list( $args['include'] );
	}

	if ( ! empty( $include ) ) {
		// Sanitized earlier.
		$ids         = implode( ',', $include );
		$query_where .= " AND $wpdb->ea_categories.id IN ($ids)";
	} elseif ( ! empty( $args['exclude'] ) ) {
		$ids         = implode( ',', wp_parse_id_list( $args['exclude'] ) );
		$query_where .= " AND $wpdb->ea_categories.id NOT IN ($ids)";
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
		return $wpdb->get_var( "SELECT count($wpdb->ea_categories.id) $query_from $query_where" );
	}


	$request = "SELECT $query_fields $query_from $query_where $query_orderby $query_limit";

	if ( is_array( $args['fields'] ) || 'all' == $args['fields'] ) {
		return $wpdb->get_results( $request );
	}

	return $wpdb->get_col( $request );
}
