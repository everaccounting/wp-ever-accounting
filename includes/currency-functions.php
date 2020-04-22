<?php

defined( 'ABSPATH' ) || exit();

/**
 * Create currency
 *
 * @param $args
 *
 * @return int|WP_Error|null
 * @since 1.0.0
 */
function eaccounting_insert_currency( $args ) {
	global $wpdb;
	$update = false;
	$id     = null;
	$args   = (array) apply_filters( 'eaccounting_insert_currency', $args );

	if ( isset( $args['id'] ) && ! empty( trim( $args['id'] ) ) ) {
		$id          = (int) $args['id'];
		$update      = true;
		$item_before = (array) eaccounting_get_currency( $id );
		if ( is_null( $item_before ) ) {
			return new \WP_Error( 'invalid_action', __( 'Could not find the item to delete', 'wp-ever-accounting' ) );
		}

		$args = array_merge( $item_before, $args );
	}

	$data = array(
		'id'         => empty( $args['id'] ) ? null : absint( $args['id'] ),
		'name'       => ! isset( $args['name'] ) ? '' : sanitize_text_field( $args['name'] ),
		'code'       => ! isset( $args['code'] ) ? '' : sanitize_text_field( $args['code'] ),
		'rate'       => ! isset( $args['rate'] ) ? '' : preg_replace( '/[^0-9\.]/', '', $args['rate'] ),
		'creator_id' => empty( $args['creator_id'] ) ? eaccounting_get_creator_id() : $args['creator_id'],
		'created_at' => empty( $args['created_at'] ) ? date( 'Y-m-d H:i:s' ) : $args['created_at'],
	);

	$required = array(
		'code' => __( 'code', 'wp-ever-accounting' ),
		'rate' => __( 'Rate', 'wp-ever-accounting' ),
	);

	foreach ( $required as $prop => $label ) {
		if ( empty( $data[ $prop ] ) ) {
			return new WP_Error( 'empty_content', sprintf( __( '%s is required', 'wp-ever-accounting' ), $label ) );
		}
	}

	$code_exist = eaccounting_get_currency( $data['code'], 'code' );
	if ( $code_exist && $code_exist->id != $id ) {
		return new WP_Error( 'invalid_name', __( 'Currency already exist', 'wp-ever-accounting' ) );
	}

	$name_exist = eaccounting_get_currency( $data['name'], 'name' );
	if ( $name_exist && $name_exist->id != $id ) {
		return new WP_Error( 'invalid_name', __( 'Currency name already exist', 'wp-ever-accounting' ) );
	}

	$where = array( 'id' => $id );
	$data  = wp_unslash( $data );

	if ( $update ) {
		do_action( 'eaccounting_pre_currency_update', $id, $data );
		if ( false === $wpdb->update( $wpdb->ea_currencies, $data, $where ) ) {
			return new WP_Error( 'db_update_error', sprintf( __( 'Could not update currency in the database - (%s)', 'wp-ever-accounting' ), $wpdb->last_error ), $wpdb->last_error );
		}
		do_action( 'eaccounting_currency_update', $id, $data, $item_before );
	} else {
		do_action( 'eaccounting_pre_currency_insert', $id, $data );
		if ( false === $wpdb->insert( $wpdb->ea_currencies, $data ) ) {

			return new WP_Error( 'db_insert_error', __( 'Could not insert currency into the database', 'wp-ever-accounting' ), $wpdb->last_error );
		}
		$id = (int) $wpdb->insert_id;
		do_action( 'eaccounting_currency_insert', $id, $data );
	}

	return $id;
}

/**
 * Get Category
 * since 1.0.0
 *
 * @param $id
 * @param string $by
 *
 * @return array|object|void|null
 */
function eaccounting_get_currency( $id, $by = 'id' ) {
	global $wpdb;
	switch ( $by ) {
		case 'name':
			$name = sanitize_text_field( $id );
			$sql  = $wpdb->prepare( "SELECT * FROM $wpdb->ea_currencies WHERE name=%s", $name );
			break;
		case 'code':
			$code = sanitize_text_field( $id );
			$sql  = $wpdb->prepare( "SELECT * FROM $wpdb->ea_currencies WHERE code=%s", $code );
			break;
		case 'id':
		default:
			$id  = absint( $id );
			$sql = $wpdb->prepare( "SELECT * FROM $wpdb->ea_currencies WHERE id=%s", $id );
			break;
	}

	return $wpdb->get_row( $sql );
}


/**
 * Delete currency
 *
 * @param $id
 *
 * @return bool
 * @since 1.0.0
 */
function eaccounting_delete_currency( $id ) {
	global $wpdb;
	$id = absint( $id );
	$currency = eaccounting_get_currency( $id );
	if ( is_null( $currency ) ) {
		return false;
	}

	do_action( 'eaccounting_pre_currency_delete', $id, $currency );
	if ( false == $wpdb->delete( $wpdb->ea_currencies, array( 'id' => $id ), array( '%d' ) ) ) {
		return false;
	}
	do_action( 'eaccounting_currency_delete', $id, $currency );

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
function eaccounting_get_currencies( $args = array(), $count = false ) {
	global $wpdb;
	$query_fields  = '';
	$query_from    = '';
	$query_where   = '';
	$query_orderby = '';
	$query_limit   = '';

	$default = array(
		'include'        => array(),
		'exclude'        => array(),
		'type'           => '',
		'search'         => '',
		'orderby'        => 'id',
		'order'          => 'DESC',
		'fields'         => 'all',
		'search_columns' => array( 'name', 'code' ),
		'per_page'       => 20,
		'page'           => 1,
		'offset'         => 0,
	);

	$args        = wp_parse_args( $args, $default );
	$query_from  = "FROM $wpdb->ea_currencies";
	$query_where = 'WHERE 1=1';


	//fields
	if ( is_array( $args['fields'] ) ) {
		$args['fields'] = array_unique( $args['fields'] );

		$query_fields = array();
		foreach ( $args['fields'] as $field ) {
			$field          = 'id' === $field ? 'id' : sanitize_key( $field );
			$query_fields[] = "$wpdb->ea_currencies.$field";
		}
		$query_fields = implode( ',', $query_fields );
	} elseif ( 'all' == $args['fields'] ) {
		$query_fields = "$wpdb->ea_currencies.*";
	} else {
		$query_fields = "$wpdb->ea_currencies.id";
	}

	//include
	$include = false;
	if ( ! empty( $args['include'] ) ) {
		$include = wp_parse_id_list( $args['include'] );
	}

	if ( ! empty( $include ) ) {
		// Sanitized earlier.
		$ids         = implode( ',', $include );
		$query_where .= " AND $wpdb->ea_currencies.id IN ($ids)";
	} elseif ( ! empty( $args['exclude'] ) ) {
		$ids         = implode( ',', wp_parse_id_list( $args['exclude'] ) );
		$query_where .= " AND $wpdb->ea_currencies.id NOT IN ($ids)";
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
		return $wpdb->get_var( "SELECT count($wpdb->ea_currencies.id) $query_from $query_where" );
	}

	$request = "SELECT $query_fields $query_from $query_where $query_orderby $query_limit";

	if ( is_array( $args['fields'] ) || 'all' == $args['fields'] ) {
		return $wpdb->get_results( $request );
	}

	return $wpdb->get_col( $request );
}
