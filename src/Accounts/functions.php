<?php
/**
 * EverAccounting account Functions.
 *
 * All account related function of the plugin.
 *
 * @since   1.0.2
 * @package EverAccounting
 */

namespace EverAccounting\Accounts;

use EverAccounting\Exception;

defined( 'ABSPATH' ) || exit;

require_once dirname( __FILE__ ) . '/hooks.php';
require_once dirname( __FILE__ ) . '/deprecated.php';

/**
 * Main function for querying accounts.
 *
 * @since 1.1.0
 *
 * @param array $args
 *
 * @return Query
 */
function query( $args = array() ) {
	return new Query( $args );
}

/**
 * Main function for returning account.
 *
 * @since 1.0.2
 *
 * @param $account
 *
 * @return Account|null
 */
function get( $account ) {
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
 * @since 1.0.2
 *
 * @param array $args Account arguments.
 *
 * @return Account|\WP_Error
 */
function insert( $args ) {
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

		$existing_id = query()
			->where( 'number', $account->get_number() )
			->value( 0 );

		if ( ! empty( $existing_id ) && absint( $existing_id ) != $account->get_id() ) {
			throw new Exception( 'duplicate_props', __( 'Duplicate account number.', 'wp-ever-accounting' ) );
		}

		$account->save();

	} catch ( Exception $e ) {
		return new \WP_Error( $e->getErrorCode(), $e->getMessage() );
	}

	return $account;
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
function delete( $account_id ) {
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
