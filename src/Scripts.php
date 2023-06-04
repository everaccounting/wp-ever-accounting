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
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
	}

	/**
	 * Enqueue scripts.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public function enqueue_scripts() {
		if ( empty( get_query_var( 'eac_page' ) ) ) {
			return;
		}
		ever_accounting()->register_script( 'eac-public-script', 'js/public.min.js', array( 'jquery' ) );
	}

	/**
	 * Enqueue styles.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public function enqueue_styles() {
		if ( empty( get_query_var( 'eac_page' ) ) ) {
			return;
		}
		// wp install style.
		ever_accounting()->enqueue_style( 'eac-public-style', 'css/public.min.css', array( 'common') );
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
		ever_accounting()->register_script( 'eac-drawer', 'js/admin/eac-drawer.min.js', array( 'jquery' ) );
		ever_accounting()->register_script( 'eac-plugins', 'js/admin/eac-plugins.min.js', array( 'jquery', 'eac-select2', 'eac-inputmask', 'eac-tiptip', 'eac-drawer', 'eac-blockui', 'eac-inputmask' ) );
		ever_accounting()->register_script( 'eac-document', 'js/admin/eac-document.min.js', array( 'jquery', 'eac-plugins' ) );
		ever_accounting()->register_script( 'eac-admin', 'js/admin/eac-admin.min.js', array( 'jquery', 'eac-plugins' ) );

		// Admin scripts for Accounting pages only.
		if ( in_array( $screen_id, eac_get_screen_ids(), true ) ) {
			$currencies = eac_get_currencies();
			// globally needed scripts.
			wp_enqueue_editor();
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_script( 'jquery-ui-tooltip' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_script( 'eac-document' );
			wp_enqueue_script( 'eac-admin' );

			wp_localize_script(
				'eac-admin',
				'eac_admin_vars',
				array(
					'site_url'      => site_url(),
					'admin_url'     => admin_url(),
					'asset_url'     => ever_accounting()->get_dir_url( '/assets' ),
					'plugin_url'    => ever_accounting()->get_dir_url(),
					'ajax_url'      => admin_url( 'admin-ajax.php' ),
					'json_search'   => wp_create_nonce( 'eac_json_search' ),
					'base_currency' => eac_get_base_currency(),
					'currencies'    => eac_get_currencies(),
					'i18n'          => array(
						'no_matches'          => __( 'No matches found', 'wp-ever-accounting' ),
						'error'               => __( 'There was an error', 'wp-ever-accounting' ),
						'delete_confirmation' => __( 'Are you sure you want to delete this?', 'wp-ever-accounting' ),
					),
				)
			);
		}
	}

}
