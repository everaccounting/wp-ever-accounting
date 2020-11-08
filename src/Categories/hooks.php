<?php
defined( 'ABSPATH' ) || exit();

/**
 * Delete category id from transactions.
 *
 * @since 1.0.2
 *
 * @param $id
 *
 * @return bool
 */
function eaccounting_update_transaction_category( $id ) {
	$id = absint( $id );
	if ( empty( $id ) ) {
		return false;
	}
	$transactions = \EverAccounting\Query::init();
	return $transactions->table( 'ea_transactions' )->where( 'category_id', absint( $id ) )->update( array( 'category_id' => '' ) );
}

add_action( 'eaccounting_delete_category', 'eaccounting_update_transaction_category' );
