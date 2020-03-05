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
function eaccounting_insert_account( $args ) {
	global $wpdb;
	$update = false;
	$id     = null;
	$args   = (array) apply_filters( 'eaccounting_create_account', $args );

	if ( isset( $args['id'] ) && ! empty( trim( $args['id'] ) ) ) {
		$id          = (int) $args['id'];
		$update      = true;
		$item_before = (array) eaccounting_get_account( $id );
		if ( is_null( $item_before ) ) {
			return new \WP_Error( 'invalid_action', __( 'Could not find the item to  update', 'wp-ever-accounting' ) );
		}

		$args = array_merge( $item_before, $args );
	}


	$data = array(
		'id'              => empty( $args['id'] ) ? null : absint( $args['id'] ),
		'name'            => ! isset( $args['name'] ) ? '' : sanitize_text_field( $args['name'] ),
		'number'          => ! isset( $args['number'] ) ? '' : sanitize_text_field( $args['number'] ),
		'opening_balance' => ! isset( $args['opening_balance'] ) ? '0.00' : eaccounting_sanitize_price( $args['opening_balance'] ),
		'currency_code'       => ! isset( $args['currency_code'] ) ? '' : sanitize_text_field( $args['currency_code'] ),//todo set default account
		'bank_name'       => ! isset( $args['bank_name'] ) ? '' : sanitize_text_field( $args['bank_name'] ),
		'bank_phone'      => ! isset( $args['bank_phone'] ) ? '' : sanitize_text_field( $args['bank_phone'] ),
		'bank_address'    => ! isset( $args['bank_address'] ) ? '' : sanitize_textarea_field( $args['bank_address'] ),
		'updated_at'      => current_time( 'Y-m-d H:i:s' ),
		'created_at'      => empty( $args['created_at'] ) ? current_time( 'Y-m-d H:i:s' ) : $args['created_at'],
	);


	if ( empty( $data['name'] ) ) {
		return new WP_Error( 'empty_content', __( 'Name is required', 'wp-ever-accounting' ) );
	}
	if ( empty( $data['currency_code'] ) ) {
		return new WP_Error( 'empty_content', __( 'Currency code is required', 'wp-ever-accounting' ) );
	}

	$where = array( 'id' => $id );
	$data  = wp_unslash( $data );

	if ( $update ) {
		do_action( 'eaccounting_pre_account_update', $id, $data );
		if ( false === $wpdb->update( $wpdb->ea_accounts, $data, $where ) ) {
			return new WP_Error( 'db_update_error', __( 'Could not update account in the database', 'wp-ever-accounting' ), $wpdb->last_error );
		}
		do_action( 'eaccounting_account_update', $id, $data, $item_before );
	} else {
		do_action( 'eaccounting_pre_account_insert', $id, $data );
		if ( false === $wpdb->insert( $wpdb->ea_accounts, $data ) ) {

			return new WP_Error( 'db_insert_error', __( 'Could not insert account into the database', 'wp-ever-accounting' ), $wpdb->last_error );
		}
		$id = (int) $wpdb->insert_id;
		do_action( 'eaccounting_account_insert', $id, $data );
	}

	return $id;
}


/**
 * Get account
 *
 * @param $id
 *
 * @return object|null
 * @since 1.0.0
 */
function eaccounting_get_account( $id ) {
	global $wpdb;

	return $wpdb->get_row( $wpdb->prepare( "select * from {$wpdb->ea_accounts} where id=%s", $id ) );
}

/**
 * Delete account
 *
 * @param $id
 *
 * @return bool|WP_Error
 * @since 1.0.0
 */
function eaccounting_delete_account( $id ) {
	global $wpdb;
	$id = absint( $id );

	$account = eaccounting_get_account( $id );
	if ( is_null( $account ) ) {
		return false;
	}

	$tables = [
		$wpdb->ea_revenues => 'account_id',
	];

	foreach ($tables as $table => $column){
		if($wpdb->get_var($wpdb->prepare( "SELECT count(id) from $table WHERE $column = %d", $id))){
			return new WP_Error('not-permitted', __('Account have records on', 'wp-ever-accounting'));
		}
	}


	do_action( 'eaccounting_pre_account_delete', $id, $account );
	if ( false == $wpdb->delete( $wpdb->ea_accounts, array( 'id' => $id ), array( '%d' ) ) ) {
		return false;
	}
	do_action( 'eaccounting_account_delete', $id, $account );

	return true;
}

/**
 * Get all accounts
 *
 * @param array $args
 * @param bool $count
 *
 * @return array|null|object|string
 * since 1.0.0
 */
function eaccounting_get_accounts( $args = array(), $count = false ) {
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
		'orderby'        => 'id',
		'order'          => 'DESC',
		'fields'         => 'all',
		'search_columns' => array( 'name', 'bank_name' ),
		'per_page'       => 20,
		'page'           => 1,
		'offset'         => 0,
	);

	$args        = wp_parse_args( $args, $default );
	$query_from  = "FROM $wpdb->ea_accounts";
	$query_where = 'WHERE 1=1';


	//opening_balance
	if ( ! empty( $args['opening_balance'] ) ) {
		$balance = trim( $args['opening_balance'] );
		$number  = preg_replace( '#[^0-9\.]#', '', $balance );

		if ( strpos( $balance, '>' ) !== false ) {
			$query_where .= $wpdb->prepare( " AND $wpdb->ea_accounts.opening_balance > %f ", $number );
		} elseif ( strpos( $balance, '<' ) !== false ) {
			$query_where .= $wpdb->prepare( " AND $wpdb->ea_accounts.opening_balance < %f ", $number );
		} else {
			$query_where .= $wpdb->prepare( " AND $wpdb->ea_accounts.opening_balance = %f ", $number );
		}
	}

	//fields
	if ( is_array( $args['fields'] ) ) {
		$args['fields'] = array_unique( $args['fields'] );

		$query_fields = array();
		foreach ( $args['fields'] as $field ) {
			$field          = 'id' === $field ? 'id' : sanitize_key( $field );
			$query_fields[] = "$wpdb->ea_accounts.$field";
		}
		$query_fields = implode( ',', $query_fields );
	} elseif ( 'all' == $args['fields'] ) {
		$query_fields = "$wpdb->ea_accounts.*";
	} else {
		$query_fields = "$wpdb->ea_accounts.id";
	}

	//include
	$include = false;
	if ( ! empty( $args['include'] ) ) {
		$include = wp_parse_id_list( $args['include'] );
	}

	if ( ! empty( $include ) ) {
		// Sanitized earlier.
		$ids         = implode( ',', $include );
		$query_where .= " AND $wpdb->ea_accounts.id IN ($ids)";
	} elseif ( ! empty( $args['exclude'] ) ) {
		$ids         = implode( ',', wp_parse_id_list( $args['exclude'] ) );
		$query_where .= " AND $wpdb->ea_accounts.id NOT IN ($ids)";
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
		return $wpdb->get_var( "SELECT count($wpdb->ea_accounts.id) $query_from $query_where" );
	}


	$request = "SELECT $query_fields $query_from $query_where $query_orderby $query_limit";

	if ( is_array( $args['fields'] ) || 'all' == $args['fields'] ) {
		return $wpdb->get_results( $request );
	}

	return $wpdb->get_col( $request );
}
