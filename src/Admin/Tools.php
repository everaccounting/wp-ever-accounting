<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Tools.
 *
 * @since   1.0.0
 * @package EverAccounting\Admin
 */
class Tools extends \EverAccounting\Singleton {

	/**
	 * Tools constructor.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {
		add_action( 'ever_accounting_tools_export_content', array( __CLASS__, 'output_accounts_export_form' ) );
		add_action( 'ever_accounting_tools_export_content', array( __CLASS__, 'output_categories_export_form' ) );
		add_action( 'ever_accounting_tools_export_content', array( __CLASS__, 'output_customers_export_form' ) );
		add_action( 'ever_accounting_tools_export_content', array( __CLASS__, 'output_expenses_export_form' ) );
		add_action( 'ever_accounting_tools_export_content', array( __CLASS__, 'output_items_export_form' ) );
		add_action( 'ever_accounting_tools_export_content', array( __CLASS__, 'output_payments_export_form' ) );
		add_action( 'ever_accounting_tools_export_content', array( __CLASS__, 'output_vendors_export_form' ) );
		add_action( 'ever_accounting_tools_elements_content', array( __CLASS__, 'output_elements_page' ) );
		add_action( 'ever_accounting_tools_settings_content', array( __CLASS__, 'output_settings_page' ) );
	}

	/**
	 * Output the tools page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output() {
		$tabs         = eac_get_tools_tabs();
		$tab          = eac_get_input_var( 'tab' );
		$current_tab  = ! empty( $tab ) && array_key_exists( $tab, $tabs ) ? $tab : key( $tabs );
		$current_page = eac_get_input_var( 'page' );
		$page_name    = 'tools';

		include dirname( __FILE__ ) . '/views/admin-page.php';
	}

	/**
	 * Output the accounts export form.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_accounts_export_form() {
		include dirname( __FILE__ ) . '/views/export/accounts-export.php';
	}

	/**
	 * Output the categories export form.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_categories_export_form() {
		include dirname( __FILE__ ) . '/views/export/categories-export.php';
	}

	/**
	 * Output the customers export form.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_customers_export_form() {
		include dirname( __FILE__ ) . '/views/export/customers-export.php';
	}

	/**
	 * Output the expenses export form.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_expenses_export_form() {
		include dirname( __FILE__ ) . '/views/export/expenses-export.php';
	}

	/**
	 * Output the items export form.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_items_export_form() {
		include dirname( __FILE__ ) . '/views/export/items-export.php';
	}

	/**
	 * Output the payments export form.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_payments_export_form() {
		include dirname( __FILE__ ) . '/views/export/payments-export.php';
	}

	/**
	 * Output the vendors export form.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_vendors_export_form() {
		include dirname( __FILE__ ) . '/views/export/vendors-export.php';
	}

	/**
	 * Output the elements page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_elements_page() {
		include dirname( __FILE__ ) . '/views/elements-page.php';
	}

	/**
	 * Output the settings page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_settings_page() {
		echo '<div id="eac-root"></div>';
	}
}
