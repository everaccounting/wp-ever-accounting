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

function eaccounting_get_contact_type( $type ) {
	return \EverAccounting\Contacts::get_contact_type( $type );
}

function eaccounting_get_customer( $customer ) {
	return \EverAccounting\Contacts::get_customer( $customer );
}

function eaccounting_get_customer_by_email( $email ) {
	return \EverAccounting\Contacts::get_customer_by_email( $email );
}

function eaccounting_insert_customer( $args, $wp_error = true ) {
	return \EverAccounting\Contacts::insert_customer( $args );
}

function eaccounting_delete_customer( $customer_id ) {
	return \EverAccounting\Contacts::delete_customer( $customer_id );
}

function eaccounting_get_customers( $args = array() ) {
	return \EverAccounting\Contacts::get_customers( $args, false );
}

function eaccounting_get_vendor( $vendor ) {
	return \EverAccounting\Contacts::get_vendor( $vendor );
}

function eaccounting_get_vendor_by_email( $email ) {
	return \EverAccounting\Contacts::get_vendor_by_email( $email );
}

function eaccounting_insert_vendor( $args, $wp_error = true ) {
	return \EverAccounting\Contacts::insert_vendor( $args );
}

function eaccounting_delete_vendor( $vendor_id ) {
	return \EverAccounting\Contacts::delete_vendor( $vendor_id );
}

function eaccounting_get_vendors( $args = array() ) {
	return \EverAccounting\Contacts::get_vendors( $args );
}

function eaccounting_get_contacts( $args = array() ) {
	return \EverAccounting\Contacts::get_contacts( $args );
}

function eaccounting_get_account( $account ) {
	return \EverAccounting\Accounts::get_account( $account );
}

function eaccounting_get_account_currency_code( $account ) {
	return \EverAccounting\Accounts::get_account_currency_code( $account );
}

function eaccounting_insert_account( $data, $wp_error = true ) {
	return \EverAccounting\Accounts::insert_account( $data );
}

function eaccounting_delete_account( $account_id ) {
	return \EverAccounting\Accounts::delete_account( $account_id );
}

function eaccounting_get_accounts( $args = array() ) {
	return \EverAccounting\Accounts::get_accounts( $args = array(), true );
}
