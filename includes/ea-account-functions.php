<?php

/**
 * Main function for returning account.
 *
 * @param $account
 *
 * @return EAccounting_Account|false
 * @since 1.0.0
 *
 */
function eaccounting_get_account( $account ) {
	if ( empty( $account ) ) {
		return false;
	}

	try {
		if ( $account instanceof EAccounting_Account ) {
			$_account = $account;
		} elseif ( is_object( $account ) && !empty($account->id)) {
			$_account = new EAccounting_Account( null );
			$_account->populate( $account );
		} else {
			$_account = new EAccounting_Account( absint( $account ) );
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
 * @return EAccounting_Account|WP_Error
 * @since 1.0.0
 *
 */
function eaccounting_insert_account( $args ) {

	try {
		$default_args = array(
			'id' => null,
		);
		$args         = (array) wp_parse_args( $args, $default_args );
		$account      = new EAccounting_Account( $args['id'] );
		$account->set_props( $args );
		$account->save();

	} catch ( Exception $e ) {
		return new WP_Error( 'error', $e->getMessage() );
	}

	return $account;
}

/**
 * Delete an account.
 *
 * @param $account_id
 *
 * @return bool
 * @since 1.0.0
 *
 */
function eaccounting_delete_account( $account_id ) {
	try {
		$account = new EAccounting_Account( $account_id );
		if ( ! $account->exists() ) {
			throw new Exception( __( 'Invalid account.', 'wp-ever-accounting' ) );
		}

		$account->delete();

		return empty( $account->get_id() );

	} catch ( Exception $exception ) {
		return false;
	}
}
