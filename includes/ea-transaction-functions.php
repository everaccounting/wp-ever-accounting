<?php
/**
 * EverAccounting Transaction functions.
 *
 * Functions for all kind of transaction of the plugin.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

use EverAccounting\Models\Payment;
use EverAccounting\Models\Revenue;

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
 * Get a single payment.
 *
 * @since 1.1.0
 *
 * @param $payment
 *
 * @return Payment|null
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
 *  Create new payment programmatically.
 *
 *  Returns a new payment object on success.
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
 * @type int    $document_id    Transaction related invoice id(optional). Default empty.
 * @type int    $category_id    Category of the transaction. Default empty.
 * @type string $payment_method Payment method used for the transaction. Default empty.
 * @type string $reference      Reference of the transaction. Default empty.
 * @type string $description    Description of the transaction. Default empty.
 *
 * }
 *
 * @return EverAccounting\Models\Payment|\WP_Error|bool
 */
function eaccounting_insert_payment( $args, $wp_error = true ) {
	// Ensure that we have data.
	if ( empty( $args ) ) {
		return false;
	}
	try {
		// The  id will be provided when updating an item.
		$args = wp_parse_args( $args, array( 'id' => null ) );

		// Retrieve the expense.
		$item = new Payment( $args['id'] );

		// Load new data.
		$item->set_props( $args );

		// Save the item
		$item->save();

		return $item;
	} catch ( \Exception $e ) {
		return $wp_error ? new WP_Error( 'insert_payment', $e->getMessage(), array( 'status' => $e->getCode() ) ) : 0;
	}
}

/**
 * Delete a payment.
 *
 * @since 1.1.0
 *
 * @param $payment_id
 *
 * @return bool
 */
function eaccounting_delete_payment( $payment_id ) {
	try {
		$payment = new EverAccounting\Models\Payment( $payment_id );

		return $payment->exists() ? $payment->delete() : false;
	} catch ( \Exception $e ) {
		return false;
	}

}

/**
 * Get payment items.
 *
 * @since 1.1.0
 *
 * @param array $args           {
 *
 * @type int    $id             Transaction id.
 * @type string $payment_date   Time of the transaction.
 * @type string $amount         Transaction amount.
 * @type int    $account_id     From/To which account the transaction is.
 * @type int    $contact_id     Contact id related to the transaction.
 * @type int    $document_id    Transaction related invoice id(optional).
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
 * @param $revenue
 *
 * @return Revenue|null
 */
function eaccounting_get_revenue( $revenue ) {
	if ( empty( $revenue ) ) {
		return null;
	}
	try {
		$result = new EverAccounting\Models\Revenue( $revenue );

		return $result->exists() ? $result : null;
	} catch ( \Exception $e ) {
		return null;
	}
}


/**
 *  Create new revenue programmatically.
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
		$item = new Revenue( $args['id'] );

		// Load new data.
		$item->set_props( $args );

		// Save the item
		$item->save();

		return $item;
	} catch ( \Exception $e ) {
		return $wp_error ? new WP_Error( 'insert_revenue', $e->getMessage(), array( 'status' => $e->getCode() ) ) : 0;
	}
}

/**
 * Delete a revenue.
 *
 * @since 1.1.0
 *
 * @param $revenue_id
 *
 * @return bool
 */
function eaccounting_delete_revenue( $revenue_id ) {
	try {
		$revenue = new EverAccounting\Models\Revenue( $revenue_id );

		return $revenue->exists() ? $revenue->delete() : false;
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
 * @type string $payment_date   Time of the transaction.
 * @type string $amount         Transaction amount.
 * @type int    $account_id     From/To which account the transaction is.
 * @type int    $contact_id     Contact id related to the transaction.
 * @type int    $document_id    Transaction related invoice id(optional).
 * @type int    $category_id    Category of the transaction.
 * @type string $payment_method Payment method used for the transaction.
 * @type string $reference      Reference of the transaction.
 * @type string $description    Description of the transaction.
 *
 * }
 * @return Revenue[]|int
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

		if ( $args['from_account_id'] == $args['to_account_id'] ) {
			throw new \Exception( __( "Source and Destination account can't be same.", 'wp-ever-accounting' ) );
		}

		// Retrieve the transfer.
		$item = new \EverAccounting\Models\Transfer( $args['id'] );

		// Load new data.
		$item->set_props( $args );

		// Save the item
		$item->save();

		return $item;
	} catch ( \Exception $e ) {
		return $wp_error ? new WP_Error( 'insert_transfer', $e->getMessage(), array( 'status' => $e->getCode() ) ) : 0;
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
			'include'      => '',
			'search'       => '',
			'from_id'      => '',
			'search_cols'  => $search_cols,
			'orderby_cols' => $orderby_cols,
			'fields'       => $fields,
			'orderby'      => 'id',
			'order'        => 'ASC',
			'number'       => 20,
			'offset'       => 0,
			'paged'        => 1,
			'return'       => 'objects',
			'count_total'  => false,
		)
	);

	$qv    = apply_filters( 'eaccounting_get_transfer_args', $args );
	$table = 'ea_transfers';

	$query_fields  = eaccounting_prepare_query_fields( $qv, $table );
	$query_from    = eaccounting_prepare_query_from( $table );
	$query_where  = "WHERE 1=1";
	$query_where   .= eaccounting_prepare_query_where( $qv, $table );
	$query_orderby = eaccounting_prepare_query_orderby( $qv, $table );
	$query_limit   = eaccounting_prepare_query_limit( $qv );
	$query_join    = '';
	$query_join .= " LEFT JOIN {$wpdb->prefix}ea_transactions expense ON (expense.id = ea_transfers.expense_id) ";
	$query_join .= " LEFT JOIN {$wpdb->prefix}ea_transactions income ON (income.id = ea_transfers.income_id) ";
	if ( ! empty( $qv['from_id'] ) ) {
		$from_id      = implode( ',', wp_parse_id_list( $qv['from_id'] ) );
		$query_where .= " AND expense.`account_id` IN ($from_id)";
	}

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

/**
 * Get transaction items.
 *
 * @since 1.0.
 *
 * @param array $args
 *
 * @return array|Payment[]|Revenue[]|int
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
			'orderby'     => 'id',
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
	//search
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

	if ( ! empty( $qv['payment_date'] ) && is_array( $qv['payment_date'] ) ) {
		$date_created_query = new \WP_Date_Query( $qv['payment_date'], "{$table}.payment_date" );
		$where             .= $date_created_query->get_sql();
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
	$cache_key   = md5( serialize( $qv ) );
	$results     = wp_cache_get( $cache_key, 'ea_transactions' );
	$clauses     = compact( 'select', 'from', 'where', 'orderby', 'limit' );

	if ( false === $results ) {
		if ( $count_total ) {
			$results = (int) $wpdb->get_var( "SELECT COUNT(id) $from $where" );
			wp_cache_set( $cache_key, $results, 'ea_transactions' );
		} else {
			$results = $wpdb->get_results( implode( ' ', $clauses ) );
			if ( in_array( $qv['fields'], array( 'all', '*' ), true ) ) {
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
 * @since 1.1.0
 *
 * @return float
 */
function eaccounting_get_total_income( $year = null ) {
	global $wpdb;
	$total_income = wp_cache_get( 'total_income_' . $year, 'ea_transactions' );
	if ( false === $total_income ) {
		$sql          = $wpdb->prepare(
			" SELECT Sum(amount) amount,currency_code,currency_rate
				FROM   {$wpdb->prefix}ea_transactions
				WHERE  type = %s AND category_id NOT IN (SELECT id FROM   {$wpdb->prefix}ea_categories WHERE  type = 'other')
				GROUP  BY currency_code, currency_rate
			",
			'income'
		);
		$results      = $wpdb->get_results( $sql );
		$total_income = 0;
		foreach ( $results as $result ) {
			$total_income += eaccounting_price_convert_to_default( $result->amount, $result->currency_code, $result->currency_rate );
		}
		wp_cache_add( 'total_income_' . $year, $total_income, 'ea_transactions' );
	}

	return $total_income;
}

/**
 * Get total expense.
 *
 * @param null $year
 * @since 1.1.0
 *
 * @return float
 */
function eaccounting_get_total_expense( $year = null ) {
	global $wpdb;
	$total_expense = wp_cache_get( 'total_expense_' . $year, 'ea_transactions' );
	if ( false === $total_expense ) {
		$sql           = $wpdb->prepare(
			" SELECT Sum(amount) amount,currency_code,currency_rate
				FROM   {$wpdb->prefix}ea_transactions
				WHERE  type = %s AND category_id NOT IN (SELECT id FROM   {$wpdb->prefix}ea_categories WHERE  type = 'other')
				GROUP  BY currency_code, currency_rate
			",
			'expense'
		);
		$results       = $wpdb->get_results( $sql );
		$total_expense = 0;
		foreach ( $results as $result ) {
			$total_expense += eaccounting_price_convert_to_default( $result->amount, $result->currency_code, $result->currency_rate );
		}
		wp_cache_add( 'total_expense_' . $year, $total_expense, 'ea_transactions' );
	}

	return $total_expense;
}

/**
 * Get total profit.
 *
 * @param null $year
 * @since 1.1.0
 *
 * @return float
 */
function eaccounting_get_total_profit( $year = null ) {
	$total_income  = (float) eaccounting_get_total_income( $year );
	$total_expense = (float) eaccounting_get_total_expense( $year );

	return $total_income - $total_expense;
}

/**
 * Get total receivable.
 *
 * @since 1.1.0
 * @return false|float|int|mixed|string
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
			$total_receivable += eaccounting_price_convert_to_default( $invoice->amount, $invoice->currency_code, $invoice->currency_rate );
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
			$total_receivable -= eaccounting_price_convert_to_default( $result->amount, $result->currency_code, $result->currency_rate );
		}
		wp_cache_add( 'total_receivable', $total_receivable, 'ea_transactions' );
	}

	return $total_receivable;
}

/**
 * Get total payable.
 *
 * @since 1.1.0
 * @return float
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
			$total_payable += eaccounting_price_convert_to_default( $bill->amount, $bill->currency_code, $bill->currency_rate );
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
			$total_payable -= eaccounting_price_convert_to_default( $result->amount, $result->currency_code, $result->currency_rate );
		}
		wp_cache_add( 'total_payable', $total_payable, 'ea_transactions' );
	}

	return $total_payable;
}

/**
 * Get total upcoming profit
 * @since 1.1.0
 * @return float
 */
function eaccounting_get_total_upcoming_profit() {
	$total_payable    = (float) eaccounting_get_total_payable();
	$total_receivable = (float) eaccounting_get_total_receivable();

	return $total_receivable - $total_payable;
}
