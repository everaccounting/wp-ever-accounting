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
		add_action( 'ever_accounting_sales_tab_payments', array( __CLASS__, 'output_payments_tab' ) );
		add_action( 'ever_accounting_sales_tab_invoices', array( __CLASS__, 'output_invoices_tab' ) );
		add_action( 'ever_accounting_sales_tab_customers', array( __CLASS__, 'output_customers_tab' ) );
		add_action( 'admin_footer', array( __CLASS__, 'output_payment_modal' ) );
		add_action( 'admin_footer', array( __CLASS__, 'output_customers_modal' ) );
	}

	/**
	 * Output the sales page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output() {
		$tabs         = eac_get_sales_tabs();
		$tab          = eac_filter_input( INPUT_GET, 'tab' );
		$current_tab  = ! empty( $tab ) && array_key_exists( $tab, $tabs ) ? $tab : key( $tabs );
		$current_page = eac_filter_input( INPUT_GET, 'page' );
		$page_name    = 'sales';

		include dirname( __FILE__ ) . '/views/admin-page.php';
	}

	/**
	 * Output the payments tab.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_payments_tab() {
		$action     = eac_filter_input( INPUT_GET, 'action' );
		$payment_id = eac_filter_input( INPUT_GET, 'payment_id', 'absint' );
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
	public static function output_invoices_tab() {
		$action     = eac_filter_input( INPUT_GET, 'action' );
		$invoice_id = eac_filter_input( INPUT_GET, 'invoice_id', 'absint' );
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
	public static function output_customers_tab() {
		$action      = eac_filter_input( INPUT_GET, 'action' );
		$customer_id = eac_filter_input( INPUT_GET, 'customer_id', 'absint' );
		if ( in_array( $action, array( 'add', 'edit' ), true ) ) {
			include dirname( __FILE__ ) . '/views/customers/edit-customer.php';
		} elseif ( 'view' === $action ) {
			include dirname( __FILE__ ) . '/views/customers/view-customer.php';
		} else {
			include dirname( __FILE__ ) . '/views/customers/list-customers.php';
		}
	}

	/**
	 * Output the payment modal.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_payment_modal() {
		$payment = new \EverAccounting\Models\Payment();
		?>
		<script type="text/template" id="eac-payment-modal" data-title="<?php esc_html_e( 'Add Payment', 'wp-ever-accounting' ); ?>">
			<?php require __DIR__ . '/views/payments/payment-form.php'; ?>
		</script>
		<?php
	}

	/**
	 * Output the customers modal.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_customers_modal() {
		$customer = new \EverAccounting\Models\Customer();
		?>
		<script type="text/template" id="eac-customer-modal" data-title="<?php esc_html_e( 'Add Customer', 'wp-ever-accounting' ); ?>">
			<?php require __DIR__ . '/views/customers/customer-form.php'; ?>
		</script>
		<?php
	}
}
