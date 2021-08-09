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
		'income'   => __( 'Income', 'wp-ever-accounting' ),
		'expense'  => __( 'Expense', 'wp-ever-accounting' ),
		'transfer' => __( 'Transfer', 'wp-ever-accounting' ),
	);

	return apply_filters( 'eaccounting_transaction_types', $types );
}

/**
 * Retrieves transaction data given a transaction id or transaction object.
 *
 * @param int|object|Transaction $transaction transaction to retrieve
 * @param string $output The required return type. One of OBJECT, ARRAY_A, or ARRAY_N. Default OBJECT.
 *
 * @return Transaction|array|null
 * @since 1.1.0
 */
function eaccounting_get_transaction( $transaction, $output = OBJECT ) {
	if ( empty( $transaction ) ) {
		return null;
	}

	if ( $transaction instanceof Transaction ) {
		$_transaction = $transaction;
	} else {
		$_transaction = new Transaction( $transaction );
	}

	if ( !$_transaction->exists() ) {
		return null;
	}

	if ( ARRAY_A === $output ) {
		return $_transaction->to_array();
	}

	if ( ARRAY_N === $output ) {
		return array_values( $_transaction->to_array() );
	}

	return $_transaction;
}

/**
 *  Insert or update a transaction.
 *
 * @param array|object|Transaction $data An array, object, or transaction object of data arguments.
 *
 * @return Transaction|WP_Error The transaction object or WP_Error otherwise.
 * @global wpdb $wpdb WordPress database abstraction object.
 * @since 1.1.0
 */
function eaccounting_insert_transaction( $data ) {
	if ( $data instanceof Transaction ) {
		$data = $data->to_array();
	} elseif ( is_object( $data ) ) {
		$data = get_object_vars( $data );
	}

	if ( empty( $data ) || ! is_array( $data ) ) {
		return new WP_Error( 'invalid_transaction_data', __( 'Transaction could not be saved.', 'wp-ever-accounting' ) );
	}

	$data = wp_parse_args( $data, array( 'id' => null ) );
	$transaction = new Transaction( (int) $data['id'] );
	$transaction->set_props( $data );
	$is_error = $transaction->save();
	if ( is_wp_error( $is_error ) ) {
		return $is_error;
	}

	return $transaction;
}

/**
 * Delete an transaction.
 *
 * @param int $transaction_id Transaction ID
 *
 * @return array|false Transaction array data on success, false on failure.
 * @since 1.1.0
 */
function eaccounting_delete_transaction( $transaction_id ) {
	if ( $transaction_id instanceof Transaction ) {
		$transaction_id = $transaction_id->get_id();
	}

	if ( empty( $transaction_id ) ) {
		return false;
	}

	$transaction = new Transaction( (int) $transaction_id );
	if ( ! $transaction->exists() ) {
		return false;
	}

	return $transaction->delete();
}

/**
 * Retrieves an array of the transactions matching the given criteria.
 *
 * @param array $args Arguments to retrieve transactions.
 *
 * @return Transaction[]|int Array of transaction objects or count.
 * @since 1.1.0
 *
 */
function eaccounting_get_transactions( $args = array() ) {
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
	$query       = new \EverAccounting\Transaction_Query( $parsed_args );
	if ( true === $parsed_args['count_total'] ) {
		return $query->get_total();
	}


	return $query->get_results();
}

/**
 * Retrieves transfer data given a transfer id or transfer object.
 *
 * @param int|object|Transfer $transfer transfer to retrieve
 * @param string $output The required return type. One of OBJECT, ARRAY_A, or ARRAY_N. Default OBJECT.
 *
 * @return Transfer|array|null
 * @since 1.1.0
 */
function eaccounting_get_transfer( $transfer, $output = OBJECT ) {
	if ( empty( $transfer ) ) {
		return null;
	}

	if ( $transfer instanceof Transfer ) {
		$_transfer = $transfer;
	} else {
		$_transfer = new Transfer( $transfer );
	}

	if ( !$_transfer->exists() ) {
		return null;
	}

	if ( ARRAY_A === $output ) {
		return $_transfer->to_array();
	}

	if ( ARRAY_N === $output ) {
		return array_values( $_transfer->to_array() );
	}

	return $_transfer;
}

/**
 *  Insert or update a transfer.
 *
 * @param array|object|Transfer $data An array, object, or transfer object of data arguments.
 *
 * @return Transfer|WP_Error The transfer object or WP_Error otherwise.
 * @global wpdb $wpdb WordPress database abstraction object.
 * @since 1.1.0
 */
function eaccounting_insert_transfer( $data ) {
	if ( $data instanceof Transfer ) {
		$data = $data->to_array();
	} elseif ( is_object( $data ) ) {
		$data = get_object_vars( $data );
	}

	if ( empty( $data ) || ! is_array( $data ) ) {
		return new WP_Error( 'invalid_transfer_data', __( 'Transfer could not be saved.', 'wp-ever-accounting' ) );
	}

	$data = wp_parse_args( $data, array( 'id' => null ) );
	$transfer = new Transfer( (int) $data['id'] );
	$transfer->set_props( $data );
	$is_error = $transfer->save();
	if ( is_wp_error( $is_error ) ) {
		return $is_error;
	}

	return $transfer;
}

/**
 * Delete an transfer.
 *
 * @param int $transfer_id Transfer ID
 *
 * @return array|false Transfer array data on success, false on failure.
 * @since 1.1.0
 */
function eaccounting_delete_transfer( $transfer_id ) {
	if ( $transfer_id instanceof Transfer ) {
		$transfer_id = $transfer_id->get_id();
	}

	if ( empty( $transfer_id ) ) {
		return false;
	}

	$transfer = new Transfer( (int) $transfer_id );
	if ( ! $transfer->exists() ) {
		return false;
	}

	return $transfer->delete();
}

/**
 * Retrieves an array of the transfers matching the given criteria.
 *
 * @param array $args Arguments to retrieve transfers.
 *
 * @return Transfer[]|int Array of transfer objects or count.
 * @since 1.1.0
 *
 */
function eaccounting_get_transfers( $args = array() ) {
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
	$query       = new \EverAccounting\Transfer_Query( $parsed_args );
	if ( true === $parsed_args['count_total'] ) {
		return $query->get_total();
	}


	return $query->get_results();
}
