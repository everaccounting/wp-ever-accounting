<?php
/**
 * EverAccounting account Functions.
 *
 * All account related function of the plugin.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

use EverAccounting\Account;

defined( 'ABSPATH' ) || exit;

/**
 * Retrieves account data given a account id or account object.
 *
 * @param int|object|Account $account account to retrieve
 * @param string $output The required return type. One of OBJECT, ARRAY_A, or ARRAY_N. Default OBJECT.
 *
 * @return Account|array|null
 * @since 1.1.0
 */
function eaccounting_get_account( $account, $output = OBJECT ) {
	if ( empty( $account ) ) {
		return null;
	}

	if ( $account instanceof Account ) {
		$_account = $account;
	} else {
		$_account = new Account( $account );
	}

	if ( $_account->exists() ) {
		return null;
	}

	if ( ARRAY_A === $output ) {
		return $_account->to_array();
	}

	if ( ARRAY_N === $output ) {
		return array_values( $_account->to_array() );
	}

	return $_account;
}

/**
 *  Insert or update a account.
 *
 * @param array|object|Account $data An array, object, or account object of data arguments.
 *
 * @return Account|WP_Error The account object or WP_Error otherwise.
 * @global wpdb $wpdb WordPress database abstraction object.
 * @since 1.1.0
 */
function eaccounting_insert_account( $data ) {
	if ( $data instanceof Account ) {
		$data = $data->to_array();
	} elseif ( is_object( $data ) ) {
		$data = get_object_vars( $data );
	}

	if ( empty( $data ) || ! is_array( $data ) ) {
		return new WP_Error( 'invalid_account_data', __( 'Account could not be saved.', 'wp-ever-accounting' ) );
	}

	$data = wp_parse_args( $data, array( 'id' => null ) );
	$account = new Account( (int) $data['id'] );
	$account->set_props( $data );
	$is_error = $account->save();
	if ( is_wp_error( $is_error ) ) {
		return $is_error;
	}

	return $account;
}

/**
 * Delete an account.
 *
 * @param int $account_id Account ID
 *
 * @return array|false Account array data on success, false on failure.
 * @since 1.1.0
 */
function eaccounting_delete_account( $account_id ) {
	if ( $account_id instanceof Account ) {
		$account_id = $account_id->get_id();
	}

	if ( empty( $account_id ) ) {
		return false;
	}

	$account = new Account( (int) $account_id );
	if ( ! $account->exists() ) {
		return false;
	}

	return $account->delete();
}

/**
 * Retrieves an array of the accounts matching the given criteria.
 *
 * @param array $args Arguments to retrieve accounts.
 *
 * @return Account[]|int Array of account objects or count.
 * @since 1.1.0
 *
 */
function eaccounting_get_accounts( $args = array() ) {
	$defaults = array(
		'number'        => 20,
		'orderby'       => 'name',
		'order'         => 'DESC',
		'include'       => array(),
		'exclude'       => array(),
		'no_found_rows' => false,
		'count_total'   => false,
	);

	$parsed_args = wp_parse_args( $args, $defaults );
	$query       = new \EverAccounting\Account_Query( $parsed_args );
	if ( true === $parsed_args['count_total'] ) {
		return $query->get_total();
	}


	return $query->get_results();
}
