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
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
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
		// 3rd party scripts.
		EAC()->scripts->register_script( 'eac-chartjs', 'js/chartjs.js', array( 'jquery' ), true );
		EAC()->scripts->register_script( 'eac-inputmask', 'js/inputmask.js', array( 'jquery' ), true );
		EAC()->scripts->register_script( 'eac-select2', 'js/select2.js', array( 'jquery' ), true );
		EAC()->scripts->register_script( 'eac-tiptip', 'js/tiptip.js', array( 'jquery' ), true );
		EAC()->scripts->register_script( 'eac-blockui', 'js/blockui.js', array( 'jquery' ), true );
		EAC()->scripts->register_script( 'eac-accounting', 'js/accounting.js', array( 'jquery' ), true );

		// Packages.
		EAC()->scripts->register_script( 'eac-api', 'packages/api.js', array( 'wp-api', 'wp-backbone', 'underscore', 'jquery', 'eac-accounting' ), true );


		EAC()->scripts->register_script( 'eac-admin', 'js/eac-admin.js', array( 'jquery', 'eac-chartjs', 'eac-inputmask', 'eac-select2', 'eac-tiptip', 'jquery-ui-datepicker', 'jquery-ui-tooltip', 'wp-util' ), true );
		EAC()->scripts->register_script( 'eac-modal', 'js/eac-modal.js', array( 'wp-backbone', 'underscore', 'jquery' ), true );
		EAC()->scripts->register_script( 'eac-invoice', 'js/eac-invoice.js', array( 'eac-modal', 'eac-api' ), true );
		EAC()->scripts->register_script( 'eac-bill-form', 'js/eac-bill-form.js', array( 'eac-modal', 'eac-api' ), true );
		EAC()->scripts->register_script( 'eac-settings', 'js/eac-settings.js', array( 'eac-admin' ), true );

		if ( ! in_array( $hook, Utilities::get_screen_ids(), true ) ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_script( 'eac-modal' );
		wp_enqueue_script( 'eac-admin' );


		// if settings page.
		if ( 'ever-accounting_page_eac-settings' === $hook ) {
			EAC()->scripts->enqueue_script( 'eac-settings' );
		}
		if ( 'toplevel_page_ever-accounting' === $hook || 'ever-accounting_page_eac-reports' === $hook ) {
			EAC()->scripts->enqueue_script( 'eac-chartjs' );
		}

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
	}


	/**
	 * Enqueue admin styles.
	 *
	 * @param string $hook The current admin page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_styles( $hook ) {
		EAC()->scripts->register_style( 'eac-jquery-ui', 'css/jquery-ui.css' );
		EAC()->scripts->register_style( 'eac-admin', 'css/eac-admin.css', array( 'eac-jquery-ui', 'wp-jquery-ui-dialog' ) );
		EAC()->scripts->register_style( 'eac-settings', 'css/eac-settings.css', array( 'eac-admin' ) );

		if ( ! in_array( $hook, Utilities::get_screen_ids(), true ) ) {
			return;
		}

		wp_enqueue_style( 'eac-admin' );

		if ( 'ever-accounting_page_eac-settings' === $hook ) {
			EAC()->scripts->enqueue_style( 'eac-settings' );
		}
	}

}
