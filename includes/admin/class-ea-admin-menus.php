<?php

defined( 'ABSPATH' ) || exit();

class EAccounting_Admin_Menus{

	/**
	 * EAccounting_Admin_Menus constructor.
	 */
	public function __construct() {
		// Add menus.
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
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
		add_submenu_page( 'ever-accounting', __( 'Dashboard', 'wp-ever-accounting' ), __( 'Dashboard', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-dashboard', array( $this, 'dashboard_page' ) );
		add_submenu_page( 'ever-accounting', __( 'Transactions', 'wp-ever-accounting' ), __( 'Transactions', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-transactions', array( $this, 'dashboard_page' ) );
		add_submenu_page( 'ever-accounting', __( 'Products', 'wp-ever-accounting' ), __( 'Products', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-products', array( $this, 'dashboard_page' ) );
		add_submenu_page( 'ever-accounting', __( 'Invoices', 'wp-ever-accounting' ), __( 'Invoices', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-invoices', array( $this, 'dashboard_page' ) );
		add_submenu_page( 'ever-accounting', __( 'Revenues', 'wp-ever-accounting' ), __( 'Revenues', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-revenues', array( $this, 'dashboard_page' ) );
		add_submenu_page( 'ever-accounting', __( 'Bills', 'wp-ever-accounting' ), __( 'Bills', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-bills', array( $this, 'dashboard_page' ) );
		add_submenu_page( 'ever-accounting', __( 'Payments', 'wp-ever-accounting' ), __( 'Payments', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-payments', array( $this, 'dashboard_page' ) );
		add_submenu_page( 'ever-accounting', __( 'Accounts', 'wp-ever-accounting' ), __( 'Accounts', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-accounts', 'eaccount_accounts_page' );
		add_submenu_page( 'ever-accounting', __( 'Transfers', 'wp-ever-accounting' ), __( 'Transfers', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-transfers', array( $this, 'dashboard_page' ) );
		add_submenu_page( 'ever-accounting', __( 'Reconciliations', 'wp-ever-accounting' ), __( 'Reconciliations', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-reconciliations', array( $this, 'dashboard_page' ) );
		add_submenu_page( 'ever-accounting', __( 'Reports', 'wp-ever-accounting' ), __( 'Reports', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-reports', array( $this, 'dashboard_page' ) );
		add_submenu_page( 'ever-accounting', __( 'Addons', 'wp-ever-accounting' ), __( 'Addons', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-addons', array( $this, 'dashboard_page' ) );
		add_submenu_page( 'ever-accounting', __( 'Helps', 'wp-ever-accounting' ), __( 'Helps', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-helps', array( $this, 'dashboard_page' ) );

	}

	public function dashboard_page(){

	}


	/**
	 * Enqueue scripts
	 * @since 1.0.0
	 */
	public function enqueue_scripts(){
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_media();
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'jquery' );
	}


}

new EAccounting_Admin_Menus();
