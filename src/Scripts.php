<?php

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;

/**
 * Class Scripts.
 *
 * @since   1.1.6
 * @package EverAccounting
 */
class Scripts extends Singleton {

	/**
	 * Scripts constructor.
	 *
	 * @since 1.1.6
	 */
	protected function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
	}

	/**
	 * Enqueue styles.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public function admin_styles() {
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';
		// Register admin styles.
		ever_accounting()->register_style( 'jquery-select2-style', 'css/select2.min.css' );
		ever_accounting()->register_style( 'jquery-ui-style', 'css/jquery-ui.min.css' );
		ever_accounting()->register_style( 'eac-settings-style', 'css/settings.min.css' );
		ever_accounting()->register_style( 'eac-admin-style', 'css/admin.min.css', array( 'dashicons', 'jquery-ui-style', 'jquery-select2-style' ) );

		// Add RTL support for admin styles.
		wp_style_add_data( 'eac-admin-style', 'rtl', 'replace' );

		// Admin styles for Accounting pages only.
		if ( in_array( $screen_id, eac_get_screen_ids(), true ) ) {
			wp_enqueue_style( 'eac-admin-style' );
			wp_enqueue_style( 'jquery-ui-style' );
			wp_enqueue_style( 'eac-settings-style' );
		}
	}

	/**
	 * Enqueue scripts.
	 *
	 * @param string $hook Hook name.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public function enqueue_admin_scripts( $hook ) {
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';
		// 3rd parties.
		ever_accounting()->register_script( 'eac-select2', 'js/select2/select2.min.js', array( 'jquery' ) );
		ever_accounting()->register_script( 'eac-blockui', 'js/blockui/blockUI.min.js', array( 'jquery' ) );
		ever_accounting()->register_script( 'eac-inputmask', 'js/inputmask/inputmask.min.js', array( 'jquery' ) );
		ever_accounting()->register_script( 'eac-chartjs', 'js/chartjs/chartjs-bundle.min.js', array( 'jquery' ) );
		ever_accounting()->register_script( 'eac-printthis', 'js/printthis/printThis.min.js', array( 'jquery' ) );
		ever_accounting()->register_script( 'eac-tiptip', 'js/tiptip/tipTip.min.js', array( 'jquery' ) );

		// core plugins.
		ever_accounting()->register_script( 'eac-modal', 'js/admin/ea-modal.min.js', array( 'jquery' ) );
		ever_accounting()->register_script( 'eac-plugins', 'js/admin/eac-plugins.min.js', array( 'jquery', 'eac-select2', 'eac-inputmask', 'eac-tiptip', 'eac-modal', 'eac-blockui', 'eac-inputmask' ) );
		ever_accounting()->register_script( 'eac-admin', 'js/admin/eac-admin.min.js', array( 'jquery', 'eac-plugins' ) );

		// Admin scripts for Accounting pages only.
		if ( in_array( $screen_id, eac_get_screen_ids(), true ) ) {
			// globally needed scripts.
			wp_enqueue_editor();
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_script( 'jquery-ui-tooltip' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_script( 'eac-admin' );

			$currency = eac_get_currency( eac_get_default_currency() );
			$currency = $currency ? $currency->get_data() : array();

			wp_localize_script(
				'eac-admin',
				'eac_admin_vars',
				array(
					'site_url'           => site_url(),
					'admin_url'          => admin_url(),
					'asset_url'          => ever_accounting()->get_url( '/assets' ),
					'plugin_url'         => ever_accounting()->get_url(),
					'ajax_url'           => admin_url( 'admin-ajax.php' ),
					'json_search'        => wp_create_nonce( 'eac_json_search' ),
					'currency_code'      => ! empty( $currency['code'] ) ? $currency['code'] : '',
					'currency_symbol'    => ! empty( $currency['symbol'] ) ? $currency['symbol'] : '',
					'currency_precision' => ! empty( $currency['precision'] ) ? $currency['precision'] : 2,
					'thousand_sep'       => ! empty( $currency['thousand_sep'] ) ? $currency['thousand_sep'] : ',',
					'decimal_sep'        => ! empty( $currency['decimal_sep'] ) ? $currency['decimal_sep'] : '.',
					'currency_rates'     => eac_get_currency_rates(),
					'account_currencies' => eac_get_account_currencies(),
					'i18n'               => array(
						'no_matches'          => __( 'No matches found', 'wp-ever-accounting' ),
						'error'               => __( 'There was an error', 'wp-ever-accounting' ),
						'delete_confirmation' => __( 'Are you sure you want to delete this?', 'wp-ever-accounting' ),
					),
				)
			);
		}
	}

}
