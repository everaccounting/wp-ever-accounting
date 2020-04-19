<?php

defined( 'ABSPATH' ) || exit();

/**
 * Insert Item
 *
 * @param $args
 *
 * @return int|WP_Error
 * @since 1.0.0
 */
function eaccounting_insert_item( $args ) {
	global $wpdb;
	$update = false;
	$id     = null;
	$args   = (array) apply_filters( 'eaccounting_create_item', $args );

	if ( isset( $args['id'] ) && ! empty( trim( $args['id'] ) ) ) {
		$id          = (int) $args['id'];
		$update      = true;
		$item_before = (array) eaccounting_get_item( $id );
		if ( is_null( $item_before ) ) {
			return new \WP_Error( 'invalid_action', __( 'Could not find the item to  update', 'wp-ever-accounting' ) );
		}

		$args = array_merge( $item_before, $args );
	}


	$data = array(
		'id'             => empty( $args['id'] ) ? null : absint( $args['id'] ),
		'name'           => ! isset( $args['name'] ) ? '' : sanitize_text_field( $args['name'] ),
		'sku'            => ! isset( $args['sku'] ) ? '' : sanitize_text_field( $args['sku'] ),
		'tax_id'         => ! isset( $args['tax_id'] ) ? '' : sanitize_text_field( $args['tax_id'] ),
		'description'    => ! isset( $args['description'] ) ? '' : sanitize_textarea_field( $args['description'] ),
		'sale_price'     => ! isset( $args['sale_price'] ) ? '0.00' : eaccounting_sanitize_price( $args['sale_price'] ),
		'purchase_price' => ! isset( $args['purchase_price'] ) ? '0.00' : eaccounting_sanitize_price( $args['purchase_price'] ),
		//todo set default account
		'quantity'       => ! isset( $args['quantity'] ) ? '1' : eaccounting_sanitize_price( $args['quantity'] ),
		'category_id'    => ! isset( $args['category_id'] ) ? '' : sanitize_text_field( $args['category_id'] ),
		'image_id'       => ! isset( $args['image_id'] ) ? '' : sanitize_text_field( $args['image_id'] ),
		'created_at'     => empty( $args['created_at'] ) ? current_time( 'Y-m-d H:i:s' ) : $args['created_at'],
	);


	if ( empty( $data['name'] ) ) {
		return new WP_Error( 'empty_content', __( 'Name is required', 'wp-ever-accounting' ) );
	}
	if ( empty( $data['sku'] ) ) {
		return new WP_Error( 'empty_content', __( 'Sku is required', 'wp-ever-accounting' ) );
	}
	if ( empty( $data['sale_price'] ) ) {
		return new WP_Error( 'empty_content', __( 'Sale Price is required', 'wp-ever-accounting' ) );
	}
	if ( empty( $data['purchase_price'] ) ) {
		return new WP_Error( 'empty_content', __( 'Purchase Price is required', 'wp-ever-accounting' ) );
	}
	if ( empty( $data['category_id'] ) ) {
		return new WP_Error( 'empty_content', __( 'Category ID is required', 'wp-ever-accounting' ) );
	}

	$where = array( 'id' => $id );
	$data  = wp_unslash( $data );

	if ( $update ) {
		do_action( 'eaccounting_pre_item_update', $id, $data );
		if ( false === $wpdb->update( $wpdb->ea_items, $data, $where ) ) {
			return new WP_Error( 'db_update_error', __( 'Could not update items in the database', 'wp-ever-accounting' ), $wpdb->last_error );
		}
		do_action( 'eaccounting_item_update', $id, $data, $item_before );
	} else {
		do_action( 'eaccounting_pre_item_insert', $id, $data );
		if ( false === $wpdb->insert( $wpdb->ea_items, $data ) ) {

			return new WP_Error( 'db_insert_error', __( 'Could not insert items into the database', 'wp-ever-accounting' ), $wpdb->last_error );
		}
		$id = (int) $wpdb->insert_id;
		do_action( 'eaccounting_item_insert', $id, $data );
	}

	return $id;
}

/**
 * @param $id
 * @param string $by
 *
 * @return array|object|void|null
 * @since 1.0.0
 */
function eaccounting_get_item( $id, $by = 'id' ) {
	global $wpdb;
	switch ( $by ) {
		case 'id':
			$item_id = absint( $id );
			$sql     = "SELECT * FROM $wpdb->ea_items WHERE id = {$item_id} ";
			break;
		case 'sku':
			$sku = (string) $id;
			$sql   = "SELECT * FROM $wpdb->ea_items WHERE sku = '{sku}'";
			break;
		default:
			$id  = absint( $id );
			$sql = "SELECT * FROM $wpdb->ea_items WHERE id = {$id} ";
			break;
	}

	$items        = $wpdb->get_row( $sql );

	return $items;
}

/**
 * Delete Item
 *
 * @param $id
 *
 * @return bool
 * @since 1.0.0
 */
function eaccounting_delete_item( $id ) {
	global $wpdb;
	$id = absint( $id );

	$item = eaccounting_get_item( $id );
	if ( is_null( $item ) ) {
		return false;
	}


	do_action( 'eaccounting_pre_item_delete', $id, $item );
	if ( false == $wpdb->delete( $wpdb->ea_items, array( 'id' => $id ), array( '%d' ) ) ) {
		return false;
	}
	do_action( 'eaccounting_item_delete', $id, $item );

	return true;
}

/**
 * Get Items
 * since 1.0.0
 *
 * @param array $args
 * @param bool $count
 *
 * @return array|object|string|null
 */
function eaccounting_get_items( $args = array(), $count = false ) {
	global $wpdb;
	$query_fields  = '';
	$query_from    = '';
	$query_where   = '';
	$query_orderby = '';
	$query_limit   = '';

	$default = array(
		'include'        => array(),
		'exclude'        => array(),
		'search'         => '',
		'type'           => '',
		'orderby'        => 'name',
		'order'          => 'ASC',
		'fields'         => 'all',
		'search_columns' => array( 'name', 'sku'),
		'per_page'       => 20,
		'page'           => 1,
		'offset'         => 0,
	);

	$args        = wp_parse_args( $args, $default );
	$query_from  = "FROM $wpdb->ea_items";
	$query_where = 'WHERE 1=1';



	//type
	/*
	if ( ! empty( $args['type'] ) && array_key_exists( $args['type'], eaccounting_get_contact_types() ) ) {
		$type        = '%' . $wpdb->esc_like( $args['type'] ) . '%';
		$query_where .= $wpdb->prepare( " AND $wpdb->ea_contacts.types LIKE %s ", $type );
	}*/


	//opening_balance
	if ( ! empty( $args['date_created'] ) ) {
		$date_created_check = trim( $args['date_created'] );
		$date_created       = preg_replace( '#[^0-9\-]#', '', $date_created_check );

		if ( strpos( $date_created_check, '>' ) !== false ) {
			$query_where .= $wpdb->prepare( " AND $wpdb->ea_items.created_at > %f ", $date_created );
		} elseif ( strpos( $date_created_check, '<' ) !== false ) {
			$query_where .= $wpdb->prepare( " AND $wpdb->ea_items.created_at < %f ", $date_created );
		} else {
			$query_where .= $wpdb->prepare( " AND $wpdb->ea_items.created_at = %f ", $date_created );
		}
	}

	//fields
	if ( is_array( $args['fields'] ) ) {
		$args['fields'] = array_unique( $args['fields'] );

		$query_fields = array();
		foreach ( $args['fields'] as $field ) {
			$field          = 'id' === $field ? 'id' : sanitize_key( $field );
			$query_fields[] = "$wpdb->ea_items.$field";
		}
		$query_fields = implode( ',', $query_fields );
	} elseif ( 'all' == $args['fields'] ) {
		$query_fields = "$wpdb->ea_items.*";
	} else {
		$query_fields = "$wpdb->ea_contacts.id";
	}

	//include
	$include = false;
	if ( ! empty( $args['include'] ) ) {
		$include = wp_parse_id_list( $args['include'] );
	}

	if ( ! empty( $include ) ) {
		// Sanitized earlier.
		$ids         = implode( ',', $include );
		$query_where .= " AND $wpdb->ea_items.id IN ($ids)";
	} elseif ( ! empty( $args['exclude'] ) ) {
		$ids         = implode( ',', wp_parse_id_list( $args['exclude'] ) );
		$query_where .= " AND $wpdb->ea_items.id NOT IN ($ids)";
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
		return $wpdb->get_var( "SELECT count($wpdb->ea_items.id) $query_from $query_where" );
	}

	$request = "SELECT $query_fields $query_from $query_where $query_orderby $query_limit";

	if ( is_array( $args['fields'] ) || 'all' == $args['fields'] ) {
		return $wpdb->get_results( $request );
	}

	return $wpdb->get_col( $request );
}
