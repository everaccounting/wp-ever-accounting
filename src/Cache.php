<?php

namespace EverAccounting;

defined( 'ABSPATH' ) || exit();

/**
 * Class Cache
 *
 * @package EverAccounting
 */
class Cache extends Singleton {

	/**
	 * Cache constructor.
	 */
	protected function __construct() {
		add_action( 'ever_accounting_payment_saved', array( $this, 'clear_transactions_cache' ) );
		add_action( 'ever_accounting_payment_deleted', array( $this, 'clear_transactions_cache' ) );
		add_action( 'ever_accounting_expense_saved', array( $this, 'clear_transactions_cache' ) );
		add_action( 'ever_accounting_expense_deleted', array( $this, 'clear_transactions_cache' ) );
	}

	/**
	 * Clear transactions cache.
	 *
	 * @since 1.0.0
	 */
	public function clear_transactions_cache() {
		delete_transient( 'eac_payment_reports' );
		delete_transient( 'eac_expense_reports' );
		delete_transient( 'eac_profit_reports' );
	}
}
