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
		add_action( 'eac_reports_page_sales', array( __CLASS__, 'render_sales' ) );
		add_action( 'eac_reports_page_expenses', array( __CLASS__, 'render_expenses' ) );
		add_action( 'eac_reports_page_profits', array( __CLASS__, 'render_profits' ) );
		add_action( 'eac_reports_page_taxes', array( __CLASS__, 'render_taxes' ) );
	}

	/**
	 * Render sales.
	 *
	 * @since 1.0.0
	 */
	public static function render_sales() {
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
