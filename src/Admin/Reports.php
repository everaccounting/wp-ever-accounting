<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Reports.
 *
 * @since   1.0.0
 * @package EverAccounting\Admin
 */
class Reports extends \EverAccounting\Singleton {

	/**
	 * Reports constructor.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {
		add_action( 'ever_accounting_reports_payments_content', array( __CLASS__, 'output_payments_content' ) );
		add_action( 'ever_accounting_reports_expenses_content', array( __CLASS__, 'output_expenses_content' ) );
		add_action( 'ever_accounting_reports_profits_content', array( __CLASS__, 'output_profits_content' ) );
	}

	/**
	 * Output the sales page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output() {
		$tabs         = eac_get_reports_tabs();
		$tab          = eac_get_input_var( 'tab' );
		$current_tab  = ! empty( $tab ) && array_key_exists( $tab, $tabs ) ? $tab : key( $tabs );
		$current_page = eac_get_input_var( 'page' );
		$page_name    = 'reports';

		include dirname( __FILE__ ) . '/views/admin-page.php';
	}

	/**
	 * Output the payments tab.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_payments_content() {
		include dirname( __FILE__ ) . '/views/reports/payments.php';
	}

	/**
	 * Output the expenses tab.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_expenses_content() {
		include dirname( __FILE__ ) . '/views/reports/expenses.php';
	}

	/**
	 * Output the profits tab.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_profits_content() {
		include dirname( __FILE__ ) . '/views/reports/profits.php';
	}
}
