<?php
defined( 'ABSPATH' ) || exit();

/**
 * Outgoing amount must same currency as assigned account
 *
 * @param $args
 *
 * @return int|WP_Error|null
 * @since 1.0.0
 */
function eaccounting_insert_payment( $args ) {
	global $wpdb;
	$update = false;
	$id     = null;

	$args = (array) apply_filters( 'eaccounting_insert_payment', $args );
	if ( isset( $args['id'] ) && ! empty( trim( $args['id'] ) ) ) {
		$id          = (int) $args['id'];
		$update      = true;
		$item_before = (array) eaccounting_get_payment( $id );
		if ( is_null( $item_before ) ) {
			return new \WP_Error( 'invalid_action', __( 'Could not find the item to  update', 'wp-ever-accounting' ) );
		}

		$args = array_merge( $item_before, $args );
	}

	$data = array(
		'id'             => empty( $args['id'] ) ? null : absint( $args['id'] ),
		'account_id'     => empty( $args['account_id'] ) ? '' : absint( $args['account_id'] ),
		'paid_at'        => empty( $args['paid_at'] ) && eaccounting_sanitize_date( $args['paid_at'] ) ? '' : $args['paid_at'],
		'amount'         => empty( $args['amount'] ) ? '' : $args['amount'],
		'contact_id'     => empty( $args['contact_id'] ) ? '' : absint( $args['contact_id'] ),
		'description'    => ! isset( $args['description'] ) ? '' : sanitize_textarea_field( $args['description'] ),
		'category_id'    => empty( $args['category_id'] ) ? '' : absint( $args['category_id'] ),
		'reference'      => ! isset( $args['reference'] ) ? '' : sanitize_text_field( $args['reference'] ),
		'payment_method' => empty( $args['payment_method'] ) || ! array_key_exists( $args['payment_method'], eaccounting_get_payment_methods() ) ? '' : sanitize_key( $args['payment_method'] ),
		'file_id'        => ! empty( $args['file_id'] ) ? intval( $args['file_id'] ) : '',
		'parent_id'      => empty( $args['parent_id'] ) ? '' : absint( $args['parent_id'] ),
		'reconciled'     => empty( $args['reconciled'] ) ? '' : absint( $args['reconciled'] ),
		'updated_at'     => current_time( 'Y-m-d H:i:s' ),
		'created_at'     => empty( $args['created_at'] ) ? current_time( 'Y-m-d H:i:s' ) : $args['created_at'],
	);

	if ( empty( $data['paid_at'] ) ) {
		return new WP_Error( 'empty_content', __( 'Payment date is required', 'wp-ever-accounting' ) );
	}

	$amount = $data['amount'];
	if ( empty( preg_replace( '/[^0-9]/', '', $amount ) ) ) {
		return new WP_Error( 'empty_content', __( 'Amount is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data['category_id'] ) ) {
		return new WP_Error( 'empty_content', __( 'Payment category is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data['payment_method'] ) ) {
		return new WP_Error( 'empty_content', __( 'Payment method is required', 'wp-ever-accounting' ) );
	}


	$account = eaccounting_get_account( $data['account_id'] );
	if ( ! $account ) {
		return new WP_Error( 'invalid_data', __( 'Account does not exist.', 'wp-ever-accounting' ) );
	}

	$currency = eaccounting_get_currency( $account->currency_code, 'code' );
	if ( ! $currency ) {
		return new WP_Error( 'invalid_data', __( 'Account associated currency does not exist.', 'wp-ever-accounting' ) );
	}

	$category = eaccounting_get_category( $data['category_id'] );
	if ( ! $category ) {
		return new WP_Error( 'invalid_data', __( 'Category does not exist.', 'wp-ever-accounting' ) );
	}

	if ( ! in_array( $category->type, [ 'expense', 'other' ] ) ) {
		return new WP_Error( 'invalid_data', __( 'Invalid category type category type must be expense.', 'wp-ever-accounting' ) );
	}

	$contact = eaccounting_get_contact( $data['contact_id'] );
	if ( ! empty( $data['contact_id'] ) && empty( $contact ) ) {
		return new WP_Error( 'invalid_data', __( 'Contact does not exist.', 'wp-ever-accounting' ) );
	}

	if ( ! empty( $data['contact_id'] ) && ! in_array( 'customer', $contact->types ) ) {
		eaccounting_insert_contact( array(
			'id'    => $id,
			'types' => array_merge( $contact->types, [ 'vendor' ] )
		) );
	}


	$data['amount']        = eaccounting_money( $data['amount'], $account->currency_code )->getAmount();
	$data['currency_rate'] = $currency->rate;
	$data['currency_code'] = $currency->code;
	$where                 = array( 'id' => $id );
	$data                  = wp_unslash( $data );

	if ( $update ) {
		do_action( 'eaccounting_pre_payment_update', $id, $data );
		if ( false === $wpdb->update( $wpdb->ea_payments, $data, $where ) ) {
			return new WP_Error( 'db_update_error', __( 'Could not update payment in the database', 'wp-ever-accounting' ), $wpdb->last_error );
		}
		do_action( 'eaccounting_payment_update', $id, $data, $item_before );
	} else {
		do_action( 'eaccounting_pre_payment_insert', $id, $data );
		if ( false === $wpdb->insert( $wpdb->ea_payments, $data ) ) {

			return new WP_Error( 'db_insert_error', __( 'Could not insert payment into the database', 'wp-ever-accounting' ), $wpdb->last_error );
		}
		$id = (int) $wpdb->insert_id;
		do_action( 'eaccounting_payment_insert', $id, $data );
	}

	return $id;
}


/**
 * Get payment
 *
 * @param $id
 *
 * @return object|null
 * @since 1.0.0
 */
function eaccounting_get_payment( $id ) {
	global $wpdb;

	return $wpdb->get_row( $wpdb->prepare( "select * from {$wpdb->ea_payments} where id=%s", $id ) );
}

/**
 * Delete payment
 *
 * @param $id
 *
 * @return bool|WP_Error
 * @since 1.0.0
 */
function eaccounting_delete_payment( $id ) {
	global $wpdb;
	$id = absint( $id );

	$account = eaccounting_get_payment( $id );
	if ( is_null( $account ) ) {
		return false;
	}

	$tables = [
		$wpdb->ea_transfers => 'payment_id',
	];

	foreach ( $tables as $table => $column ) {
		if ( $wpdb->get_var( $wpdb->prepare( "SELECT count(id) FROM $table WHERE $column = %d", $id ) ) ) {
			return new WP_Error( 'not-permitted', __( 'Major dependencies are associated with payments, you are not permitted to delete them.', 'wp-ever-accounting' ) );
		}
	}

	do_action( 'eaccounting_pre_payment_delete', $id, $account );
	if ( false == $wpdb->delete( $wpdb->ea_payments, array( 'id' => $id ), array( '%d' ) ) ) {
		return false;
	}
	do_action( 'eaccounting_payment_delete', $id, $account );

	return true;
}


/**
 * Get all payments
 *
 * @param array $args
 * @param bool $count
 *
 * @return array|null|object|string
 * since 1.0.0
 */
function eaccounting_get_payments( $args = array(), $count = false ) {
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
		'orderby'        => 'created_at',
		'order'          => 'DESC',
		'fields'         => 'all',
		'search_columns' => array( 'description', 'reference' ),
		'per_page'       => 20,
		'page'           => 1,
		'offset'         => 0,
	);

	$args        = wp_parse_args( $args, $default );
	$query_from  = "FROM $wpdb->ea_payments";
	$query_where = 'WHERE 1=1';

	//account_id
	if ( ! empty( $args['account_id'] ) ) {
		$query_where .= $wpdb->prepare( " AND $wpdb->ea_payments.account_id= %s", absint( $args['account_id'] ) );
	}

	//exclude from others category
	$query_where .= " AND $wpdb->ea_payments.category_id NOT IN ( SELECT id from $wpdb->ea_categories WHERE type='other') ";

	//amount
	if ( ! empty( $args['amount'] ) ) {
		$amount = trim( $args['amount'] );
		$number = preg_replace( '#[^0-9\.]#', '', $amount );

		if ( strpos( $amount, '>' ) !== false ) {
			$query_where .= $wpdb->prepare( " AND $wpdb->ea_payments.amount > %f ", $number );
		} elseif ( strpos( $amount, '<' ) !== false ) {
			$query_where .= $wpdb->prepare( " AND $wpdb->ea_payments.amount < %f ", $number );
		} else {
			$query_where .= $wpdb->prepare( " AND $wpdb->ea_payments.amount = %f ", $number );
		}
	}

	//fields
	if ( is_array( $args['fields'] ) ) {
		$args['fields'] = array_unique( $args['fields'] );

		$query_fields = array();
		foreach ( $args['fields'] as $field ) {
			$field          = 'id' === $field ? 'id' : sanitize_key( $field );
			$query_fields[] = "$wpdb->ea_payments.$field";
		}
		$query_fields = implode( ',', $query_fields );
	} elseif ( 'all' == $args['fields'] ) {
		$query_fields = "$wpdb->ea_payments.*";
	} else {
		$query_fields = "$wpdb->ea_payments.id";
	}

	//include
	$include = false;
	if ( ! empty( $args['include'] ) ) {
		$include = wp_parse_id_list( $args['include'] );
	}

	if ( ! empty( $include ) ) {
		// Sanitized earlier.
		$ids         = implode( ',', $include );
		$query_where .= " AND $wpdb->ea_payments.id IN ($ids)";
	} elseif ( ! empty( $args['exclude'] ) ) {
		$ids         = implode( ',', wp_parse_id_list( $args['exclude'] ) );
		$query_where .= " AND $wpdb->ea_payments.id NOT IN ($ids)";
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
		return $wpdb->get_var( "SELECT count($wpdb->ea_payments.id) $query_from $query_where" );
	}


	$request = "SELECT $query_fields $query_from $query_where $query_orderby $query_limit";

	if ( is_array( $args['fields'] ) || 'all' == $args['fields'] ) {
		return $wpdb->get_results( $request );
	}

	return $wpdb->get_col( $request );
}


/**
 * Get total income
 * @return string|null
 * @since 1.0.0
 */
function eaccounting_get_total_expense() {
	global $wpdb;

	return $wpdb->get_var( "SELECT SUM(amount) from $wpdb->ea_payments WHERE category_id IN (SELECT id FROM $wpdb->ea_categories WHERE type='expense')" );
}
