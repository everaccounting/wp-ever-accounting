<?php
/**
 * Handles admin related menus.
 */

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit();

/**
 * Class Menu
 * @package EverAccounting\Admin
 */
class Menu {

	/**
	 * Menu constructor.
	 */
	public function __construct() {
		//Register menus.
		add_action( 'admin_menu', array( $this, 'register_parent_page' ), 1 );
		add_action( 'admin_menu', array( $this, 'register_items_page' ), 20 );
		add_action( 'admin_menu', array( $this, 'register_sales_page' ), 30 );
		add_action( 'admin_menu', array( $this, 'register_expenses_page' ), 40 );
		add_action( 'admin_menu', array( $this, 'register_banking_page' ), 50 );

		//Register tabs.
		add_action( 'eaccounting_items_page_tab_items', array( $this, 'render_items_tab' ), 20 );
		add_action( 'eaccounting_sales_page_tab_revenues', array( $this, 'render_revenues_tab' ) );
		add_action( 'eaccounting_sales_page_tab_invoices', array( $this, 'render_invoices_tab' ), 20 );
		add_action( 'eaccounting_sales_page_tab_customers', array( $this, 'render_customers_tab' ) );
		add_action( 'eaccounting_expenses_page_tab_payments', array( $this, 'render_payments_tab' ) );
		add_action( 'eaccounting_expenses_page_tab_bills', array( $this, 'render_bills_tab' ), 20 );
		add_action( 'eaccounting_expenses_page_tab_vendors', array( $this, 'render_vendors_tab' ) );
		add_action( 'eaccounting_banking_page_tab_accounts', array( $this, 'render_accounts_tab' ) );
		add_action( 'eaccounting_banking_page_tab_transactions', array( $this, 'render_transactions_tab' ), 20 );
		add_action( 'eaccounting_banking_page_tab_transfers', array( $this, 'render_transfers_tab' ) );
	}

	/**
	 * Registers the overview page.
	 *
	 * @since 1.1.0
	 */
	public function register_parent_page() {
		global $menu;

		if ( current_user_can( 'manage_eaccounting' ) ) {
			$menu[] = array( '', 'read', 'ea-separator', '', 'wp-menu-separator accounting' );
		}
		$icons = 'data:image/svg+xml;base64,' . base64_encode( file_get_contents( eaccounting()->plugin_path( 'assets/images/icon.svg' ) ) );

		add_menu_page(
			__( 'Accounting', 'wp-ever-accounting' ),
			__( 'Accounting', 'wp-ever-accounting' ),
			'manage_eaccounting',
			'eaccounting',
			null,
			$icons,
			'54.5'
		);
		add_submenu_page(
			'eaccounting',
			__( 'Overview', 'wp-ever-accounting' ),
			__( 'Overview', 'wp-ever-accounting' ),
			'manage_eaccounting',
			'eaccounting',
			array( $this, 'render_overview_page' )
		);
	}

	/**
	 * Registers the items page.
	 *
	 */
	public function register_items_page() {
		add_submenu_page(
			'eaccounting',
			__( 'Items', 'wp-ever-accounting' ),
			__( 'Items', 'wp-ever-accounting' ),
			'manage_eaccounting',
			'ea-items',
			array( $this, 'render_items_page' )
		);
	}

	/**
	 * Registers the sales page.
	 *
	 */
	public function register_sales_page() {
		add_submenu_page(
			'eaccounting',
			__( 'Sales', 'wp-ever-accounting' ),
			__( 'Sales', 'wp-ever-accounting' ),
			'manage_eaccounting',
			'ea-sales',
			array( $this, 'render_sales_page' )
		);
	}

	/**
	 * Registers the expenses page.
	 *
	 */
	public function register_expenses_page() {
		add_submenu_page(
			'eaccounting',
			__( 'Expenses', 'wp-ever-accounting' ),
			__( 'Expenses', 'wp-ever-accounting' ),
			'manage_eaccounting',
			'ea-expenses',
			array( $this, 'render_expenses_page' )
		);
	}

	/**
	 * Registers the banking page.
	 *
	 */
	public function register_banking_page() {
		add_submenu_page(
			'eaccounting',
			__( 'Banking', 'wp-ever-accounting' ),
			__( 'Banking', 'wp-ever-accounting' ),
			'manage_eaccounting',
			'ea-banking',
			array( $this, 'render_banking_page' )
		);
	}

	/**
	 * Render overview page.
	 *
	 * @since 1.1.0
	 */
	public function render_overview_page() {
		include dirname( __FILE__ ) . '/views/admin-page-overview.php';
	}

	/**
	 * Render items page.
	 *
	 * @since 1.1.0
	 */
	public function render_items_page() {
		$tabs = array();
		if ( current_user_can( 'ea_manage_item' ) ) {
			$tabs['items'] = __( 'Items', 'wp-ever-accounting' );
		}
		$tabs        = apply_filters( 'eaccounting_item_tabs', $tabs );
		$first_tab   = current( array_keys( $tabs ) );
		$current_tab = ! empty( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $tabs ) ? sanitize_title( $_GET['tab'] ) : $first_tab;
		include dirname( __FILE__ ) . '/views/admin-page-items.php';
	}

	/**
	 * Render sales page.
	 *
	 * @since 1.1.0
	 */
	public function render_sales_page() {
		$tabs = array();
		if ( current_user_can( 'ea_manage_revenue' ) ) {
			$tabs['revenues'] = __( 'Revenues', 'wp-ever-accounting' );
		}
		if ( current_user_can( 'ea_manage_invoice' ) ) {
			$tabs['invoices'] = __( 'Invoices', 'wp-ever-accounting' );
		}
		if ( current_user_can( 'ea_manage_customer' ) ) {
			$tabs['customers'] = __( 'Customers', 'wp-ever-accounting' );
		}
		$tabs = apply_filters( 'eaccounting_sales_tabs', $tabs );

		$first_tab   = current( array_keys( $tabs ) );
		$current_tab = ! empty( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $tabs ) ? sanitize_title( $_GET['tab'] ) : $first_tab;
		include dirname( __FILE__ ) . '/views/admin-page-sales.php';
	}

	/**
	 * Render page.
	 *
	 * @since 1.1.0
	 */
	public function render_expenses_page() {
		$tabs = array();
		if ( current_user_can( 'ea_manage_payment' ) ) {
			$tabs['payments'] = __( 'Payments', 'wp-ever-accounting' );
		}
		if ( current_user_can( 'ea_manage_bill' ) ) {
			$tabs['bills'] = __( 'Bills', 'wp-ever-accounting' );
		}
		if ( current_user_can( 'ea_manage_vendor' ) ) {
			$tabs['vendors'] = __( 'Vendors', 'wp-ever-accounting' );
		}
		$tabs =  apply_filters( 'eaccounting_expenses_tabs', $tabs );

		$first_tab   = current( array_keys( $tabs ) );
		$current_tab = ! empty( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $tabs ) ? sanitize_title( $_GET['tab'] ) : $first_tab;
		if ( empty( $_GET['tab'] ) ) {
			wp_redirect(
				add_query_arg(
					array(
						'page' => 'ea-expenses',
						'tab'  => $current_tab,
					),
					admin_url( 'admin.php' )
				)
			);
			exit();
		}
		include dirname( __FILE__ ) . '/views/admin-page-expenses.php';
	}

	/**
	 * Render banking page.
	 *
	 * @since 1.1.0
	 */
	public function render_banking_page() {
		$tabs = array();
		if ( current_user_can( 'ea_manage_account' ) ) {
			$tabs['accounts'] = __( 'Accounts', 'wp-ever-accounting' );
		}
		if ( current_user_can( 'ea_manage_payment' ) && current_user_can( 'ea_manage_revenue' ) ) {
			$tabs['transactions'] = __( 'Transactions', 'wp-ever-accounting' );
		}
		if ( current_user_can( 'ea_manage_transfer' ) ) {
			$tabs['transfers'] = __( 'Transfers', 'wp-ever-accounting' );
		}

		$tabs =  apply_filters( 'eaccounting_banking_tabs', $tabs );

		$first_tab   = current( array_keys( $tabs ) );
		$current_tab = ! empty( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $tabs ) ? sanitize_title( $_GET['tab'] ) : $first_tab;
		include dirname( __FILE__ ) . '/views/admin-page-banking.php';
	}

	/**
	 * Render Items tab.
	 *
	 * @since 1.1.0
	 */
	public function render_items_tab() {
		$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
		if ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
			$item_id = isset( $_GET['item_id'] ) ? absint( $_GET['item_id'] ) : null;
			include dirname( __FILE__ ) . '/views/items/edit-item.php';
		} else {
			include dirname( __FILE__ ) . '/views/items/list-item.php';
		}
	}

	/**
	 * Render revenues tab.
	 *
	 * @since 1.1.0
	 */
	public function render_revenues_tab() {
		$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
		if ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
			$invoice_id = isset( $_GET['revenue_id'] ) ? absint( $_GET['revenue_id'] ) : null;
			include dirname( __FILE__ ) . '/views/revenues/edit-revenue.php';
		} else {
			include dirname( __FILE__ ) . '/views/revenues/list-revenue.php';
		}
	}

	/**
	 * Render tab.
	 *
	 * @since 1.1.0
	 */
	public function render_invoices_tab() {
		$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
		if ( in_array( $requested_view, array( 'view' ), true ) && ! empty( $_GET['invoice_id'] ) ) {
			$invoice_id = isset( $_GET['invoice_id'] ) ? absint( $_GET['invoice_id'] ) : null;
			Invoice_Actions::view_invoice( $invoice_id );
		} elseif ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
			$invoice_id = isset( $_GET['invoice_id'] ) ? absint( $_GET['invoice_id'] ) : null;
			Invoice_Actions::edit_invoice( $invoice_id );
		} else {
			include dirname( __FILE__ ) . '/views/invoices/list-invoice.php';
		}
	}

	/**
	 * Render customers tab.
	 *
	 * @since 1.1.0
	 */
	public function render_customers_tab(){
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

	/**
	 * Render payments tab.
	 *
	 * @since 1.1.0
	 */
	public function render_payments_tab(){
		$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
		if ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
			$payment_id = isset( $_GET['payment_id'] ) ? absint( $_GET['payment_id'] ) : null;
			include dirname( __FILE__ ) . '/views/payments/edit-payment.php';
		} else {
			include dirname( __FILE__ ) . '/views/payments/list-payment.php';
		}
	}

	/**
	 * Render bills tab
	 *
	 * @since 1.1.0
	 */
	public function render_bills_tab() {
		$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
		if ( in_array( $requested_view, array( 'view' ), true ) && ! empty( $_GET['bill_id'] ) ) {
			$bill_id = isset( $_GET['bill_id'] ) ? absint( $_GET['bill_id'] ) : null;
			Bill_Actions::view_bill( $bill_id );
		} elseif ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
			$bill_id = isset( $_GET['bill_id'] ) ? absint( $_GET['bill_id'] ) : null;
			Bill_Actions::edit_bill( $bill_id );
		} else {
			include dirname( __FILE__ ) . '/views/bills/list-bill.php';
		}
	}

	/**
	 * Render vendors tab.
	 *
	 * @since 1.1.0
	 */
	public function render_vendors_tab(){
		$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
		if ( in_array( $requested_view, array( 'view' ), true ) && ! empty( $_GET['vendor_id'] ) ) {
			$vendor_id = isset( $_GET['vendor_id'] ) ? absint( $_GET['vendor_id'] ) : null;
			include dirname( __FILE__ ) . '/views/vendors/view-vendor.php';
		} elseif ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
			$vendor_id = isset( $_GET['vendor_id'] ) ? absint( $_GET['vendor_id'] ) : null;
			include dirname( __FILE__ ) . '/views/vendors/edit-vendor.php';
		} else {
			include dirname( __FILE__ ) . '/views/vendors/list-vendor.php';
		}
	}

	/**
	 * Render accounts tab.
	 *
	 * @since 1.1.0
	 */
	public function render_accounts_tab(){
		$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
		if ( in_array( $requested_view, array( 'view' ), true ) && ! empty( $_GET['account_id'] ) ) {
			$account_id = isset( $_GET['account_id'] ) ? absint( $_GET['account_id'] ) : null;
			include dirname( __FILE__ ) . '/views/accounts/view-account.php';
		} elseif ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
			$account_id = isset( $_GET['account_id'] ) ? absint( $_GET['account_id'] ) : null;
			include dirname( __FILE__ ) . '/views/accounts/edit-account.php';
		} else {
			include dirname( __FILE__ ) . '/views/accounts/list-account.php';
		}
	}

	/**
	 * Render transactions tab.
	 *
	 * @since 1.1.0
	 */
	public function render_transactions_tab(){
		include dirname( __FILE__ ) . '/views/transactions/list-transactions.php';
	}

	/**
	 * Render transfers tab.
	 *
	 * @since 1.1.0
	 */
	public function render_transfers_tab(){
		$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
		if ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
			$transfer_id = isset( $_GET['transfer_id'] ) ? absint( $_GET['transfer_id'] ) : null;
			include dirname( __FILE__ ) . '/views/transfers/edit-transfer.php';
		} else {
			include dirname( __FILE__ ) . '/views/transfers/list-transfer.php';
		}
	}

}

new Menu();
