<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class purchase.
 *
 * @since   1.0.0
 * @package EverAccounting\Admin
 */
class Purchase extends \EverAccounting\Singleton {

	/**
	 * purchase constructor.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {
		add_action( 'ever_accounting_purchase_tab_expenses', array( __CLASS__, 'output_expenses_tab' ) );
		add_action( 'ever_accounting_purchase_tab_bills', array( __CLASS__, 'output_bills_tab' ) );
		add_action( 'ever_accounting_purchase_tab_vendors', array( __CLASS__, 'output_vendors_tab' ) );
		// add_action( 'admin_footer', array( __CLASS__, 'output_expense_modal' ) );
		// add_action( 'admin_footer', array( __CLASS__, 'output_vendor_modal' ) );
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
		$page_name    = 'purchase';

		include dirname( __FILE__ ) . '/views/admin-page.php';
	}

	/**
	 * Output the expenses tab.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_expenses_tab() {
		$action     = eac_get_input_var( 'action' );
		$expense_id = eac_get_input_var( 'expense_id' );
		if ( eac_is_input_var_set( 'expense_id' ) && empty( eac_get_expense( $expense_id ) ) ) {
			wp_safe_redirect( admin_url( 'admin.php?page=eac-purchase&tab=expenses' ) );
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
	public static function output_bills_tab() {
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
	public static function output_vendors_tab() {
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

	/**
	 * Output the expense modal.
	 *
	 * @since 1.0.0
	 */
	public static function output_expense_modal() {
		$expense = new \EverAccounting\Models\Expense();
		?>
		<script type="text/template" id="eac-expense-modal" data-title="<?php esc_html_e( 'Add Expense', 'wp-ever-accounting' ); ?>">
			<?php require __DIR__ . '/views/expenses/expense-form.php'; ?>
		</script>
		<?php
	}

	/**
	 * Output the vendor modal.
	 *
	 * @since 1.0.0
	 */
	public static function output_vendor_modal() {
		$vendor = new \EverAccounting\Models\Vendor();
		?>
		<script type="text/template" id="eac-vendor-modal" data-title="<?php esc_html_e( 'Add Vendor', 'wp-ever-accounting' ); ?>">
			<?php require __DIR__ . '/views/vendors/vendor-form.php'; ?>
		</script>
		<?php
	}
}
