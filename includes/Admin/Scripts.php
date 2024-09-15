<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Scripts
 *
 * @package EverAccounting\Admin
 */
class Scripts {

	/**
	 * Scripts constructor.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Register admin scripts.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register_scripts() {
		// 3rd party scripts.
		EAC()->scripts->register_script( 'eac-chartjs', 'js/chartjs.js', array( 'jquery' ), true );
		EAC()->scripts->register_script( 'eac-inputmask', 'js/inputmask.js', array( 'jquery' ), true );
		EAC()->scripts->register_script( 'eac-select2', 'js/select2.js', array( 'jquery' ), true );
		EAC()->scripts->register_script( 'eac-tiptip', 'js/tiptip.js', array( 'jquery' ), true );
		EAC()->scripts->register_script( 'eac-blockui', 'js/blockui.js', array( 'jquery' ), true );
		EAC()->scripts->register_script( 'eac-accounting', 'js/accounting.js', array( 'jquery' ), true );

		// Packages.
		EAC()->scripts->register_script( 'eac-api', 'packages/api.js', array( 'wp-api', 'wp-backbone', 'underscore', 'jquery', 'eac-accounting' ), true );
		EAC()->scripts->register_script( 'eac-expenses', 'client/expenses.js' );

		// Plugin scripts.
		EAC()->scripts->register_script( 'eac-admin', 'js/eac-admin.js', array( 'jquery', 'eac-chartjs', 'eac-inputmask', 'eac-select2', 'eac-tiptip', 'jquery-ui-datepicker', 'jquery-ui-tooltip', 'wp-util' ), true );
		EAC()->scripts->register_script( 'eac-sales', 'js/eac-sales.js', array( 'eac-api' ), true );
		EAC()->scripts->register_script( 'eac-purchases', 'js/eac-purchases.js', array( 'eac-api' ), true );
		EAC()->scripts->register_script( 'eac-settings', 'js/eac-settings.js', array( 'eac-admin' ), true );

		EAC()->scripts->register_style( 'eac-jquery-ui', 'css/jquery-ui.css' );
		EAC()->scripts->register_style( 'eac-admin', 'css/eac-admin.css', array( 'eac-jquery-ui', 'wp-jquery-ui-dialog' ) );
		EAC()->scripts->register_style( 'eac-settings', 'css/eac-settings.css', array( 'eac-admin' ) );
	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @param string $hook The current admin page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_scripts( $hook ) {
		if ( ! in_array( $hook, Utilities::get_screen_ids(), true ) ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_script( 'eac-admin' );
		wp_enqueue_style( 'eac-admin' );

		wp_localize_script(
			'eac-admin',
			'eac_admin_vars',
			array(
				'ajax_url'       => admin_url( 'admin-ajax.php' ),
				'search_nonce'   => wp_create_nonce( 'eac_search_action' ),
				'currency_nonce' => wp_create_nonce( 'eac_currency' ),
				'account_nonce'  => wp_create_nonce( 'eac_account' ),
				'item_nonce'     => wp_create_nonce( 'eac_item' ),
				'customer_nonce' => wp_create_nonce( 'eac_customer' ),
				'vendor_nonce'   => wp_create_nonce( 'eac_vendor' ),
				'payment_nonce'  => wp_create_nonce( 'eac_payment' ),
				'expense_nonce'  => wp_create_nonce( 'eac_expense' ),
				'invoice_nonce'  => wp_create_nonce( 'eac_invoice' ),
				'purchase_nonce' => wp_create_nonce( 'eac_purchase' ),

				'i18n' => array(
					'confirm_delete' => __( 'Are you sure you want to delete this item?', 'wp-ever-accounting' ),
					'close'          => __( 'Close', 'wp-ever-accounting' ),
				),
			)
		);

		// If sales page.
		if ( 'ever-accounting_page_eac-sales' === $hook ) {
			EAC()->scripts->enqueue_script( 'eac-sales' );
		}

		// If purchases page.
		if ( 'ever-accounting_page_eac-purchases' === $hook ) {
//			EAC()->scripts->enqueue_script( 'eac-purchases' );
			EAC()->scripts->enqueue_script( 'eac-expenses' );
		}

		// if settings page.
		if ( 'ever-accounting_page_eac-settings' === $hook ) {
			EAC()->scripts->enqueue_script( 'eac-settings' );
			EAC()->scripts->enqueue_style( 'eac-settings' );
		}

		if ( 'toplevel_page_ever-accounting' === $hook || 'ever-accounting_page_eac-reports' === $hook ) {
			EAC()->scripts->enqueue_script( 'eac-chartjs' );
		}
	}
}
