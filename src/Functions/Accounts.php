<?php

use EverAccounting\Models\Account;

defined( 'ABSPATH' ) || exit;


/**
 * Get bank types.
 *
 * @since 1.0.2
 *
 * @return array
 */
function eac_get_account_types() {
	$bank_types = array(
		'bank' => __( 'Bank', 'wp-ever-accounting' ),
		'card' => __( 'Card', 'wp-ever-accounting' ),
	);

	return apply_filters( 'wp_ever_accounting_account_types', $bank_types );
}


/**
 * Main function for returning account.
 *
 * @param mixed  $account Account ID or object.
 * @param string $column Optional. Column to get. Default null.
 * @param array  $args Optional. Additional arguments. Default empty array.
 *
 * @since 1.1.6
 *
 * @return EverAccounting\Models\Account|null
 */
function eac_get_account( $account, $column = null, $args = array() ) {
	return Account::get( $account, $column, $args );
}

/**
 *  Create new account programmatically.
 *
 *  Returns a new account object on success.
 *
 * @param array $data Account data.
 * @param bool  $wp_error Optional. Whether to return a WP_Error on failure. Default false.
 *
 * @since 1.1.0
 * @return Account|false|WP_Error Account object on success, false or WP_Error on failure.
 */
function eac_insert_account( $data, $wp_error = true ) {
	return Account::insert( $data, $wp_error );
}

/**
 * Delete an account.
 *
 * @param int $account_id Account ID.
 *
 * @since 1.1.0
 *
 * @return bool
 */
function eac_delete_account( $account_id ) {
	$account = eac_get_account( $account_id );
	if ( ! $account ) {
		return false;
	}

	return $account->delete();
}

/**
 * Get account items.
 *
 * @param array $args Query arguments.
 * @param bool  $count Optional. Whether to return only the total found accounts for the query.
 *
 * @since 1.1.0
 *
 * @return array|int|Account[] Array of account objects, the total found accounts for the query, or the total found accounts for the query as int when `$count` is true.
 */
function eac_get_accounts( $args = array(), $count = false ) {
	$defaults = array(
		'limit'   => 20,
		'offset'  => 0,
		'orderby' => 'id',
		'order'   => 'DESC',
		'fields'  => 'all',
	);

	$args = wp_parse_args( $args, $defaults );

	if ( $count ) {
		return Account::count( $args );
	}

	return Account::query( $args );
}

/**
 * Get Accounts currencies.
 *
 * @since 1.1.0
 * @returns array
 */
function eac_get_account_currencies() {
	$currencies = get_transient( 'eac_account_currencies' );
	if ( false === $currencies ) {
		$currencies = array();
		$accounts   = eac_get_accounts(
			array(
				'limit'    => - 1,
				'no_count' => true,
			)
		);
		foreach ( $accounts as $account ) {
			$currencies[ $account->get_id() ] = $account->get_currency_code();
		}
		set_transient( 'eac_account_currencies', $currencies, 24 * DAY_IN_SECONDS );
	}

	return $currencies;
}

/**
 * Flush accounts currencies.
 *
 * @since 1.1.0
 */
function eac_flush_account_currencies() {
	delete_transient( 'eac_account_currencies' );
}

add_action( 'ever_accounting_account_saved', 'eac_flush_account_currencies' );
add_action( 'ever_accounting_account_deleted', 'eac_flush_account_currencies' );
