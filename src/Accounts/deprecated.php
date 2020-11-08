<?php
defined( 'ABSPATH' ) || exit;

/**
 * Main function for returning account.
 *
 * @since 1.0.2
 *
 * @param $account
 *
 * @return EverAccounting\Accounts\Account|null
 */
function eaccounting_get_account( $account ) {
	return \EverAccounting\Accounts\get( $account );
}

/**
 *  Create new account programmatically.
 *
 *  Returns a new account object on success.
 *
 * @since 1.0.2
 *
 * @param array $args Account arguments.
 *
 * @return EverAccounting\Accounts\Account|WP_Error
 */
function eaccounting_insert_account( $args ) {
	return \EverAccounting\Accounts\insert( $args );
}

/**
 * Delete an account.
 *
 * @since 1.0.2
 *
 * @param $account_id
 *
 * @return void
 */
function eaccounting_delete_account( $account_id ) {
	\EverAccounting\Accounts\delete( $account_id );
}
