<?php

defined( 'ABSPATH' ) || exit();

class EAccounting_Admin_Menus {

	/**
	 * EAccounting_Admin_Menus constructor.
	 */
	public function __construct() {
		// Add menus.
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 10 );
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
		add_submenu_page( 'ever-accounting', __( 'Dashboard', 'wp-ever-accounting' ), __( 'Dashboard', 'wp-ever-accounting' ), 'manage_options', 'ever-accounting', array( $this, 'dashboard_page' ) );
		add_submenu_page( 'ever-accounting', __( 'Transactions', 'wp-ever-accounting' ), __( 'Transactions', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-transactions', array( $this, 'transaction_page' ) );
		add_submenu_page( 'ever-accounting', __( 'Contacts', 'wp-ever-accounting' ), __( 'Contacts', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-contacts', array($this, 'contacts_page') );
		add_submenu_page( 'ever-accounting', __( 'Payments', 'wp-ever-accounting' ), __( 'Payments', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-payments', array( $this, 'payments_page') );
		add_submenu_page( 'ever-accounting', __( 'Revenues', 'wp-ever-accounting' ), __( 'Revenues', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-revenues', array( $this, 'revenues_page') );
		add_submenu_page( 'ever-accounting', __( 'Accounts', 'wp-ever-accounting' ), __( 'Accounts', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-accounts', array( $this, 'accounts_page') );
		add_submenu_page( 'ever-accounting', __( 'Transfers', 'wp-ever-accounting' ), __( 'Transfers', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-transfers', array( $this, 'transfers_page') );
		add_submenu_page( 'ever-accounting', __( 'Categories', 'wp-ever-accounting' ), __( 'Categories', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-categories', array( $this, 'categories_page') );
	}


	/**
	 * Render dashboard page
	 * since 1.0.0
	 */
	public function dashboard_page(){
		wp_enqueue_script('chart-js');
		eaccounting_get_views('dashboard-page.php');
	}

	public function transaction_page(){
		eaccounting_get_views('transactions-page.php');
	}

	/**
	 * render contact page
	 * since 1.0.0
	 */
	public function contacts_page(){
		eaccounting_get_views('contacts-page.php');
	}

	public function revenues_page(){
		eaccounting_get_views('revenue-page.php');
	}

	public function payments_page(){
		eaccounting_get_views('payment-page.php');
	}

	public function accounts_page(){
		eaccounting_get_views('accounts-page.php');
	}

	public function transfers_page(){
		eaccounting_get_views('transfers-page.php');
	}

	public function categories_page(){
		eaccounting_get_views('categories-page.php');
	}

}

new EAccounting_Admin_Menus();
