<?php
/**
 * EverAccounting account Functions.
 *
 * All account related function of the plugin.
 *
 * @since 1.0.2
 * @package EverAccounting
 */

use EverAccounting\Exception;
use EverAccounting\Account;

/**
 * Main function for returning account.
 *
 * @param $account
 *
 * @since 1.0.2
 *
 * @return Account|null
 */
function eaccounting_get_account( $account ) {
	if ( empty( $account ) ) {
		return null;
	}

	try {
		if ( $account instanceof Account ) {
			$_account = $account;
		} elseif ( is_object( $account ) && ! empty( $account->id ) ) {
			$_account = new Account( null );
			$_account->populate( $account );
		} else {
			$_account = new Account( absint( $account ) );
		}

		if ( ! $_account->exists() ) {
			throw new Exception( 'invalid_id', __( 'Invalid account.', 'wp-ever-accounting' ) );
		}

		return $_account;
	} catch ( Exception $exception ) {
		return null;
	}
}

/**
 *  Create new account programmatically.
 *
 *  Returns a new account object on success.
 *
 * @param array $args Account arguments.
 *
 * @since 1.0.2
 *
 * @return Account|WP_Error
 */
function eaccounting_insert_account( $args ) {
	try {
		$default_args = array(
			'id' => null,
		);
		$args         = (array) wp_parse_args( $args, $default_args );
		$account      = new Account( $args['id'] );
		$account->set_props( $args );

		//validation
		if ( ! $account->get_date_created() ) {
			$account->set_date_created( time() );
		}
		if ( ! $account->get_company_id() ) {
			$account->set_company_id( 1 );
		}
		if ( ! $account->get_creator_id() ) {
			$account->set_creator_id();
		}

		if ( empty( $account->get_name() ) ) {
			throw new Exception( 'empty_props', __( 'Account Name is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $account->get_number( 'edit' ) ) ) {
			throw new Exception( 'empty_props', __( 'Account Number is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $account->get_currency_code( 'edit' ) ) ) {
			throw new Exception( 'empty_props', __( 'Currency code is required', 'wp-ever-accounting' ) );
		}

		$currency = eaccounting_get_currency( $account->get_currency_code() );
		if ( ! $currency || ! $currency->exists() ) {
			throw new Exception( 'invalid_props', __( 'Currency with provided code does not not exist.', 'wp-ever-accounting' ) );
		}

		$existing_id = \EverAccounting\Query_Account::init()
		                                            ->where( 'number', $account->get_number() )
		                                            ->where( 'company_id', $account->get_company_id() )
		                                            ->value( 0 );

		if ( ! empty( $existing_id ) && absint( $existing_id ) != $account->get_id() ) {
			throw new Exception( 'duplicate_props', __( 'Duplicate account number.', 'wp-ever-accounting' ) );
		}

		$account->save();

	} catch ( Exception $e ) {
		return new WP_Error( $e->getErrorCode(), $e->getMessage() );
	}

	return $account;
}

/**
 * Delete an account.
 *
 * @param $account_id
 *
 * @since 1.0.2
 *
 * @return bool
 */
function eaccounting_delete_account( $account_id ) {
	try {
		$account = new Account( $account_id );
		if ( ! $account->exists() ) {
			throw new Exception( 'invalid_id', __( 'Invalid account.', 'wp-ever-accounting' ) );
		}

		$account->delete();

		return empty( $account->get_id() );

	} catch ( Exception $exception ) {
		return false;
	}
}

/**
 * Delete default account from settings
 *
 * @param int $id ID of the default account.
 *
 * @since 1.0.2
 */
function eaccounting_delete_default_account( $id ) {
	$default_account = eaccounting()->settings->get( 'default_account' );
	if ( $default_account == $id ) {
		eaccounting()->settings->set( array( [ 'default_account' => '' ] ), true );
	}
}

add_action( 'eaccounting_delete_account', 'eaccounting_delete_default_account' );
