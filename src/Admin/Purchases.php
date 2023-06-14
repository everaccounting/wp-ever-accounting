<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class purchase.
 *
 * @since   1.0.0
 * @package EverAccounting\Admin
 */
class Purchases extends \EverAccounting\Singleton {

	/**
	 * purchase constructor.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {
		add_action( 'ever_accounting_purchases_expenses_content', array( __CLASS__, 'output_expenses_content' ) );
		add_action( 'ever_accounting_purchases_bills_content', array( __CLASS__, 'output_bills_content' ) );
		add_action( 'ever_accounting_purchases_vendors_content', array( __CLASS__, 'output_vendors_content' ) );
	}

	/**
	 * Output the purchase page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output() {
		$tabs         = eac_get_purchase_tabs();
		$tab          = eac_get_input_var( 'tab' );
		$current_tab  = ! empty( $tab ) && array_key_exists( $tab, $tabs ) ? $tab : key( $tabs );
		$current_page = eac_get_input_var( 'page' );
		$page_name    = 'purchases';

		include dirname( __FILE__ ) . '/views/admin-page.php';
	}

	/**
	 * Output the expenses tab.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_expenses_content() {
		$action     = eac_get_input_var( 'action' );
		$expense_id = eac_get_input_var( 'expense_id' );
		if ( eac_is_input_var_set( 'expense_id' ) && empty( eac_get_expense( $expense_id ) ) ) {
			wp_safe_redirect( admin_url( 'admin.php?page=eac-purchases&tab=expenses' ) );
			exit;
		}
		if ( in_array( $action, array( 'add', 'edit' ), true ) ) {
			include dirname( __FILE__ ) . '/views/expenses/edit-expense.php';
		} elseif ( 'view' === $action ) {
			include dirname( __FILE__ ) . '/views/expenses/view-expense.php';
		} else {
			include dirname( __FILE__ ) . '/views/expenses/list-expenses.php';
		}
	}

	/**
	 * Output the bills tab.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_bills_content() {
		$action  = eac_get_input_var( 'action' );
		$bill_id = eac_get_input_var( 'bill_id' );
		if ( in_array( $action, array( 'add', 'edit' ), true ) ) {
			include dirname( __FILE__ ) . '/views/bills/edit-bill.php';
		} elseif ( 'view' === $action ) {
			include dirname( __FILE__ ) . '/views/bills/view-bill.php';
		} else {
			include dirname( __FILE__ ) . '/views/bills/list-bills.php';
		}
	}

	/**
	 * Output the vendors tab.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_vendors_content() {
		$action    = eac_get_input_var( 'action' );
		$vendor_id = eac_get_input_var( 'vendor_id' );
		if ( in_array( $action, array( 'add', 'edit' ), true ) ) {
			include dirname( __FILE__ ) . '/views/vendors/edit-vendor.php';
		} elseif ( 'view' === $action ) {
			include dirname( __FILE__ ) . '/views/vendors/view-vendor.php';
		} else {
			include dirname( __FILE__ ) . '/views/vendors/list-vendors.php';
		}
	}
}
