<?php

/**
 * Main function for returning account.
 *
 * @param $account_id
 *
 * @return EAccounting_Account|WP_Error
 * @since 1.0.0
 *
 */
function eaccounting_get_account( $account_id ) {
	try {
		$account = new EAccounting_Account( $account_id );
		if ( ! $account->exists() ) {
			throw new Exception( __( 'Invalid account.', 'wp-ever-accounting' ) );
		}

		return $account;
	} catch ( Exception $exception ) {
		return new WP_Error( 'error', $exception->getMessage() );
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
	$default_args = array(
		'id'              => null,
		'name'            => '',
		'number'          => '',
		'opening_balance' => 0.0000,
		'currency_code'   => 'USD',
		'bank_name'       => null,
		'bank_phone'      => null,
		'bank_address'    => null,
		'company_id'      => null,
		'date_created'    => null,
	);

	try {
		$args    = wp_parse_args( $args, $default_args );
		$account = new EAccounting_Account( $args['id'] );
		if ( ! is_null( $args['name'] ) ) {
			$account->set_name( $args['name'] );
		}
		if ( ! is_null( $args['number'] ) ) {
			$account->set_number( $args['number'] );
		}
		$account->set_opening_balance( $args['opening_balance'] );
		$account->set_currency_code( $args['currency_code'] );
		$account->set_bank_name( $args['bank_name'] );
		$account->get_bank_phone( $args['bank_phone'] );
		$account->get_bank_address( $args['bank_address'] );
		$account->set_company_id( $args['company_id'] );

		if ( isset( $args['date_created'] ) ) {
			$account->set_date_created( $args['date_created'] );
		}

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
 * @since 1.0.0
 *
 * @return bool|WP_Error  true when delete or WP_Error when found an error.
 */
function eaccounting_delete_account( $account_id ) {
	try {
		$account = new EAccounting_Account( $account_id );
		if ( ! $account->exists() ) {
			throw new Exception( __( 'Invalid account.', 'wp-ever-accounting' ) );
		}

		$account->delete();
		return empty($account->get_id());

	} catch ( Exception $exception ) {
		return new WP_Error( 'error', $exception->getMessage() );
	}
}
