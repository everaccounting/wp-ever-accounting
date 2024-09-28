<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Reports
 *
 * @since 1.0.0
 * @package EverAccounting\Admin
 */
class Reports {

	/**
	 * Reports constructor.
	 */
	public function __construct() {
		add_filter( 'eac_reports_page_tabs', array( __CLASS__, 'register_tabs' ) );
		add_action( 'eac_reports_page_payments', array( __CLASS__, 'render_payments' ) );
		add_action( 'eac_reports_page_expenses', array( __CLASS__, 'render_expenses' ) );
		add_action( 'eac_reports_page_profits', array( __CLASS__, 'render_profits' ) );
		add_action( 'eac_reports_page_taxes', array( __CLASS__, 'render_taxes' ) );
	}

	/**
	 * Register tabs.
	 *
	 * @param array $tabs Tabs.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function register_tabs( $tabs ) {
		$tabs['payments'] = __( 'Payments', 'ever-accounting' );
		$tabs['expenses'] = __( 'Expenses', 'ever-accounting' );
		$tabs['profits']  = __( 'Profits', 'ever-accounting' );
		$tabs['taxes']    = __( 'Taxes', 'ever-accounting' );

		return $tabs;
	}

	/**
	 * Render payments.
	 *
	 * @since 1.0.0
	 */
	public static function render_payments() {
		include __DIR__ . '/views/report-payments.php';
	}

	/**
	 * Render expenses.
	 *
	 * @since 1.0.0
	 */
	public static function render_expenses() {
		include __DIR__ . '/views/report-expenses.php';
	}

	/**
	 * Render profits.
	 *
	 * @since 1.0.0
	 */
	public static function render_profits() {
		include __DIR__ . '/views/report-profits.php';
	}

	/**
	 * Render taxes.
	 *
	 * @since 1.0.0
	 */
	public static function render_taxes() {
		include __DIR__ . '/views/report-taxes.php';
	}
}
