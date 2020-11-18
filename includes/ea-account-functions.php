<?php
/**
 * EverAccounting account Functions.
 *
 * All account related function of the plugin.
 *
 * @since   1.0.2
 * @package EverAccounting
 */
defined( 'ABSPATH' ) || exit;

/**
 * Main function for returning account.
 *
 * @since 1.0.2
 *
 * @param $account
 *
 * @return EverAccounting\Models\Account|null
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
 * @since 1.0.2
 *
 * @param array $args Account arguments.
 *
 * @return EverAccounting\Models\Account|\WP_Error
 */
function eaccounting_insert_account( $args ) {
	$account = new EverAccounting\Models\Account( $args );

	return $account->save();
}

/**
 * Delete an account.
 *
 * @since 1.0.2
 *
 * @param $account_id
 *
 * @return bool
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
 * @since 1.1.0
 *
 * @param array  $args
 *
 * @param string $callback
 *
 * @return array|int
 */
function eaccounting_get_accounts( $args = array(), $callback = 'eaccounting_get_account' ) {
	return \EverAccounting\Repositories\Accounts::instance()->get_items( $args, $callback );
}
