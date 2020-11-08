<?php
defined( 'ABSPATH' ) || exit();


function eaccounting_get_transfer( $transfer ) {
	return \EverAccounting\Transfers\get( $transfer );
}

function eaccounting_insert_transfer( $args ) {
	return \EverAccounting\Transfers\insert( $args );
}

function eaccounting_delete_transfer( $transfer_id ) {
	return \EverAccounting\Transfers\delete( $transfer_id );
}
