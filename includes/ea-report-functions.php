<?php
/**
 * EverAccounting report related functions.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

defined( 'ABSPATH' ) || exit;


/**
 * Get total income.
 *
 * @param null $year
 *
 * @return float
 * @since 1.1.0
 *
 */
function eaccounting_get_total_income( $year = null ) {
	global $wpdb;
	$total_income = wp_cache_get( 'total_income_' . $year, 'ea_transactions' );
	if ( false === $total_income ) {
		$where = '';
		if ( absint( $year ) ) {
			$financial_start = eaccounting_get_financial_start( $year );
			$financial_end   = eaccounting_get_financial_end( $year );
			$where           .= $wpdb->prepare( 'AND ( payment_date between %s AND %s )', $financial_start, $financial_end );
		}

		$sql          = $wpdb->prepare(
			" SELECT Sum(amount) amount,currency_code,currency_rate
				FROM   {$wpdb->prefix}ea_transactions
				WHERE 1=1 $where AND type = %s AND category_id NOT IN (SELECT id FROM   {$wpdb->prefix}ea_categories WHERE  type = 'other')
				GROUP  BY currency_code, currency_rate
			",
			'income'
		);
		$results      = $wpdb->get_results( $sql );
		$total_income = 0;
		foreach ( $results as $result ) {
			$total_income += eaccounting_price_to_default( $result->amount, $result->currency_code, $result->currency_rate );
		}
		wp_cache_add( 'total_income_' . $year, $total_income, 'ea_transactions' );
	}

	return $total_income;
}

/**
 * Get total expense.
 *
 * @param null $year
 *
 * @return float
 * @since 1.1.0
 *
 */
function eaccounting_get_total_expense( $year = null ) {
	global $wpdb;
	$total_expense = wp_cache_get( 'total_expense_' . $year, 'ea_transactions' );
	if ( false === $total_expense ) {
		$where = '';
		if ( absint( $year ) ) {
			$financial_start = eaccounting_get_financial_start( $year );
			$financial_end   = eaccounting_get_financial_end( $year );
			$where           .= $wpdb->prepare( 'AND ( payment_date between %s AND %s )', $financial_start, $financial_end );
		}

		$sql           = $wpdb->prepare(
			" SELECT Sum(amount) amount,currency_code,currency_rate
				FROM   {$wpdb->prefix}ea_transactions
				WHERE 1=1 $where AND type = %s AND category_id NOT IN (SELECT id FROM   {$wpdb->prefix}ea_categories WHERE  type = 'other')
				GROUP  BY currency_code, currency_rate
			",
			'expense'
		);
		$results       = $wpdb->get_results( $sql );
		$total_expense = 0;
		foreach ( $results as $result ) {
			$total_expense += eaccounting_price_to_default( $result->amount, $result->currency_code, $result->currency_rate );
		}
		wp_cache_add( 'total_expense_' . $year, $total_expense, 'ea_transactions' );
	}

	return $total_expense;
}

/**
 * Get total profit.
 *
 * @param null $year
 *
 * @return float
 * @since 1.1.0
 *
 */
function eaccounting_get_total_profit( $year = null ) {
	$total_income  = (float) eaccounting_get_total_income( $year );
	$total_expense = (float) eaccounting_get_total_expense( $year );
	$profit        = $total_income - $total_expense;

	return $profit < 0 ? 0 : $profit;
}

/**
 * Get total receivable.
 *
 * @return false|float|int|mixed|string
 * @since 1.1.0
 */
function eaccounting_get_total_receivable() {
	global $wpdb;
	$total_receivable = wp_cache_get( 'total_receivable', 'ea_transactions' );
	if ( false === $total_receivable ) {
		$total_receivable = 0;
		$invoices_sql     = $wpdb->prepare(
			"
			SELECT SUM(total) amount, currency_code, currency_rate  FROM   {$wpdb->prefix}ea_documents
			WHERE  status NOT IN ( 'draft', 'cancelled', 'refunded' )
			AND `status` <> 'paid'  AND type = %s GROUP BY currency_code, currency_rate
			",
			'invoice'
		);
		$invoices         = $wpdb->get_results( $invoices_sql );
		foreach ( $invoices as $invoice ) {
			$total_receivable += eaccounting_price_to_default( $invoice->amount, $invoice->currency_code, $invoice->currency_rate );
		}
		$sql     = $wpdb->prepare(
			"
		  SELECT Sum(amount) amount, currency_code, currency_rate
		  FROM   {$wpdb->prefix}ea_transactions
		  WHERE  type = %s
				 AND document_id IN (SELECT id FROM   {$wpdb->prefix}ea_documents WHERE  status NOT IN ( 'draft', 'cancelled', 'refunded' )
				 AND `status` <> 'paid'
				 AND type = 'invoice')
		  GROUP  BY currency_code,currency_rate
		  ",
			'income'
		);
		$results = $wpdb->get_results( $sql );
		foreach ( $results as $result ) {
			$total_receivable -= eaccounting_price_to_default( $result->amount, $result->currency_code, $result->currency_rate );
		}
		wp_cache_add( 'total_receivable', $total_receivable, 'ea_transactions' );
	}

	return $total_receivable;
}

/**
 * Get total payable.
 *
 * @return float
 * @since 1.1.0
 */
function eaccounting_get_total_payable() {
	global $wpdb;
	$total_payable = wp_cache_get( 'total_payable', 'ea_transactions' );
	if ( false === $total_payable ) {
		$total_payable = 0;
		$bills_sql     = $wpdb->prepare(
			"
			SELECT SUM(total) amount, currency_code, currency_rate  FROM   {$wpdb->prefix}ea_documents
			WHERE  status NOT IN ( 'draft', 'cancelled', 'refunded' )
			AND `status` <> 'paid'  AND type = %s GROUP BY currency_code, currency_rate
			",
			'bill'
		);
		$bills         = $wpdb->get_results( $bills_sql );
		foreach ( $bills as $bill ) {
			$total_payable += eaccounting_price_to_default( $bill->amount, $bill->currency_code, $bill->currency_rate );
		}
		$sql     = $wpdb->prepare(
			"
		  SELECT Sum(amount) amount, currency_code, currency_rate
		  FROM   {$wpdb->prefix}ea_transactions
		  WHERE  type = %s
				 AND document_id IN (SELECT id FROM   {$wpdb->prefix}ea_documents WHERE  status NOT IN ( 'draft', 'cancelled', 'refunded' )
				 AND `status` <> 'paid'
				 AND type = 'bill')
		  GROUP  BY currency_code,currency_rate
		  ",
			'expense'
		);
		$results = $wpdb->get_results( $sql );
		foreach ( $results as $result ) {
			$total_payable -= eaccounting_price_to_default( $result->amount, $result->currency_code, $result->currency_rate );
		}
		wp_cache_add( 'total_payable', $total_payable, 'ea_transactions' );
	}

	return $total_payable;
}

/**
 * Get total upcoming profit
 *
 * @return float
 * @since 1.1.0
 */
function eaccounting_get_total_upcoming_profit() {
	$total_payable    = (float) eaccounting_get_total_payable();
	$total_receivable = (float) eaccounting_get_total_receivable();
	$upcoming         = $total_receivable - $total_payable;

	return $upcoming < 0 ? 0 : $upcoming;
}
