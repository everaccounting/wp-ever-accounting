<?php

defined( 'ABSPATH' ) || exit;

function eaccounting_get_transaction_types() {
	return \EverAccounting\Transactions\get_types();
}

function eaccounting_get_transaction( $transaction ) {
	return \EverAccounting\Transactions\get($transaction);
}

function eaccounting_insert_transaction( $args ) {
	return \EverAccounting\Transactions\insert( $args );
}

function eaccounting_delete_transaction( $transaction_id ) {
	return \EverAccounting\Transactions\delete( $transaction_id );
}
