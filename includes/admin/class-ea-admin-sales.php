<?php
/**
 * EverAccounting Admin Sales Page.
 *
 * @package     EverAccounting
 * @subpackage  Admin
 * @version     1.1.0
 */

defined( 'ABSPATH' ) || exit();

class EAccounting_Admin_Sales {

	/**
	 * EAccounting_Admin_Sales constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_page' ), 30 );
		add_action( 'eaccounting_sales_page_tab_invoices', array( $this, 'render_invoices_tab' ) );
		add_action( 'eaccounting_sales_page_tab_revenues', array( $this, 'render_revenues_tab' ) );
		add_action( 'eaccounting_sales_page_tab_customers', array( $this, 'render_customers_tab' ) );
	}

	/**
	 * Registers the reports page.
	 *
	 */
	public function register_page() {
		add_submenu_page(
			'eaccounting',
			__( 'Sales', 'wp-ever-accounting' ),
			__( 'Sales', 'wp-ever-accounting' ),
			'manage_eaccounting',
			'ea-sales',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Get banking page tabs.
	 *
	 * @since 1.1.0
	 * @return array
	 */
	public function get_tabs() {
		$tabs = array();
		if ( current_user_can( 'ea_manage_invoice' ) ) {
			$tabs['invoices'] = __( 'Invoices', 'wp-ever-accounting' );
		}
		if ( current_user_can( 'ea_manage_revenue' ) ) {
			$tabs['revenues'] = __( 'Revenues', 'wp-ever-accounting' );
		}
		if ( current_user_can( 'ea_manage_customer' ) ) {
			$tabs['customers'] = __( 'Customers', 'wp-ever-accounting' );
		}

		return apply_filters( 'eaccounting_sales_tabs', $tabs );
	}

	/**
	 * Render page.
	 *
	 * @since 1.1.0
	 */
	public function render_page() {
		$tabs        = $this->get_tabs();
		$first_tab   = current( array_keys( $tabs ) );
		$current_tab = ! empty( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $tabs ) ? sanitize_title( $_GET['tab'] ) : $first_tab;
		include dirname( __FILE__ ) . '/views/admin-page-sales.php';
	}

	/**
	 * Render invoice tab.
	 *
	 * @since 1.1.0
	 */
	public function render_invoices_tab() {
		$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
		if ( in_array( $requested_view, array( 'view' ), true ) && ! empty( $_GET['invoice_id'] ) ) {
			$invoice_id = isset( $_GET['invoice_id'] ) ? absint( $_GET['invoice_id'] ) : null;
			include dirname( __FILE__ ) . '/views/invoices/view-invoice.php';
		} elseif ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
			$invoice_id = isset( $_GET['invoice_id'] ) ? absint( $_GET['invoice_id'] ) : null;
			include dirname( __FILE__ ) . '/views/invoices/edit-invoice.php';
		} else {
			include dirname( __FILE__ ) . '/views/invoices/list-invoice.php';
		}
	}

	/**
	 * Render revenues tab.
	 *
	 * @since 1.1.0
	 */
	public function render_revenues_tab(){
		$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
		if ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
			$invoice_id = isset( $_GET['revenue_id'] ) ? absint( $_GET['revenue_id'] ) : null;
			include dirname( __FILE__ ) . '/views/revenues/edit-revenue.php';
		} else {
			include dirname( __FILE__ ) . '/views/revenues/list-revenue.php';
		}
	}

	/**
	 * Render customer tab.
	 *
	 * @since 1.1.0
	 */
	public function render_customers_tab() {
		$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
		if ( in_array( $requested_view, array( 'view' ), true ) && ! empty( $_GET['customer_id'] ) ) {
			$customer_id = isset( $_GET['customer_id'] ) ? absint( $_GET['customer_id'] ) : null;
			include dirname( __FILE__ ) . '/views/customers/view-customer.php';
		} elseif ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
			$customer_id = isset( $_GET['customer_id'] ) ? absint( $_GET['customer_id'] ) : null;
			include dirname( __FILE__ ) . '/views/customers/edit-customer.php';
		} else {
			include dirname( __FILE__ ) . '/views/customers/list-customer.php';
		}
	}

}

new EAccounting_Admin_Sales();
