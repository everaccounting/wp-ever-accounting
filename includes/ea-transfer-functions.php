<?php
/**
 * EverAccounting Transfer functions.
 *
 * Functions related to the transfer transactions of the plugin.
 *
 * @since   1.0.2
 * @package EverAccounting
 */

use \EverAccounting\Transfer;
use \EverAccounting\Query_Category;
use \EverAccounting\Exception;

/**
 * Main function for returning transfer.
 *
 * @since 1.0.2
 *
 * @param int|object|array $transfer
 *
 * @return Transfer|null
 */
function eaccounting_get_transfer( $transfer ) {
	if ( empty( $transfer ) ) {
		return null;
	}

	try {
		if ( $transfer instanceof Transfer ) {
			$_transfer = $transfer;
		} elseif ( is_object( $transfer ) && ! empty( $transfer->id ) ) {
			$_transfer = new Transfer( null );
			$_transfer->populate( $transfer );
		} else {
			$_transfer = new Transfer( absint( $transfer ) );
		}

		if ( ! $_transfer->exists() ) {
			throw new Exception( 'invalid_id', __( 'Invalid transfer.', 'wp-ever-accounting' ) );
		}

		return $_transfer;
	} catch ( Exception $exception ) {
		return null;
	}
}

/**
 * Insert transfer.
 *
 * @since    1.0.2
 *
 * @param array $args            {
 *
 * @type int    $id              ID of the transfer. If equal to something other than 0,
 *                               the post with that ID will be updated. Default 0.
 * @type int    $from_account_id ID of the source account from where transfer is initiating.
 *                               default null.
 * @type int    $to_account_id   ID of the target account where the transferred amount will be
 *                               deposited. default null.
 * @type string $amount          Amount of the money that will be transferred. default 0.
 * @type string $date            Date of the transfer. default null.
 * @type string $payment_method  Payment method used in transfer. default null.
 * @type string $reference
 * @type string $description
 *
 * }
 *
 * @return \EverAccounting\Transfer|\WP_Error
 */
function eaccounting_insert_transfer( $args ) {
	try {
		$default_args = array(
			'id' => null,
		);
		$args         = (array) wp_parse_args( $args, $default_args );
		$transfer     = new Transfer( $args['id'] );
		$transfer->set_props( $args );

		//validation
		if ( ! $transfer->get_date_created() ) {
			$transfer->set_date_created( time() );
		}
		if ( ! $transfer->get_company_id() ) {
			$transfer->set_company_id();
		}
		if ( ! $transfer->get_creator_id() ) {
			$transfer->set_creator_id();
		}
		if ( empty( $transfer->get_from_account_id() ) ) {
			throw new Exception( 'empty_prop', __( 'From account is required', 'wp-ever-accounting' ) );
		}
		if ( empty( $transfer->get_to_account_id() ) ) {
			throw new Exception( 'empty_prop', __( 'To account is required', 'wp-ever-accounting' ) );
		}
		if ( $transfer->get_from_account_id() == $transfer->get_to_account_id() ) {
			throw new Exception( 'empty_prop', __( 'Source & Target account can not be same.', 'wp-ever-accounting' ) );
		}
		if ( empty( eaccounting_sanitize_number( $transfer->get_amount(), false ) ) ) {
			throw new Exception( 'empty_prop', __( 'Transfer amount is required', 'wp-ever-accounting' ) );
		}
		if ( empty( $transfer->get_date() ) ) {
			throw new Exception( 'empty_prop', __( 'Transfer date is required', 'wp-ever-accounting' ) );
		}
		if ( empty( $transfer->get_payment_method() ) ) {
			throw new Exception( 'empty_prop', __( 'Payment method is required', 'wp-ever-accounting' ) );
		}

		$from_account = eaccounting_get_account( $transfer->get_from_account_id() );
		if ( ! $from_account->exists() ) {
			throw new Exception( 'empty_prop', __( 'Could not find from account.', 'wp-ever-accounting' ) );
		}
		$to_account = eaccounting_get_account( $transfer->get_to_account_id() );
		if ( ! $to_account->exists() ) {
			throw new Exception( 'empty_prop', __( 'Could not find to account.', 'wp-ever-accounting' ) );
		}

		$category_id = Query_Category::init()
		                             ->select( 'id' )
		                             ->where( 'name', __( 'Transfer', 'wp-ever-accounting' ) )
		                             ->where( 'type', 'other' )
		                             ->value( 0 );

		if ( empty( $category_id ) ) {
			$category = eaccounting_insert_category( array(
				'name' => __( 'Transfer', 'wp-ever-accounting' ),
				'type' => 'other'
			) );

			$category_id = $category && $category->exists() ? $category->get_id() : null;
		}

		if ( empty( $category_id ) ) {
			throw new Exception( 'empty_prop', __( 'Could not find transfer category.', 'wp-ever-accounting' ) );
		}

		$expense_currency = eaccounting_get_currency( $from_account->get_currency_code() );
		$income_currency  = eaccounting_get_currency( $to_account->get_currency_code() );
		$expense_id       = $transfer->get_expense_id();
		$income_id        = $transfer->get_income_id();
		$amount           = eaccounting_sanitize_price( $transfer->get_amount(), $from_account->get_currency_code() );
		$expense          = eaccounting_insert_transaction( array(
			'id'             => $expense_id,
			'account_id'     => $from_account->get_id(),
			'paid_at'        => $transfer->get_date()->date_i18n(),
			'amount'         => $amount,
			'vendor_id'      => 0,
			'description'    => $transfer->get_description(),
			'category_id'    => $category_id,
			'payment_method' => $transfer->get_payment_method(),
			'reference'      => $transfer->get_reference(),
			'type'           => 'expense'
		) );
		if ( is_wp_error( $expense ) ) {
			throw new Exception( $expense->get_error_code(), $expense->get_error_message() );
		}

		$transfer->set_expense_id( $expense->get_id() );

		if ( $from_account->get_currency_code() != $to_account->get_currency_code() ) {
			$amount = eaccounting_price_convert_to_default( $amount, $from_account->get_currency_code(), $expense_currency->get_rate() );
			$amount = eaccounting_price_convert_from_default( $amount, $to_account->get_currency_code(), $income_currency->get_rate() );
		}

		$income = eaccounting_insert_transaction( array(
			'id'             => $income_id,
			'account_id'     => $to_account->get_id(),
			'paid_at'        => $transfer->get_date()->date_i18n(),
			'amount'         => $amount,
			'vendor_id'      => 0,
			'description'    => $transfer->get_description(),
			'category_id'    => $category_id,
			'payment_method' => $transfer->get_payment_method(),
			'reference'      => $transfer->get_reference(),
			'type'           => 'income'
		) );
		if ( is_wp_error( $income ) ) {
			eaccounting_delete_transaction( $expense->get_id() );
			throw new Exception( $income->get_error_code(), $income->get_error_message() );
		}
		$transfer->set_income_id( $income->get_id() );

		$transfer->save();

	} catch ( Exception $e ) {
		return new WP_Error( $e->getErrorCode(), $e->getMessage() );
	}

	return $transfer;
}


/**
 * Delete an transfer.
 *
 * @since 1.0.2
 *
 * @param $transfer_id
 *
 * @return bool
 */
function eaccounting_delete_transfer( $transfer_id ) {
	try {
		$transfer = new Transfer( $transfer_id );
		if ( ! $transfer->exists() ) {
			throw new Exception( 'invalid_id', __( 'Invalid transfer.', 'wp-ever-accounting' ) );
		}

		$transfer->delete();

		return empty( $transfer->get_id() );

	} catch ( Exception $exception ) {
		return false;
	}
}
