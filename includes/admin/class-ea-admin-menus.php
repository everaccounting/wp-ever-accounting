<?php

defined( 'ABSPATH' ) || exit();

class EAccounting_Admin_Menus {

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
		add_submenu_page( 'ever-accounting', __( 'Dashboard', 'wp-ever-accounting' ), __( 'Dashboard', 'wp-ever-accounting' ), 'manage_options', 'ever-accounting', array(
			$this,
			'dashboard_page'
		) );
		add_submenu_page( 'ever-accounting', __( 'Transactions', 'wp-ever-accounting' ), __( 'Transactions', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-transactions', array(
			$this,
			'dashboard_page'
		) );
		add_submenu_page( 'ever-accounting', __( 'Products', 'wp-ever-accounting' ), __( 'Products', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-products', 'eaccounting_products_page' );
		add_submenu_page( 'ever-accounting', __( 'Contacts', 'wp-ever-accounting' ), __( 'Contacts', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-contacts', 'eaccounting_contacts_page' );
		add_submenu_page( 'ever-accounting', __( 'Income', 'wp-ever-accounting' ), __( 'Income', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-income', 'eaccounting_income_page' );
		add_submenu_page( 'ever-accounting', __( 'Expense', 'wp-ever-accounting' ), __( 'Expense', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-expense', 'eaccounting_expense_page' );
		add_submenu_page( 'ever-accounting', __( 'Banking', 'wp-ever-accounting' ), __( 'Banking', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-banking', 'eaccounting_banking_page' );
		//add_submenu_page( 'ever-accounting', __( 'Contacts', 'wp-ever-accounting' ), __( 'Contacts', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-contacts', 'eaccounting_contacts_page' );
		add_submenu_page( 'ever-accounting', __( 'Misc', 'wp-ever-accounting' ), __( 'Misc', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-misc', 'eaccounting_misc_page' );
		add_submenu_page( 'ever-accounting', __( 'Tools', 'wp-ever-accounting' ), __( 'Tools', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-tools', 'eaccounting_tools_page' );
		add_submenu_page( 'ever-accounting', __( 'Import/Export', 'wp-ever-accounting' ), __( 'Import/Export', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-import-export', 'eaccounting_importing_page' );
		add_submenu_page( 'ever-accounting', __( 'Reports', 'wp-ever-accounting' ), __( 'Reports', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-reports', array( $this, 'dashboard_page' ) );
		add_submenu_page( 'ever-accounting', __( 'Addons', 'wp-ever-accounting' ), __( 'Addons', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-addons', array( $this, 'dashboard_page' ) );
		add_submenu_page( 'ever-accounting', __( 'Helps', 'wp-ever-accounting' ), __( 'Helps', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-helps', array(
			$this,
			'dashboard_page'
		) );

	}


}

new EAccounting_Admin_Menus();
