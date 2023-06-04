<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Sales.
 *
 * @since   1.0.0
 * @package EverAccounting\Admin
 */
class Sales extends \EverAccounting\Singleton {

	/**
	 * Sales constructor.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {
		add_filter( 'ever_accounting_sales_incomes_sections', array( __CLASS__, 'add_incomes_sections' ) );
		add_action( 'ever_accounting_sales_incomes_incomes_content', array( __CLASS__, 'output_incomes_incomes_content' ) );
		add_action( 'ever_accounting_sales_incomes_categories_content', array( __CLASS__, 'output_income_categories_section' ) );
		add_action( 'ever_accounting_sales_invoices_content', array( __CLASS__, 'output_invoices_content' ) );
		add_action( 'ever_accounting_sales_customers_content', array( __CLASS__, 'output_customers_content' ) );
	}

	/**
	 * Output the sales page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output() {
		$tabs         = eac_get_sales_tabs();
		$tab          = eac_get_input_var( 'tab' );
		$current_tab  = ! empty( $tab ) && array_key_exists( $tab, $tabs ) ? $tab : key( $tabs );
		$current_page = eac_get_input_var( 'page' );
		$page_name    = 'sales';

		include dirname( __FILE__ ) . '/views/admin-page.php';
	}

	/**
	 * Add the incomes sections.
	 *
	 * @param array $sections The sections.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public static function add_incomes_sections( $sections ) {
		$sections['incomes']    = __( 'Incomes', 'wp-ever-accounting' );
		$sections['categories'] = __( 'Categories', 'wp-ever-accounting' );

		return $sections;
	}

	/**
	 * Output the payments tab.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_incomes_incomes_content() {
		$action    = eac_get_input_var( 'action' );
		$income_id = eac_get_input_var( 'income_id' );
		if ( eac_is_input_var_set( 'income_id' ) && empty( eac_get_income( $income_id ) ) ) {
			wp_safe_redirect( admin_url( 'admin.php?page=eac-sales&tab=incomes' ) );
			exit;
		}
		if ( in_array( $action, array( 'add', 'edit' ), true ) ) {
			include dirname( __FILE__ ) . '/views/incomes/edit-income.php';
		} elseif ( 'view' === $action ) {
			include dirname( __FILE__ ) . '/views/incomes/view-income.php';
		} else {
			include dirname( __FILE__ ) . '/views/incomes/list-incomes.php';
		}
	}

	/**
	 * Output the income categories tab.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_income_categories_section() {
		$action  = eac_get_input_var( 'action' );
		$term_id = eac_get_input_var( 'term_id' );
		$term    = empty( $term_id ) ? false : eac_get_term( $term_id, 'income_cat' );
		if ( ! empty( $term_id ) && empty( $term ) ) {
			wp_safe_redirect( admin_url( 'admin.php?page=eac-sales&tab=incomes&section=categories' ) );
			exit;
		}
		if ( in_array( $action, array( 'add', 'edit' ), true ) ) {
			include dirname( __FILE__ ) . '/views/incomes/edit-category.php';
		} else {
			include dirname( __FILE__ ) . '/views/incomes/list-categories.php';
		}
	}

	/**
	 * Output the invoices tab.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_invoices_content() {
		$action     = eac_get_input_var( 'action' );
		$invoice_id = eac_get_input_var( 'invoice_id' );
		if ( in_array( $action, array( 'add', 'edit' ), true ) ) {
			include dirname( __FILE__ ) . '/views/invoices/edit-invoice.php';
		} elseif ( 'view' === $action ) {
			include dirname( __FILE__ ) . '/views/invoices/view-invoice.php';
		} else {
			include dirname( __FILE__ ) . '/views/invoices/list-invoices.php';
		}
	}

	/**
	 * Output the customers tab.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_customers_content() {
		$action      = eac_get_input_var( 'action' );
		$customer_id = eac_get_input_var( 'customer_id' );
		if ( in_array( $action, array( 'add', 'edit' ), true ) ) {
			include dirname( __FILE__ ) . '/views/customers/edit-customer.php';
		} elseif ( 'view' === $action ) {
			include dirname( __FILE__ ) . '/views/customers/view-customer.php';
		} else {
			include dirname( __FILE__ ) . '/views/customers/list-customers.php';
		}
	}
}
