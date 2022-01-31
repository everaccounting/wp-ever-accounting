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

function eaccounting_get_category_types() {
	return \EverAccounting\Categories::get_category_types();
}

function eaccounting_get_category_type( $type ) {
	return \EverAccounting\Categories::get_category_type( $type );
}

function eaccounting_get_category( $category ) {
	return \EverAccounting\Categories::get_category( $category );
}

function eaccounting_get_category_by_name( $name, $type ) {
	return \EverAccounting\Categories::get_category_by_name( $name, $type );
}

function eaccounting_insert_category( $data = array(), $wp_error = true ) {
	return \EverAccounting\Categories::insert_category( $data = array() );
}

function eaccounting_delete_category( $category_id ) {
	return \EverAccounting\Categories::delete_category( $category_id );
}

function eaccounting_get_categories( $args = array() ) {
	return \EverAccounting\Categories::get_categories( $args = array(), true );
}

function eaccounting_get_transaction_types() {
	return \EverAccounting\Transactions::get_transaction_types();
}

function eaccounting_get_payment( $payment ) {
	return \EverAccounting\Transactions::get_payment( $payment );
}

function eaccounting_insert_payment( $args, $wp_error = true ) {
	return \EverAccounting\Transactions::insert_payment( $args );
}

function eaccounting_delete_payment( $payment_id ) {
	return \EverAccounting\Transactions::delete_revenue( $payment_id );
}

function eaccounting_get_payments( $args ) {
	return \EverAccounting\Transactions::get_payments( $args );
}

function eaccounting_get_revenue( $revenue ) {
	return \EverAccounting\Transactions::get_revenue( $revenue );
}

function eaccounting_insert_revenue( $args, $wp_error = true ) {
	return \EverAccounting\Transactions::insert_revenue( $args );
}

function eaccounting_delete_revenue( $revenue_id ) {
	return \EverAccounting\Transactions::delete_revenue( $revenue_id );
}

function eaccounting_get_revenues( $args ) {
	return \EverAccounting\Transactions::get_revenues( $args );
}

function eaccounting_get_transactions( $args ) {
	return \EverAccounting\Transactions::get_transactions( $args );
}

function eaccounting_get_note( $item ) {
	return \EverAccounting\Notes::get_note( $item->get_id() );
}

function eaccounting_insert_note( $args, $wp_error = true ) {
	return \EverAccounting\Notes::insert_note( $args );
}

function eaccounting_delete_note( $note_id ) {
	return \EverAccounting\Notes::delete_note( $note_id );
}

function eaccounting_get_notes( $args = array() ) {
	return \EverAccounting\Notes::get_notes( $args = array(), false );
}

function eaccounting_get_currency_codes() {
	return \EverAccounting\Currencies::get_currency_codes();
}

function eaccounting_sanitize_currency_code( $code ) {
	return \EverAccounting\Currencies::sanitize_currency_code( $code );
}

function eaccounting_get_currency( $currency ) {
	return \EverAccounting\Currencies::get_currency_by_code( $currency );
}

function eaccounting_get_currency_rate( $currency ) {
	return \EverAccounting\Currencies::get_currency_rate( $currency );
}

function eaccounting_insert_currency( $args, $wp_error = true ) {
	return \EverAccounting\Currencies::insert_currency( $args );
}

function eaccounting_get_currencies( $args = array() ) {
	return \EverAccounting\Currencies::get_currencies( $args, false );
}
