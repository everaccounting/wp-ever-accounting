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
 * Get estimated payment for a given period.
 *
 * @param string $period The time period to calculate estimated payment for. Accepts 'month', 'quarter', or 'year'.
 * @param string $type The type of transaction to calculate estimated payment for. Accepts 'payment' or 'refund'.
 *
 * @since 1.1.0
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
		$result = $wpdb->results(
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
