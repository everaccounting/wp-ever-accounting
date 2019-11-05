<?php

defined( 'ABSPATH' ) || exit();

class EAccounting_Admin_Menus{

	/**
	 * EAccounting_Admin_Menus constructor.
	 */
	public function __construct() {
		// Add menus.
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );
	}

	/**
	 * Add menu items.
	 */
	public function admin_menu() {
		global $menu;

		if ( current_user_can( 'manage_options' ) ) {
			$menu[] = array( '', 'read', 'separator-eaccounting', '', 'wp-menu-separator eaccounting' );
		}

		add_menu_page( __( 'Accounting', 'wp-ever-accounting' ), __( 'Accounting', 'wp-ever-accounting' ), 'manage_options', 'ever-accounting', null, 'dashicons-chart-area', '55.5' );
		add_submenu_page( 'ever-accounting', __( 'Dashboard', 'wp-ever-accounting' ), __( 'Dashboard', 'wp-ever-accounting' ), 'manage_options', 'ea-dashboard', array( $this, 'dashboard_page' ) );
		add_submenu_page( 'ever-accounting', __( 'Transactions', 'wp-ever-accounting' ), __( 'Transactions', 'wp-ever-accounting' ), 'manage_options', 'ea-transactions', array( $this, 'dashboard_page' ) );
		add_submenu_page( 'ever-accounting', __( 'Products', 'wp-ever-accounting' ), __( 'Products', 'wp-ever-accounting' ), 'manage_options', 'ea-products', array( $this, 'dashboard_page' ) );
		add_submenu_page( 'ever-accounting', __( 'Invoices', 'wp-ever-accounting' ), __( 'Invoices', 'wp-ever-accounting' ), 'manage_options', 'ea-invoices', array( $this, 'dashboard_page' ) );
		add_submenu_page( 'ever-accounting', __( 'Revenues', 'wp-ever-accounting' ), __( 'Revenues', 'wp-ever-accounting' ), 'manage_options', 'ea-revenues', array( $this, 'dashboard_page' ) );
		add_submenu_page( 'ever-accounting', __( 'Bills', 'wp-ever-accounting' ), __( 'Bills', 'wp-ever-accounting' ), 'manage_options', 'ea-bills', array( $this, 'dashboard_page' ) );
		add_submenu_page( 'ever-accounting', __( 'Payments', 'wp-ever-accounting' ), __( 'Payments', 'wp-ever-accounting' ), 'manage_options', 'ea-payments', array( $this, 'dashboard_page' ) );
		add_submenu_page( 'ever-accounting', __( 'Accounts', 'wp-ever-accounting' ), __( 'Accounts', 'wp-ever-accounting' ), 'manage_options', 'ea-accounts', array( $this, 'dashboard_page' ) );
		add_submenu_page( 'ever-accounting', __( 'Transfers', 'wp-ever-accounting' ), __( 'Transfers', 'wp-ever-accounting' ), 'manage_options', 'ea-transfers', array( $this, 'dashboard_page' ) );
		add_submenu_page( 'ever-accounting', __( 'Reconciliations', 'wp-ever-accounting' ), __( 'Reconciliations', 'wp-ever-accounting' ), 'manage_options', 'ea-reconciliations', array( $this, 'dashboard_page' ) );
		add_submenu_page( 'ever-accounting', __( 'Reports', 'wp-ever-accounting' ), __( 'Reports', 'wp-ever-accounting' ), 'manage_options', 'ea-reports', array( $this, 'dashboard_page' ) );
		add_submenu_page( 'ever-accounting', __( 'Addons', 'wp-ever-accounting' ), __( 'Addons', 'wp-ever-accounting' ), 'manage_options', 'ea-addons', array( $this, 'dashboard_page' ) );
		add_submenu_page( 'ever-accounting', __( 'Helps', 'wp-ever-accounting' ), __( 'Helps', 'wp-ever-accounting' ), 'manage_options', 'ea-helps', array( $this, 'dashboard_page' ) );
		add_submenu_page( 'ever-accounting', __( 'Settings', 'wp-ever-accounting' ), __( 'Settings', 'wp-ever-accounting' ), 'manage_options', 'ea-settings', array( $this, 'dashboard_page' ) );
	}

	public function dashboard_page(){

	}
}

new EAccounting_Admin_Menus();
