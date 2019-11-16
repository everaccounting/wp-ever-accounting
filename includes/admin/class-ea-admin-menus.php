<?php

defined( 'ABSPATH' ) || exit();

class EAccounting_Admin_Menus {

	/**
	 * EAccounting_Admin_Menus constructor.
	 */
	public function __construct() {
		// Add menus.
		add_action( 'wp_loaded', array( $this, 'save_settings' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 10 );
		add_action( 'admin_menu', array( $this, 'settings_menu' ), 50 );
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
		add_submenu_page( 'ever-accounting', __( 'Transactions', 'wp-ever-accounting' ), __( 'Transactions', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-transactions', array( $this, 'dashboard_page' ) );
		add_submenu_page( 'ever-accounting', __( 'Contacts', 'wp-ever-accounting' ), __( 'Contacts', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-contacts', array($this, 'contacts_page') );
		add_submenu_page( 'ever-accounting', __( 'Payments', 'wp-ever-accounting' ), __( 'Payments', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-payments', array( $this, 'payments_page') );
		add_submenu_page( 'ever-accounting', __( 'Revenues', 'wp-ever-accounting' ), __( 'Revenues', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-revenues', array( $this, 'revenues_page') );
		add_submenu_page( 'ever-accounting', __( 'Accounts', 'wp-ever-accounting' ), __( 'Accounts', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-accounts', array( $this, 'accounts_page') );
		add_submenu_page( 'ever-accounting', __( 'Categories', 'wp-ever-accounting' ), __( 'Categories', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-categories', array( $this, 'categories_page') );
	}

	public function settings_menu(){
		add_submenu_page( 'ever-accounting', __( 'Settings', 'wp-ever-accounting' ), __( 'Settings', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-settings', array($this, 'settings_page'));
	}


	/**
	 * Render dashboard page
	 * since 1.0.0
	 */
	public function dashboard_page(){

	}

	/**
	 * Render settings page
	 * since 1.0.0
	 */
	public function settings_page(){
		EAccounting_Admin_Settings::output();
	}

	/**
	 * Handle saving of settings.
	 *
	 * @return void
	 */
	public function save_settings() {
		global $current_tab, $current_section;
		// We should only save on the settings page.
		if ( ! is_admin() || ! isset( $_GET['page'] ) || 'eaccounting-settings' !== $_GET['page'] ) {
			return;
		}

		// Get current tab/section.
		$current_tab     = empty( $_GET['tab'] ) ? 'general' : sanitize_title( wp_unslash( $_GET['tab'] ) );

		$current_section = empty( $_REQUEST['section'] ) ? '' : sanitize_title( wp_unslash( $_REQUEST['section'] ) );

		// Include settings pages.
		EAccounting_Admin_Settings::get_settings_pages();


		// Save settings if data has been posted.
		if ( '' !== $current_section && apply_filters( "woocommerce_save_settings_{$current_tab}_{$current_section}", ! empty( $_POST['save'] ) ) ) {
			EAccounting_Admin_Settings::save();
		} elseif ( '' === $current_section && apply_filters( "woocommerce_save_settings_{$current_tab}", ! empty( $_POST['save'] ) ) ) {
			EAccounting_Admin_Settings::save();
		}
	}


	public function transaction_page(){

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

	public function categories_page(){
		eaccounting_get_views('categories-page.php');
	}

}

new EAccounting_Admin_Menus();
