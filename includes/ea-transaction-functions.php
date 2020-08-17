<?php
/**
 * EverAccounting Transaction functions.
 *
 * Functions for all kind of transaction of the plugin.
 *
 * @package EverAccounting
 * @since 1.0.2
 */

defined( 'ABSPATH' ) || exit;


/**
 * Get Transaction Types
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
 * Main function for returning transaction.
 *
 * @param $transaction
 *
 * @return \EverAccounting\Transaction|null
 * @since 1.0.2
 *
 */
function eaccounting_get_transaction( $transaction ) {
	if ( empty( $transaction ) ) {
		return null;
	}

	try {
		if ( $transaction instanceof \EverAccounting\Transaction ) {
			$_transaction = $transaction;
		} elseif ( is_object( $transaction ) && ! empty( $transaction->id ) ) {
			$_transaction = new \EverAccounting\Transaction( null );
			$_transaction->populate( $transaction );
		} else {
			$_transaction = new \EverAccounting\Transaction( absint( $transaction ) );
		}

		if ( ! $_transaction->exists() ) {
			throw new Exception( __( 'Invalid account.', 'wp-ever-accounting' ) );
		}

		return $_transaction;
	} catch ( Exception $exception ) {
		return null;
	}
}

/**
 *  Create new transaction programmatically.
 *
 *  Returns a new transaction object on success.
 *
 * @param array $args transaction arguments.
 *
 * @return \EverAccounting\Transaction|WP_Error
 * @since 1.0.2
 *
 */
function eaccounting_insert_transaction( $args ) {

	try {
		$default_args = array(
			'id' => null,
		);
		$args         = (array) wp_parse_args( $args, $default_args );
		$transaction  = new \EverAccounting\Transaction( $args['id'] );
		$transaction->set_props( $args );
		$transaction->save();

	} catch ( Exception $e ) {
		return new WP_Error( 'error', $e->getMessage() );
	}

	return $transaction;
}

/**
 * Delete an transaction.
 *
 * @param $transaction_id
 *
 * @return bool
 * @since 1.0.2
 *
 */
function eaccounting_delete_transaction( $transaction_id ) {
	try {
		$transaction = new \EverAccounting\Transaction( $transaction_id );
		if ( ! $transaction->exists() ) {
			throw new Exception( __( 'Invalid transaction.', 'wp-ever-accounting' ) );
		}

		$transaction->delete();

		return empty( $transaction->get_id() );

	} catch ( Exception $exception ) {
		return false;
	}
}
