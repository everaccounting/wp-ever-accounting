<?php
/**
 * EverAccounting account Functions.
 *
 * All account related function of the plugin.
 *
 * @since   1.1.0
 * @package EverAccounting
 */
defined( 'ABSPATH' ) || exit;

/**
 * Main function for returning account.
 *
 * @param $account
 *
 * @return EverAccounting\Models\Account|null
 * @since 1.1.0
 *
 */
function eaccounting_get_account( $account ) {
	if ( empty( $account ) ) {
		return null;
	}
	$result = new EverAccounting\Models\Account( $account );

	return $result->exists() ? $result : null;
}

/**
 *  Create new account programmatically.
 *
 *  Returns a new account object on success.
 *
 * @param array $args {
 *  An array of elements that make up an account to update or insert.
 *
 *  @type int $id The account ID. If equal to something other than 0,
 *                                         the post with that ID will be updated. Default 0.
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
 * @type int $creator_id The creator id for the account. Default is current user id of the wordpress.
 *
 * @type string $date_created The date when the account is created. Default is current current time.
 *
 *
 * }
 *
 * @return EverAccounting\Models\Account|\WP_Error
 * @since 1.1.0
 *
 */
function eaccounting_insert_account( $args ) {
	$account = new EverAccounting\Models\Account( $args );

	return $account->save();
}

/**
 * Delete an account.
 *
 * @param $account_id
 *
 * @return bool
 * @since 1.1.0
 *
 */
function eaccounting_delete_account( $account_id ) {
	$account = new EverAccounting\Models\Account( $account_id );
	if ( ! $account->exists() ) {
		return false;
	}

	return \EverAccounting\Repositories\Accounts::instance()->delete( $account->get_id() );
}

/**
 * Get account items.
 *
 * @param array $args {
 *  Optional. Arguments to retrieve accounts.
 *
 *  @type int $id The account ID. If equal to something other than 0,
 *                                         the post with that ID will be updated. Default 0.
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
 * @type int $creator_id The creator id for the account. Default is current user id of the wordpress.
 *
 * @type string $date_created The date when the account is created. Default is current current time.
 *
 *
 * }
 *
 * @param bool $callback
 *
 * @return array|int
 * @since 1.1.0
 *
 */
function eaccounting_get_accounts( $args = array(), $callback = true ) {
	return \EverAccounting\Repositories\Accounts::instance()->get_items(
		$args,
		function ( $item ) use ( $callback ) {
			if ( $callback ) {
				$account = new \EverAccounting\Models\Account();
				$account->set_props( $item );
				$account->set_object_read( true );

				return $account;
			}

			return $item;
		}
	);
}
