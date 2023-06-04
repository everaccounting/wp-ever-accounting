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
		add_action( 'ever_accounting_income_saved', array( $this, 'clear_transactions_cache' ) );
		add_action( 'ever_accounting_income_deleted', array( $this, 'clear_transactions_cache' ) );
		add_action( 'ever_accounting_expense_saved', array( $this, 'clear_transactions_cache' ) );
		add_action( 'ever_accounting_expense_deleted', array( $this, 'clear_transactions_cache' ) );
	}

	/**
	 * Clear transactions cache.
	 *
	 * @since 1.0.0
	 */
	public function clear_transactions_cache() {
		global $wpdb;
		// Clear any transients that started with 'eac_sales_summary_'.
		$wpdb->query( "DELETE FROM {$wpdb->options} WHERE `option_name` LIKE '_transient_eac_income_summary_%'" );
		$wpdb->query( "DELETE FROM {$wpdb->options} WHERE `option_name` LIKE '_transient_timeout_eac_income_summary_%'" );
		$wpdb->query( "DELETE FROM {$wpdb->options} WHERE `option_name` LIKE '_transient_eac_expense_summary_%'" );
		$wpdb->query( "DELETE FROM {$wpdb->options} WHERE `option_name` LIKE '_transient_timeout_eac_expense_summary_%'" );
	}
}
