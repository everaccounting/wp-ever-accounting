<?php
/**
 * EverAccounting Transaction functions.
 *
 * Functions for all kind of transaction of the plugin.
 *
 * @since   1.0.2
 * @package EverAccounting
 */

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
		'expense' => __( 'Expense', 'wp-ever-accounting' ),
	);

	return $types;
}

/**
 * Get payment.
 *
 * @since 1.0.2
 *
 * @param $payment
 *
 * @return \EverAccounting\Models\Payment|null
 */
function eaccounting_get_payment( $payment ) {
	if ( empty( $payment ) ) {
		return null;
	}

	$result = new EverAccounting\Models\Payment( $payment );

	return $result->exists() ? $result : null;
}


/**
 *  Create new payment programmatically.
 *
 *  Returns a new payment object on success.
 *
 * @since 1.0.2
 *
 * @param array $args payment arguments.
 *
 * @return EverAccounting\Models\Payment|\WP_Error
 */
function eaccounting_insert_payment( $args ) {
	$payment = new EverAccounting\Models\Payment( $args );

	return $payment->save();
}

/**
 * Delete a payment.
 *
 * @since 1.0.2
 *
 * @param $payment_id
 *
 * @return bool
 */
function eaccounting_delete_payment( $payment_id ) {
	$payment = new EverAccounting\Models\Payment( $payment_id );
	if ( ! $payment->exists() ) {
		return false;
	}

	return \EverAccounting\Repositories\Payments::instance()->delete( $payment->get_id() );
}

/**
 * Get payments items.
 *
 * @since 1.1.0
 *
 * @param array $args
 *
 * @return array|int
 */
function eaccounting_get_payments( $args = array() ) {
	return \EverAccounting\Repositories\Payments::instance()->get_items( $args );
}


/**
 * Get revenue.
 *
 * @since 1.0.2
 *
 * @param $revenue
 *
 * @return \EverAccounting\Models\Revenue|null
 */
function eaccounting_get_revenue( $revenue ) {
	if ( empty( $revenue ) ) {
		return null;
	}

	$result = new EverAccounting\Models\Revenue( $revenue );

	return $result->exists() ? $result : null;
}


/**
 *  Create new revenue programmatically.
 *
 *  Returns a new revenue object on success.
 *
 * @since 1.0.2
 *
 * @param array $args           {
 *
 * @type int    $id             Transaction id. If the id is something other than 0 then it will update the transaction.
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
 * @return EverAccounting\Models\Revenue|\WP_Error
 */
function eaccounting_insert_revenue( $args ) {
	$revenue = new EverAccounting\Models\Revenue( $args );

	return $revenue->save();
}

/**
 * Delete a revenue.
 *
 * @since 1.0.2
 *
 * @param $revenue_id
 *
 * @return bool
 */
function eaccounting_delete_revenue( $revenue_id ) {
	$revenue = new EverAccounting\Models\Revenue( $revenue_id );
	if ( ! $revenue->exists() ) {
		return false;
	}

	return \EverAccounting\Repositories\Revenues::instance()->delete( $revenue->get_id() );
}

/**
 * Get revenues items.
 *
 * @since 1.1.0
 *
 * @param array $args           {
 *
 * @type int    $id             Transaction id. If the id is something other than 0 then it will update the transaction.
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
 * @return array|int
 */
function eaccounting_get_revenues( $args = array() ) {
	return \EverAccounting\Repositories\Revenues::instance()->get_items( $args );
}

/**
 * Get transfer.
 *
 * @since 1.1.1
 *
 * @param $transfer
 *
 * @return \EverAccounting\Models\Transfer|null
 */
function eaccounting_get_transfer( $transfer ) {
	if ( empty( $transfer ) ) {
		return null;
	}

	$result = new EverAccounting\Models\Transfer( $transfer );

	return $result->exists() ? $result : null;
}

/**
 * Insert transfer.
 *
 * @since 1.1.1
 *
 * @param array $args
 *
 * @return \EverAccounting\Models\Transfer|\WP_Error
 */
function eaccounting_insert_transfer( $args ) {
	$transfer = new EverAccounting\Models\Transfer( $args );

	return $transfer->save();
}

/**
 * Delete a transfer.
 *
 * @since 1.0.2
 *
 * @param $transfer_id
 *
 * @return bool
 */
function eaccounting_delete_transfer( $transfer_id ) {
	$transfer = new EverAccounting\Models\Transfer( $transfer_id );
	if ( ! $transfer->exists() ) {
		return false;
	}

	return \EverAccounting\Repositories\Transfers::instance()->delete( $transfer->get_id() );
}

/**
 * Get transfers.
 *
 * @since 1.1.0
 *
 * @param array $args
 *
 * @return array|int
 */
function eaccounting_get_transfers( $args = array() ) {
	return \EverAccounting\Repositories\Transfers::instance()->get_items( $args );
}
