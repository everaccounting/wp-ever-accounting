<?php

defined( 'ABSPATH' ) || exit();

/**
 * @param $args
 *
 * @return array|int|WP_Error|null
 * @since 1.0.0
 */
function eaccounting_insert_transfer( $args ) {
	global $wpdb;
	$update = false;
	$id     = null;

	$args = (array) apply_filters( 'eaccounting_insert_transfer', $args );
	if ( isset( $args['id'] ) && ! empty( trim( $args['id'] ) ) ) {
		$id          = (int) $args['id'];
		$update      = true;
		$item_before = (array) eaccounting_get_transfer( $id );
		if ( is_null( $item_before ) ) {
			return new \WP_Error( 'invalid_action', __( 'Could not find the item to  update', 'wp-ever-accounting' ) );
		}

		$args = array_merge( $item_before, $args );
	}


	$data = array(
		'id'              => empty( $args['id'] ) ? null : absint( $args['id'] ),
		'from_account_id' => empty( $args['from_account_id'] ) ? '' : absint( $args['from_account_id'] ),
		'to_account_id'   => empty( $args['to_account_id'] ) ? '' : absint( $args['to_account_id'] ),
		'payment_id'      => empty( $args['payment_id'] ) ? null : absint( $args['payment_id'] ),
		'revenue_id'      => empty( $args['revenue_id'] ) ? null : absint( $args['revenue_id'] ),
		'amount'          => ! isset( $args['amount'] ) ? '' : eaccounting_sanitize_price( $args['amount'] ),
		'transferred_at'  => empty( $args['transferred_at'] ) ? current_time( 'Y-m-d H:i:s' ) : $args['transferred_at'],
		'description'     => ! isset( $args['description'] ) ? '' : sanitize_textarea_field( $args['description'] ),
		'payment_method'  => empty( $args['payment_method'] ) || ! array_key_exists( $args['payment_method'], eaccounting_get_payment_methods() ) ? '' : sanitize_key( $args['payment_method'] ),
		'reference'       => ! isset( $args['reference'] ) ? '' : sanitize_text_field( $args['reference'] ),
		'updated_at'      => current_time( 'Y-m-d H:i:s' ),
		'created_at'      => empty( $args['created_at'] ) ? current_time( 'Y-m-d H:i:s' ) : $args['created_at'],
	);

	if ( empty( $data['from_account_id'] ) ) {
		return new WP_Error( 'empty_content', __( 'From account is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data['to_account_id'] ) ) {
		return new WP_Error( 'empty_content', __( 'To account is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data['transferred_at'] ) ) {
		return new WP_Error( 'empty_content', __( 'Transfer date is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data['amount'] ) || $data['amount'] == '0.00' ) {
		return new WP_Error( 'empty_content', __( 'Amount is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data['payment_method'] ) ) {
		return new WP_Error( 'empty_content', __( 'Payment method is required', 'wp-ever-accounting' ) );
	}

	$from_account = new EAccounting_Account( $data['from_account_id'] );

	if ( $from_account->get_current_balance() < $data['amount'] ) {
		return new WP_Error( 'invalid_amount', __( 'Amount is higher than the available fund in source account', 'wp-ever-accounting' ) );
	}

	$category    = eaccounting_get_category( 'Transfer', 'name' );
	$category_id = $category ? $category->id : false;
	if ( empty( $category ) ) {
		$category_id = eaccounting_insert_category( [
			'name'   => __( 'Transfer', 'wp-ever-accounting' ),
			'type'   => 'other',
			'status' => 'active',
		] );
	}

	if ( empty( $category_id ) ) {
		return new WP_Error( 'empty_content', __( 'Seems transfer category is missing from database', 'wp-ever-accounting' ) );
	}


	$payment_id = eaccounting_insert_payment( [
		'id'             => $data['payment_id'],
		'account_id'     => $data['from_account_id'],
		'paid_at'        => $data['transferred_at'],
		'amount'         => $data['amount'],
		'vendor_id'      => 0,
		'description'    => $data['description'],
		'category_id'    => $category_id,
		'payment_method' => $data['payment_method'],
		'reference'      => $data['reference'],
	] );

	if ( is_wp_error( $payment_id ) ) {
		return $payment_id;
	}

	$revenue_id = eaccounting_insert_revenue( [
		'id'             => $data['revenue_id'],
		'account_id'     => $data['to_account_id'],
		'paid_at'        => $data['transferred_at'],
		'amount'         => $data['amount'],
		'customer_id'    => 0,
		'description'    => $data['description'],
		'category_id'    => $category_id,
		'payment_method' => $data['payment_method'],
		'reference'      => $data['reference'],
	] );

	if ( is_wp_error( $revenue_id ) ) {
		return $revenue_id;
	}


	$where = array( 'id' => $id );
	$data  = wp_unslash( array(
		'payment_id' => $payment_id,
		'revenue_id' => $revenue_id,
		'created_at' => $data['created_at'],
		'updated_at' => $data['updated_at'],
	) );

	if ( $update ) {
		do_action( 'eaccounting_pre_transfer_update', $id, $data );
		if ( false === $wpdb->update( $wpdb->ea_transfers, $data, $where ) ) {
			return new WP_Error( 'db_update_error', sprintf( __( 'Could not update transfer in the database - (%s)', 'wp-ever-accounting' ), $wpdb->last_error ), $wpdb->last_error );
		}
		do_action( 'eaccounting_transfer_update', $id, $data, $item_before );
	} else {
		do_action( 'eaccounting_pre_transfer_insert', $id, $data );
		if ( false === $wpdb->insert( $wpdb->ea_transfers, $data ) ) {

			return new WP_Error( 'db_insert_error', __( 'Could not insert transfer into the database', 'wp-ever-accounting' ), $wpdb->last_error );
		}
		$id = (int) $wpdb->insert_id;
		do_action( 'eaccounting_transfer_insert', $id, $data );
	}

	return $id;
}


/**
 * Get transfer
 *
 * @param $id
 *
 * @return array|object|void|null
 * @since 1.0.0
 */
function eaccounting_get_transfer( $id ) {
	global $wpdb;

	return $wpdb->get_row( $wpdb->prepare( "SELECT t.id, 
		       p.account_id from_account_id, 
		       r.account_id to_account_id, 
		       p.id payment_id,
		       p.amount,
		       p.paid_at transferred_at,
		       p.description description, 
		       p.payment_method payment_method, 
		       p.reference reference, 
		       r.id revenue_id 
		FROM   {$wpdb->ea_transfers} t 
		       LEFT JOIN {$wpdb->ea_payments} p 
		              ON p.id = t.payment_id 
		       LEFT JOIN {$wpdb->ea_revenues} r 
              ON r.id = t.revenue_id WHERE t.id=%d", $id ) );
}

/**
 * Delete transfer
 *
 * @param $id
 *
 * @return bool
 * @since 1.0.0
 */
function eaccounting_delete_transfer( $id ) {
	global $wpdb;
	$id = absint( $id );

	$transfer = eaccounting_get_transfer( $id );
	if ( is_null( $transfer ) ) {
		return false;
	}

	do_action( 'eaccounting_pre_transfer_delete', $id, $transfer );
	if ( false == $wpdb->delete( $wpdb->prefix . "ea_transfers", array( 'id' => $id ), array( '%d' ) ) ) {
		return false;
	}
	do_action( 'eaccounting_transfer_delete', $id, $transfer );

	eaccounting_delete_payment( $transfer->payment_id );
	eaccounting_delete_revenue( $transfer->revenue_id );

	return true;
}


/**
 * Get transfers
 *
 * @param array $args
 * @param bool $count
 *
 * @return array|null|int
 * @since 1.0.0
 */
function eaccounting_get_transfers( $args = array(), $count = false ) {
	global $wpdb;
	$query_fields  = '';
	$query_from    = '';
	$query_where   = '';
	$query_join    = '';
	$query_orderby = '';
	$query_limit   = '';

	$default = array(
		'include'  => array(),
		'exclude'  => array(),
		'orderby'  => 'transferred_at',
		'order'    => 'DESC',
		'per_page' => 20,
		'page'     => 1,
		'offset'   => 0,
	);

	$args         = wp_parse_args( $args, $default );
	$query_from   = "FROM $wpdb->ea_transfers t ";
	$query_where  = 'WHERE 1=1';
	$query_fields = " t.id, 
			       p.account_id from_account_id, 
			       r.account_id to_account_id, 
			       p.id payment_id,
			       p.amount,
			       p.paid_at transferred_at,
			       p.description description, 
			       p.payment_method payment_method, 
			       p.reference reference, 
			       r.id revenue_id,
			       t.created_at,
			       t.updated_at";

	$query_join = " LEFT JOIN {$wpdb->ea_payments} p ON p.id = t.payment_id ";
	$query_join .= " LEFT JOIN {$wpdb->ea_revenues} r ON r.id = t.revenue_id ";


	//include
	$include = false;
	if ( ! empty( $args['include'] ) ) {
		$include = wp_parse_id_list( $args['include'] );
	}

	if ( ! empty( $include ) ) {
		// Sanitized earlier.
		$ids         = implode( ',', $include );
		$query_where .= " AND $wpdb->ea_transfers.id IN ($ids)";
	} elseif ( ! empty( $args['exclude'] ) ) {
		$ids         = implode( ',', wp_parse_id_list( $args['exclude'] ) );
		$query_where .= " AND $wpdb->ea_transfers.id NOT IN ($ids)";
	}

	//ordering
	$order    = isset( $args['order'] ) ? esc_sql( strtoupper( $args['order'] ) ) : 'ASC';
	$order_by = esc_sql( $args['orderby'] );
	if ( in_array( $order_by, [
		'from_account_id',
		'amount',
		'paid_at',
		'payment_method',
		'reference',
		'payment_id'
	] ) ) {
		$order_by = "p.$order_by";
	} elseif ( in_array( $order_by, [ 'to_account_id', 'revenue_id' ] ) ) {
		$order_by = "r.$order_by";
	} else {
		$order_by = "t.created_at";
	}


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
		return $wpdb->get_var( "SELECT count(*) $query_from $query_join $query_where" );
	}

	$request = "SELECT $query_fields $query_from $query_join $query_where $query_orderby $query_limit";

	return $wpdb->get_results( $request );
}
