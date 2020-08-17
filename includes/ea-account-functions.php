<?php
/**
 * EverAccounting account Functions.
 *
 * All account related function of the plugin.
 *
 * @package EverAccounting
 * @since 1.0.2
 */

/**
 * Main function for returning account.
 *
 * @param $account
 *
 * @return \EverAccounting\Account|null
 * @since 1.0.2
 *
 */
function eaccounting_get_account( $account ) {
	if ( empty( $account ) ) {
		return null;
	}

	try {
		if ( $account instanceof \EverAccounting\Account ) {
			$_account = $account;
		} elseif ( is_object( $account ) && ! empty( $account->id ) ) {
			$_account = new \EverAccounting\Account( null );
			$_account->populate( $account );
		} else {
			$_account = new \EverAccounting\Account( absint( $account ) );
		}

		if ( ! $_account->exists() ) {
			throw new Exception( __( 'Invalid account.', 'wp-ever-accounting' ) );
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
 * @return \EverAccounting\Account|WP_Error
 * @since 1.0.2
 *
 */
function eaccounting_insert_account( $args ) {
	try {
		$default_args = array(
			'id' => null,
		);
		$args         = (array) wp_parse_args( $args, $default_args );
		$account      = new \EverAccounting\Account( $args['id'] );
		$account->set_props( $args );
		$account->save();

	} catch ( \EverAccounting\Exception $e ) {
		return new WP_Error( $e->getErrorCode(), $e->getMessage() );
	}

	return $account;
}

/**
 * Delete an account.
 *
 * @param $account_id
 *
 * @return bool
 * @since 1.0.2
 *
 */
function eaccounting_delete_account( $account_id ) {
	try {
		$account = new \EverAccounting\Account( $account_id );
		if ( ! $account->exists() ) {
			throw new Exception( __( 'Invalid account.', 'wp-ever-accounting' ) );
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
