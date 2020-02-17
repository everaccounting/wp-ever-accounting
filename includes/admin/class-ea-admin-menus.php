<?php

defined( 'ABSPATH' ) || exit();

class EAccounting_Admin_Menus {

	/**
	 * EAccounting_Admin_Menus constructor.
	 */
	public function __construct() {
		// Add menus.
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 10 );
//		add_action( 'eaccounting_misc_tab_categories', array( $this, 'categories_page' ) );
//		add_action( 'eaccounting_misc_tab_taxes', array( $this, 'taxes_page' ) );
//		add_action( 'eaccounting_misc_tab_currencies', array( $this, 'currencies_page' ) );
//		add_action( 'eaccounting_inventory_tab_items', array( $this, 'items_page' ) );
		add_action( 'wp_loaded', array( 'EAccounting_Settings', 'save' ) );

		//banking
//		add_action( 'eaccounting_banking_tab_accounts', array( $this, 'accounts_tab' ) );
	}

	/**
	 * Add menu items.
	 */
	public function admin_menu() {
		global $menu;

		if ( current_user_can( 'manage_options' ) ) {
			$menu[] = array( '', 'read', 'separator-eaccounting', '', 'wp-menu-separator eaccounting' );
		}

		add_menu_page( __( 'Accounting', 'wp-ever-accounting' ), __( 'Accounting', 'wp-ever-accounting' ), 'manage_options', 'eaccounting',  array( __CLASS__, 'page_wrapper' ), 'dashicons-chart-area', '55.5' );
		add_submenu_page( 'eaccounting', __( 'Accounting', 'wp-ever-accounting' ), __( 'Dashboard', 'wp-ever-accounting' ), 'manage_options', 'eaccounting', array( __CLASS__, 'page_wrapper' ) );
		add_submenu_page( 'eaccounting', __( 'Accounting', 'wp-ever-accounting' ), __( 'Transactions', 'wp-ever-accounting' ), 'manage_options', 'eaccounting#/transactions', array( $this, 'page_wrapper' ) );
		add_submenu_page( 'eaccounting', __( 'Accounting', 'wp-ever-accounting' ), __( 'Contacts', 'wp-ever-accounting' ), 'manage_options', 'eaccounting#/contacts', array( $this, 'page_wrapper' ) );
//		add_submenu_page( 'ever-accounting', __( 'Inventory', 'wp-ever-accounting' ), __( 'Inventory', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-inventory', array( $this, 'inventory_page' ) );
//		add_submenu_page( 'ever-accounting', __( 'Invoices', 'wp-ever-accounting' ), __( 'Invoices', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-invoices', array( $this, 'invoices_page' ) );
		add_submenu_page( 'eaccounting', __( 'Accounting', 'wp-ever-accounting' ), __( 'Payments', 'wp-ever-accounting' ), 'manage_options', 'eaccounting#/payments', array( $this, 'page_wrapper' ) );
		add_submenu_page( 'eaccounting', __( 'Accounting', 'wp-ever-accounting' ), __( 'Revenues', 'wp-ever-accounting' ), 'manage_options', 'eaccounting#/revenues', array( $this, 'page_wrapper' ) );
		add_submenu_page( 'eaccounting', __( 'Accounting', 'wp-ever-accounting' ), __( 'Accounts', 'wp-ever-accounting' ), 'manage_options', 'eaccounting#/accounts', array( $this, 'page_wrapper' ) );
//		add_submenu_page( 'ever-accounting', __( 'Banking', 'wp-ever-accounting' ), __( 'Banking', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-banking', array( $this, 'banking_page' ) );
//		add_submenu_page( 'ever-accounting', __( 'Transfers', 'wp-ever-accounting' ), __( 'Transfers', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-transfers', array( $this, 'transfers_page' ) );
//		add_submenu_page( 'ever-accounting', __( 'Misc', 'wp-ever-accounting' ), __( 'Misc', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-misc', array( $this, 'misc_page' ) );
		add_submenu_page( 'eaccounting', __( 'Reports', 'wp-ever-accounting' ), __( 'Reports', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-reports', array( $this, 'reports_page' ) );
		$help = '<span style="color:#ff7a03;">' . __( 'Help', 'wp-ever-accounting' ) . '</span>';
		add_submenu_page( 'eaccounting', '', $help, 'manage_options', 'eaccounting-help', array( $this, 'help_page' ) );
		add_submenu_page( 'eaccounting', __( 'Settings', 'wp-ever-accounting' ), __( 'Settings', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-settings', array( $this, 'settings_page' ) );
	}

	/**
	 * Wrapper for all page
	 *
	 * @since 1.0.0
	 */
	public static function page_wrapper() {
		?>
		<div class="wrap eaccounting">
			<div id="eaccounting"></div>
		</div>
		<?php
	}

	/**
	 * Render dashboard page
	 * since 1.0.0
	 */
	public function dashboard_page() {
		wp_enqueue_script( 'eaccounting-dashboard' );
		eaccounting_get_views( 'dashboard-page.php' );
	}

	public function transaction_page() {
		eaccounting_get_views( 'transactions-page.php' );
	}

	/**
	 * render contact page
	 * since 1.0.0
	 */
	public function contacts_page() {
		eaccounting_get_views( 'contacts-page.php' );
	}

	public function inventory_page() {
		eaccounting_get_views( 'inventory-page.php' );
	}

	public function items_page() {
		eaccounting_get_views( 'inventory/items-tab.php' );
	}

	public function revenues_page() {
		eaccounting_get_views( 'revenue-page.php' );
	}

	public function invoices_page() {
		eaccounting_get_views( 'invoice-page.php' );
	}

	public function payments_page() {
		eaccounting_get_views( 'payment-page.php' );
	}

	public function banking_page() {
		eaccounting_get_views( 'banking-page.php' );
	}

	public function transfers_page() {
		eaccounting_get_views( 'transfers-page.php' );
	}

	public function misc_page() {
		eaccounting_get_views( 'misc-page.php' );
	}

	public function categories_page() {
		eaccounting_get_views( 'misc/categories-page.php' );
	}

	public function reports_page() {
		wp_enqueue_script( 'chart-js' );
		eaccounting_get_views( 'reports-page.php' );
	}

	public function help_page() {
		if ( isset( $_GET['page'] ) && 'eaccounting-help' === $_GET['page'] ) {
			wp_redirect( 'https://pluginever.com/docs/wp-ever-accounting?utm_source=wpmenu&utm_medium=admindash&utm_campaign=eaccounting' );
			die;
		}
	}

	public function taxes_page() {
		eaccounting_get_views( 'misc/tax-page.php' );
	}

	public function currencies_page() {
		eaccounting_get_views( 'misc/currencies-page.php' );
	}

	public function accounts_tab() {
		eaccounting_get_views( 'banking/accounts-tab.php' );
	}

	public function settings_page() {
		EAccounting_Settings::output();
	}
}

new EAccounting_Admin_Menus();
