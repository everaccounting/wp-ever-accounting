<?php
/**
 * Deprecated functions
 *
 * Where functions come to die.
 * @version  1.1.0
 */

use Ever_Accounting\Account;
use Ever_Accounting\Currency;
use Ever_Accounting\Category;
use Ever_Accounting\Item;

defined( 'ABSPATH' ) || exit;


/**
 * Create new currency programmatically.
 *
 * @param array $args
 *
 * @since 1.1.0
 * @return Currency|\WP_Error
 * @deprecated 1.1.4
 */
function eaccounting_insert_currency( $args, $wp_error = true ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Currencies::insert()' );

	return \Ever_Accounting\Currencies::insert( $args );
}

/**
 * Delete a currency.
 *
 * @param $currency_code
 *
 * @return bool
 * @since 1.1.0
 * @deprecated 1.1.4
 */
function eaccounting_delete_currency( $currency_code ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Currencies::delete()' );

	return \Ever_Accounting\Currencies::delete( $currency_code );
}

/**
 * Create new category programmatically.
 *
 * @param array $args
 *
 * @since 1.1.0
 * @return Category|\WP_Error
 * @deprecated 1.1.4
 */
function eaccounting_insert_category( $args, $wp_error = true ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Categories::insert()' );

	return \Ever_Accounting\Categories::insert( $args );
}

/**
 * Delete a category.
 *
 * @param $category_id
 *
 * @return bool
 * @since 1.1.0
 * @deprecated 1.1.4
 */
function eaccounting_delete_category( $category_id ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Categories::delete()' );

	return \Ever_Accounting\Categories::delete( $category_id );
}

/**
 * Get category items.
 *
 * @param array $args
 *
 * @return int|array|null
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_categories( $args = array() ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Categories::query()' );

	return \Ever_Accounting\Categories::query( $args );
}

/**
 * Get all the available type of category the plugin support.
 *
 * @return array
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_category_types() {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Categories::get_types()' );

	return \Ever_Accounting\Categories::get_types();
}

/**
 * Get the category type label of a specific type.
 *
 * @param $type
 *
 * @return string
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_category_type( $type ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Categories::get_type()' );

	return \Ever_Accounting\Categories::get_type( $type );
}

/**
 * Main function for returning item.
 *
 * @param $item
 *
 * @return Item|null
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_item( $item ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Items::get()' );

	return \Ever_Accounting\Items::get( $item );
}

/**
 * Get item by sku.
 *
 * @param $sku
 *
 * @return Item
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_item_by_sku( $sku ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Items::get_by_sku()' );

	return \Ever_Accounting\Items::get_by_sku( $sku );
}

/**
 *  Create new item programmatically.
 *
 * @param array $args Arguments.
 * @param \WP_Error $wp_error Either true or false
 *
 * @return Item|\WP_Error
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_insert_item( $args, $wp_error = true ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Items::insert()' );

	return \Ever_Accounting\Items::insert( $args );
}

/**
 * Delete an item.
 *
 * @param $item_id
 *
 * @return bool
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_delete_item( $item_id ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Items::delete()' );

	return \Ever_Accounting\Items::delete( $item_id );
}

/**
 * Get items.
 *
 * @param array $args
 *
 * @return array|int
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_items( $args = array() ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Items::query()' );

	return \Ever_Accounting\Items::query( $args );
}

/**
 * Main function for returning account.
 *
 * @param array|int $account Account array or id.
 *
 * @return Account|null
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_account( $account ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Accounts::get()' );

	return \Ever_Accounting\Accounts::get( $account );
}

/**
 * Get account currency code
 *
 * @param Account $account account object.
 *
 * @return mixed|null
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_account_currency_code( $account ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Accounts::get_currency_code()' );

	return \Ever_Accounting\Accounts::get_currency_code( $account );
}

/**
 * Create new account programmatically.
 *
 * @param array $args
 *
 * @return Account| \WP_Error
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_insert_account( $data, $wp_error = true ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Accounts::insert()' );

	return \Ever_Accounting\Accounts::insert( $data );
}

/**
 * Delete an account.
 *
 * @param $account_id
 *
 * @return bool
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_delete_account( $account_id ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Accounts::delete()' );

	return \Ever_Accounting\Accounts::delete( $account_id );
}

/**
 * Get account items.
 *
 * @param array $args
 *
 * @return array|int
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_accounts( $args = array() ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Accounts::query()' );

	return \Ever_Accounting\Accounts::query( $args );
}

/**
 * Get contact types.
 *
 * @return array
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_contact_types() {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Contacts::get_types()' );

	return \Ever_Accounting\Contacts::get_types();
}

/**
 * Get the contact type label of a specific type.
 *
 * @param $type
 *
 * @return string
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_contact_type( $type ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Contacts::get_type()' );

	return \Ever_Accounting\Contacts::get_type( $type );
}

/**
 * Get customer.
 *
 * @param $customer
 *
 * @return \Ever_Accounting\Customer|null
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_customer( $customer ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Contacts::get_customer()' );

	return \Ever_Accounting\Contacts::get_customer( $customer );
}

/**
 * Get customer by email.
 *
 * @param $email
 *
 * @return \Ever_Accounting\Customer
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_customer_by_email( $email ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Contacts::get_customer_by_email()' );

	return \Ever_Accounting\Contacts::get_customer_by_email( $email );
}

/**
 *  Create new customer programmatically.
 *
 *  Returns a new customer object on success.
 *
 * @param array $args
 * @param bool $wp_error
 *
 * @return \Ever_Accounting\Customer|\WP_Error|bool
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_insert_customer( $args, $wp_error = true ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Contacts::insert_customer()' );

	return \Ever_Accounting\Contacts::insert_customer( $args );
}

/**
 * Delete a customer.
 *
 * @param $customer_id
 *
 * @return bool
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_delete_customer( $customer_id ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Contacts::delete_customer()' );

	return \Ever_Accounting\Contacts::delete_customer( $customer_id );
}

/**
 * Get customers items.
 *
 * @param array $args
 *
 * @return array|int
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_customers( $args = array() ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Contacts::query_customers()' );

	return \Ever_Accounting\Contacts::query_customers( $args, false );
}

/**
 * Get vendor.
 *
 * @param $vendor
 *
 * @return \Ever_Accounting\Vendor|null
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_vendor( $vendor ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Contacts::get_vendor()' );

	return \Ever_Accounting\Contacts::get_vendor( $vendor );
}

/**
 * Get vendor by email.
 *
 * @param $email
 *
 * @return \Ever_Accounting\Vendor
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_vendor_by_email( $email ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Contacts::get_vendor_by_email()' );

	return \Ever_Accounting\Contacts::get_vendor_by_email( $email );
}

/**
 *  Create new vendor programmatically.
 *
 *  Returns a new vendor object on success.
 *
 * @param array $args
 * @param bool $wp_error
 *
 * @return Ever_Accounting\Vendor|\WP_Error|bool
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_insert_vendor( $args, $wp_error = true ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Contacts::insert_vendor()' );

	return \Ever_Accounting\Contacts::insert_vendor( $args );
}

/**
 * Delete a vendor.
 *
 * @param $vendor_id
 *
 * @return bool
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_delete_vendor( $vendor_id ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Contacts::delete_vendor()' );

	return \Ever_Accounting\Contacts::delete_vendor( $vendor_id );
}

/**
 * Get vendors items.
 *
 * @param array $args
 *
 * @return array|int
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_vendors( $args = array() ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Contacts::query_vendors()' );

	return \Ever_Accounting\Contacts::query_vendors( $args, false );
}

/**
 * Get contact items.
 *
 * @param array $args
 *
 * @return array|int
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_contacts( $args = array() ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Contacts::query()' );

	return \Ever_Accounting\Contacts::query( $args, false );
}

/**
 * Get Transaction Types
 *
 * @return array
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_transaction_types() {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Transactions::get_types()' );

	return \Ever_Accounting\Transactions::get_types();
}

/**
 * Get a single payment.
 *
 * @param $payment
 *
 * @return \Ever_Accounting\Payment|null
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_payment( $payment ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Transactions::get_payment()' );

	return \Ever_Accounting\Transactions::get_payment( $payment );
}

/**
 *  Create new payment programmatically.
 *
 *  Returns a new payment object on success.
 *
 * @param array $args
 * @param bool $wp_error
 *
 * @return Ever_Accounting\Payment|\WP_Error|bool
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_insert_payment( $args, $wp_error = true ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Transactions::insert_payment()' );

	return \Ever_Accounting\Transactions::insert_payment( $args );
}

/**
 * Delete a payment.
 *
 * @param $payment_id
 *
 * @return bool
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_delete_payment( $payment_id ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Transactions::delete_payment()' );

	return \Ever_Accounting\Transactions::delete_payment( $payment_id );
}

/**
 * Get payment items.
 *
 * @param array $args
 *
 * @return array|int
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_payments( $args = array() ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Transactions::query_payments()' );

	return \Ever_Accounting\Transactions::query_payments( $args, false );
}

/**
 * Get revenue.
 *
 * @param $revenue
 *
 * @return \Ever_Accounting\Revenue|null
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_revenue( $revenue ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Transactions::get_revenue()' );

	return \Ever_Accounting\Transactions::get_revenue( $revenue );
}

/**
 *  Create new revenue programmatically.
 *
 *  Returns a new revenue object on success.
 *
 * @param array $args
 * @param bool $wp_error
 *
 * @return Ever_Accounting\Revenue|\WP_Error|bool
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_insert_revenue( $args, $wp_error = true ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Transactions::insert_revenue()' );

	return \Ever_Accounting\Transactions::insert_revenue( $args );
}

/**
 * Delete a revenue.
 *
 * @param $revenue_id
 *
 * @return bool
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_delete_revenue( $revenue_id ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Transactions::delete_revenue()' );

	return \Ever_Accounting\Transactions::delete_revenue( $revenue_id );
}

/**
 * Get revenues items.
 *
 * @param array $args
 *
 * @return array|int
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_revenues( $args = array() ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Transactions::query_revenues()' );

	return \Ever_Accounting\Transactions::query_revenues( $args, false );
}

/**
 * Get transfer.
 *
 * @param $transfer
 *
 * @return \Ever_Accounting\Transfer|null
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_transfer( $transfer ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Transfers::get()' );

	return \Ever_Accounting\Transfers::get( $transfer );
}

/**
 * Create new transfer programmatically.
 *
 * Returns a new transfer object on success.
 *
 * @param array $args
 * @param bool $wp_error
 *
 * @return \Ever_Accounting\Transfer|\WP_Error|bool
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_insert_transfer( $args, $wp_error = true ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Transfers::insert()' );

	return \Ever_Accounting\Transfers::insert( $args );
}

/**
 * Delete a transfer.
 *
 * @param $transfer_id
 *
 * @return bool
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_delete_transfer( $transfer_id ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Transfers::delete()' );

	return \Ever_Accounting\Transfers::delete( $transfer_id );
}

/**
 * Get transfers.
 *
 * @param array $args
 *
 * @return array|int
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_transfers( $args = array() ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Transfers::query()' );

	return \Ever_Accounting\Transfers::query( $args, false );
}

/**
 * Get transaction items.
 *
 * @param array $args
 *
 * @return array|\Ever_Accounting\Payment|\Ever_Accounting\Revenue|int
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_transactions( $args = array() ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Transactions::query()' );

	return \Ever_Accounting\Transactions::query( $args, false );
}

/**
 * Get total income.
 *
 * @param null $year
 *
 * @return float
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_total_income( $year = null ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Transactions::get_total_income()' );

	return \Ever_Accounting\Transactions::get_total_income( $year );
}

/**
 * Get total expense.
 *
 * @param null $year
 *
 * @return float
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_total_expense( $year = null ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Transactions::get_total_expense()' );

	return \Ever_Accounting\Transactions::get_total_expense( $year );
}

/**
 * Get total profit.
 *
 * @param null $year
 *
 * @return float
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_total_profit( $year = null ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Transactions::get_total_profit()' );

	return \Ever_Accounting\Transactions::get_total_profit( $year );
}

/**
 * Get total receivable.
 *
 * @return false|float|int|mixed|string
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_total_receivable() {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Transactions::get_total_receivable()' );

	return \Ever_Accounting\Transactions::get_total_receivable();
}

/**
 * Get total payable.
 *
 * @return float
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_total_payable() {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Transactions::get_total_payable()' );

	return \Ever_Accounting\Transactions::get_total_payable();
}

/**
 * Get total upcoming profit
 *
 * @return float
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_total_upcoming_profit() {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Transactions::get_total_upcoming_profit()' );

	return \Ever_Accounting\Transactions::get_total_upcoming_profit();
}

/**
 * Main function for returning note.
 *
 * @param $item
 *
 * @return \Ever_Accounting\Note|null
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_note( $item ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Notes::get()' );

	return \Ever_Accounting\Notes::get( $item );
}

/**
 * Insert note.
 *
 * @param      $args
 * @param bool $wp_error
 * @since 1.1.0
 * @deprecatd 1.1.4
 * @return \Ever_Accounting\Note|false|int|WP_Error
 */
function eaccounting_insert_note( $args, $wp_error = true ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Notes::insert()' );

	return \Ever_Accounting\Notes::insert( $args );
}

/**
 * Delete an item.
 *
 * @param $note_id
 *
 * @return bool
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_delete_note( $note_id ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Notes::delete()' );

	return \Ever_Accounting\Notes::delete( $note_id );
}

/**
 * @param array $args
 *
 * @return array|void
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_notes( $args = array() ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Notes::query()' );

	return \Ever_Accounting\Notes::query( $args );
}

/**
 * Get import export headers.
 *
 * @param $type
 *
 * @return mixed|void
 * @since 1.0.2
 * @deprecatd 1.1.4
 */
function eaccounting_get_io_headers( $type ) {
	_deprecated_function( __FUNCTION__, '1.1.4', 'ever_accounting_get_io_headers()' );

	return ever_accounting_get_io_headers( $type );
}

/**
 * Main function for returning currency.
 *
 * This function is little different from rest
 * Even if the currency in the database doest not
 * exist it will it populate with default data.
 *
 * Whenever need to check existence of the object
 * in database must check $currency->exist()
 *
 * @param object|string|int $currency
 *
 * @return Ever_Accounting\Currency|null
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_currency( $currency ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Currencies::get()' );

	return \Ever_Accounting\Currencies::get( $currency );
}

/**
 * Return all available currency codes.
 *
 * @return array
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_currency_codes() {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Currencies::get_codes()' );

	return \Ever_Accounting\Currencies::get_codes();
}

/**
 * Check if currency code is a valid one.
 *
 * @param string $code Currency code.
 *
 * @return string
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_sanitize_currency_code( $code ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Currencies::sanitize_code()' );

	return \Ever_Accounting\Currencies::sanitize_code( $code );
}

/**
 * @param $currency
 *
 * @return mixed|null
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_currency_rate( $currency ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Currencies::get_rate()' );

	return \Ever_Accounting\Currencies::get_rate( $currency );
}

/**
 * Get currency items.
 *
 * @param array $args
 *
 * @return array|int|null
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_currencies( $args = array() ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Currencies::query()' );

	return \Ever_Accounting\Currencies::query( $args, false );
}
