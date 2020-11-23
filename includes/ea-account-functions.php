<?php
/**
 * EverAccounting account Functions.
 *
 * All account related function of the plugin.
 *
 * @since   1.1.0
 * @package EverAccounting
 */
defined( 'ABSPATH' ) || exit;

/**
 * Main function for returning account.
 *
 * @param $account
 *
 * @return EverAccounting\Models\Account|null
 * @since 1.1.0
 *
 */
function eaccounting_get_account( $account ) {
	if ( empty( $account ) ) {
		return null;
	}
	try {
		$result = new EverAccounting\Models\Account( $account );

		return $result->exists() ? $result : null;
	} catch ( \EverAccounting\Core\Exception $e ) {
		return null;
	}
}

/**
 *  Create new account programmatically.
 *
 *  Returns a new account object on success.
 *
 * @param array $data {
 *                               An array of elements that make up an account to update or insert.
 *
 * @type int $id The account ID. If equal to something other than 0,
 *                                         the account with that id will be updated. Default 0.
 *
 * @type string $name The name of the account . Default empty.
 *
 * @type string $number The number of account. Default empty.
 *
 * @type string $currency_code The currency_code for the account.Default is empty.
 *
 * @type double $opening_balance The opening balance of the account. Default 0.0000.
 *
 * @type string $bank_name The bank name for the account. Default null.
 *
 * @type string $bank_phone The phone number of the bank on which the account is opened. Default null.
 *
 * @type string $bank_address The address of the bank. Default null.
 *
 * @type int $enabled The status of the account. Default 1.
 *
 * @type int $creator_id The creator id for the account. Default is current user id of the WordPress.
 *
 * @type string $date_created The date when the account is created. Default is current time.
 *
 *
 * }
 *
 * @return EverAccounting\Models\Account|\WP_Error|bool
 * @since 1.1.0
 *
 */
function eaccounting_insert_account( $data, $wp_error = true ) {
	// Ensure that we have data.
	if ( empty( $data ) ) {
		return false;
	}
	try {
		// The  id will be provided when updating an item.
		$data = wp_parse_args( $data, array( 'id' => null ) );

		// Retrieve the account.
		$item = new \EverAccounting\Models\Account( $data['id'] );

		// Load new data.
		$item->set_props( $data );

		// Save the item
		$item->save();

		return $item;
	} catch ( \EverAccounting\Core\Exception $e ) {
		return $wp_error ? new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) ) : 0;
	}
}

/**
 * Delete an account.
 *
 * @param $account_id
 *
 * @return bool
 * @since 1.1.0
 *
 */
function eaccounting_delete_account( $account_id ) {
	try {
		$account = new EverAccounting\Models\Account( $account_id );

		return $account->exists() ? $account->delete() : false;
	} catch ( \EverAccounting\Core\Exception $e ) {
		return false;
	}
}

/**
 * Get account items.
 *
 * @param array $args {
 *                               Optional. Arguments to retrieve accounts.
 *
 * @type string $name The name of the account .
 *
 * @type string $number The number of account.
 *
 * @type string $currency_code The currency_code for the account.
 *
 * @type double $opening_balance The opening balance of the account.
 *
 * @type string $bank_name The bank name for the account.
 *
 * @type string $bank_phone The phone number of the bank on which the account is opened.
 *
 * @type string $bank_address The address of the bank.
 *
 * @type int $enabled The status of the account.
 *
 * @type int $creator_id The creator id for the account.
 *
 * @type string $date_created The date when the account is created.
 *
 *
 * }
 *
 * @return array|int
 * @since 1.1.0
 *
 *
 */
function eaccounting_get_accounts( $args = array() ) {
	global $wpdb;
	$search_cols  = array( 'id', 'name', 'number', 'currency_code', 'bank_name', 'bank_phone', 'bank_address' );
	$orderby_cols = array( 'id', 'name', 'number', 'currency_code', 'bank_name', 'bank_phone', 'bank_address', 'enabled', 'date_created' );
	// Prepare args.
	$args = wp_parse_args(
		$args,
		array(
			'status'       => 'all',
			'include'      => '',
			'search'       => '',
			'search_cols'  => $search_cols,
			'orderby_cols' => $orderby_cols,
			'fields'       => '*',
			'orderby'      => 'id',
			'order'        => 'ASC',
			'number'       => 20,
			'offset'       => 0,
			'paged'        => 1,
			'return'       => 'objects',
			'count_total'  => false,
		)
	);

	$qv    = apply_filters( 'eaccounting_get_accounts_args', $args );
	$table = 'ea_accounts';

	$query_fields  = eaccounting_prepare_query_fields( $qv, $table );
	$query_from    = eaccounting_prepare_query_from( $table );
	$query_where   = 'WHERE 1=1';
	$query_where   .= eaccounting_prepare_query_where( $qv, $table );
	$query_orderby = eaccounting_prepare_query_orderby( $qv, $table );
	$query_limit   = eaccounting_prepare_query_limit( $qv );
	$count_total   = true === $qv['count_total'];
	$cache_key     = md5( serialize( $qv ) );
	$results       = wp_cache_get( $cache_key, 'eaccounting_account' );
	$request       = "SELECT $query_fields $query_from $query_where $query_orderby $query_limit";

	if ( false === $results ) {
		if ( $count_total ) {
			$results = (int) $wpdb->get_var( $request );
			wp_cache_set( $cache_key, $results, 'eaccounting_account' );
		} else {
			$results = $wpdb->get_results( $request );
			if ( in_array( $qv['fields'], array( 'all', '*' ), true ) ) {
				foreach ( $results as $key => $item ) {
					wp_cache_set( $item->id, $item, 'eaccounting_account' );
					wp_cache_set( $item->number, $item, 'eaccounting_account' );
				}
			}
			wp_cache_set( $cache_key, $results, 'eaccounting_category' );
		}
	}

	if ( 'objects' === $qv['return'] && true !== $qv['count_total'] ) {
		$results = array_map( 'eaccounting_get_account', $results );
	}

	return $results;
}
