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

		// Packages.
		EAC()->scripts->enqueue_script( 'eac-components', 'packages/components.js' );
		EAC()->scripts->register_script( 'eac-money', 'packages/money.js' );
		EAC()->scripts->register_script( 'eac-api', 'packages/api.js', array( 'wp-api', 'wp-backbone' ), true );
		EAC()->scripts->enqueue_script( 'eac-admin-client', 'js/admin-client.js', array( 'eac-components' ) );

		// Plugins.
		EAC()->scripts->register_script( 'eac-modal', 'js/modal.js', array( 'jquery' ), true );
		EAC()->scripts->register_script( 'eac-form', 'js/form.js', array( 'jquery' ), true );

		// Plugin scripts.
		EAC()->scripts->register_script( 'eac-admin', 'js/admin.js', array( 'jquery', 'eac-inputmask', 'eac-select2', 'eac-tiptip', 'jquery-ui-datepicker', 'jquery-ui-tooltip' ), true );
		EAC()->scripts->register_script( 'eac-admin-sales', 'js/admin-sales.js', array( 'eac-api', 'eac-form', 'eac-money' ), true );
		EAC()->scripts->register_script( 'eac-admin-invoices', 'js/admin-invoices.js', array( 'eac-api', 'eac-money' ), true );
		EAC()->scripts->register_script( 'eac-admin-settings', 'js/admin-settings.js', array( 'eac-admin', 'eac-form' ), true );

		EAC()->scripts->register_style( 'eac-jquery-ui', 'css/jquery-ui.css' );
		EAC()->scripts->register_style( 'eac-admin', 'css/admin.css', array( 'eac-jquery-ui' ) );
		EAC()->scripts->register_style( 'eac-admin-settings', 'css/admin-settings.css', array( 'eac-admin' ) );
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
				'ajax_url'      => admin_url( 'admin-ajax.php' ),
				'base_currency' => eac_base_currency(),
				'currencies'    => eac_get_currencies(),
				'search_nonce'  => wp_create_nonce( 'eac_search_action' ),
				'i18n'          => array(
					'confirm_delete' => __( 'Are you sure you want to delete this item?', 'wp-ever-accounting' ),
					'close'          => __( 'Close', 'wp-ever-accounting' ),
				),
			)
		);

		// Payments page.
		if ( EAC()->get( Menus::class )->page === 'sales' ) {
			EAC()->scripts->enqueue_script( 'eac-admin-sales' );
		}

		// if settings page.
		if ( 'ever-accounting_page_eac-settings' === $hook ) {
			EAC()->scripts->enqueue_script( 'eac-admin-settings' );
			EAC()->scripts->enqueue_style( 'eac-admin-settings' );
		}

		if ( 'toplevel_page_ever-accounting' === $hook || 'ever-accounting_page_eac-reports' === $hook ) {
			EAC()->scripts->enqueue_script( 'eac-chartjs' );
		}
	}
}
