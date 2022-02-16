<?php
/**
 * Deprecated functions
 *
 * Where functions come to die.
 *
 * @author   Ever_Accounting
 * @category Core
 * @package  Ever_Accounting\Functions
 * @version  1.1.0
 */

defined( 'ABSPATH' ) || exit;

function eaccounting_get_global_currencies() {
	return eaccounting_get_currency_codes();
}

function eaccounting_get_contact_type( $type ) {
	return \Ever_Accounting\Contacts::get_contact_type( $type );
}

function eaccounting_get_customer( $customer ) {
	return \Ever_Accounting\Contacts::get_customer( $customer );
}

function eaccounting_get_customer_by_email( $email ) {
	return \Ever_Accounting\Contacts::get_customer_by_email( $email );
}

function eaccounting_insert_customer( $args, $wp_error = true ) {
	return \Ever_Accounting\Contacts::insert_customer( $args );
}

function eaccounting_delete_customer( $customer_id ) {
	return \Ever_Accounting\Contacts::delete_customer( $customer_id );
}

function eaccounting_get_customers( $args = array() ) {
	return \Ever_Accounting\Contacts::get_customers( $args, false );
}

function eaccounting_get_vendor( $vendor ) {
	return \Ever_Accounting\Contacts::get_vendor( $vendor );
}

function eaccounting_get_vendor_by_email( $email ) {
	return \Ever_Accounting\Contacts::get_vendor_by_email( $email );
}

function eaccounting_insert_vendor( $args, $wp_error = true ) {
	return \Ever_Accounting\Contacts::insert_vendor( $args );
}

function eaccounting_delete_vendor( $vendor_id ) {
	return \Ever_Accounting\Contacts::delete_vendor( $vendor_id );
}

function eaccounting_get_vendors( $args = array() ) {
	return \Ever_Accounting\Contacts::get_vendors( $args );
}

function eaccounting_get_contacts( $args = array() ) {
	return \Ever_Accounting\Contacts::get_contacts( $args );
}

function eaccounting_get_account( $account ) {
	return \Ever_Accounting\Accounts::get_account( $account );
}

function eaccounting_get_account_currency_code( $account ) {
	return \Ever_Accounting\Accounts::get_account_currency_code( $account );
}

function eaccounting_insert_account( $data, $wp_error = true ) {
	return \Ever_Accounting\Accounts::insert_account( $data );
}

function eaccounting_delete_account( $account_id ) {
	return \Ever_Accounting\Accounts::delete_account( $account_id );
}

function eaccounting_get_accounts( $args = array() ) {
	return \Ever_Accounting\Accounts::get_accounts( $args = array(), true );
}

function eaccounting_get_category_types() {
	return \Ever_Accounting\Categories::get_category_types();
}

function eaccounting_get_category_type( $type ) {
	return \Ever_Accounting\Categories::get_category_type( $type );
}

function eaccounting_get_category( $category ) {
	return \Ever_Accounting\Categories::get_category( $category );
}

function eaccounting_get_category_by_name( $name, $type ) {
	return \Ever_Accounting\Categories::get_category_by_name( $name, $type );
}

function eaccounting_insert_category( $data = array(), $wp_error = true ) {
	return \Ever_Accounting\Categories::insert_category( $data = array() );
}

function eaccounting_delete_category( $category_id ) {
	return \Ever_Accounting\Categories::delete_category( $category_id );
}

function eaccounting_get_categories( $args = array() ) {
	return \Ever_Accounting\Categories::get_categories( $args = array(), true );
}

function eaccounting_get_transaction_types() {
	return \Ever_Accounting\Transactions::get_transaction_types();
}

function eaccounting_get_payment( $payment ) {
	return \Ever_Accounting\Transactions::get_payment( $payment );
}

function eaccounting_insert_payment( $args, $wp_error = true ) {
	return \Ever_Accounting\Transactions::insert_payment( $args );
}

function eaccounting_delete_payment( $payment_id ) {
	return \Ever_Accounting\Transactions::delete_revenue( $payment_id );
}

function eaccounting_get_payments( $args ) {
	return \Ever_Accounting\Transactions::get_payments( $args );
}

function eaccounting_get_revenue( $revenue ) {
	return \Ever_Accounting\Transactions::get_revenue( $revenue );
}

function eaccounting_insert_revenue( $args, $wp_error = true ) {
	return \Ever_Accounting\Transactions::insert_revenue( $args );
}

function eaccounting_delete_revenue( $revenue_id ) {
	return \Ever_Accounting\Transactions::delete_revenue( $revenue_id );
}

function eaccounting_get_revenues( $args ) {
	return \Ever_Accounting\Transactions::get_revenues( $args );
}

function eaccounting_get_transactions( $args ) {
	return \Ever_Accounting\Transactions::get_transactions( $args );
}

function eaccounting_get_total_income( $year = null ) {
	return \Ever_Accounting\Transactions::get_total_income( $year = null );
}

function eaccounting_get_total_expense( $year = null ) {
	return \Ever_Accounting\Transactions::get_total_expense( $year = null );
}

function eaccounting_get_total_profit( $year = null ) {
	return \Ever_Accounting\Transactions::get_total_profit( $year = null );
}

function eaccounting_get_total_receivable() {
	return \Ever_Accounting\Transactions::get_total_receivable();
}

function eaccounting_get_total_payable() {
	return \Ever_Accounting\Transactions::get_total_payable();
}

function eaccounting_get_total_upcoming_profit() {
	return \Ever_Accounting\Transactions::get_total_upcoming_profit();
}

function eaccounting_get_note( $item ) {
	return \Ever_Accounting\Notes::get_note( $item->get_id() );
}

function eaccounting_insert_note( $args, $wp_error = true ) {
	return \Ever_Accounting\Notes::insert_note( $args );
}

function eaccounting_delete_note( $note_id ) {
	return \Ever_Accounting\Notes::delete_note( $note_id );
}

function eaccounting_get_notes( $args = array() ) {
	return \Ever_Accounting\Notes::get_notes( $args = array(), false );
}

function eaccounting_get_currency_codes() {
	return \Ever_Accounting\Currencies::get_currency_codes();
}

function eaccounting_sanitize_currency_code( $code ) {
	return \Ever_Accounting\Currencies::sanitize_currency_code( $code );
}

function eaccounting_get_currency( $currency ) {
	return \Ever_Accounting\Currencies::get_currency_by_code( $currency );
}

function eaccounting_get_currency_rate( $currency ) {
	return \Ever_Accounting\Currencies::get_currency_rate( $currency );
}

function eaccounting_insert_currency( $args, $wp_error = true ) {
	return \Ever_Accounting\Currencies::insert_currency( $args );
}

function eaccounting_get_currencies( $args = array() ) {
	return \Ever_Accounting\Currencies::get_currencies( $args, false );
}

function eaccounting_get_invoice( $invoice ) {
	return \Ever_Accounting\Documents::get_invoice( $invoice );
}

function eaccounting_insert_invoice( $args, $wp_error = true ) {
	return \Ever_Accounting\Documents::insert_invoice( $args );
}

function eaccounting_delete_invoice( $invoice_id ) {
	return \Ever_Accounting\Documents::delete_invoice( $invoice_id );
}

function eaccounting_get_invoices( $args = array() ) {
	return \Ever_Accounting\Documents::get_invoices( $args, false );
}

function eaccounting_get_bill( $bill ) {
	return \Ever_Accounting\Documents::get_bill( $bill );
}

function eaccounting_insert_bill( $args, $wp_error = true ) {
	return \Ever_Accounting\Documents::insert_bill( $args );
}

function eaccounting_delete_bill( $bill_id ) {
	return \Ever_Accounting\Documents::delete_bill( $bill_id );
}

function eaccounting_get_bills( $args = array() ) {
	return \Ever_Accounting\Documents::get_bills( $args = array(),false );
}

function eaccounting_get_documents( $args = array() ) {
	return \Ever_Accounting\Documents::get_documents( $args = array(), false );
}

function eaccounting_get_bill_statuses() {
	return \Ever_Accounting\Documents::get_bill_statuses();
}

function eaccounting_get_invoice_statuses() {
	return \Ever_Accounting\Documents::get_invoice_statuses();
}
