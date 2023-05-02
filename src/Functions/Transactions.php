<?php
/**
 * EverAccounting Transaction functions.
 *
 * Functions for all kind of transaction of the plugin.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

use EverAccounting\Models\Expense;
use EverAccounting\Models\Payment;
use EverAccounting\Models\Transfer;
use EverAccounting\Models\Transaction;

defined( 'ABSPATH' ) || exit;

/**
 * Get Transaction Types
 *
 * @since 1.1.0
 * @return array
 */
function eac_get_transaction_types() {
	$types = array(
		'payment' => esc_html__( 'Payment', 'wp-ever-accounting' ),
		'expense' => esc_html__( 'Expense', 'wp-ever-accounting' ),
	);

	return apply_filters( 'ever_accounting_transaction_types', $types );
}


/**
 * Get Transaction Statuses
 *
 * @since 1.1.0
 * @return array
 */
function eac_get_transaction_statuses() {
	$statuses = array(
		'pending'   => esc_html__( 'Pending', 'wp-ever-accounting' ),
		'paid'      => esc_html__( 'Paid', 'wp-ever-accounting' ),
		'refunded'  => esc_html__( 'Refunded', 'wp-ever-accounting' ),
		'cancelled' => esc_html__( 'Cancelled', 'wp-ever-accounting' ),
	);

	return apply_filters( 'ever_accounting_transaction_statuses', $statuses );
}

/**
 * Get payment.
 *
 * @param mixed  $payment Payment object or ID.
 * @param string $column Optional. Column to get. Default null.
 * @param array  $args Optional. Additional arguments. Default empty array.
 *
 * @since 1.1.6
 * @return Payment|null
 */
function eac_get_payment( $payment, $column = null, $args = array() ) {
	return Payment::get( $payment, $column, $args );
}

/**
 *  Create new payment.
 *
 *  Returns a new payment object on success.
 *
 * @param array $data Payment data.
 * @param bool  $wp_error Optional. Whether to return a WP_Error on failure. Default false.
 *
 * @since 1.1.0
 * @return Payment|false|WP_Error Payment object on success, false or WP_Error on failure.
 */
function eac_insert_payment( $data, $wp_error = true ) {
	return Payment::insert( $data, $wp_error );
}

/**
 * Delete an payment.
 *
 * @param int $payment_id Payment ID.
 *
 * @since 1.1.0
 *
 * @return bool
 */
function eac_delete_payment( $payment_id ) {
	$payment = eac_get_payment( $payment_id );

	if ( ! $payment ) {
		return false;
	}

	return $payment->delete();
}

/**
 * Get payments.
 *
 * @param array $args Query arguments.
 * @param bool  $count Optional. Whether to return only the total found accounts for the query.
 *
 * @since 1.1.0
 *
 * @return array|int|Payment[] Array of payment objects, the total found payment for the query, or the total found payments for the query as int when `$count` is true.
 */
function eac_get_payments( $args = array(), $count = false ) {
	$defaults = array(
		'limit'   => 20,
		'offset'  => 0,
		'orderby' => 'id',
		'order'   => 'DESC',
		'fields'  => 'all',
	);

	$args = wp_parse_args( $args, $defaults );

	if ( $count ) {
		return Payment::count( $args );
	}

	return Payment::query( $args );
}

/**
 * Get expense.
 *
 * @param mixed  $expesnse Payment object or ID.
 * @param string $column Optional. Column to get. Default null.
 * @param array  $args Optional. Additional arguments. Default empty array.
 *
 * @since 1.1.6
 * @return Expense|null
 */
function eac_get_expense( $expesnse, $column = null, $args = array() ) {
	return Expense::get( $expesnse, $column, $args );
}

/**
 *  Create new expense.
 *
 *  Returns a new expense object on success.
 *
 * @param array $data Expense data.
 * @param bool  $wp_error Optional. Whether to return a WP_Error on failure. Default false.
 *
 * @since 1.1.0
 * @return Expense|false|WP_Error Expense object on success, false or WP_Error on failure.
 */
function eac_insert_expense( $data, $wp_error = true ) {
	return Expense::insert( $data, $wp_error );
}

/**
 * Delete an expense.
 *
 * @param int $expense_id Expense ID.
 *
 * @since 1.1.0
 *
 * @return bool
 */
function eac_delete_expense( $expense_id ) {
	$expense = eac_get_expense( $expense_id );

	if ( ! $expense ) {
		return false;
	}

	return $expense->delete();
}

/**
 * Get expenses.
 *
 * @param array $args Query arguments.
 * @param bool  $count Optional. Whether to return only the total found accounts for the query.
 * @since 1.1.0
 *
 * @return array|int|Expense[] Array of expense objects, the total found expense for the query, or the total found expenses for the query as int when `$count` is true.
 */
function eac_get_expenses( $args = array(), $count = false ) {
	$defaults = array(
		'limit'   => 20,
		'offset'  => 0,
		'orderby' => 'id',
		'order'   => 'DESC',
		'fields'  => 'all',
	);

	$args = wp_parse_args( $args, $defaults );

	if ( $count ) {
		return Expense::count( $args );
	}

	return Expense::query( $args );
}

/**
 * Get transactions.
 *
 * @param array $args Query arguments.
 * @param bool  $count Optional. Whether to return only the total found accounts for the query.
 *
 * @since 1.1.0
 *
 * @return array|int|Transaction[] Array of expense objects, the total found expense for the query, or the total found expenses for the query as int when `$count` is true.
 */
function eac_get_transactions( $args = array(), $count = false ) {
	$defaults = array(
		'limit'   => 20,
		'offset'  => 0,
		'orderby' => 'id',
		'order'   => 'DESC',
		'fields'  => 'all',
	);

	$args = wp_parse_args( $args, $defaults );

	if ( $count ) {
		return Transaction::count( $args );
	}

	return Transaction::query( $args );
}

/**
 * Get transfer.
 *
 * @param mixed  $transfer Transfer object or ID.
 * @param string $column Optional. Column to get. Default null.
 * @param array  $args Optional. Additional arguments. Default empty array.
 *
 * @since 1.1.6
 * @return Transfer|null
 */
function eac_get_transfer( $transfer, $column = null, $args = array() ) {
	return Transfer::get( $transfer, $column, $args );
}

/**
 *  Create new transfer.
 *
 *  Returns a new transfer object on success.
 *
 * @param array $data Transfer data.
 * @param bool  $wp_error Optional. Whether to return a WP_Error on failure. Default false.
 *
 * @since 1.1.0
 * @return Transfer|false|WP_Error Transfer object on success, false or WP_Error on failure.
 */
function eac_insert_transfer( $data, $wp_error = true ) {
	return Transfer::insert( $data, $wp_error );
}

/**
 * Delete an transfer.
 *
 * @param int $transfer_id Transfer ID.
 *
 * @since 1.1.0
 *
 * @return bool
 */
function eac_delete_transfer( $transfer_id ) {
	$transfer = eac_get_transfer( $transfer_id );

	if ( ! $transfer ) {
		return false;
	}

	return $transfer->delete();
}

/**
 * Get transfers.
 *
 * @param array $args Query arguments.
 * @param bool  $count Optional. Whether to return only the total found accounts for the query.
 *
 * @since 1.1.0
 *
 * @return array|int|Transfer[] Array of transfer objects, the total found transfer for the query, or the total found transfers for the query as int when `$count` is true.
 */
function eac_get_transfers( $args = array(), $count = false ) {
	$defaults = array(
		'limit'   => 20,
		'offset'  => 0,
		'orderby' => 'id',
		'order'   => 'DESC',
		'fields'  => 'all',
	);

	$args = wp_parse_args( $args, $defaults );

	if ( $count ) {
		return Transfer::count( $args );
	}

	return Transfer::query( $args );
}

/**
 * Get estimated income for a given period.
 *
 * @since 1.1.0
 *
 * @param string $period The time period to calculate estimated income for. Accepts 'month', 'quarter', or 'year'.
 * @param string $type The type of transaction to calculate estimated income for. Accepts 'payment' or 'refund'.
 *
 * @return float
 */
function eac_get_estimated_transaction_total( $period = 'month', $type = 'payment' ) {
	global $wpdb;

	switch ( $period ) {
		case 'quarter':
			$date_format = '%Y-%m-01';
			$interval    = '3 MONTH';
			break;
		case 'year':
			$date_format = '%Y-%m-01';
			$interval    = '1 YEAR';
			break;
		case 'week':
			$date_format = '%Y-%m-%d';
			$interval    = '1 WEEK';
			break;

		case 'fortnight':
			$date_format = '%Y-%m-%d';
			$interval    = '2 WEEK';
			break;

		default:
			$date_format = '%Y-%m-%d';
			$interval    = '1 MONTH';
	}

	$estimate = false; // get_transient( "eac_estimated_payment_for_{$period}_{$type}" );
	if ( false === $estimate ) {
		$result = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT DATE_FORMAT(payment_date, %s) AS period, SUM(amount/currency_rate) AS total_amount
				FROM {$wpdb->prefix}ea_transactions
				WHERE payment_date >= DATE_SUB(NOW(), INTERVAL $interval) AND type = %s
				GROUP BY DATE_FORMAT(payment_date, %s)",
				$date_format,
				$type,
				$date_format
			)
		);

		var_dump( $result );

		// Calculate the average amount of transactions per period.
		$amounts = wp_list_pluck( $result, 'total_amount' );
		$average = ! empty( $amounts ) ? array_sum( $amounts ) / count( $amounts ) : 0;

		// Calculate the estimated total amount for this period.
		$days_in_period = wp_date( 't', strtotime( $interval ) );
		$remaining_days = $days_in_period - wp_date( 'j' );
		$estimate       = $average * ( $days_in_period - $remaining_days );

		// If the remaining time is less than 7 days and the actual total amount is less than 80% of the estimated total amount, adjust the estimate.
		$actual_total_amount = $result[ count( $result ) - 1 ]->total_amount;

		$difference = abs( $estimate - $actual_total_amount );
		if ( $remaining_days < 7 && $actual_total_amount < ( $estimate * 0.8 ) && $difference > ( $average * $remaining_days ) ) {
			$estimate = $actual_total_amount + ( $average * $remaining_days );
		}

		// If the actual total amount is more than 120% of the estimated total amount, adjust the estimate.
		if ( $actual_total_amount > ( $estimate * 1.2 ) ) {
			$estimate = $actual_total_amount + ( $average * $remaining_days );
		}
		//
		// set_transient( "eac_estimated_payment_for_{$period}_{$type}", $estimate, 60 * 60 * 24 );
	}

	return $estimate;
}
