<?php
/**
 * Deprecated functions
 *
 * Where functions come to die.
 *
 * @author   EverAccounting
 * @category Core
 * @package  EverAccounting\Functions
 * @version  1.1.0
 */

defined( 'ABSPATH' ) || exit;

function eaccounting_get_global_currencies() {
	return eaccounting_get_currency_codes();
}

function eaccounting_get_currency_codes() {
	return eaccounting_get_currency_iso_codes();
}

function eaccounting_get_customers( $args = [] ) {
	return eaccounting_get_contacts( array_merge( $args, [ 'type' => 'customer' ] ) );
}

function eaccounting_get_vendors( $args = [] ) {
	return eaccounting_get_contacts( array_merge( $args, [ 'type' => 'vendor' ] ) );
}

function eaccounting_get_customer( $args ) {
	return eaccounting_get_contact( $args );
}

function eaccounting_get_vendor( $args ) {
	return eaccounting_get_contact( $args );
}


function eaccounting_get_revenues( $args = [] ) {
	return eaccounting_get_transactions( array_merge( $args, [ 'type' => 'income' ] ) );
}

function eaccounting_get_payments( $args = [] ) {
	return eaccounting_get_contacts( array_merge( $args, [ 'type' => 'expense' ] ) );
}
