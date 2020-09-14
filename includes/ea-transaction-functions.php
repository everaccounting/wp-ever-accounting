<?php
/**
 * EverAccounting Transaction functions.
 *
 * Functions for all kind of transaction of the plugin.
 *
 * @since   1.0.2
 * @package EverAccounting
 */

use \EverAccounting\Transaction;
use \EverAccounting\Payment;
use \EverAccounting\Revenue;
use \EverAccounting\Exception;

defined( 'ABSPATH' ) || exit;


/**
 * Get Transaction Types
 *
 * @since 1.0.2
 * @return array
 */
function eaccounting_get_transaction_types() {
	$types = array(
		'income'  => __( 'Income', 'wp-ever-accounting' ),
		'expense' => __( 'Expense', 'wp-ever-accounting' )
	);

	return $types;
}


/**
 * Main function for returning transaction.
 *
 * @since 1.0.2
 *
 * @param $transaction
 *
 * @return Transaction|null
 */
function eaccounting_get_transaction( $transaction ) {
	if ( empty( $transaction ) ) {
		return null;
	}

	try {
		if ( $transaction instanceof Transaction ) {
			$_transaction = $transaction;
		} elseif ( is_object( $transaction ) && ! empty( $transaction->id ) ) {
			$_transaction = new Transaction( null );
			$_transaction->populate( $transaction );
		} else {
			$_transaction = new Transaction( absint( $transaction ) );
		}

		if ( ! $_transaction->exists() ) {
			throw new Exception( 'invalid_id', __( 'Invalid transaction.', 'wp-ever-accounting' ) );
		}

		return $_transaction;
	} catch ( Exception $exception ) {
		return null;
	}
}

/**
 *  Create new transaction programmatically.
 *
 *  Returns a new transaction object on success.
 *
 * @since 1.0.2
 *
 * @see   eaccounting_sanitize_price()
 * @see   eaccounting_get_account()
 * @see   eaccounting_get_currency()
 * @see   eaccounting_get_category()
 *
 * @see   eaccounting_get_contact()
 *
 * @param array $args           {
 *
 * @type int    $id             Transaction id. If the id is something other than 0 then it will update the transaction.
 * @type string $type           Type of the transaction eg income, expense etc.
 * @type string $paid_at        Time of the transaction.
 * @type string $amount         Transaction amount.
 * @type int    $account_id     From/To which account the transaction is.
 * @type int    $contact_id     Contact id related to the transaction.
 * @type int    $invoice_id     Transaction related invoice id(optional).
 * @type int    $category_id    Category of the transaction.
 * @type string $payment_method Payment method used for the transaction.
 * @type string $reference      Reference of the transaction.
 * @type string $description    Description of the transaction.
 *
 * }
 *
 * @return Transaction|WP_Error
 */
function eaccounting_insert_transaction( $args ) {

	try {
		$default_args = array(
			'id' => null,
		);
		$args         = (array) wp_parse_args( $args, $default_args );

		$transaction = new Transaction( $args['id'] );
		$transaction->set_props( $args );

		//validation
		if ( ! $transaction->get_date_created() ) {
			$transaction->set_date_created( time() );
		}
		if ( ! $transaction->get_creator_id() ) {
			$transaction->set_creator_id();
		}
		if ( empty( $transaction->get_paid_at() ) ) {
			throw new Exception( 'empty_prop', __( 'Transaction date is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $transaction->get_type() ) ) {
			throw new Exception( 'empty_prop', __( 'Transaction type is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $transaction->get_paid_at() ) ) {
			throw new Exception( 'empty_prop', __( 'Paid date is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $transaction->get_category_id() ) ) {
			throw new Exception( 'empty_prop', __( 'Category is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $transaction->get_payment_method() ) ) {
			throw new Exception( 'empty_prop', __( 'Payment method is required', 'wp-ever-accounting' ) );
		}

		$account = eaccounting_get_account( $transaction->get_account_id() );
		if ( ! $account || ! $account->exists() ) {
			throw new Exception( 'invalid_prop', __( 'Account is required.', 'wp-ever-accounting' ) );
		}

		$currency = eaccounting_get_currency( $account->get_currency_code( 'edit' ) );
		if ( ! $currency || ! $currency->exists() ) {
			throw new Exception( 'invalid_prop', __( 'Transaction associated account is not exist.', 'wp-ever-accounting' ) );
		}

		$category = eaccounting_get_category( $transaction->get_category_id() );
		if ( ! $category->exists() ) {
			throw new Exception( 'invalid_prop', __( 'Category does not exist.', 'wp-ever-accounting' ) );
		}

		if ( ! in_array( $category->get_type(), [ 'expense', 'other', 'income' ] ) ) {
			throw new Exception( 'invalid_prop', __( 'Invalid category type.', 'wp-ever-accounting' ) );
		}
		//if expense category type must be expense
		//if type other category type must be other
		//if type income category type must be income
		if ( $transaction->get_type() !== $category->get_type() && $category->get_type() !== 'other' ) {
			throw new Exception( 'invalid-category-type', __( 'Transaction type and category type does not match.', 'wp-ever-accounting' ) );
		}

		if ( ! empty( $transaction->get_contact_id() ) ) {
			$contact = eaccounting_get_contact( $transaction->get_contact_id() );
			if ( ! empty( $transaction->get_contact_id() ) && ! $contact->exists() ) {
				throw new Exception( 'invalid_prop', __( 'Contact does not exist.', 'wp-ever-accounting' ) );
			}
		}

		//May be changing the bank account so its need to update all the time.
		$transaction->set_currency_code( $account->get_currency_code() );
		$transaction->set_currency_rate( $currency->get_rate() );


		$transaction->set_amount( eaccounting_sanitize_price( $transaction->get_amount(), $currency->get_code() ) );


		$transaction->save();

	} catch ( Exception $e ) {
		return new WP_Error( $e->getErrorCode(), $e->getMessage() );
	}

	return $transaction;
}

/**
 * Delete an transaction.
 *
 * @since 1.0.2
 *
 * @param $transaction_id
 *
 * @return bool
 */
function eaccounting_delete_transaction( $transaction_id ) {
	try {
		$transaction = new Transaction( $transaction_id );
		if ( ! $transaction->exists() ) {
			throw new Exception( 'invalid_id', __( 'Invalid transaction.', 'wp-ever-accounting' ) );
		}

		$transaction->delete();

		return empty( $transaction->get_id() );

	} catch ( Exception $exception ) {
		return false;
	}
}

/**
 * @param $payment
 * @since 1.0.2
 *
 * @return \EverAccounting\Payment|null
 */
function eaccounting_get_payment( $payment ) {
	if ( empty( $payment ) ) {
		return null;
	}

	try {
		if ( $payment instanceof Payment ) {
			$_payment = $payment;
		} elseif ( is_object( $payment ) && ! empty( $payment->id ) ) {
			$_payment = new Payment( null );
			$_payment->populate( $payment );
		} else {
			$_payment = new Payment( absint( $payment ) );
		}

		if ( ! $_payment->exists() || $_payment->get_type() != 'expense' ) {
			throw new Exception( 'invalid_id', __( 'Invalid payment.', 'wp-ever-accounting' ) );
		}

		return $_payment;
	} catch ( Exception $exception ) {
		return null;
	}
}
