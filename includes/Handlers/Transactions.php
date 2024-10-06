<?php

namespace EverAccounting\Handlers;

use ByteKit\Models\Query;

defined( 'ABSPATH' ) || exit;

/**
 * Class Transactions.
 *
 * @since   1.0.0
 * @package EverAccounting\Controllers
 */
class Transactions {
	/**
	 * Transactions constructor.
	 */
	public function __construct() {
		add_action( 'ever_accounting_payment_saved', array( $this, 'reset_report_cache' ) );
		add_action( 'ever_accounting_payment_deleted', array( $this, 'reset_report_cache' ) );
		add_action( 'ever_accounting_expense_saved', array( $this, 'reset_report_cache' ) );
		add_action( 'ever_accounting_expense_deleted', array( $this, 'reset_report_cache' ) );
	}

	/**
	 * Reset report cache.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function reset_report_cache() {
		delete_transient( 'eac_payments_reports' );
		delete_transient( 'eac_expenses_reports' );
		delete_transient( 'eac_profit_reports' );
	}

	/**
	 * Exclude transfers from the query.
	 *
	 * @param array $clauses The query clauses.
	 * @param Query $query The query object.
	 *
	 * @return array
	 */
	public function exclude_transfers( $clauses, $query ) {
		$clauses['join']  .= " LEFT JOIN {$query->get_db()->prefix}ea_transfers AS ea_transfers ON ea_transactions.id=ea_transfers.payment_id";
		$clauses['where'] .= ' AND ea_transfers.id IS NULL';

		return $clauses;
	}

	/**
	 * Initialize payment.
	 */
	public static function setup_payment() {
		$account_id     = get_option( 'eac_default_sales_account_id', 0 );
		$sales_category = get_option( 'eac_default_sales_category_id', 0 );
		$method = get_option( 'eac_default_sales_method', 'cash' );
		$account        = eac_get_account( $account_id );
		$category       = eac_get_category( $sales_category );

		if ( ! empty( $account ) ) {

		}
	}
}
