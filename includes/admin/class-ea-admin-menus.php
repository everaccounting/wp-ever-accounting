<?php
/**
 * Setup menus in WP admin.
 *
 * @package EverAccounting\Admin
 * @version 1.0.2
 */

defined( 'ABSPATH' ) || exit;

class EAccounting_Admin_Menus {

	/**
	 * EAccounting_Admin_Menus constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );
		add_action( 'admin_menu', array( $this, 'reports_menu' ), 20 );
		add_action( 'admin_menu', array( $this, 'tools_menu' ), 50 );
		add_action( 'admin_menu', array( $this, 'status_menu' ), 60 );
		add_action( 'admin_menu', array( $this, 'settings_menu' ), 80 );
	}

	/**
	 * Add menu items.
	 */
	public function admin_menu() {
		global $menu;

//		if ( current_user_can( 'edit_others_shop_orders' ) ) {
//			$menu[] = array( '', 'read', 'separator-woocommerce', '', 'wp-menu-separator woocommerce' ); // WPCS: override ok.
//		}

		add_menu_page( __( 'Accounting', 'wp-ever-accounting' ), __( 'Accounting', 'wp-ever-accounting' ), 'manage_options', 'eaccounting', null, 'dashicons-chart-area', '54.5' );
		$overview     = add_submenu_page( 'eaccounting', __( 'Overview', 'wp-ever-accounting' ), __( 'Overview', 'wp-ever-accounting' ), 'manage_options', 'eaccounting', 'eaccounting_admin_dashboard' );
		$transactions = add_submenu_page( 'eaccounting', __( 'Transactions', 'wp-ever-accounting' ), __( 'Transactions', 'wp-ever-accounting' ), 'manage_options', 'ea-transactions', 'eaccounting_admin_dashboard' );
		$sales        = add_submenu_page( 'eaccounting', __( 'Sales', 'wp-ever-accounting' ), __( 'Sales', 'wp-ever-accounting' ), 'manage_options', 'ea-sales', 'eaccounting_admin_dashboard' );
		$purchases    = add_submenu_page( 'eaccounting', __( 'Purchases', 'wp-ever-accounting' ), __( 'Purchases', 'wp-ever-accounting' ), 'manage_options', 'ea-purchases', 'eaccounting_admin_dashboard' );
		$banking      = add_submenu_page( 'eaccounting', __( 'Banking', 'wp-ever-accounting' ), __( 'Banking', 'wp-ever-accounting' ), 'manage_options', 'ea-banking', 'eaccounting_admin_banking' );

		add_action( 'load-' . $overview, 'eaccounting_overview_screen_options' );
		add_action( 'load-' . $transactions, 'eaccounting_transactions_screen_options' );
		add_action( 'load-' . $sales, 'eaccounting_sales_screen_options' );
		add_action( 'load-' . $purchases, 'eaccounting_purchases_screen_options' );
		add_action( 'load-' . $banking, 'eaccounting_baking_screen_options' );
	}

	public function tools_menu() {
		add_submenu_page( 'eaccounting', __( 'Tools', 'wp-ever-accounting' ), __( 'Tools', 'wp-ever-accounting' ), 'manage_options', 'ea-tools', 'eaccounting_admin_dashboard' );
	}

	public function reports_menu() {
		add_submenu_page( 'eaccounting', __( 'Reports', 'wp-ever-accounting' ), __( 'Reports', 'wp-ever-accounting' ), 'manage_options', 'ea-reports', 'eaccounting_admin_dashboard' );
	}

	public function settings_menu() {
		add_submenu_page( 'eaccounting', __( 'Settings', 'wp-ever-accounting' ), __( 'Settings', 'wp-ever-accounting' ), 'manage_options', 'ea-settings', 'eaccounting_admin_dashboard' );
	}

	public function status_menu() {
		add_submenu_page( 'eaccounting', __( 'Status', 'wp-ever-accounting' ), __( 'Status', 'wp-ever-accounting' ), 'manage_options', 'ea-status', 'eaccounting_admin_dashboard' );
	}


}

return new EAccounting_Admin_Menus();
