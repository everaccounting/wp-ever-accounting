<?php
/**
 * EverAccounting account Functions.
 *
 * All account related function of the plugin.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

use EverAccounting\Models\Account;

defined( 'ABSPATH' ) || exit;

/**
 * Main function for returning account.
 *
 * @param mixed $account Account ID or object.
 *
 * @since 1.1.0
 *
 * @return EverAccounting\Models\Account|null
 */
function eaccounting_get_account( $account ) {
	return Account::get( $account );
}

/**
 * Get account currency code.
 *
 * @param mixed $account Account ID or object.
 *
 * @since 1.1.0
 *
 * @return mixed|null
 */
function eaccounting_get_account_currency_code( $account ) {
	$exist = eaccounting_get_account( $account );
	if ( $exist ) {
		return $exist->get_currency_code();
	}

	return null;
}

/**
 *  Create new account programmatically.
 *
 *  Returns a new account object on success.
 *
 * @param array $data {
 *                               An array of elements that make up an account to update or insert.
 *
 * @type int $id The account ID. If equal to something other than 0,
 *                                         the account with that id will be updated. Default 0.
 *
 * @type string $name The name of the account . Default empty.
 *
 * @type string $number The number of account. Default empty.
 *
 * @type string $currency_code The currency_code for the account.Default is empty.
 *
 * @type double $opening_balance The opening balance of the account. Default 0.0000.
 *
 * @type string $bank_name The bank name for the account. Default null.
 *
 * @type string $bank_phone The phone number of the bank on which the account is opened. Default null.
 *
 * @type string $bank_address The address of the bank. Default null.
 *
 * @type int $enabled The status of the account. Default 1.
 *
 * @type int $creator_id The creator id for the account. Default is current user id of the WordPress.
 *
 * @type string $date_created The date when the account is created. Default is current time.
 * }
 *
 * @param bool $wp_error Optional. Whether to return a WP_Error on failure. Default false.
 *
 * @throws \Exception When account is not created.
 * @since 1.1.0
 *
 * @return EverAccounting\Models\Account|\WP_Error|bool
 */
function eaccounting_insert_account( $data, $wp_error = true ) {
	return Account::insert( $data, $wp_error );
}

/**
 * Delete an account.
 *
 * @param int $account_id Account ID.
 *
 * @since 1.1.0
 *
 * @return bool
 */
function eaccounting_delete_account( $account_id ) {
	try {
		$account = new EverAccounting\Models\Account( $account_id );

		return $account->exists() ? $account->delete() : false;
	} catch ( \Exception $e ) {
		return false;
	}
}

/**
 * Get account items.
 *
 * @param array $args {
 *                               Optional. Arguments to retrieve accounts.
 *
 * @type string $name The name of the account .
 *
 * @type string $number The number of account.
 *
 * @type string $currency_code The currency_code for the account.
 *
 * @type double $opening_balance The opening balance of the account.
 *
 * @type string $bank_name The bank name for the account.
 *
 * @type string $bank_phone The phone number of the bank on which the account is opened.
 *
 * @type string $bank_address The address of the bank.
 *
 * @type int $enabled The status of the account.
 *
 * @type int $creator_id The creator id for the account.
 *
 * @type string $date_created The date when the account is created.
 *
 *
 * }
 *
 * @since 1.1.0
 *
 * @return array|int
 */
function eaccounting_get_accounts( $args = array() ) {
	return Account::query( $args );
}

