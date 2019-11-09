<?php

defined( 'ABSPATH' ) || exit();

/**
 *  Get rax rate types
 * since 1.0.0
 * @return array
 */
function eaccounting_get_tax_rate_types(){
	return apply_filters('eaccounting_tax_rate_types', array(
		'normal' => __('Normal', 'wp-eaccounting'),
		'inclusive' => __('Inclusive', 'wp-eaccounting'),
		'compound' => __('Compound', 'wp-eaccounting'),
	));
}
/**
 * Insert Tax rate
 *
 * @param $args
 *
 * @return int|WP_Error
 * @since 1.0.0
 */
function eaccounting_insert_tax_rate( $args ) {
	global $wpdb;
	$update = false;
	$id     = null;
	$args   = (array) apply_filters( 'eaccounting_create_tax', $args );

	if ( isset( $args['id'] ) && ! empty( trim( $args['id'] ) ) ) {
		$id          = (int) $args['id'];
		$update      = true;
		$item_before = (array) eaccounting_get_tax_rate ($id );
		if ( is_null( $item_before ) ) {
			return new \WP_Error( 'invalid_action', __( 'Could not find the item to  update', 'wp-ever-accounting' ) );
		}

		$args = array_merge( $item_before, $args );
	}

	$data = array(
		'id'         => empty( $args['id'] ) ? null : absint( $args['id'] ),
		'name'       => ! isset( $args['name'] ) ? '' : sanitize_text_field( $args['name'] ),
		'rate'       => ! isset( $args['rate'] ) ? '' : (float) $args['rate'],
		'type'       => ! isset( $args['type'] ) ? '' : sanitize_text_field( $args['type'] ),
		'status'     => 'active' == $args['status'] ? 'active' : 'inactive',
		'updated_at' => current_time('mysql'),
		'created_at' => empty( $args['created_at'] ) ? current_time('mysql') : $args['created_at'],
	);


	if ( empty( $data['name'] ) ) {
		return new WP_Error( 'empty_content', __( 'Empty name is not permitted', 'wp-eaccounting' ) );
	}

	if ( empty( $data['rate'] ) ) {
		return new WP_Error( 'empty_content', __( 'Tax Rate is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data['type'] ) ) {
		return new WP_Error( 'empty_content', __( 'Tax Type is required', 'wp-ever-accounting' ) );
	}

	$where = array( 'id' => $id );
	$data  = wp_unslash( $data );

	if ( $update ) {
		do_action( 'eaccounting_pre_tax_update', $id, $data );
		if ( false === $wpdb->update( $wpdb->ea_taxes, $data, $where ) ) {
			return new WP_Error( 'db_update_error', __( 'Could not update Tax Rate in the database', 'wp-ever-accounting' ), $wpdb->last_error );
		}
		do_action( 'eaccounting_tax_update', $id, $data, $item_before );
	} else {
		do_action( 'eaccounting_pre_tax_insert', $id, $data );
		if ( false === $wpdb->insert( $wpdb->ea_taxes, $data ) ) {
			return new WP_Error( 'db_insert_error', __( 'Could not insert Tax Rate into the database', 'wp-ever-accounting' ), $wpdb->last_error );
		}
		$id = (int) $wpdb->insert_id;
		do_action( 'eaccounting_tax_insert', $id, $data );
	}

	return $id;
}

/**
 * Get Tax rate
 *
 * @param $id
 *
 * @return object|null
 * @since 1.0.0
 */
function eaccounting_get_tax_rate ($id ) {
	global $wpdb;
	return $wpdb->get_row( $wpdb->prepare( "select * from {$wpdb->ea_taxes} where id=%s", $id ) );
}

/**
 * Delete Tax rate
 *
 * @param $id
 *
 * @return bool
 * @since 1.0.0
 */
function eaccounting_delete_tax_rate( $id ) {
	global $wpdb;
	$id = absint( $id );

	$tax = eaccounting_get_tax_rate ($id );
	if ( is_null( $tax ) ) {
		return false;
	}

	do_action( 'eaccounting_pre_tax_delete', $id, $tax );
	if ( false == $wpdb->delete( $wpdb->ea_taxes, array( 'id' => $id ), array( '%d' ) ) ) {
		return false;
	}
	do_action( 'eaccounting_tax_delete', $id, $tax );

	return true;
}

function eaccounting_get_tax_rates( $args = array(), $count = false ) {
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
		'search_columns' => array( 'name', 'type' ),
		'per_page'       => 20,
		'page'           => 1,
		'offset'         => 0,
	);

	$args        = wp_parse_args( $args, $default );
	$query_from  = "FROM $wpdb->ea_taxes";
	$query_where = 'WHERE 1=1';

	//status
	if ( ! empty( $args['status'] ) ) {
		$query_where .= $wpdb->prepare( " AND $wpdb->ea_taxes.status= %s", sanitize_key( $args['status'] ) );
	}

	//type
	if ( ! empty( $args['type'] ) ) {
		$query_where .= $wpdb->prepare( " AND $wpdb->ea_taxes.type= %s", sanitize_key( $args['type'] ) );
	}

	//fields
	if ( is_array( $args['fields'] ) ) {
		$args['fields'] = array_unique( $args['fields'] );

		$query_fields = array();
		foreach ( $args['fields'] as $field ) {
			$field          = 'id' === $field ? 'id' : sanitize_key( $field );
			$query_fields[] = "$wpdb->ea_taxes.$field";
		}
		$query_fields = implode( ',', $query_fields );
	} elseif ( 'all' == $args['fields'] ) {
		$query_fields = "$wpdb->ea_taxes.*";
	} else {
		$query_fields = "$wpdb->ea_taxes.id";
	}

	//include
	$include = false;
	if ( ! empty( $args['include'] ) ) {
		$include = wp_parse_id_list( $args['include'] );
	}

	if ( ! empty( $include ) ) {
		// Sanitized earlier.
		$ids         = implode( ',', $include );
		$query_where .= " AND $wpdb->ea_taxes.id IN ($ids)";
	} elseif ( ! empty( $args['exclude'] ) ) {
		$ids         = implode( ',', wp_parse_id_list( $args['exclude'] ) );
		$query_where .= " AND $wpdb->ea_taxes.id NOT IN ($ids)";
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
		return $wpdb->get_var( "SELECT count($wpdb->ea_taxes.id) $query_from $query_where" );
	}


	$request = "SELECT $query_fields $query_from $query_where $query_orderby $query_limit";

	if ( is_array( $args['fields'] ) || 'all' == $args['fields'] ) {
		return $wpdb->get_results( $request );
	}

	return $wpdb->get_col( $request );
}
