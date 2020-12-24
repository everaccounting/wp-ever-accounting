<?php
/**
 * EverAccounting Transaction functions.
 *
 * Functions for all kind of transaction of the plugin.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get Transaction Types
 *
 * @since 1.1.0
 * @return array
 */
function eaccounting_get_transaction_types() {
	$types = array(
		'income'  => __( 'Income', 'wp-ever-accounting' ),
		'expense' => __( 'Expense', 'wp-ever-accounting' ),
	);

	return apply_filters( 'eaccounting_transaction_types', $types );
}

/**
 * Get a single expense.
 *
 * @since 1.1.0
 *
 * @param $payment
 *
 * @return \EverAccounting\Models\Payment|null
 */
function eaccounting_get_payment( $payment ) {
	if ( empty( $payment ) ) {
		return null;
	}
	try {
		$result = new EverAccounting\Models\Payment( $payment );

		return $result->exists() ? $result : null;
	} catch ( \Exception $e ) {
		return null;
	}
}


/**
 *  Create new expense programmatically.
 *
 *  Returns a new payment object on success.
 *
 * @since 1.1.0
 *
 * @param array $args           {
 *                              An array of elements that make up an expense to update or insert.
 *
 * @type int    $id             Transaction id. If the id is something other than 0 then it will update the transaction.
 * @type string $payment_date        Time of the transaction. Default null.
 * @type string $amount         Transaction amount. Default null.
 * @type int    $account_id     From/To which account the transaction is. Default empty.
 * @type int    $contact_id     Contact id related to the transaction. Default empty.
 * @type int    $document_id     Transaction related invoice id(optional). Default empty.
 * @type int    $category_id    Category of the transaction. Default empty.
 * @type string $payment_method Payment method used for the transaction. Default empty.
 * @type string $reference      Reference of the transaction. Default empty.
 * @type string $description    Description of the transaction. Default empty.
 *
 * }
 *
 * @return EverAccounting\Models\Payment|\WP_Error|bool
 */
function eaccounting_insert_expense( $args, $wp_error = true ) {
	// Ensure that we have data.
	if ( empty( $args ) ) {
		return false;
	}
	try {
		// The  id will be provided when updating an item.
		$args = wp_parse_args( $args, array( 'id' => null ) );

		// Retrieve the expense.
		$item = new \EverAccounting\Models\Payment( $args['id'] );

		// Load new data.
		$item->set_props( $args );

		// Save the item
		$item->save();

		return $item;
	} catch ( \Exception $e ) {
		return $wp_error ? new WP_Error( $e->getMessage(), array( 'status' => $e->getCode() ) ) : 0;
	}
}

/**
 * Delete a expense.
 *
 * @since 1.1.0
 *
 * @param $payment_id
 *
 * @return bool
 */
function eaccounting_delete_expense( $payment_id ) {
	try {
		$payment = new EverAccounting\Models\Payment( $payment_id );

		return $payment->exists() ? $payment->delete() : false;
	} catch ( \Exception $e ) {
		return false;
	}

}

/**
 * Get expense items.
 *
 * @since 1.1.0
 *
 * @param array $args           {
 *
 * @type int    $id             Transaction id.
 * @type string $payment_date        Time of the transaction.
 * @type string $amount         Transaction amount.
 * @type int    $account_id     From/To which account the transaction is.
 * @type int    $contact_id     Contact id related to the transaction.
 * @type int    $document_id     Transaction related invoice id(optional).
 * @type int    $category_id    Category of the transaction.
 * @type string $payment_method Payment method used for the transaction.
 * @type string $reference      Reference of the transaction.
 * @type string $description    Description of the transaction.
 *
 * }
 *
 *
 * @return array|int
 */
function eaccounting_get_payments( $args = array() ) {
	return eaccounting_get_transactions( array_merge( $args, array( 'type' => 'expense' ) ) );
}

/**
 * Get revenue.
 *
 * @since 1.1.0
 *
 * @param $income
 *
 * @return \EverAccounting\Models\Revenue|null
 */
function eaccounting_get_income( $income ) {
	if ( empty( $income ) ) {
		return null;
	}
	try {
		$result = new EverAccounting\Models\Revenue( $income );

		return $result->exists() ? $result : null;
	} catch ( \Exception $e ) {
		return null;
	}
}


/**
 *  Create new income programmatically.
 *
 *  Returns a new revenue object on success.
 *
 * @since 1.1.0
 *
 * @param array $args           {
 *                              An array of elements that make up an expense to update or insert.
 *
 * @type int    $id             Transaction id. If the id is something other than 0 then it will update the transaction.
 * @type string $payment_date   Time of the transaction. Default null.
 * @type string $amount         Transaction amount. Default null.
 * @type int    $account_id     From/To which account the transaction is. Default empty.
 * @type int    $contact_id     Contact id related to the transaction. Default empty.
 * @type int    $category_id    Category of the transaction. Default empty.
 * @type string $payment_method Payment method used for the transaction. Default empty.
 * @type string $reference      Reference of the transaction. Default empty.
 * @type string $description    Description of the transaction. Default empty.
 *
 * }
 *
 * @return EverAccounting\Models\Revenue|\WP_Error|bool
 */
function eaccounting_insert_revenue( $args, $wp_error = true ) {
	// Ensure that we have data.
	if ( empty( $args ) ) {
		return false;
	}
	try {
		// The  id will be provided when updating an item.
		$args = wp_parse_args( $args, array( 'id' => null ) );

		// Retrieve the income.
		$item = new \EverAccounting\Models\Revenue( $args['id'] );

		// Load new data.
		$item->set_props( $args );

		// Save the item
		$item->save();

		return $item;
	} catch ( \Exception $e ) {
		return $wp_error ? new WP_Error( $e->getMessage(), array( 'status' => $e->getCode() ) ) : 0;
	}
}

/**
 * Delete a income.
 *
 * @since 1.1.0
 *
 * @param $revenue_id
 *
 * @return bool
 */
function eaccounting_delete_revenue( $revenue_id ) {
	try {
		$income = new EverAccounting\Models\Revenue( $revenue_id );

		return $income->exists() ? $income->delete() : false;
	} catch ( \Exception $e ) {
		return false;
	}
}

/**
 * Get revenues items.
 *
 * @since 1.1.0
 *
 *
 * @param array $args           {
 *
 * @type int    $id             Transaction id.
 * @type string $payment_date        Time of the transaction.
 * @type string $amount         Transaction amount.
 * @type int    $account_id     From/To which account the transaction is.
 * @type int    $contact_id     Contact id related to the transaction.
 * @type int    $document_id     Transaction related invoice id(optional).
 * @type int    $category_id    Category of the transaction.
 * @type string $payment_method Payment method used for the transaction.
 * @type string $reference      Reference of the transaction.
 * @type string $description    Description of the transaction.
 *
 * }
 * @return \EverAccounting\Models\Revenue[]|int
 */
function eaccounting_get_revenues( $args = array() ) {
	return eaccounting_get_transactions( array_merge( $args, array( 'type' => 'income' ) ) );
}

/**
 * Get transfer.
 *
 * @since 1.1.0
 *
 * @param $transfer
 *
 * @return \EverAccounting\Models\Transfer|null
 */
function eaccounting_get_transfer( $transfer ) {
	if ( empty( $transfer ) ) {
		return null;
	}
	try {
		$result = new EverAccounting\Models\Transfer( $transfer );

		return $result->exists() ? $result : null;
	} catch ( \Exception $e ) {
		return null;
	}
}

/**
 * Create new transfer programmatically.
 *
 * Returns a new transfer object on success.
 *
 * @since 1.1.0
 *
 * @param array $args            {
 *                               An array of elements that make up an transfer to update or insert.
 *
 * @type int    $id              ID of the transfer. If equal to something other than 0,
 *                               the post with that ID will be updated. Default 0.
 * @type int    $from_account_id ID of the source account from where transfer is initiating.
 *                               default null.
 * @type int    $to_account_id   ID of the target account where the transferred amount will be
 *                               deposited. default null.
 * @type string $amount          Amount of the money that will be transferred. default 0.
 * @type string $date            Date of the transfer. default null.
 * @type string $payment_method  Payment method used in transfer. default null.
 * @type string $reference       Reference used in transfer. Default empty.
 * @type string $description     Description of the transfer. Default empty.
 *
 * }
 *
 * @return \EverAccounting\Models\Transfer|\WP_Error|\bool
 */
function eaccounting_insert_transfer( $args, $wp_error = true ) {
	// Ensure that we have data.
	if ( empty( $args ) ) {
		return false;
	}
	try {
		// The id will be provided when updating an item.
		$args = wp_parse_args( $args, array( 'id' => null ) );

		// Retrieve the transfer.
		$item = new \EverAccounting\Models\Transfer( $args['id'] );

		// Load new data.
		$item->set_props( $args );

		// Save the item
		$item->save();

		return $item;
	} catch ( \Exception $e ) {
		return $wp_error ? new WP_Error( $e->getMessage(), array( 'status' => $e->getCode() ) ) : 0;
	}
}

/**
 * Delete a transfer.
 *
 * @since 1.1.0
 *
 * @param $transfer_id
 *
 * @return bool
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
 * @since 1.1.0
 *
 *
 * @param array $args            {
 *
 * @type int    $id              ID of the transfer.
 * @type int    $from_account_id ID of the source account from where transfer is initiating.
 * @type int    $to_account_id   ID of the target account where the transferred amount will be deposited.
 * @type string $amount          Amount of the money that will be transferred.
 * @type string $date            Date of the transfer.
 * @type string $payment_method  Payment method used in transfer.
 * @type string $reference       Reference used in transfer.
 * @type string $description     Description of the transfer.
 *
 * }
 *
 * @return array|int
 */
function eaccounting_get_transfers( $args = array() ) {
	global $wpdb;
	$search_cols  = array( 'income.description', 'income.reference' );
	$orderby_cols = array(
		'id',
		'date',
	);
	$fields       = array(
		'ea_transfers.*',
		'expense.account_id from_account_id',
		'income.account_id to_account_id',
		'income.description description',
		'income.reference reference',
		'income.amount amount',
	);
	// Prepare args.
	$args = wp_parse_args(
		$args,
		array(
			'include'          => '',
			'search'           => '',
			'search_cols'      => $search_cols,
			'orderby_cols'     => $orderby_cols,
			'exclude_transfer' => true,
			'fields'           => $fields,
			'orderby'          => 'id',
			'order'            => 'ASC',
			'number'           => 20,
			'offset'           => 0,
			'paged'            => 1,
			'return'           => 'objects',
			'count_total'      => false,
		)
	);

	$qv    = apply_filters( 'eaccounting_get_transfer_args', $args );
	$table = 'ea_transfers';

	$query_fields  = eaccounting_prepare_query_fields( $qv, $table );
	$query_from    = eaccounting_prepare_query_from( $table );
	$query_where   = 'WHERE 1=1';
	$query_where  .= eaccounting_prepare_query_where( $qv, $table );
	$query_orderby = eaccounting_prepare_query_orderby( $qv, $table );
	$query_limit   = eaccounting_prepare_query_limit( $qv );
	$query_join    = '';

	$query_join .= " LEFT JOIN {$wpdb->prefix}ea_transactions expense ON (expense.id = ea_transfers.expense_id) ";
	$query_join .= " LEFT JOIN {$wpdb->prefix}ea_transactions income ON (income.id = ea_transfers.income_id) ";

	$count_total = true === $qv['count_total'];
	$cache_key   = md5( serialize( $qv ) );
	$results     = wp_cache_get( $cache_key, 'eaccounting_transaction' );
	$request     = "SELECT $query_fields $query_from $query_join $query_where $query_orderby $query_limit";

	if ( false === $results ) {
		if ( $count_total ) {
			$results = (int) $wpdb->get_var( $request );
			wp_cache_set( $cache_key, $results, 'eaccounting_transfer' );
		} else {
			$results = $wpdb->get_results( $request );
			if ( in_array( $qv['fields'], array( 'all', '*' ), true ) ) {
				foreach ( $results as $key => $item ) {
					wp_cache_set( $item->id, $item, 'eaccounting_transfer' );
				}
			}
			wp_cache_set( $cache_key, $results, 'eaccounting_transfer' );
		}
	}

	if ( 'objects' === $qv['return'] && true !== $qv['count_total'] ) {
		$results = array_map( 'eaccounting_get_transfer', $results );
	}

	return $results;
}


function eaccounting_get_transactions( $args = array() ) {
	global $wpdb;
	$search_cols  = array( 'description', 'reference' );
	$orderby_cols = array(
		'id',
		'type',
		'payment_date',
		'amount',
		'currency_code',
		'currency_rate',
		'account_id',
		'document_id',
		'contact_id',
		'category_id',
		'description',
		'payment_method',
		'reference',
		'attachment',
		'parent_id',
		'reconciled',
		'creator_id',
		'date_created',
	);
	// Prepare args.
	$args = wp_parse_args(
		$args,
		array(
			'type'             => '',
			'include'          => '',
			'search'           => '',
			'search_cols'      => $search_cols,
			'orderby_cols'     => $orderby_cols,
			'exclude_transfer' => true,
			'fields'           => '*',
			'orderby'          => 'id',
			'order'            => 'ASC',
			'number'           => 20,
			'offset'           => 0,
			'paged'            => 1,
			'return'           => 'objects',
			'count_total'      => false,
		)
	);

	$qv    = apply_filters( 'eaccounting_get_transactions_args', $args );
	$table = 'ea_transactions';

	$query_fields  = eaccounting_prepare_query_fields( $qv, $table );
	$query_from    = eaccounting_prepare_query_from( $table );
	$query_where   = 'WHERE 1=1';
	$query_where  .= eaccounting_prepare_query_where( $qv, $table );
	$query_orderby = eaccounting_prepare_query_orderby( $qv, $table );
	$query_limit   = eaccounting_prepare_query_limit( $qv );

	if ( ! empty( $qv['type'] ) ) {
		$types        = implode( "','", wp_parse_list( $qv['type'] ) );
		$query_where .= " AND $table.`type` IN ('$types')";
	}
	if ( ! empty( $qv['currency_code'] ) ) {
		$currency_code = implode( "','", wp_parse_list( $qv['currency_code'] ) );
		$query_where  .= " AND $table.`currency_code` IN ('$currency_code')";
	}
	if ( ! empty( $qv['payment_method'] ) ) {
		$payment_method = implode( "','", wp_parse_list( $qv['payment_method'] ) );
		$query_where   .= " AND $table.`payment_method` IN ('$payment_method')";
	}

	if ( ! empty( $qv['category_id'] ) ) {
		$category_in  = implode( ',', wp_parse_id_list( $qv['category_id'] ) );
		$query_where .= " AND $table.`category_id` IN ($category_in)";
	}
	if ( ! empty( $qv['account_id'] ) ) {
		$account_id   = implode( ',', wp_parse_id_list( $qv['account_id'] ) );
		$query_where .= " AND $table.`account_id` IN ($account_id)";
	}
	if ( ! empty( $qv['document_id'] ) ) {
		$document_id  = implode( ',', wp_parse_id_list( $qv['document_id'] ) );
		$query_where .= " AND $table.`document_id` IN ($document_id)";
	}
	if ( ! empty( $qv['invoice_id'] ) ) {
		$invoice_id   = implode( ',', wp_parse_id_list( $qv['invoice_id'] ) );
		$query_where .= " AND $table.`invoice_id` IN ($invoice_id)";
	}
	if ( ! empty( $qv['contact_id'] ) ) {
		$contact_id   = implode( ',', wp_parse_id_list( $qv['contact_id'] ) );
		$query_where .= " AND $table.`contact_id` IN ($contact_id)";
	}
	if ( ! empty( $qv['creator_id'] ) ) {
		$creator_id   = implode( ',', wp_parse_id_list( $qv['creator_id'] ) );
		$query_where .= " AND $table.`creator_id` IN ($creator_id)";
	}
	if ( ! empty( $qv['parent_id'] ) ) {
		$parent_id    = implode( ',', wp_parse_id_list( $qv['parent_id'] ) );
		$query_where .= " AND $table.`parent_id` IN ($parent_id)";
	}
	if ( ! empty( $qv['payment_date'] ) ) {
		$query_where .= eaccounting_sql_parse_date_query( $qv['payment_date'], "$table.payment_date" );
	}
	if ( true === $qv['exclude_transfer'] ) {
		$query_where .= " AND $table.`category_id` NOT IN (SELECT id from {$wpdb->prefix}ea_categories where type='other' )";
	}

	$count_total = true === $qv['count_total'];
	$cache_key   = md5( serialize( $qv ) );
	$results     = wp_cache_get( $cache_key, 'eaccounting_transaction' );
	$request     = "SELECT $query_fields $query_from $query_where $query_orderby $query_limit";

	if ( false === $results ) {
		if ( $count_total ) {
			$results = (int) $wpdb->get_var( $request );
			wp_cache_set( $cache_key, $results, 'eaccounting_transaction' );
		} else {
			$results = $wpdb->get_results( $request );
			if ( in_array( $qv['fields'], array( 'all', '*' ), true ) ) {
				foreach ( $results as $key => $item ) {
					wp_cache_set( $item->id, $item, 'eaccounting_transaction' );
					wp_cache_set( $item->id, $item, 'eaccounting_' . $item->type );
				}
			}
			wp_cache_set( $cache_key, $results, 'eaccounting_transaction' );
		}
	}

	if ( 'objects' === $qv['return'] && true !== $qv['count_total'] ) {
		$results = array_map(
			function ( $item ) {
				switch ( $item->type ) {
					case 'income':
						$transaction = new \EverAccounting\Models\Revenue();
						$transaction->set_props( $item );
						$transaction->set_object_read( true );

						return $transaction;
						break;
					case 'expense':
						$transaction = new \EverAccounting\Models\Payment();
						$transaction->set_props( $item );
						$transaction->set_object_read( true );

						return $transaction;
						break;
				}

				return null;
			},
			$results
		);
	}

	return $results;
}

