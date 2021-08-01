<?php
/**
 * EverAccounting Transaction functions.
 *
 * Functions for all kind of transaction of the plugin.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

use EverAccounting\Transaction;
use EverAccounting\Transfer;

defined( 'ABSPATH' ) || exit;

/**
 * Get Transaction Types
 *
 * @return array
 * @since 1.1.0
 */
function eaccounting_get_transaction_types() {
	$types = array(
		'income'  => __( 'Income', 'wp-ever-accounting' ),
		'expense' => __( 'Expense', 'wp-ever-accounting' ),
	);

	return apply_filters( 'eaccounting_transaction_types', $types );
}

/**
 * Retrieves transaction data given a transaction id or transaction object.
 *
 * @param int|object|Transaction $transaction transaction to retrieve
 * @param string                 $output The required return type. One of OBJECT, ARRAY_A, or ARRAY_N.Default OBJECT.
 * @param string                 $filter Type of filter to apply. Accepts 'raw', 'edit', 'db', or 'display'. Default 'raw'.
 *
 * @return Transaction|array|null
 * @since 1.1.0
 */
function eaccounting_get_transaction( $transaction, $output = OBJECT, $filter = 'raw' ) {
	if ( empty( $transaction ) ) {
		return null;
	}

	if ( $transaction instanceof Transaction ) {
		$_transaction = $transaction;
	} elseif ( is_object( $transaction ) ) {
		$_transaction = new Transaction( $transaction );
	} else {
		$_transaction = Transaction::get_instance( $transaction );
	}

	if ( ! $_transaction ) {
		return null;
	}

	$_transaction = $_transaction->filter( $filter );

	if ( ARRAY_A === $output ) {
		return $_transaction->to_array();
	}

	if ( ARRAY_N === $output ) {
		return array_values( $_transaction->to_array() );
	}

	return $_transaction->filter( $filter );
}

/**
 *  Insert or update a transaction.
 *
 * @param array|object|Transaction $transaction_arr An array, object, or transaction object of data arguments.
 *
 * @return Transaction|WP_Error The transaction object or WP_Error otherwise.
 * @global wpdb $wpdb WordPress database abstraction object.
 * @since 1.1.0
 */
function eaccounting_insert_transaction( $transaction_arr ) {
	global $wpdb;
	$user_id = get_current_user_id();
	if ( $transaction_arr instanceof Transaction ) {
		$transaction_arr = $transaction_arr->to_array();
	} elseif ( $transaction_arr instanceof stdClass ) {
		$transaction_arr = get_object_vars( $transaction_arr );
	}

	$defaults = array(
		'type'           => 'income',
		'type_id'        => null,
		'payment_date'   => null,
		'amount'         => 0.00,
		'currency_code'  => '', // protected
		'currency_rate'  => 0.00, // protected
		'account_id'     => null,
		'document_id'    => null,
		'contact_id'     => null,
		'category_id'    => null,
		'description'    => '',
		'payment_method' => '',
		'reference'      => '',
		'attachment_id'  => null,
		'parent_id'      => 0,
		'reconciled'     => 0,
		'creator_id'     => $user_id,
		'date_created'   => null,
	);

	// Are we updating or creating?
	$id          = null;
	$update      = false;
	$data_before = array();
	if ( ! empty( $item_data['id'] ) ) {
		$update      = true;
		$id          = absint( $item_data['id'] );
		$data_before = eaccounting_get_transaction( $id, ARRAY_A );

		if ( is_null( $data_before ) ) {
			return new WP_Error( 'invalid_transaction_id', __( 'Invalid transaction id to update.', 'wp-ever-accounting' ) );
		}

		// Merge old and new fields with new fields overwriting old ones.
		$transaction_arr = array_merge( $data_before, $transaction_arr );
		$data_before     = $data_before->to_array();
	}

	$item_data = wp_parse_args( $transaction_arr, $defaults );
	$data_arr  = eaccounting_sanitize_transaction( $transaction_arr, 'db' );

	// Check required
	if ( empty( $data_arr['type'] ) ) {
		return new WP_Error( 'invalid_transaction_type', esc_html__( 'Transaction type is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['payment_date'] ) ) {
		return new WP_Error( 'invalid_transaction_payment_date', esc_html__( 'Transaction payment date id is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['account_id'] ) ) {
		return new WP_Error( 'invalid_transaction_account_id', esc_html__( 'Transaction account id is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['category_id'] ) ) {
		return new WP_Error( 'invalid_transaction_category_id', esc_html__( 'Transaction category id is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['payment_method'] ) ) {
		return new WP_Error( 'invalid_transaction_payment_method', esc_html__( 'Transaction payment method id is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['date_created'] ) || '0000-00-00 00:00:00' === $data_arr['date_created'] ) {
		$data_arr['date_created'] = current_time( 'mysql' );
	}

	$fields = array_keys( $defaults );
	$data   = wp_array_slice_assoc( $data_arr, $fields );

	/**
	 * Filters transaction data before it is inserted into the database.
	 *
	 * @param array $data Data to be inserted.
	 * @param array $data_arr Sanitized data.
	 *
	 * @since 1.2.1
	 */
	$data = apply_filters( 'eaccounting_insert_transaction', $data, $data_arr );

	$data  = wp_unslash( $data );
	$where = array( 'id' => $id );

	if ( $update ) {

		/**
		 * Fires immediately before an existing transaction item is updated in the database.
		 *
		 * @param int $id Transaction id.
		 * @param array $data Transaction data to be inserted.
		 * @param array $changes Transaction data to be updated.
		 * @param array $data_arr Sanitized transaction item data.
		 * @param array $data_before Transaction previous data.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_update_transaction', $id, $data, $data_arr, $data_before );
		if ( false === $wpdb->update( $wpdb->prefix . 'ea_transactions', $data, $where, $data_before ) ) {
			new WP_Error( 'db_update_error', __( 'Could not update transaction in the database.', 'wp-ever-accounting' ), $wpdb->last_error );
		}

		/**
		 * Fires immediately after an existing transaction is updated in the database.
		 *
		 * @param int $id Transaction id.
		 * @param array $data Transaction data to be inserted.
		 * @param array $changes Transaction data to be updated.
		 * @param array $data_arr Sanitized Transaction data.
		 * @param array $data_before Transaction previous data.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_update_transaction', $id, $data, $data_arr, $data_before );
	} else {

		/**
		 * Fires immediately before an existing transaction is inserted in the database.
		 *
		 * @param array $data Transaction data to be inserted.
		 * @param string $data_arr Sanitized transaction item data.
		 * @param array $item_data Transaction item data as originally passed to the function.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_insert_transaction', $data, $data_arr, $item_data );

		if ( false === $wpdb->insert( $wpdb->prefix . 'ea_transactions', $data ) ) {
			new WP_Error( 'db_insert_error', __( 'Could not insert transaction into the database.', 'wp-ever-accounting' ), $wpdb->last_error );
		}

		$id = (int) $wpdb->insert_id;

		/**
		 * Fires immediately after an existing transaction is inserted in the database.
		 *
		 * @param int $id Transaction id.
		 * @param array $data Transaction has been inserted.
		 * @param array $data_arr Sanitized transaction data.
		 * @param array $item_data Transaction data as originally passed to the function.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_insert_transaction', $id, $data, $data_arr, $item_data );
	}

	// Clear cache.
	wp_cache_delete( $id, 'ea_transactions' );
	wp_cache_set( 'last_changed', microtime(), 'ea_transactions' );

	// Get new transaction object.
	$transaction = eaccounting_get_transaction( $id );

	/**
	 * Fires once a transaction has been saved.
	 *
	 * @param int $id Transaction id.
	 * @param Transaction $transaction Transaction object.
	 * @param bool $update Whether this is an existing transaction being updated.
	 *
	 * @since 1.2.1
	 */
	do_action( 'eaccounting_saved_transaction', $id, $transaction, $update, $data_arr, $data_before );

	return $transaction;
}


/**
 * Delete a transaction.
 *
 * @param int $transaction_id Transaction id.
 *
 * @return Transaction |false|null Transaction data on success, false or null on failure.
 * @since 1.1.0
 */
function eaccounting_delete_transaction( $transaction_id ) {
	global $wpdb;

	$transaction = eaccounting_get_transaction( $transaction_id );
	if ( ! $transaction || ! $transaction->exists() ) {
		return false;
	}

	/**
	 * Filters whether a transaction delete should take place.
	 *
	 * @param bool|null $delete Whether to go forward with deletion.
	 * @param Transaction $transaction contact object.
	 *
	 * @since 1.2.1
	 */
	$check = apply_filters( 'eaccounting_pre_delete_transaction', null, $transaction );
	if ( null !== $check ) {
		return $check;
	}

	/**
	 * Fires before a transaction is deleted.
	 *
	 * @param int $transaction_id Contact id.
	 * @param Transaction $transaction transaction object.
	 *
	 * @since 1.2.1
	 *
	 * @see eaccounting_delete_transaction()
	 */
	do_action( 'eaccounting_before_delete_transaction', $transaction_id, $transaction );

	$result = $wpdb->delete( $wpdb->prefix . 'ea_transactions', array( 'id' => $transaction_id ) );
	if ( ! $result ) {
		return false;
	}

	wp_cache_delete( $transaction_id, 'ea_transactions' );
	wp_cache_set( 'last_changed', microtime(), 'ea_transactions' );

	/**
	 * Fires after a transaction is deleted.
	 *
	 * @param int $transaction_id transaction id.
	 * @param Transaction $transaction transaction object.
	 *
	 * @since 1.2.1
	 *
	 * @see eaccounting_delete_transaction()
	 */
	do_action( 'eaccounting_delete_transaction', $transaction_id, $transaction );

	return $transaction;
}

/**
 * Retrieves transfer data given a transfer id or transfer object.
 *
 * @param int|object|Transfer $transfer transfer to retrieve
 * @param string              $output The required return type. One of OBJECT, ARRAY_A, or ARRAY_N.Default OBJECT.
 * @param string              $filter Type of filter to apply. Accepts 'raw', 'edit', 'db', or 'display'. Default 'raw'.
 *
 * @return Transfer|array|null
 * @since 1.1.0
 */
function eaccounting_get_transfer( $transfer, $output = OBJECT, $filter = 'raw' ) {
	if ( empty( $transfer ) ) {
		return null;
	}

	if ( $transfer instanceof Transfer ) {
		$_transfer = $transfer;
	} elseif ( is_object( $transfer ) ) {
		$_transfer = new Transfer( $transfer );
	} else {
		$_transfer = Transfer::get_instance( $transfer );
	}

	if ( ! $_transfer ) {
		return null;
	}

	$_transfer = $_transfer->filter( $filter );

	if ( ARRAY_A === $output ) {
		return $_transfer->to_array();
	}

	if ( ARRAY_N === $output ) {
		return array_values( $_transfer->to_array() );
	}

	return $_transfer->filter( $filter );
}


/**
 *  Insert or update a transfer.
 *
 * @param array|object|Transfer $transfer_arr An array, object, or transfer object of data arguments.
 *
 * @return Transfer|WP_Error The transfer object or WP_Error otherwise.
 * @global wpdb $wpdb WordPress database abstraction object.
 * @since 1.1.0
 */
function eaccounting_insert_transfer( $transfer_arr ) {
	global $wpdb;
	$user_id = get_current_user_id();
	if ( $transfer_arr instanceof Transfer ) {
		$transfer_arr = $transfer_arr->to_array();
	} elseif ( $transfer_arr instanceof stdClass ) {
		$transfer_arr = get_object_vars( $transfer_arr );
	}

	$defaults = array(
		'date'            => '',
		'from_account_id' => null,
		'amount'          => '',
		'to_account_id'   => null,
		'income_id'       => null,
		'expense_id'      => null,
		'payment_method'  => '',
		'reference'       => '',
		'description'     => '',
		'creator_id'      => $user_id,
		'date_created'    => '',
	);

	// Are we updating or creating?
	$id          = null;
	$update      = false;
	$data_before = array();
	if ( ! empty( $item_data['id'] ) ) {
		$update      = true;
		$id          = absint( $item_data['id'] );
		$data_before = eaccounting_get_transfer( $id, ARRAY_A );

		if ( is_null( $data_before ) ) {
			return new WP_Error( 'invalid_transfer_id', __( 'Invalid transfer id to update.' ) );
		}

		// Merge old and new fields with new fields overwriting old ones.
		$transfer_arr = array_merge( $data_before, $transfer_arr );
		$data_before  = $data_before->to_array();
	}

	$item_data = wp_parse_args( $transfer_arr, $defaults );
	$data_arr  = eaccounting_sanitize_transfer( $transfer_arr, 'db' );

	// Check required
	if ( empty( $data_arr['content'] ) ) {
		return new WP_Error( 'invalid_transfer_content', esc_html__( 'Transfer content id is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['type'] ) ) {
		return new WP_Error( 'invalid_transfer_type', esc_html__( 'Transfer type is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['date_created'] ) || '0000-00-00 00:00:00' === $data_arr['date_created'] ) {
		$data_arr['date_created'] = current_time( 'mysql' );
	}

	$fields = array_keys( $defaults );
	$data   = wp_array_slice_assoc( $data_arr, $fields );

	/**
	 * Filters transfer data before it is inserted into the database.
	 *
	 * @param array $data Data to be inserted.
	 * @param array $data_arr Sanitized data.
	 *
	 * @since 1.2.1
	 */
	$data = apply_filters( 'eaccounting_insert_transfer', $data, $data_arr );

	$data  = wp_unslash( $data );
	$where = array( 'id' => $id );

	if ( $update ) {

		/**
		 * Fires immediately before an existing transfer is updated in the database.
		 *
		 * @param int $id Transfer id.
		 * @param array $data Transfer data to be inserted.
		 * @param array $changes Transfer data to be updated.
		 * @param array $data_arr Sanitized transfer item data.
		 * @param array $data_before Transfer previous data.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_update_transfer', $id, $data, $data_arr, $data_before );
		if ( false === $wpdb->update( $wpdb->prefix . 'ea_transfers', $data, $where, $data_before ) ) {
			new WP_Error( 'db_update_error', __( 'Could not update transfer in the database.' ), $wpdb->last_error );
		}

		/**
		 * Fires immediately after an existing transfer is updated in the database.
		 *
		 * @param int $id Transfer id.
		 * @param array $data Transfer data to be inserted.
		 * @param array $changes Transfer data to be updated.
		 * @param array $data_arr Sanitized transfer data.
		 * @param array $data_before Transfer previous data.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_update_transfer', $id, $data, $data_arr, $data_before );
	} else {

		/**
		 * Fires immediately before an existing transfer is inserted in the database.
		 *
		 * @param array $data Transfer data to be inserted.
		 * @param string $data_arr Sanitized transfer item data.
		 * @param array $item_data Transfer data as originally passed to the function.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_insert_transfer', $data, $data_arr, $item_data );

		if ( false === $wpdb->insert( $wpdb->prefix . 'ea_transfers', $data ) ) {
			new WP_Error( 'db_insert_error', __( 'Could not insert transfer into the database.' ), $wpdb->last_error );
		}

		$id = (int) $wpdb->insert_id;

		/**
		 * Fires immediately after an existing transfer is inserted in the database.
		 *
		 * @param int $id Transfer id.
		 * @param array $data Transfer has been inserted.
		 * @param array $data_arr Sanitized transfer data.
		 * @param array $item_data Transfer data as originally passed to the function.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_insert_transfer', $id, $data, $data_arr, $item_data );
	}

	// Clear cache.
	wp_cache_delete( $id, 'ea_transfers' );
	wp_cache_set( 'last_changed', microtime(), 'ea_transfers' );

	// Get new item object.
	$transfer = eaccounting_get_transfer( $id );

	/**
	 * Fires once a transfer has been saved.
	 *
	 * @param int $id Transfer id.
	 * @param Transfer $transfer Transfer object.
	 * @param bool $update Whether this is an existing transfer being updated.
	 *
	 * @since 1.2.1
	 */
	do_action( 'eaccounting_saved_transfer', $id, $transfer, $update, $data_arr, $data_before );

	return $transfer;
}


/**
 * Delete a transfer.
 *
 * @param int $transfer_id Transfer id.
 *
 * @return Transfer |false|null Transfer data on success, false or null on failure.
 * @since 1.1.0
 */
function eaccounting_delete_transfer( $transfer_id ) {
	global $wpdb;

	$transfer = eaccounting_get_transfer( $transfer_id );
	if ( ! $transfer || ! $transfer->exists() ) {
		return false;
	}

	/**
	 * Filters whether an transfer delete should take place.
	 *
	 * @param bool|null $delete Whether to go forward with deletion.
	 * @param Transfer $transfer contact object.
	 *
	 * @since 1.2.1
	 */
	$check = apply_filters( 'eaccounting_pre_delete_transfer', null, $transfer );
	if ( null !== $check ) {
		return $check;
	}

	/**
	 * Fires before an transfer is deleted.
	 *
	 * @param int $transfer_id Contact id.
	 * @param Transfer $transfer transfer object.
	 *
	 * @since 1.2.1
	 *
	 * @see eaccounting_delete_transfer()
	 */
	do_action( 'eaccounting_before_delete_transfer', $transfer_id, $transfer );

	$result = $wpdb->delete( $wpdb->prefix . 'ea_transfers', array( 'id' => $transfer_id ) );
	if ( ! $result ) {
		return false;
	}

	wp_cache_delete( $transfer_id, 'ea_transfers' );
	wp_cache_set( 'last_changed', microtime(), 'ea_transfers' );

	/**
	 * Fires after an transfer is deleted.
	 *
	 * @param int $transfer_id contact id.
	 * @param Transfer $transfer contact object.
	 *
	 * @since 1.2.1
	 *
	 * @see eaccounting_delete_transfer()
	 */
	do_action( 'eaccounting_delete_transfer', $transfer_id, $transfer );

	return $transfer;
}

/**
 * Get transfers.
 *
 * @param array $args {
 *
 * @type int $id ID of the transfer.
 * @type int $from_account_id ID of the source account from where transfer is initiating.
 * @type int $to_account_id ID of the target account where the transferred amount will be deposited.
 * @type string $amount Amount of the money that will be transferred.
 * @type string $date Date of the transfer.
 * @type string $payment_method Payment method used in transfer.
 * @type string $reference Reference used in transfer.
 * @type string $description Description of the transfer.
 *
 * }
 *
 * @return array|int
 * @since 1.1.0
 */
function eaccounting_get_transfers( $args = array() ) {
	// Prepare args.
	$args = wp_parse_args(
		$args,
		array(
			'include'     => '',
			'search'      => '',
			'from_id'     => '',
			'fields'      => '',
			'orderby'     => 'date_created',
			'order'       => 'ASC',
			'number'      => 20,
			'offset'      => 0,
			'paged'       => 1,
			'return'      => 'objects',
			'count_total' => false,
		)
	);
	global $wpdb;
	$qv           = apply_filters( 'eaccounting_get_transfers_args', $args );
	$table        = \EverAccounting\Repositories\Transfers::TABLE;
	$columns      = \EverAccounting\Repositories\Transfers::get_columns();
	$qv['fields'] = wp_parse_list( $qv['fields'] );
	foreach ( $qv['fields'] as $index => $field ) {
		if ( ! in_array( $field, $columns, true ) ) {
			unset( $qv['fields'][ $index ] );
		}
	}
	$fields = is_array( $qv['fields'] ) && ! empty( $qv['fields'] ) ? implode( ',', $qv['fields'] ) : 'ea_transfers.*';
	$where  = 'WHERE 1=1';

	if ( ! empty( $qv['include'] ) ) {
		$include = implode( ',', wp_parse_id_list( $qv['include'] ) );
		$where  .= " AND $table.`id` IN ($include)";
	} elseif ( ! empty( $qv['exclude'] ) ) {
		$exclude = implode( ',', wp_parse_id_list( $qv['exclude'] ) );
		$where  .= " AND $table.`id` NOT IN ($exclude)";
	}

	if ( ! empty( $qv['from_account_id'] ) ) {
		$from_account_in = implode( ',', wp_parse_id_list( $qv['from_account_id'] ) );
		$where          .= " AND expense.`account_id` IN ($from_account_in)";
	}

	if ( ! empty( $qv['to_account_id'] ) ) {
		$to_account_in = implode( ',', wp_parse_id_list( $qv['to_account_id'] ) );
		$where        .= " AND income.`account_id` IN ($to_account_in)";
	}

	$join  = " LEFT JOIN {$wpdb->prefix}ea_transactions expense ON (expense.id = ea_transfers.expense_id) ";
	$join .= " LEFT JOIN {$wpdb->prefix}ea_transactions income ON (income.id = ea_transfers.income_id) ";

	if ( ! empty( $qv['date_created'] ) && is_array( $qv['date_created'] ) ) {
		$date_created_query = new \WP_Date_Query( $qv['date_created'], "{$table}.date_created" );
		$where             .= $date_created_query->get_sql();
	}

	if ( ! empty( $qv['payment_date'] ) && is_array( $qv['payment_date'] ) ) {
		$date_created_query = new \WP_Date_Query( $qv['payment_date'], 'expense.payment_date' );
		$where             .= $date_created_query->get_sql();
	}

	$order   = isset( $qv['order'] ) ? strtoupper( $qv['order'] ) : 'ASC';
	$orderby = empty( $qv['orderby'] ) ? 'date_created' : eaccounting_clean( $qv['orderby'] );
	if ( in_array( $qv['orderby'], $columns, true ) ) {
		$orderby = "$table." . $qv['orderby'];
	} elseif ( in_array( $qv['orderby'], array( 'from_account_id' ), true ) ) {
		$orderby = 'expense.account_id';
	} elseif ( in_array( $qv['orderby'], array( 'amount', 'reference' ), true ) ) {
		$orderby = 'expense.' . $qv['orderby'];
	} elseif ( in_array( $qv['orderby'], array( 'to_account_id' ), true ) ) {
		$orderby = 'income.account_id';
	} else {
		$orderby = "$table.id";
	}

	$limit = '';
	if ( isset( $qv['number'] ) && $qv['number'] > 0 ) {
		if ( $qv['offset'] ) {
			$limit = $wpdb->prepare( 'LIMIT %d, %d', $qv['offset'], $qv['number'] );
		} else {
			$limit = $wpdb->prepare( 'LIMIT %d, %d', $qv['number'] * ( $qv['paged'] - 1 ), $qv['number'] );
		}
	}
	$select      = "SELECT {$fields}";
	$from        = "FROM {$wpdb->prefix}$table $table";
	$orderby     = "ORDER BY {$orderby} {$order}";
	$count_total = true === $qv['count_total'];
	$clauses     = compact( 'select', 'from', 'join', 'where', 'orderby', 'limit' );
	$cache_key   = 'query:' . md5( serialize( $qv ) ) . ':' . wp_cache_get_last_changed( 'ea_transfers' );
	$results     = wp_cache_get( $cache_key, 'ea_transfers' );
	if ( false === $results ) {
		if ( $count_total ) {
			$results = (int) $wpdb->get_var( "SELECT COUNT($table.id) $from $join $where" );
			wp_cache_set( $cache_key, $results, 'ea_transfers' );
		} else {
			$results = $wpdb->get_results( implode( ' ', $clauses ) );
			if ( in_array( $fields, array( 'all', '*', 'ea_transfers.*' ), true ) ) {
				foreach ( $results as $key => $item ) {
					wp_cache_set( $item->id, $item, 'ea_transfers' );
				}
			}
			wp_cache_set( $cache_key, $results, 'ea_transfers' );
		}
	}

	if ( 'objects' === $qv['return'] && true !== $qv['count_total'] ) {
		$results = array_map( 'eaccounting_get_transfer', $results );
	}

	return $results;
}

/**
 * Get transaction items.
 *
 * @param array $args
 *
 * @return array|Payment[]|Revenue[]|int
 * @since 1.0.
 */
function eaccounting_get_transactions( $args = array() ) {
	// Prepare args.
	$args = wp_parse_args(
		$args,
		array(
			'type'        => '',
			'include'     => '',
			'search'      => '',
			'transfer'    => true,
			'fields'      => '*',
			'orderby'     => 'payment_date',
			'order'       => 'ASC',
			'number'      => 20,
			'offset'      => 0,
			'paged'       => 1,
			'return'      => 'objects',
			'count_total' => false,
		)
	);
	global $wpdb;
	$qv           = apply_filters( 'eaccounting_get_transactions_args', $args );
	$table        = \EverAccounting\Repositories\Transactions::TABLE;
	$columns      = \EverAccounting\Repositories\Transactions::get_columns();
	$qv['fields'] = wp_parse_list( $qv['fields'] );
	foreach ( $qv['fields'] as $index => $field ) {
		if ( ! in_array( $field, $columns, true ) ) {
			unset( $qv['fields'][ $index ] );
		}
	}
	$fields = is_array( $qv['fields'] ) && ! empty( $qv['fields'] ) ? implode( ',', $qv['fields'] ) : '*';
	$where  = 'WHERE 1=1';
	if ( ! empty( $qv['include'] ) ) {
		$include = implode( ',', wp_parse_id_list( $qv['include'] ) );
		$where  .= " AND $table.`id` IN ($include)";
	} elseif ( ! empty( $qv['exclude'] ) ) {
		$exclude = implode( ',', wp_parse_id_list( $qv['exclude'] ) );
		$where  .= " AND $table.`id` NOT IN ($exclude)";
	}
	// search
	$search_cols = array( 'description', 'reference' );
	if ( ! empty( $qv['search'] ) ) {
		$searches = array();
		$where   .= ' AND (';
		foreach ( $search_cols as $col ) {
			$searches[] = $wpdb->prepare( $col . ' LIKE %s', '%' . $wpdb->esc_like( $qv['search'] ) . '%' );
		}
		$where .= implode( ' OR ', $searches );
		$where .= ')';
	}

	if ( ! empty( $qv['type'] ) ) {
		$types  = implode( "','", wp_parse_list( $qv['type'] ) );
		$where .= " AND $table.`type` IN ('$types')";
	}

	if ( ! empty( $qv['currency_code'] ) ) {
		$currency_code = implode( "','", wp_parse_list( $qv['currency_code'] ) );
		$where        .= " AND $table.`currency_code` IN ('$currency_code')";
	}

	if ( ! empty( $qv['payment_method'] ) ) {
		$payment_method = implode( "','", wp_parse_list( $qv['payment_method'] ) );
		$where         .= " AND $table.`payment_method` IN ('$payment_method')";
	}

	if ( ! empty( $qv['account_id'] ) ) {
		$account_id = implode( ',', wp_parse_id_list( $qv['account_id'] ) );
		$where     .= " AND $table.`account_id` IN ($account_id)";
	}

	if ( ! empty( $qv['document_id'] ) ) {
		$document_id = implode( ',', wp_parse_id_list( $qv['document_id'] ) );
		$where      .= " AND $table.`document_id` IN ($document_id)";
	}

	if ( ! empty( $qv['category_id'] ) ) {
		$category_in = implode( ',', wp_parse_id_list( $qv['category_id'] ) );
		$where      .= " AND $table.`category_id` IN ($category_in)";
	}

	if ( ! empty( $qv['contact_id'] ) ) {
		$contact_id = implode( ',', wp_parse_id_list( $qv['contact_id'] ) );
		$where     .= " AND $table.`contact_id` IN ($contact_id)";
	}

	if ( ! empty( $qv['parent_id'] ) ) {
		$parent_id = implode( ',', wp_parse_id_list( $qv['parent_id'] ) );
		$where    .= " AND $table.`parent_id` IN ($parent_id)";
	}

	if ( ! empty( $qv['date_created'] ) && is_array( $qv['date_created'] ) ) {
		$date_created_query = new \WP_Date_Query( $qv['date_created'], "{$table}.date_created" );
		$where             .= $date_created_query->get_sql();
	}

	// if ( ! empty( $qv['payment_date'] ) && is_array( $qv['payment_date'] ) ) {
	// $date_created_query = new \WP_Date_Query( $qv['payment_date'], "{$table}.payment_date" );
	// $where             .= $date_created_query->get_sql();
	// }

	if ( ! empty( $qv['payment_date'] ) && is_array( $qv['payment_date'] ) ) {
		$before = $qv['payment_date']['before'];
		$after  = $qv['payment_date']['after'];
		$where .= " AND $table.`payment_date` BETWEEN '$before' AND '$after'";
	}

	if ( ! empty( $qv['creator_id'] ) ) {
		$creator_id = implode( ',', wp_parse_id_list( $qv['creator_id'] ) );
		$where     .= " AND $table.`creator_id` IN ($creator_id)";
	}

	if ( true === $qv['transfer'] ) {
		$where .= " AND $table.`category_id` NOT IN (SELECT id from {$wpdb->prefix}ea_categories where type='other' )";
	}

	$order   = isset( $qv['order'] ) ? strtoupper( $qv['order'] ) : 'ASC';
	$orderby = isset( $qv['orderby'] ) && in_array( $qv['orderby'], $columns, true ) ? eaccounting_clean( $qv['orderby'] ) : "{$table}.id";

	$limit = '';
	if ( isset( $qv['number'] ) && $qv['number'] > 0 ) {
		if ( $qv['offset'] ) {
			$limit = $wpdb->prepare( 'LIMIT %d, %d', $qv['offset'], $qv['number'] );
		} else {
			$limit = $wpdb->prepare( 'LIMIT %d, %d', $qv['number'] * ( $qv['paged'] - 1 ), $qv['number'] );
		}
	}

	$select      = "SELECT {$fields}";
	$from        = "FROM {$wpdb->prefix}$table $table";
	$orderby     = "ORDER BY {$orderby} {$order}";
	$count_total = true === $qv['count_total'];
	$cache_key   = 'query:' . md5( serialize( $qv ) ) . ':' . wp_cache_get_last_changed( 'ea_transactions' );
	$results     = wp_cache_get( sanitize_key( $cache_key ), 'ea_transactions' );
	$clauses     = compact( 'select', 'from', 'where', 'orderby', 'limit' );

	if ( false === $results ) {
		if ( $count_total ) {
			$results = (int) $wpdb->get_var( "SELECT COUNT(id) $from $where" );
			wp_cache_set( $cache_key, $results, 'ea_transactions' );
		} else {
			$results = $wpdb->get_results( implode( ' ', $clauses ) );
			if ( in_array( $fields, array( 'all', '*' ), true ) ) {
				foreach ( $results as $key => $item ) {
					wp_cache_set( $item->id, $item, 'ea_transactions' );
				}
			}
			wp_cache_set( $cache_key, $results, 'ea_transactions' );
		}
	}

	if ( 'objects' === $qv['return'] && true !== $qv['count_total'] ) {
		$results = array_map(
			function ( $item ) {
				switch ( $item->type ) {
					case 'income':
						$transaction = new Revenue();
						$transaction->set_props( $item );
						$transaction->set_object_read( true );

						break;
					case 'expense':
						$transaction = new Payment();
						$transaction->set_props( $item );
						$transaction->set_object_read( true );

						break;
					default:
						$transaction = apply_filters( 'eaccounting_transaction_object_' . $item->type, null, $item );
				}

				return $transaction;
			},
			$results
		);
	}

	return $results;
}

/**
 * Get total income.
 *
 * @param null $year
 *
 * @return float
 * @since 1.1.0
 */
function eaccounting_get_total_income( $year = null ) {
	global $wpdb;
	$total_income = wp_cache_get( 'total_income_' . $year, 'ea_transactions' );
	if ( false === $total_income ) {
		$where = '';
		if ( absint( $year ) ) {
			$financial_start = eaccounting_get_financial_start( $year );
			$financial_end   = eaccounting_get_financial_end( $year );
			$where          .= $wpdb->prepare( 'AND ( payment_date between %s AND %s )', $financial_start, $financial_end );
		}

		$sql          = $wpdb->prepare(
			" SELECT Sum(amount) amount,currency_code,currency_rate
				FROM   {$wpdb->prefix}ea_transactions
				WHERE 1=1 $where AND type = %s AND category_id NOT IN (SELECT id FROM   {$wpdb->prefix}ea_categories WHERE  type = 'other')
				GROUP  BY currency_code, currency_rate
			",
			'income'
		);
		$results      = $wpdb->get_results( $sql );
		$total_income = 0;
		foreach ( $results as $result ) {
			$total_income += eaccounting_price_to_default( $result->amount, $result->currency_code, $result->currency_rate );
		}
		wp_cache_add( 'total_income_' . $year, $total_income, 'ea_transactions' );
	}

	return $total_income;
}

/**
 * Get total expense.
 *
 * @param null $year
 *
 * @return float
 * @since 1.1.0
 */
function eaccounting_get_total_expense( $year = null ) {
	global $wpdb;
	$total_expense = wp_cache_get( 'total_expense_' . $year, 'ea_transactions' );
	if ( false === $total_expense ) {
		$where = '';
		if ( absint( $year ) ) {
			$financial_start = eaccounting_get_financial_start( $year );
			$financial_end   = eaccounting_get_financial_end( $year );
			$where          .= $wpdb->prepare( 'AND ( payment_date between %s AND %s )', $financial_start, $financial_end );
		}

		$sql           = $wpdb->prepare(
			" SELECT Sum(amount) amount,currency_code,currency_rate
				FROM   {$wpdb->prefix}ea_transactions
				WHERE 1=1 $where AND type = %s AND category_id NOT IN (SELECT id FROM   {$wpdb->prefix}ea_categories WHERE  type = 'other')
				GROUP  BY currency_code, currency_rate
			",
			'expense'
		);
		$results       = $wpdb->get_results( $sql );
		$total_expense = 0;
		foreach ( $results as $result ) {
			$total_expense += eaccounting_price_to_default( $result->amount, $result->currency_code, $result->currency_rate );
		}
		wp_cache_add( 'total_expense_' . $year, $total_expense, 'ea_transactions' );
	}

	return $total_expense;
}

/**
 * Get total profit.
 *
 * @param null $year
 *
 * @return float
 * @since 1.1.0
 */
function eaccounting_get_total_profit( $year = null ) {
	$total_income  = (float) eaccounting_get_total_income( $year );
	$total_expense = (float) eaccounting_get_total_expense( $year );
	$profit        = $total_income - $total_expense;

	return $profit < 0 ? 0 : $profit;
}

/**
 * Get total receivable.
 *
 * @return false|float|int|mixed|string
 * @since 1.1.0
 */
function eaccounting_get_total_receivable() {
	global $wpdb;
	$total_receivable = wp_cache_get( 'total_receivable', 'ea_transactions' );
	if ( false === $total_receivable ) {
		$total_receivable = 0;
		$invoices_sql     = $wpdb->prepare(
			"
			SELECT SUM(total) amount, currency_code, currency_rate  FROM   {$wpdb->prefix}ea_documents
			WHERE  status NOT IN ( 'draft', 'cancelled', 'refunded' )
			AND `status` <> 'paid'  AND type = %s GROUP BY currency_code, currency_rate
			",
			'invoice'
		);
		$invoices         = $wpdb->get_results( $invoices_sql );
		foreach ( $invoices as $invoice ) {
			$total_receivable += eaccounting_price_to_default( $invoice->amount, $invoice->currency_code, $invoice->currency_rate );
		}
		$sql     = $wpdb->prepare(
			"
		  SELECT Sum(amount) amount, currency_code, currency_rate
		  FROM   {$wpdb->prefix}ea_transactions
		  WHERE  type = %s
				 AND document_id IN (SELECT id FROM   {$wpdb->prefix}ea_documents WHERE  status NOT IN ( 'draft', 'cancelled', 'refunded' )
				 AND `status` <> 'paid'
				 AND type = 'invoice')
		  GROUP  BY currency_code,currency_rate
		  ",
			'income'
		);
		$results = $wpdb->get_results( $sql );
		foreach ( $results as $result ) {
			$total_receivable -= eaccounting_price_to_default( $result->amount, $result->currency_code, $result->currency_rate );
		}
		wp_cache_add( 'total_receivable', $total_receivable, 'ea_transactions' );
	}

	return $total_receivable;
}

/**
 * Get total payable.
 *
 * @return float
 * @since 1.1.0
 */
function eaccounting_get_total_payable() {
	global $wpdb;
	$total_payable = wp_cache_get( 'total_payable', 'ea_transactions' );
	if ( false === $total_payable ) {
		$total_payable = 0;
		$bills_sql     = $wpdb->prepare(
			"
			SELECT SUM(total) amount, currency_code, currency_rate  FROM   {$wpdb->prefix}ea_documents
			WHERE  status NOT IN ( 'draft', 'cancelled', 'refunded' )
			AND `status` <> 'paid'  AND type = %s GROUP BY currency_code, currency_rate
			",
			'bill'
		);
		$bills         = $wpdb->get_results( $bills_sql );
		foreach ( $bills as $bill ) {
			$total_payable += eaccounting_price_to_default( $bill->amount, $bill->currency_code, $bill->currency_rate );
		}
		$sql     = $wpdb->prepare(
			"
		  SELECT Sum(amount) amount, currency_code, currency_rate
		  FROM   {$wpdb->prefix}ea_transactions
		  WHERE  type = %s
				 AND document_id IN (SELECT id FROM   {$wpdb->prefix}ea_documents WHERE  status NOT IN ( 'draft', 'cancelled', 'refunded' )
				 AND `status` <> 'paid'
				 AND type = 'bill')
		  GROUP  BY currency_code,currency_rate
		  ",
			'expense'
		);
		$results = $wpdb->get_results( $sql );
		foreach ( $results as $result ) {
			$total_payable -= eaccounting_price_to_default( $result->amount, $result->currency_code, $result->currency_rate );
		}
		wp_cache_add( 'total_payable', $total_payable, 'ea_transactions' );
	}

	return $total_payable;
}

/**
 * Get total upcoming profit
 *
 * @return float
 * @since 1.1.0
 */
function eaccounting_get_total_upcoming_profit() {
	$total_payable    = (float) eaccounting_get_total_payable();
	$total_receivable = (float) eaccounting_get_total_receivable();
	$upcoming         = $total_receivable - $total_payable;

	return $upcoming < 0 ? 0 : $upcoming;
}

/**
 * Sanitizes every transaction field.
 *
 * If the context is 'raw', then the transaction object or array will get minimal
 * sanitization of the integer fields.
 *
 * @param object|array $transaction The invoice item object or array
 * @param string $context Optional. How to sanitize post fields. Accepts 'raw', 'edit', 'db', 'display'. Default 'display'.
 *
 * @return object|Transaction|array The now sanitized transaction object or array
 * @see eaccounting_sanitize_transaction_field()
 *
 * @since 1.2.1
 *
 */
function eaccounting_sanitize_transaction( $transaction, $context = 'raw' ) {
	if ( is_object( $transaction ) ) {
		// Check if post already filtered for this context.
		if ( isset( $transaction->filter ) && $context === $transaction->filter ) {
			return $transaction;
		}
		if ( ! isset( $transaction->id ) ) {
			$transaction->id = 0;
		}

		foreach ( array_keys( get_object_vars( $transaction ) ) as $field ) {
			$transaction->$field = eaccounting_sanitize_transaction_field( $field, $transaction->$field, $transaction->id, $context );
		}
		$transaction->filter = $context;
	} elseif ( is_array( $transaction ) ) {
		// Check if post already filtered for this context.
		if ( isset( $transaction['filter'] ) && $context === $transaction['filter'] ) {
			return $transaction;
		}
		if ( ! isset( $transaction['id'] ) ) {
			$transaction['id'] = 0;
		}
		foreach ( array_keys( $transaction ) as $field ) {
			$transaction[ $field ] = eaccounting_sanitize_transaction_field( $field, $transaction[ $field ], $transaction['id'], $context );
		}
		$transaction['filter'] = $context;
	}

	return $transaction;
}

/**
 * Sanitizes transaction field based on context.
 *
 * Possible context values are:  'raw', 'edit', 'db', 'display'.
 *
 * @param string $field The transaction Object field name.
 * @param mixed  $value The transaction Object value.
 * @param int    $transaction_id transaction id.
 * @param string $context Optional. How to sanitize the field. Possible values are 'raw', 'edit','db', 'display'. Default 'display'.
 *
 * @return mixed Sanitized value.
 * @since 1.2.1
 */
function eaccounting_sanitize_transaction_field( $field, $value, $transaction_id, $context ) {
	if ( false !== strpos( $field, '_id' ) || $field === 'id' ) { //phpcs:ignore
		$value = absint( $value );
	}

	$context = strtolower( $context );

	if ( 'raw' === $context ) {
		if ( $field === 'extra' ) { //phpcs:ignore
			$value = maybe_unserialize( $value );
		}

		return $value;
	}

	if ( 'edit' === $context ) {

		/**
		 * Filters transaction field to edit before it is sanitized.
		 *
		 * @param mixed $value Value of the transaction field.
		 * @param int $transaction_id Transaction id.
		 *
		 * @since 1.2.1
		 */
		$value = apply_filters( "eaccounting_edit_transaction_{$field}", $value, $transaction_id );

	} elseif ( 'db' === $context ) {

		/**
		 * Filters transaction field value before it is sanitized.
		 *
		 * @param mixed $value Value of the transaction field.
		 * @param int $transaction_id Transaction id.
		 *
		 * @since 1.2.1
		 */
		$value = apply_filters( "eaccounting_pre_transaction_{$field}", $value, $transaction_id );
	} else {
		// Use display filters by default.

		/**
		 * Filters the transaction field sanitized for display.
		 *
		 * @param mixed $value Value of the transaction field.
		 * @param int $transaction_id Transaction id.
		 * @param string $context Context to retrieve the account field value.
		 *
		 * @since 1.2.1
		 */
		$value = apply_filters( "eaccounting_transaction_{$field}", $value, $transaction_id, $context );
	}

	return $value;
}


/**
 * Sanitizes every transfer field.
 *
 * If the context is 'raw', then the transfer object or array will get minimal
 * sanitization of the integer fields.
 *
 * @param object|array $transfer The invoice item object or array
 * @param string       $context Optional. How to sanitize post fields. Accepts 'raw', 'edit', 'db', 'display'. Default 'display'.
 *
 * @return object|Transfer|array The now sanitized transfer object or array
 * @see eaccounting_sanitize_transfer_field()
 *
 * @since 1.2.1
 */
function eaccounting_sanitize_transfer( $transfer, $context = 'raw' ) {
	if ( is_object( $transfer ) ) {
		// Check if post already filtered for this context.
		if ( isset( $transfer->filter ) && $context == $transfer->filter ) {
			return $transfer;
		}
		if ( ! isset( $transfer->id ) ) {
			$transfer->id = 0;
		}

		foreach ( array_keys( get_object_vars( $transfer ) ) as $field ) {
			$transfer->$field = eaccounting_sanitize_transfer_field( $field, $transfer->$field, $transfer->id, $context );
		}
		$transfer->filter = $context;
	} elseif ( is_array( $transfer ) ) {
		// Check if post already filtered for this context.
		if ( isset( $transfer['filter'] ) && $context == $transfer['filter'] ) {
			return $transfer;
		}
		if ( ! isset( $transfer['id'] ) ) {
			$transfer['id'] = 0;
		}
		foreach ( array_keys( $transfer ) as $field ) {
			$transfer[ $field ] = eaccounting_sanitize_transfer_field( $field, $transfer[ $field ], $transfer['id'], $context );
		}
		$transfer['filter'] = $context;
	}

	return $transfer;
}

/**
 * Sanitizes transfer field based on context.
 *
 * Possible context values are:  'raw', 'edit', 'db', 'display'.
 *
 * @param string $field The transfer Object field name.
 * @param mixed  $value The transfer Object value.
 * @param int    $transfer_id transfer id.
 * @param string $context Optional. How to sanitize the field. Possible values are 'raw', 'edit','db', 'display'. Default 'display'.
 *
 * @return mixed Sanitized value.
 * @since 1.2.1
 */
function eaccounting_sanitize_transfer_field( $field, $value, $transfer_id, $context ) {
	if ( false !== strpos( $field, '_id' ) || $field === 'id' ) {
		$value = absint( $value );
	}

	$context = strtolower( $context );

	if ( 'raw' === $context ) {
		if ( $field === 'extra' ) {
			$value = maybe_unserialize( $value );
		}

		return $value;
	}

	if ( 'edit' === $context ) {

		/**
		 * Filters transfer field to edit before it is sanitized.
		 *
		 * @param mixed $value Value of the transfer field.
		 * @param int $transfer_id Transfer id.
		 *
		 * @since 1.2.1
		 */
		$value = apply_filters( "eaccounting_edit_transfer_{$field}", $value, $transfer_id );

	} elseif ( 'db' === $context ) {

		/**
		 * Filters transfer field value before it is sanitized.
		 *
		 * @param mixed $value Value of the transfer field.
		 * @param int $transfer_id Transfer id.
		 *
		 * @since 1.2.1
		 */
		$value = apply_filters( "eaccounting_pre_transfer_{$field}", $value, $transfer_id );
	} else {
		// Use display filters by default.

		/**
		 * Filters the transfer field sanitized for display.
		 *
		 * @param mixed $value Value of the transfer field.
		 * @param int $transfer_id Transfer id.
		 * @param string $context Context to retrieve the account field value.
		 *
		 * @since 1.2.1
		 */
		$value = apply_filters( "eaccounting_transfer_{$field}", $value, $transfer_id, $context );
	}

	return $value;
}
