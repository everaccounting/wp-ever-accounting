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
		add_action( 'ever_accounting_sales_payments_content', array( __CLASS__, 'output_payments_content' ) );
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
	 * Output the payments tab.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_payments_content() {
		$action    = eac_get_input_var( 'action' );
		$payment_id = eac_get_input_var( 'payment_id' );
		if ( eac_is_input_var_set( 'payment_id' ) && empty( eac_get_payment( $payment_id ) ) ) {
			wp_safe_redirect( admin_url( 'admin.php?page=eac-sales&tab=payments' ) );
			exit;
		}
		if ( in_array( $action, array( 'add', 'edit' ), true ) ) {
			include dirname( __FILE__ ) . '/views/payments/edit-payment.php';
		} elseif ( 'view' === $action ) {
			include dirname( __FILE__ ) . '/views/payments/view-payment.php';
		} else {
			include dirname( __FILE__ ) . '/views/payments/list-payments.php';
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
