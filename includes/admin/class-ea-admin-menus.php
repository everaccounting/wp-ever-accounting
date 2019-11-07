<?php

defined( 'ABSPATH' ) || exit();

class EAccounting_Admin_Menus {

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
		add_submenu_page( 'ever-accounting', __( 'Dashboard', 'wp-ever-accounting' ), __( 'Dashboard', 'wp-ever-accounting' ), 'manage_options', 'ever-accounting', array(
			$this,
			'dashboard_page'
		) );
		add_submenu_page( 'ever-accounting', __( 'Transactions', 'wp-ever-accounting' ), __( 'Transactions', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-transactions', array(
			$this,
			'dashboard_page'
		) );
		add_submenu_page( 'ever-accounting', __( 'Products', 'wp-ever-accounting' ), __( 'Products', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-products', 'eaccounting_products_page' );
		add_submenu_page( 'ever-accounting', __( 'Income', 'wp-ever-accounting' ), __( 'Income', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-income', 'eaccounting_income_page' );
		add_submenu_page( 'ever-accounting', __( 'Expense', 'wp-ever-accounting' ), __( 'Expense', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-expense', 'eaccounting_expense_page' );
		add_submenu_page( 'ever-accounting', __( 'Banking', 'wp-ever-accounting' ), __( 'Banking', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-banking', 'eaccounting_banking_page' );
		add_submenu_page( 'ever-accounting', __( 'Contacts', 'wp-ever-accounting' ), __( 'Contacts', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-contacts', 'eaccounting_contacts_page' );
		add_submenu_page( 'ever-accounting', __( 'Tools', 'wp-ever-accounting' ), __( 'Tools', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-tools', 'eaccounting_tools_page' );
		//add_submenu_page( 'ever-accounting', __( 'Import/Export', 'wp-ever-accounting' ), __( 'Import/Export', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-import-export', 'eaccounting_importing_page' );

//		add_submenu_page( 'ever-accounting', __( 'Invoices', 'wp-ever-accounting' ), __( 'Invoices', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-invoices', array(
//			$this,
//			'dashboard_page'
//		) );
//		add_submenu_page( 'ever-accounting', __( 'Revenues', 'wp-ever-accounting' ), __( 'Revenues', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-revenues', array(
//			$this,
//			'dashboard_page'
//		) );
//		add_submenu_page( 'ever-accounting', __( 'Bills', 'wp-ever-accounting' ), __( 'Bills', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-bills', array(
//			$this,
//			'dashboard_page'
//		) );
//		add_submenu_page( 'ever-accounting', __( 'Payments', 'wp-ever-accounting' ), __( 'Payments', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-payments', array(
//			$this,
//			'dashboard_page'
//		) );
//		add_submenu_page( 'ever-accounting', __( 'Accounts', 'wp-ever-accounting' ), __( 'Accounts', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-accounts', 'eaccount_accounts_page' );
//		add_submenu_page( 'ever-accounting', __( 'Taxes', 'wp-ever-accounting' ), __( 'Taxes', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-taxes', 'eaccount_taxes_page' );
//		add_submenu_page( 'ever-accounting', __( 'Transfers', 'wp-ever-accounting' ), __( 'Transfers', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-transfers', array(
//			$this,
//			'dashboard_page'
//		) );
//		add_submenu_page( 'ever-accounting', __( 'Reconciliations', 'wp-ever-accounting' ), __( 'Reconciliations', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-reconciliations', array(
//			$this,
//			'dashboard_page'
//		) );
		add_submenu_page( 'ever-accounting', __( 'Reports', 'wp-ever-accounting' ), __( 'Reports', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-reports', array(
			$this,
			'dashboard_page'
		) );
		add_submenu_page( 'ever-accounting', __( 'Addons', 'wp-ever-accounting' ), __( 'Addons', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-addons', array(
			$this,
			'dashboard_page'
		) );
		add_submenu_page( 'ever-accounting', __( 'Helps', 'wp-ever-accounting' ), __( 'Helps', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-helps', array(
			$this,
			'dashboard_page'
		) );

	}

	public function dashboard_page() {
		?>
		<div style="max-width: 600px;margin: 50px auto;">
		<?php
		echo eaccounting_select_field( array(
			'label'    => 'select',
			'disabled' => true,
			'required' => true,
			'select2'  => true,
			'name'     => 'account',
			'icon'     => 'fa fa-user',
			'button'   => '<button class="button">Ok</button>',
			'options'  => wp_list_pluck( (array) eaccounting_get_accounts(), 'name', 'id' )
		) );

		echo eaccounting_select_field( array(
			'label'    => 'select',
			'required' => true,
			'disabled' => true,
			//'select2' => true,
			'name'     => 'accounta',
			'icon'     => 'fa fa-user',
			'button'   => '<button class="button"><i class="fa fa-calendar"></i></button>',
			'options'  => wp_list_pluck( (array) eaccounting_get_accounts(), 'name', 'id' )
		) );

		echo eaccounting_input_field( array(
			'wrapper_class' => 'Currency',
			'label'         => 'Currency',
			'required'      => true,
			'name'          => 'currency_code',
			'icon'          => 'fa fa-exchange',
		) );

		echo eaccounting_switch_field( array(
			'wrapper_class' => 'Currency',
			'label'         => 'Status',
			'check'         => '1',
			'value'         => '1',
			'required'      => true,
			'name'          => 'status',
		) );

		echo eaccounting_switch_field( array(
			'wrapper_class' => 'Currency',
			'label'         => 'Status',
			'value'         => 'on',
			'required'      => true,
			'name'          => 'status1',
		) );

		echo eaccounting_textarea_field( array(
			'wrapper_class' => 'Currency',
			'label'         => 'Status',
			'required'      => true,
			'name'          => 'status11',
		) );


		echo '</div>';
	}


	/**
	 * Enqueue scripts
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_media();
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'jquery' );
	}


}

new EAccounting_Admin_Menus();
