<?php
/**
 * EverAccounting Transaction functions.
 *
 * Functions for all kind of transaction of the plugin.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

use \EverAccounting\Transaction;
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
		'other'   => __( 'Other', 'wp-ever-accounting' ),
	);

	return apply_filters( 'eaccounting_transaction_types', $types );
}

/**
 * Insert or update a transaction.
 *
 * @param Array|Object|Transaction $transaction Transaction data.
 * @param false $wp_error Whether to return a WP_Error on failure. Default false.
 *
 * @return Transaction|\WP_Error|int
 */
function eaccounting_insert_transaction( $transaction, $wp_error = false ) {
	global $wpdb;
	$user_id = get_current_user_id();
	if ( $transaction instanceof Transaction ) {
		$transaction = $transaction->to_array();
	} elseif ( $transaction instanceof stdClass ) {
		$transaction = get_object_vars( $transaction );
	}

	$defaults = array(
		'type'           => 'income',
		'payment_date'   => '',
		'amount'         => 0.00,
		'currency_code'  => '',
		'currency_rate'  => '',
		'account_id'     => 0,
		'document_id'    => 0,
		'contact_id'     => 0,
		'category_id'    => 0,
		'description'    => '',
		'payment_method' => '',
		'reference'      => '',
		'attachment_id'  => 0,
		'parent_id'      => '',
		'reconciled'     => '',
		'creator_id'     => $user_id,
		'date_created'   => '',
	);

	$posted = eaccounting_sanitize_fields( $transaction);
	$transaction_arr = wp_parse_args( $posted, $defaults );

	// Are we updating or creating?
	$transaction_id = null;
	$update         = false;
	$changes        = $transaction_arr;
	if ( ! empty( $transaction_arr['id'] ) ) {
		$update             = true;
		$transaction_id     = $transaction_arr['id'];
		$transaction_before = eaccounting_get_transaction( $transaction_id, ARRAY_A );

		if ( is_null( $transaction_before ) ) {
			if ( $wp_error ) {
				return new WP_Error( 'invalid_transaction', __( 'Invalid transaction id.' ) );
			}

			return 0;
		}

		$transaction_arr = wp_parse_args( $posted, $transaction_before );
		$changes         = array_diff_assoc( $transaction_arr, $transaction_before );
	}

	// Update properties when update account id.
	if ( array_key_exists( 'account_id', $changes ) ) {
		$account = eaccounting_get_account( (int) $changes['account_id'] );
		if ( ! empty( $account ) || ! is_wp_error( $account ) ) {
			$transaction_arr['currency_code'] = $account->currency_code;
			$changes['currency_code'] = $account->currency_code;
		} else {
			$transaction_arr['currency_code'] = '';
			$changes['currency_code'] = '';
		}
	}


	if ( array_key_exists( 'currency_code', $changes ) ) {
		$currency = eaccounting_get_currency( $changes['currency_code'] );
		if ( ! empty( $currency ) || ! is_wp_error( $currency ) ) {
			$transaction_arr['currency_rate'] = $currency->rate;
		} else {
			$transaction_arr['currency_rate'] = '';
		}
	}

	$transaction_arr['type'] = eaccounting_sanitize_field( 'transaction_type', $transaction_arr['type'] );
	if ( empty( $transaction_arr['type'] ) ) {
		if ( $wp_error ) {
			return new WP_Error( 'invalid_transaction_type', __( 'Invalid transaction type.' ) );
		}

		return 0;
	}

	if ( empty( $transaction_arr['payment_date'] ) || '0000-00-00 00:00:00' === $transaction_arr['payment_date'] ) {
		$transaction_arr['payment_date'] = current_time( 'mysql' );
	}

	if ( empty( $transaction_arr['amount'] ) ) {
		if ( $wp_error ) {
			return new WP_Error( 'invalid_transaction_amount', __( 'Invalid transaction amount.' ) );
		}

		return 0;
	}

	if ( empty( $transaction_arr['currency_code'] ) ) {
		if ( $wp_error ) {
			return new WP_Error( 'invalid_transaction_currency_code', __( 'Invalid transaction currency code.' ) );
		}

		return 0;
	}

	if ( empty( $transaction_arr['currency_rate'] ) ) {
		if ( $wp_error ) {
			return new WP_Error( 'invalid_transaction_currency_rate', __( 'Invalid transaction currency rate.' ) );
		}

		return 0;
	}

	if ( empty( $transaction_arr['account_id'] ) ) {
		if ( $wp_error ) {
			return new WP_Error( 'invalid_transaction_account_id', __( 'Invalid transaction account id.' ) );
		}

		return 0;
	}

	if ( empty( $transaction_arr['payment_method'] ) ) {
		if ( $wp_error ) {
			return new WP_Error( 'invalid_transaction_payment_method', __( 'Invalid transaction payment method.' ) );
		}

		return 0;
	}

	if ( empty( $transaction_arr['date_created'] ) || '0000-00-00 00:00:00' === $transaction_arr['date_created'] ) {
		$transaction_arr['date_created'] = current_time( 'mysql' );
	}

	$data  = apply_filters( 'eaccounting_transaction_data', $transaction_arr, $posted );
	$data  = wp_unslash( array_intersect_key( $data, $defaults ) );
	$where = array( 'id' => $transaction_id );

	if ( $update ) {
		do_action( 'eaccounting_pre_update_transaction', $transaction_id, $data, $posted );
		if ( false === $wpdb->update( $wpdb->prefix . 'ea_transactions', $data, $where ) ) {
			if ( $wp_error ) {
				return new WP_Error( 'db_update_error', __( 'Could not update transaction in the database.' ), $wpdb->last_error );
			}
			return 0;
		}
		do_action( 'eaccounting_update_transaction', $transaction_id, $data, $posted );
	} else {
		if ( false === $wpdb->insert( $wpdb->prefix . 'ea_transactions', $data ) ) {
			if ( $wp_error ) {
				return new WP_Error( 'db_insert_error', __( 'Could not insert transaction into the database.' ), $wpdb->last_error );
			}

			return 0;
		}
		do_action( 'eaccounting_pre_insert_transaction', $data, $posted );

		$transaction_id = (int) $wpdb->insert_id;

		do_action( 'eaccounting_insert_transaction', $transaction_id, $data, $posted );
	}

	eaccounting_clean_transaction_cache( $transaction_id );

	$transaction = eaccounting_get_transaction( $transaction_id );

	/**
	 * Fires once a transaction has been saved.
	 *
	 * The dynamic portion of the hook name, `$transaction->type`, refers to
	 * the transaction type.
	 *
	 * @param int $transaction_id Transaction ID.
	 * @param Transaction $transaction Transaction object.
	 * @param bool $update Whether this is an existing post being updated.
	 *
	 * @since 1.2.1
	 *
	 */
	do_action( "eaccounting_save_{$transaction->type}", $transaction_id, $transaction, $update );

	/**
	 * Fires once a transaction has been saved.
	 *
	 * @param int $post_ID Transaction id.
	 * @param Transaction $post Transaction object.
	 * @param bool $update Whether this is an existing transaction being updated.
	 *
	 * @since 1.2.1
	 *
	 */
	do_action( 'eaccounting_save_transaction', $transaction_id, $transaction, $update );

	return $transaction;
}

/**
 * Retrieves transaction data given a transaction od or transaction object.
 *
 * @param int|Transaction|Array|null $transaction
 * @param string $output The required return type.
 *
 * @return array|Transaction|false|null
 */
function eaccounting_get_transaction( $transaction = null, $output = OBJECT ) {
	if ( $transaction instanceof Transaction ) {
		$transaction = $transaction->id;
	} elseif ( is_object( $transaction ) && ! empty( $transaction->id ) ) {
		$transaction = $transaction->id;
	} elseif ( is_array( $transaction ) && ! empty( $transaction['id'] ) ) {
		$transaction = $transaction['id'];
	} else {
		$transaction = (int) $transaction;
	}

	$transaction = Transaction::get_instance( $transaction );

	if ( empty( $transaction ) ) {
		return null;
	}

	if ( ARRAY_A == $output ) {
		return $transaction->to_array();
	}

	if ( ARRAY_N == $output ) {
		return array_values( $transaction->to_array() );
	}

	return $transaction;
}

/**
 * Deletes a transaction.
 *
 * @param int $transaction_id Transaction id.
 *
 * @return Transaction|false|null|WP_Error
 */
function eaccounting_delete_transaction( $transaction_id ) {
	global $wpdb;

	$transaction = eaccounting_get_transaction( $transaction_id );
	if ( ! $transaction ) {
		return $transaction;
	}

	/**
	 * Filters whether a transaction deletion should take place.
	 *
	 * @param bool|null $delete Whether to go forward with deletion.
	 * @param Transaction $transaction Transaction object.
	 *
	 * @since 1.2.0
	 *
	 */
	$check = apply_filters( 'eaccounting_pre_delete_transaction', null, $transaction );
	if ( null !== $check ) {
		return $check;
	}

	/**
	 * Fires before a transaction is deleted.
	 *
	 * @param int $transaction_id Transaction id.
	 * @param Transaction $transaction Transaction object.
	 *
	 * @since 1.2.1
	 *
	 * @see eaccounting_delete_transaction()
	 *
	 */
	do_action( 'eaccounting_before_delete_transaction', $transaction_id, $transaction );

	$result = $wpdb->delete( $wpdb->prefix . 'ea_transactions', array( 'id' => $transaction_id ) );
	if ( ! $result ) {
		return false;
	}

	eaccounting_clean_transaction_cache( $transaction );

	/**
	 * Fires after a transaction is deleted.
	 *
	 * @param int $transaction_id Transaction id.
	 * @param Transaction $transaction Transaction object.
	 *
	 * @since 1.2.1
	 *
	 * @see eaccounting_delete_transaction()
	 *
	 */
	do_action( 'eaccounting_delete_transaction', $transaction_id, $transaction );

	return $transaction;
}

/**
 * Get transaction items.
 *
 * @param array $args
 *
 * @return Transaction[] Array of transaction.
 * @since 1.0.
 *
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
		$where   .= " AND $table.`id` IN ($include)";
	} elseif ( ! empty( $qv['exclude'] ) ) {
		$exclude = implode( ',', wp_parse_id_list( $qv['exclude'] ) );
		$where   .= " AND $table.`id` NOT IN ($exclude)";
	}
	//search
	$search_cols = array( 'description', 'reference' );
	if ( ! empty( $qv['search'] ) ) {
		$searches = array();
		$where    .= ' AND (';
		foreach ( $search_cols as $col ) {
			$searches[] = $wpdb->prepare( $col . ' LIKE %s', '%' . $wpdb->esc_like( $qv['search'] ) . '%' );
		}
		$where .= implode( ' OR ', $searches );
		$where .= ')';
	}

	if ( ! empty( $qv['type'] ) ) {
		$types = implode( "','", wp_parse_list( $qv['type'] ) );
		$where .= " AND $table.`type` IN ('$types')";
	}

	if ( ! empty( $qv['currency_code'] ) ) {
		$currency_code = implode( "','", wp_parse_list( $qv['currency_code'] ) );
		$where         .= " AND $table.`currency_code` IN ('$currency_code')";
	}

	if ( ! empty( $qv['payment_method'] ) ) {
		$payment_method = implode( "','", wp_parse_list( $qv['payment_method'] ) );
		$where          .= " AND $table.`payment_method` IN ('$payment_method')";
	}

	if ( ! empty( $qv['account_id'] ) ) {
		$account_id = implode( ',', wp_parse_id_list( $qv['account_id'] ) );
		$where      .= " AND $table.`account_id` IN ($account_id)";
	}

	if ( ! empty( $qv['account__in'] ) ) {
		$account_in = implode( ',', wp_parse_id_list( $qv['account__in'] ) );
		$where      .= " AND $table.`account_id` IN ($account_in)";
	}

	if ( ! empty( $qv['account__not_in'] ) ) {
		$account_not_in = implode( ',', wp_parse_id_list( $qv['account__not_in'] ) );
		$where          .= " AND $table.`account_id` NOT IN ($account_not_in)";
	}

	if ( ! empty( $qv['document_id'] ) ) {
		$document_id = implode( ',', wp_parse_id_list( $qv['document_id'] ) );
		$where       .= " AND $table.`document_id` IN ($document_id)";
	}

	if ( ! empty( $qv['category_id'] ) ) {
		$category_in = implode( ',', wp_parse_id_list( $qv['category_id'] ) );
		$where       .= " AND $table.`category_id` IN ($category_in)";
	}

	if ( ! empty( $qv['category__in'] ) ) {
		$category_in = implode( ',', wp_parse_id_list( $qv['category__in'] ) );
		$where       .= " AND $table.`contact_id` IN ($category_in)";
	}

	if ( ! empty( $qv['category__not_in'] ) ) {
		$category_not_in = implode( ',', wp_parse_id_list( $qv['category__not_in'] ) );
		$where           .= " AND $table.`contact_id` NOT IN ($category_not_in)";
	}

	if ( ! empty( $qv['contact_id'] ) ) {
		$contact_id = implode( ',', wp_parse_id_list( $qv['contact_id'] ) );
		$where      .= " AND $table.`contact_id` IN ($contact_id)";
	}

	if ( ! empty( $qv['parent_id'] ) ) {
		$parent_id = implode( ',', wp_parse_id_list( $qv['parent_id'] ) );
		$where     .= " AND $table.`parent_id` IN ($parent_id)";
	}

	if ( ! empty( $qv['amount_min'] ) ) {
		$where .= $wpdb->prepare( " AND $table.`amount` >= (%f)", (float) $qv['amount_min'] );
	}

	if ( ! empty( $qv['amount_max'] ) ) {
		$where .= $wpdb->prepare( " AND $table.`amount` <= (%f)", (float) $qv['amount_max'] );
	}

	if ( ! empty( $qv['amount_between'] ) && is_array( $qv['amount_between'] ) ) {
		$min   = min( $qv['amount_between'] );
		$max   = max( $qv['amount_between'] );
		$where .= $wpdb->prepare( " AND $table.`amount` >= (%f) AND $table.`amount` <= (%f) ", (float) $min, (float) $max );
	}

	if ( ! empty( $qv['date_created'] ) && is_array( $qv['date_created'] ) ) {
		$date_created_query = new \WP_Date_Query( $qv['date_created'], "{$table}.date_created" );
		$where              .= $date_created_query->get_sql();
	}

	if ( ! empty( $qv['payment_date'] ) && is_array( $qv['payment_date'] ) ) {
		$date_created_query = new \WP_Date_Query( $qv['payment_date'], "{$table}.payment_date" );
		$where              .= $date_created_query->get_sql();
	}

	if ( ! empty( $qv['creator_id'] ) ) {
		$creator_id = implode( ',', wp_parse_id_list( $qv['creator_id'] ) );
		$where      .= " AND $table.`creator_id` IN ($creator_id)";
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
	$cache_key   = 'query:' . md5( serialize( $qv ) ) . ':' . wp_cache_get_last_changed( 'eaccounting_transactions' );
	$results     = wp_cache_get( sanitize_key( $cache_key ), 'eaccounting_transactions' );
	$clauses     = compact( 'select', 'from', 'where', 'orderby', 'limit' );

	if ( false === $results ) {
		if ( $count_total ) {
			$results = (int) $wpdb->get_var( "SELECT COUNT(id) $from $where" );
			wp_cache_set( $cache_key, $results, 'eaccounting_transactions' );
		} else {
			$results = $wpdb->get_results( implode( ' ', $clauses ) );
			if ( in_array( $fields, array( 'all', '*' ), true ) ) {
				foreach ( $results as $key => $item ) {
					wp_cache_set( $item->id, $item, 'eaccounting_transactions' );
				}
			}
			wp_cache_set( $cache_key, $results, 'eaccounting_transactions' );
		}
	}

	if ( 'objects' === $qv['return'] && true !== $qv['count_total'] ) {
		$results = array_map( 'eaccounting_get_transaction', $results );
	}

	return $results;
}

/**
 * Deletes transaction cache.
 *
 * @param Transaction|int $transaction Transaction object.
 *
 * @return void
 * @since 1.2.1
 */
function eaccounting_clean_transaction_cache( $transaction ) {
	$transaction = eaccounting_get_transaction( $transaction );
	if ( ! $transaction ) {
		return;
	}
	wp_cache_delete( $transaction->id, 'eaccounting_transactions' );
	wp_cache_delete( $transaction->id, 'eaccounting_transactions_meta' );
	wp_cache_set( 'last_changed', microtime(), 'eaccounting_transactions' );
}


/**
 * Retrieves transfer data given a transaction od or transaction object.
 *
 * @param null $transfer
 * @param string $output The required return type.
 *
 * @return array|Transfer|false|null
 */
function eaccounting_get_transfer( $transfer = null, $output = OBJECT ) {
	if ( $transfer instanceof Transfer ) {
		$transfer = $transfer->id;
	} elseif ( is_object( $transfer ) && ! empty( $transfer->id ) ) {
		$transfer = $transfer->id;
	} elseif ( is_array( $transfer ) && ! empty( $transfer['id'] ) ) {
		$transfer = $transfer['id'];
	} else {
		$transfer = (int) $transfer;
	}

	$transfer = Transfer::get_instance( $transfer );

	if ( empty( $transfer ) ) {
		return null;
	}

	if ( ARRAY_A == $output ) {
		return $transfer->to_array();
	}

	if ( ARRAY_N == $output ) {
		return array_values( $transfer->to_array() );
	}

	return $transfer;
}

/**
 * Create new transfer programmatically.
 *
 * Returns a new transfer object on success.
 *
 * @param array $args {
 *                               An array of elements that make up an transfer to update or insert.
 *
 * @type int $id ID of the transfer. If equal to something other than 0,
 *                               the post with that ID will be updated. Default 0.
 * @type int $from_account_id ID of the source account from where transfer is initiating.
 *                               default null.
 * @type int $to_account_id ID of the target account where the transferred amount will be
 *                               deposited. default null.
 * @type string $amount Amount of the money that will be transferred. default 0.
 * @type string $date Date of the transfer. default null.
 * @type string $payment_method Payment method used in transfer. default null.
 * @type string $reference Reference used in transfer. Default empty.
 * @type string $description Description of the transfer. Default empty.
 *
 * }
 *
 * @return \EverAccounting\Models\Transfer|\WP_Error|\bool
 * @since 1.1.0
 *
 */
function eaccounting_insert_transfer( $transfer, $wp_error = false ) {
	global $wpdb;
	$user_id = get_current_user_id();
	if ( $transfer instanceof Transfer ) {
		$transfer = $transfer->to_array();
	} elseif ( $transfer instanceof stdClass ) {
		$transfer = get_object_vars( $transfer );
	}

	$defaults = array(
		'date'            => '',
		'amount'          => 0.00,
		'from_account_id' => 0,
		'to_account_id'   => 0,
		'income_id'       => 0,
		'expense_id'      => 0,
		'description'     => '',
		'payment_method'  => '',
		'currency_code'   => '',
		'currency_rate'   => '',
		'reference'       => '',
		'creator_id'      => $user_id,
		'date_created'    => '',
	);

	$posted = eaccounting_sanitize_fields( $transfer);
	$transfer_arr = wp_parse_args( $posted, $defaults );
	// Are we updating or creating?
	$transfer_id = null;
	$update      = false;
	$changes     = wp_parse_args( $posted, $defaults );
	if ( ! empty( $transfer_arr['id'] ) ) {
		$update          = true;
		$transfer_id     = (int) $transfer_arr['id'];
		$transfer_before = eaccounting_get_transfer( $transfer_id, ARRAY_A );
		if ( is_null( $transfer_before ) ) {
			if ( $wp_error ) {
				return new WP_Error( 'invalid_transaction', __( 'Invalid transfer id.' ) );
			}

			return 0;
		}

		$transfer_arr = wp_parse_args( $posted, $transfer_before );
		$changes      = array_diff_assoc( $transfer_arr, $transfer_before );
	}

	// Update properties when update account id.
	if ( array_key_exists( 'from_account_id', $changes ) ) {
		$account = eaccounting_get_account( (int) $changes['from_account_id'] );
		if ( ! empty( $account ) || ! is_wp_error( $account ) ) {
			$transfer_arr['currency_code'] = $account->currency_code;
		} else {
			$transfer_arr['currency_code'] = '';
		}
	}

	if ( array_key_exists( 'currency_code', $changes ) ) {
		$currency = eaccounting_get_currency( $transfer_arr['currency_code'] );
		if ( ! empty( $currency ) || ! is_wp_error( $currency ) ) {
			$transfer_arr['currency_rate'] = $currency->rate;
		} else {
			$transfer_arr['currency_rate'] = '';
		}
	}

	if ( empty( $transfer_arr['date'] ) || '0000-00-00 00:00:00' === $transfer_arr['date'] ) {
		$transfer_arr['date'] = current_time( 'mysql' );
	}

	if ( empty( $transfer_arr['amount'] ) ) {
		if ( $wp_error ) {
			return new WP_Error( 'invalid_transfer_amount', __( 'Invalid transfer amount.' ) );
		}

		return 0;
	}

	if ( empty( $transfer_arr['from_account_id'] ) ) {
		if ( $wp_error ) {
			return new WP_Error( 'invalid_transfer_from_account_id', __( 'Invalid transfer from account id.' ) );
		}

		return 0;
	}

	if ( empty( $transfer_arr['to_account_id'] ) ) {
		if ( $wp_error ) {
			return new WP_Error( 'invalid_transfer_to_account_id', __( 'Invalid transfer to account id.' ) );
		}

		return 0;
	}

	if ( empty( $transfer_arr['payment_method'] ) ) {
		if ( $wp_error ) {
			return new WP_Error( 'invalid_transfer_payment_method', __( 'Invalid transaction payment method.' ) );
		}

		return 0;
	}

	if ( empty( $transfer_arr['date_created'] ) || '0000-00-00 00:00:00' === $transfer_arr['date_created'] ) {
		$transfer_arr['date_created'] = current_time( 'mysql' );
	}

	$wpdb->query( 'START TRANSACTION' );

	$expense = eaccounting_insert_transaction( [
		'id'             => (int) $transfer_arr['expense_id'],
		'payment_date'   => $transfer_arr['date'],
		'type'           => 'other',
		'amount'         => $transfer_arr['amount'],
		'account_id'     => $transfer_arr['from_account_id'],
		'description'    => $transfer_arr['description'],
		'payment_method' => $transfer_arr['payment_method'],
		'reference'      => $transfer_arr['reference'],
		'date_created'   => $transfer_arr['date_created'],
	], true );

	if ( is_wp_error( $expense ) ) {
		$wpdb->query( 'ROLLBACK' );

		if ( $wp_error ) {
			return new WP_Error( 'invalid_transfer_expense', $expense->get_error_message() );
		}

		return 0;
	}

	$income = eaccounting_insert_transaction( [
		'id'             => (int) $transfer_arr['income_id'],
		'payment_date'   => $transfer_arr['date'],
		'type'           => 'other',
		'amount'         => $transfer_arr['amount'],
		'account_id'     => $transfer_arr['to_account_id'],
		'description'    => $transfer_arr['description'],
		'payment_method' => $transfer_arr['payment_method'],
		'reference'      => $transfer_arr['reference'],
		'date_created'   => $transfer_arr['date_created'],
	], true );

	if ( is_wp_error( $income ) ) {
		$wpdb->query( 'ROLLBACK' );

		if ( $wp_error ) {
			return new WP_Error( 'invalid_transfer_income', $income->get_error_message() );
		}

		return 0;
	}

	$income_id   = $income->id;
	$expense_id  = $expense->id;
	$creator_id  = $transfer_arr['creator_id'];
	$date_created = $transfer_arr['date_created'];

	$data = compact( 'income_id', 'expense_id', 'creator_id', 'date_created' );
	$data  = wp_unslash( $data );
	$where = array( 'id' => $transfer_id );

	if ( $update ) {
		do_action( 'eaccounting_pre_update_transfer', $transfer_id, $data, $posted );
		if ( false === $wpdb->update( $wpdb->prefix . 'ea_transfers', $data, $where ) ) {
			$wpdb->query( 'ROLLBACK' );
			if ( $wp_error ) {
				return new WP_Error( 'db_update_error', __( 'Could not update transfer in the database.' ), $wpdb->last_error );
			}
			return 0;
		}
		do_action( 'eaccounting_update_transfer', $transfer_id, $data, $posted );
	} else {
		if ( false === $wpdb->insert( $wpdb->prefix . 'ea_transfers', $data ) ) {
			$wpdb->query( 'ROLLBACK' );
			if ( $wp_error ) {
				return new WP_Error( 'db_insert_error', __( 'Could not insert transfer into the database.' ), $wpdb->last_error );
			}

			return 0;
		}
		do_action( 'eaccounting_pre_insert_transfer', $data, $posted );

		$transfer_id = (int) $wpdb->insert_id;

		do_action( 'eaccounting_insert_transfer', $transfer_id, $data, $posted );
	}

	$transfer = eaccounting_get_transfer( $transfer_id );

	/**
	 * Fires once a transfer has been saved.
	 *
	 * @param int $transfer_id Transaction id.
	 * @param Transaction $transfer Transaction object.
	 * @param bool $update Whether this is an existing transfer being updated.
	 *
	 * @since 1.2.1
	 *
	 */
	do_action( 'eaccounting_save_transfer', $transfer_id, $transfer, $posted );

	$wpdb->query( 'COMMIT' );

	return $transfer;
}

/**
 * Delete a transfer.
 *
 * @param $transfer_id
 *
 * @return bool
 * @since 1.1.0
 *
 */
function eaccounting_delete_transfer( $transfer_id ) {
	try {
		$transfer = new EverAccounting\Models\Transfer( $transfer_id );

		return $transfer->exists() ? $transfer->delete() : false;
	} catch ( \Exception $e ) {
		return false;
	}
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
 *
 *
 */
function eaccounting_get_transfers( $args = array() ) {
	//Prepare args.
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
		$where   .= " AND $table.`id` IN ($include)";
	} elseif ( ! empty( $qv['exclude'] ) ) {
		$exclude = implode( ',', wp_parse_id_list( $qv['exclude'] ) );
		$where   .= " AND $table.`id` NOT IN ($exclude)";
	}

	if ( ! empty( $qv['from_account_id'] ) ) {
		$from_account_in = implode( ',', wp_parse_id_list( $qv['from_account_id'] ) );
		$where           .= " AND expense.`account_id` IN ($from_account_in)";
	}

	if ( ! empty( $qv['to_account_id'] ) ) {
		$to_account_in = implode( ',', wp_parse_id_list( $qv['to_account_id'] ) );
		$where         .= " AND income.`account_id` IN ($to_account_in)";
	}

	$join = " LEFT JOIN {$wpdb->prefix}ea_transactions expense ON (expense.id = ea_transfers.expense_id) ";
	$join .= " LEFT JOIN {$wpdb->prefix}ea_transactions income ON (income.id = ea_transfers.income_id) ";

	if ( ! empty( $qv['date_created'] ) && is_array( $qv['date_created'] ) ) {
		$date_created_query = new \WP_Date_Query( $qv['date_created'], "{$table}.date_created" );
		$where              .= $date_created_query->get_sql();
	}

	if ( ! empty( $qv['payment_date'] ) && is_array( $qv['payment_date'] ) ) {
		$date_created_query = new \WP_Date_Query( $qv['payment_date'], 'expense.payment_date' );
		$where              .= $date_created_query->get_sql();
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
 * Get total income.
 *
 * @param null $year
 *
 * @return float
 * @since 1.1.0
 *
 */
function eaccounting_get_total_income( $year = null ) {
	global $wpdb;
	$total_income = wp_cache_get( 'total_income_' . $year, 'ea_transactions' );
	if ( false === $total_income ) {
		$where = '';
		if ( absint( $year ) ) {
			$financial_start = eaccounting_get_financial_start( $year );
			$financial_end   = eaccounting_get_financial_end( $year );
			$where           .= $wpdb->prepare( 'AND ( payment_date between %s AND %s )', $financial_start, $financial_end );
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
 *
 */
function eaccounting_get_total_expense( $year = null ) {
	global $wpdb;
	$total_expense = wp_cache_get( 'total_expense_' . $year, 'ea_transactions' );
	if ( false === $total_expense ) {
		$where = '';
		if ( absint( $year ) ) {
			$financial_start = eaccounting_get_financial_start( $year );
			$financial_end   = eaccounting_get_financial_end( $year );
			$where           .= $wpdb->prepare( 'AND ( payment_date between %s AND %s )', $financial_start, $financial_end );
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
 *
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
