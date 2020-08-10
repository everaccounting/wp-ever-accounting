<?php
/**
 * Setup menus in WP admin.
 *
 * @package EverAccounting
 * @subpackage Admin
 * @version 1.0.2
 */

namespace EverAccounting\Admin;
defined( 'ABSPATH' ) || exit;

class Admin_Menus {

	/**
	 * EAccounting_Admin_Menus constructor.
     * @version 1.0.2
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );
		add_action( 'admin_menu', array( $this, 'reports_menu' ), 86 );
		add_action( 'admin_menu', array( $this, 'tools_menu' ), 88 );
		//add_action( 'admin_menu', array( $this, 'status_menu' ), 60 );
		add_action( 'admin_menu', array( $this, 'settings_menu' ), 90 );
	}

	/**
	 * Add menu items.
     * @version 1.0.2
	 */
	public function admin_menu() {
		global $menu;

//		if ( current_user_can( 'edit_others_shop_orders' ) ) {
//			$menu[] = array( '', 'read', 'separator-woocommerce', '', 'wp-menu-separator woocommerce' ); // WPCS: override ok.
//		}

		add_menu_page( __( 'Accounting', 'wp-ever-accounting' ), __( 'Accounting', 'wp-ever-accounting' ), 'manage_options', 'eaccounting', null, 'dashicons-chart-area', '54.5' );
		$overview     = add_submenu_page( 'eaccounting', __( 'Overview', 'wp-ever-accounting' ), __( 'Overview', 'wp-ever-accounting' ), 'manage_options', 'eaccounting', 'eaccounting_admin_overview_page' );
		$transactions = add_submenu_page( 'eaccounting', __( 'Transactions', 'wp-ever-accounting' ), __( 'Transactions', 'wp-ever-accounting' ), 'manage_options', 'ea-transactions', 'eaccounting_admin_transactions_page' );
		$sales        = add_submenu_page( 'eaccounting', __( 'Sales', 'wp-ever-accounting' ), __( 'Sales', 'wp-ever-accounting' ), 'manage_options', 'ea-sales', 'eaccounting_admin_sales_page' );
		$expenses     = add_submenu_page( 'eaccounting', __( 'Expenses', 'wp-ever-accounting' ), __( 'Expenses', 'wp-ever-accounting' ), 'manage_options', 'ea-expenses', 'eaccounting_admin_expenses_page' );
		$banking      = add_submenu_page( 'eaccounting', __( 'Banking', 'wp-ever-accounting' ), __( 'Banking', 'wp-ever-accounting' ), 'manage_options', 'ea-banking', 'eaccounting_admin_banking_page' );

		add_action( 'load-' . $sales, 'eaccounting_load_sales_page' );
		add_action( 'load-' . $expenses, 'eaccounting_load_expenses_page' );
		add_action( 'load-' . $banking, 'eaccounting_load_banking_page' );
	}

	public function tools_menu() {
		$tools = add_submenu_page( 'eaccounting', __( 'Tools', 'wp-ever-accounting' ), __( 'Tools', 'wp-ever-accounting' ), 'manage_options', 'ea-tools', 'eaccounting_admin_tools_page' );
		add_action( 'load-' . $tools, 'eaccounting_load_tools_page' );
	}

	public function reports_menu() {
		$reports = add_submenu_page( 'eaccounting', __( 'Reports', 'wp-ever-accounting' ), __( 'Reports', 'wp-ever-accounting' ), 'manage_options', 'ea-reports', 'eaccounting_admin_reports_page' );
		add_action( 'load-' . $reports, 'eaccounting_load_reports_page' );
	}

	public function settings_menu() {
		add_submenu_page( 'eaccounting', __( 'Settings', 'wp-ever-accounting' ), __( 'Settings', 'wp-ever-accounting' ), 'manage_options', 'ea-settings', 'eaccounting_admin_settings_page' );
	}

//	public function status_menu() {
//		add_submenu_page( 'eaccounting', __( 'Status', 'wp-ever-accounting' ), __( 'Status', 'wp-ever-accounting' ), 'manage_options', 'ea-status', 'eaccounting_admin_dashboard' );
//	}
}

return new Admin_Menus();
