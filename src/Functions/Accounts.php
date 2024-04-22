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

	return apply_filters( 'ever_accounting_account_types', $bank_types );
}


/**
 * Main function for returning account.
 *
 * @param mixed $account Account ID or object.
 *
 * @since 1.1.6
 *
 * @return EverAccounting\Models\Account|null
 */
function eac_get_account( $account ) {
	return Account::get( $account );
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
