<?php
namespace EverAccounting\Payments;

defined( 'ABSPATH' ) || exit();

/**
 * Main function for querying payments.
 *
 * @since 1.1.0
 *
 * @param array $args
 *
 * @return \EverAccounting\Transactions\Query
 */
function query( $args = array() ) {
	return \EverAccounting\Transactions\query( array_merge( $args, array( 'type' => 'customer' ) ) );
}

/**
 * Main function for returning payment.
 *
 * @since 1.1.0
 *
 * @param $payment
 *
 * @return \EverAccounting\Transactions\Transaction
 */
function get( $payment ) {
	$payment = \EverAccounting\Transactions\get( $payment );

	return $payment && $payment->get_type() === 'expense' ? $payment : null;
}

/**
 *  Create new payment programmatically.
 *
 *  Returns a new payment object on success.
 *
 * @since 1.1.0
 *
 * @param array $args Contact arguments.
 *
 * @return \EverAccounting\Transactions\Transaction|\WP_Error
 */
function insert( $args ) {
	$args['type'] = 'expense';

	return \EverAccounting\Transactions\insert( $args );
}


/**
 * Delete an payment.
 *
 * @since 1.1.0
 *
 * @param $payment_id
 *
 * @return bool
 */
function delete( $payment_id ) {
	$payment = get( $payment_id );

	return $payment && \EverAccounting\Transactions\delete( $payment );
}
