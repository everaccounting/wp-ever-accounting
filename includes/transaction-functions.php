<?php
/**
 * EverAccounting Transaction functions.
 *
 * Functions for all kind of transaction of the plugin.
 *
 * @package EverAccounting
 * @since   1.0.2
 */
defined( 'ABSPATH' ) || exit();

/**
 * Get all transaction types
 * @return array
 * @since 1.0.2
 */
function eaccounting_get_transaction_types() {
	$types = array(
		'income'   => __( 'Income', 'wp-ever-accounting' ),
		'expense'  => __( 'Expense', 'wp-ever-accounting' ),
		'transfer' => __( 'Transfer', 'wp-ever-accounting' ),
	);

	return $types;
}

/**
 * Insert transaction
 *
 * @param $args
 *
 * @return int|WP_Error|null
 *                          @since 1.0.2
 */
function eaccounting_insert_transaction( $args ) {
	global $wpdb;
	$update = false;
	$id     = null;
	$args   = (array) apply_filters( 'eaccounting_insert_transaction', $args );
	if ( isset( $args['id'] ) && ! empty( trim( $args['id'] ) ) ) {
		$id          = (int) $args['id'];
		$update      = true;
		$item_before = (array) eaccounting_get_transaction( $id );
		if ( is_null( $item_before ) ) {
			return new \WP_Error( 'invalid_action', __( 'Could not find the transaction to  update', 'wp-ever-accounting' ) );
		}
		$args = array_merge( $item_before, $args );
	}

	$methods = array_keys( eaccounting_get_payment_methods() );
	$data    = array(
		'id'             => empty( $args['id'] ) ? null : absint( $args['id'] ),
		'type'           => empty( $args['type'] ) || ! in_array( $args['type'], [ 'income', 'expense' ] ) ? null : sanitize_key( $args['type'] ),
		'paid_at'        => empty( $args['paid_at'] ) ? '' : eaccounting_sanitize_date( $args['paid_at'], '' ),
		'amount'         => empty( $args['amount'] ) ? '' : $args['amount'],
		'account_id'     => empty( $args['account_id'] ) ? '' : intval( $args['account_id'] ),
		'contact_id'     => empty( $args['contact_id'] ) ? '' : intval( $args['contact_id'] ),
		'invoice_id'     => empty( $args['invoice_id'] ) ? '' : intval( $args['invoice_id'] ),
		'category_id'    => empty( $args['category_id'] ) ? '' : intval( $args['category_id'] ),
		'description'    => empty( $args['description'] ) ? '' : sanitize_textarea_field( $args['description'] ),
		'payment_method' => empty( $args['payment_method'] ) || ! in_array( $args['payment_method'], $methods ) ? '' : sanitize_key( $args['payment_method'] ),
		'reference'      => empty( $args['reference'] ) ? '' : sanitize_text_field( $args['reference'] ),
		'file_id'        => empty( $args['file_id'] ) ? '' : intval( $args['file_id'] ),
		'parent_id'      => empty( $args['parent_id'] ) ? '' : intval( $args['parent_id'] ),
		'reconciled'     => empty( $args['reconciled'] ) ? '' : intval( $args['reconciled'] ),
		'creator_id'     => empty( $args['creator_id'] ) ? eaccounting_get_creator_id() : $args['creator_id'],
		'created_at'     => empty( $args['created_at'] ) ? current_time( 'Y-m-d H:i:s' ) : $args['created_at'],
	);
	if ( empty( $data['type'] ) ) {
		return new WP_Error( 'empty_content', __( 'Type is required', 'wp-ever-accounting' ) );
	}
	if ( empty( $data['paid_at'] ) ) {
		return new WP_Error( 'empty_content', __( 'Date is required formatted as yyyy-mm-dd', 'wp-ever-accounting' ) );
	}
	if ( empty( preg_replace( '/[^0-9]/', '', $args['amount'] ) ) ) {
		return new WP_Error( 'empty_content', __( 'Amount is required', 'wp-ever-accounting' ) );
	}
	if ( empty( $data['category_id'] ) ) {
		return new WP_Error( 'empty_content', __( 'Category is required', 'wp-ever-accounting' ) );
	}
	if ( empty( $data['payment_method'] ) ) {
		return new WP_Error( 'empty_content', __( 'Payment method is required', 'wp-ever-accounting' ) );
	}
	if ( empty( $data['account_id'] ) ) {
		return new WP_Error( 'empty_content', __( 'Account is required', 'wp-ever-accounting' ) );
	}

	$account = eaccounting_get_account( $data['account_id'] );
	if ( ! $account ) {
		return new WP_Error( 'invalid_data', __( 'Account does not exist.', 'wp-ever-accounting' ) );
	}
	$category = eaccounting_get_category( $data['category_id'] );
	if ( ! $category ) {
		return new WP_Error( 'invalid_data', __( 'Category does not exist.', 'wp-ever-accounting' ) );
	}
	$contact = eaccounting_get_contact( $data['contact_id'] );
	if ( ! empty( $data['contact_id'] ) && empty( $contact ) ) {
		return new WP_Error( 'invalid_data', __( 'Contact does not exist.', 'wp-ever-accounting' ) );
	}
	$currency = eaccounting_get_currency( $account->currency_code, 'code' );
	if ( ! $currency ) {
		return new WP_Error( 'invalid_data', __( 'Account associated currency does not exist.', 'wp-ever-accounting' ) );
	}
	//other type is required for transfer
	if ( ! in_array( $category->type, [ $args['type'], 'other' ] ) ) {
		return new WP_Error( 'invalid_data', __( 'Invalid category type category type must be correct according to the transaction.', 'wp-ever-accounting' ) );
	}
	$contact = eaccounting_get_contact( $data['contact_id'] );
	if ( ! empty( $data['contact_id'] ) && empty( $contact ) ) {
		return new WP_Error( 'invalid_data', __( 'Contact does not exist.', 'wp-ever-accounting' ) );
	}

	$data['amount']        = eaccounting_money( $data['amount'], $account->currency_code )->getAmount();
	$data['currency_rate'] = $currency->rate;
	$data['currency_code'] = $currency->code;
	$where                 = array( 'id' => $id );
	$data                  = wp_unslash( $data );

	if ( $update ) {
		do_action( 'eaccounting_pre_transaction_update', $id, $data );
		if ( false === $wpdb->update( $wpdb->ea_transactions, $data, $where ) ) {
			return new WP_Error( 'db_update_error', __( 'Could not update transaction in the database', 'wp-ever-accounting' ), $wpdb->last_error );
		}
		do_action( 'eaccounting_transaction_update', $id, $data, $item_before );
	} else {
		do_action( 'eaccounting_pre_transaction_insert', $id, $data );
		if ( false === $wpdb->insert( $wpdb->ea_transactions, $data ) ) {

			return new WP_Error( 'db_insert_error', __( 'Could not insert transaction into the database', 'wp-ever-accounting' ), $wpdb->last_error );
		}
		$id = (int) $wpdb->insert_id;
		do_action( 'eaccounting_transaction_insert', $id, $data );
	}

	return $id;

}

/**
 * Get single transaction
 * @param @id
 * @return array
 * @since 1.0.2
*/
function eaccounting_get_transaction( $id ) {
	global $wpdb;

	return $wpdb->get_row( $wpdb->prepare( "select * from {$wpdb->ea_transactions} where id=%s", $id ) );
}

/**
 * Delete transaction
 *
 * @param $id
 *
 * @return bool|WP_Error
 * @since 1.0.2
 */
function eaccounting_delete_transaction( $id ) {
	global $wpdb;
	$id      = absint( $id );
	$account = eaccounting_get_transaction( $id );
	if ( is_null( $account ) ) {
		return false;
	}
	do_action( 'eaccounting_pre_transaction_delete', $id, $account );
	if ( false == $wpdb->delete( $wpdb->ea_transactions, array( 'id' => $id ), array( '%d' ) ) ) {
		return false;
	}
	do_action( 'eaccounting_transaction_delete', $id, $account );

	return true;
}

/**
 * Get all transactions
 *
 * @param $args
 * @param $count
 *
 * @return bool|WP_Error|mixed
 * @since 1.0.2
 */
function eaccounting_get_transactions( $args = array(), $count = false ) {
	global $wpdb;
	$query_fields  = '';
	$query_from    = '';
	$query_where   = '';
	$query_join    = '';
	$query_groupby = '';
	$query_orderby = '';
	$query_limit   = '';

	$default = array(
		'type'             => 'income',
		'account_id'       => '',
		'invoice_id'       => '',
		'parent_id'        => '',
		'contact_id'       => '',
		'category_id'      => '',
		'creator_id'       => '',
		'company_id'       => '1',
		'reconciled'       => '',
		'paid_at'          => '',
		'start_date'       => '',
		'end_date'         => '',
		'amount'           => '',
		'search'           => '',
		'order'            => 'DESC',
		'orderby'          => 'paid_at',
		'fields'           => 'all',
		'search_columns'   => array( 'description', 'reference' ),
		'per_page'         => 20,
		'page'             => 1,
		'offset'           => 0,
		'include_transfer' => false,
		'nopaging'         => false,
	);

	$args              = wp_parse_args( $args, $default );
	$transaction_table = $wpdb->ea_transactions;
	$account_table     = $wpdb->ea_accounts;
	$category_table    = $wpdb->ea_categories;
	$query_from        = "FROM $transaction_table ";
	$query_where       = 'WHERE 1=1';

	//type
	if ( ! empty( $args['type'] ) && strtolower( $args['type'] ) !== 'all' ) {
		$query_where .= $wpdb->prepare( " AND $transaction_table.type=%s ", $args['type'] );
	}

	//account_id
	if ( ! empty( $args['account_id'] ) ) {
		$account_ids = implode( ',', wp_parse_id_list( $args['account_id'] ) );
		$query_where .= " AND $transaction_table.account_id IN( {$account_ids} ) ";
	}

	//contact_id
	if ( ! empty( $args['contact_id'] ) ) {
		$contact_ids = implode( ',', wp_parse_id_list( $args['contact_id'] ) );
		$query_where .= " AND $transaction_table.contact_id IN( {$contact_ids} ) ";
	}

	//category_id
	if ( ! empty( $args['category_id'] ) ) {
		$category_ids = implode( ',', wp_parse_id_list( $args['category_id'] ) );
		$query_where  .= " AND $transaction_table.category_id IN( {$category_ids} ) ";
	}

	//start_date date
	if ( ! empty( eaccounting_sanitize_date( $args['start_date'] ) ) ) {
		$query_where .= $wpdb->prepare( " AND $transaction_table.paid_at >= date(%s) ", sanitize_text_field( $args['start_date'] ) );
	}

	//end_date date
	if ( ! empty( eaccounting_sanitize_date( $args['end_date'] ) ) ) {
		$query_where .= $wpdb->prepare( " AND $transaction_table.paid_at <= date(%s) ", sanitize_text_field( $args['end_date'] ) );
	}

	if ( !wp_validate_boolean($args['include_transfer']) ) {
		$query_where .= $wpdb->prepare( " AND $transaction_table.category_id NOT IN (SELECT id from $category_table WHERE type=%s) ", 'other' );
	}


	//amount
	if ( ! empty( $args['amount'] ) ) {
		$amount = trim( $args['amount'] );
		$number = preg_replace( '#[^0-9\.]#', '', $amount );

		if ( strpos( $amount, '>' ) !== false ) {
			$query_where .= $wpdb->prepare( " AND $transaction_table.amount > %f ", $number );
		} elseif ( strpos( $amount, '<' ) !== false ) {
			$query_where .= $wpdb->prepare( " AND $transaction_table.amount < %f ", $number );
		} else {
			$query_where .= $wpdb->prepare( " AND $transaction_table.amount = %f ", $number );
		}
	}

	//fields
	if ( is_array( $args['fields'] ) ) {
		$args['fields'] = array_unique( $args['fields'] );

		$query_fields = array();
		foreach ( $args['fields'] as $field ) {
			$field          = 'id' === $field ? 'id' : sanitize_key( $field );
			$query_fields[] = "$transaction_table.$field";
		}
		$query_fields = implode( ',', $query_fields );
	} elseif ( 'all' == $args['fields'] ) {
		$query_fields = "$transaction_table.*";
	} else {
		$query_fields = "$transaction_table.id";
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
	$query_orderby = sprintf( " ORDER BY $transaction_table.%s %s ", $order_by, $order );

	// limit
	if ( $args['nopaging'] == false && isset( $args['per_page'] ) && $args['per_page'] > 0 ) {
		if ( $args['offset'] ) {
			$query_limit = $wpdb->prepare( 'LIMIT %d, %d', $args['offset'], $args['per_page'] );
		} else {
			$query_limit = $wpdb->prepare( 'LIMIT %d, %d', $args['per_page'] * ( $args['page'] - 1 ), $args['per_page'] );
		}
	}


	if ( $count ) {
		return $wpdb->get_var( "SELECT count($transaction_table.id) $query_from $query_where" );
	}

	$request = "SELECT $query_fields $query_from $query_where $query_orderby $query_limit";

	if ( is_array( $args['fields'] ) || 'all' == $args['fields'] ) {
		return $wpdb->get_results( $request );
	}

	return $wpdb->get_col( $request );

}

