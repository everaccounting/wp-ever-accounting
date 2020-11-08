<?php

defined( 'ABSPATH' ) || exit();
/**
 * Delete contact id from transactions.
 *
 * @since 1.0.2
 *
 * @param $id
 *
 * @return bool
 */
function eaccounting_update_transaction_contact( $id ) {
	$id = absint( $id );
	if ( empty( $id ) ) {
		return false;
	}
	$transactions = \EverAccounting\Query::init();

	return $transactions->table( 'ea_transactions' )->where( 'contact_id', absint( $id ) )->update( array( 'contact_id' => '' ) );
}

add_action( 'eaccounting_delete_contact', 'eaccounting_update_transaction_contact' );
