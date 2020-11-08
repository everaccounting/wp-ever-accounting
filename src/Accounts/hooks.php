<?php
defined( 'ABSPATH' ) || exit;

/**
 * Delete default account from settings
 *
 * @since 1.0.2
 *
 * @param int $id ID of the default account.
 *
 */
function eaccounting_delete_default_account( $id ) {
	$default_account = eaccounting()->settings->get( 'default_account' );
	if ( intval( $default_account ) === intval( $id ) ) {
		eaccounting()->settings->set( array( array( 'default_account' => '' ) ), true );
	}

}

add_action( 'eaccounting_delete_account', 'eaccounting_delete_default_account' );

/**
 * Delete account id from transactions.
 *
 * @since 1.0.2
 *
 * @param $id
 *
 * @return bool
 */
function eaccounting_update_transaction_account( $id ) {
	$id = absint( $id );
	if ( empty( $id ) ) {
		return false;
	}
	$transactions = \EverAccounting\Query::init();

	return $transactions->table( 'ea_transactions' )->where( 'account_id', absint( $id ) )->update( array( 'account_id' => '' ) );
}

add_action( 'eaccounting_delete_account', 'eaccounting_update_transaction_account' );


