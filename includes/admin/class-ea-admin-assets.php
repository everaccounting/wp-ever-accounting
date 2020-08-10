<?php
/**
 * Load assets
 *
 * @package EverAccounting/Admin
 * @version 3.7.0
 */

namespace EverAccounting\Admin;
defined( 'ABSPATH' ) || exit();

class Admin_Assets {
	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
	}

	/**
	 * Enqueue styles.
	 */
	public function admin_styles() {
		$version   = eaccounting()->get_version();
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';

		// Register admin styles.
		wp_register_style( 'eaccounting-admin-styles', eaccounting()->plugin_url() . '/assets/css/admin.css', array(), $version );
		wp_register_style( 'jquery-ui-style', eaccounting()->plugin_url() . '/assets/css/jquery-ui/jquery-ui.min.css', array(), $version );


		// Add RTL support for admin styles.
		wp_style_add_data( 'eaccounting-admin-styles', 'rtl', 'replace' );


		// Admin styles for Accounting pages only.
		if ( in_array( $screen_id, eaccounting_get_screen_ids() ) ) {
			wp_enqueue_style( 'eaccounting-admin-styles' );
			wp_enqueue_style( 'jquery-ui-style' );
		}
	}


	/**
	 * Enqueue scripts.
	 */
	public function admin_scripts() {
		$screen                = get_current_screen();
		$screen_id             = $screen ? $screen->id : '';
		$eaccounting_screen_id = sanitize_title( __( 'Accounting', 'wp-ever-accounting' ) );
		$suffix                = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$version               = eaccounting()->get_version();

//		//3rd parties
		wp_register_script( 'jquery-blockui', eaccounting()->plugin_url() . '/assets/js/jquery-blockui/jquery.blockUI' . $suffix . '.js', array( 'jquery' ), '2.70', true );
		wp_register_script( 'jquery-tiptip', eaccounting()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip' . $suffix . '.js', array( 'jquery' ), $version, true );
		wp_register_script( 'jquery-pace', eaccounting()->plugin_url() . '/assets/js/pace/pace' . $suffix . '.js', array( 'jquery' ), '1.0.2' );
//		wp_register_script( 'jquery-validation', eaccounting()->plugin_url() . '/assets/js/jquery-validation/jquery.validate' . $suffix . '.js', array( 'jquery' ), '1.19.2' );
		wp_register_script( 'select2', eaccounting()->plugin_url() . '/assets/js/select2/select2.full' . $suffix . '.js', array( 'jquery' ), $version );
		wp_register_script( 'ea-backbone-modal', eaccounting()->plugin_url() . '/assets/js/eaccounting/ea-backbone-modal' . $suffix . '.js', array( 'underscore', 'backbone', 'wp-util' ), $version );
		wp_register_script( 'ea-notice', eaccounting()->plugin_url() . '/assets/js/eaccounting/ea-notice' . $suffix . '.js', array( 'jquery' ), '1.0.2' );
		wp_register_script( 'jquery-inputmask', eaccounting()->plugin_url() . '/assets/js/inputmask/jquery.inputmask' . $suffix . '.js', array( 'jquery' ), '1.0.2' );

		//core js
		wp_register_script( 'eaccounting', eaccounting()->plugin_url() . '/assets/js/eaccounting/eaccounting' . $suffix . '.js', array( 'jquery' ), $version );
		wp_register_script( 'eaccounting-settings', eaccounting()->plugin_url() . '/assets/js/admin/admin-settings' . $suffix . '.js', array( 'jquery' ), $version );
		wp_register_script( 'ea-quick-add', eaccounting()->plugin_url() . '/assets/js/admin/ea-quick-add' . $suffix . '.js', array( 'jquery' ), $version );
		wp_register_script( 'eaccounting-admin', eaccounting()->plugin_url() . '/assets/js/admin/eaccounting-admin' . $suffix . '.js', array( 'jquery' ), $version );
		wp_register_script( 'ea-form', eaccounting()->plugin_url() . '/assets/js/admin/ea-form' . $suffix . '.js', array( 'jquery', 'underscore', 'backbone', 'wp-util' ), $version );

		// Admin scripts for Accounting pages only.
		if ( in_array( $screen_id, eaccounting_get_screen_ids() ) ) {
			wp_enqueue_script( 'jquery-pace' );
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_script( 'ea-notice' );
			wp_enqueue_script( 'jquery-tiptip' );
			wp_enqueue_script( 'select2' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_script( 'jquery-inputmask' );
			wp_enqueue_script( 'jquery-blockui' );
			wp_enqueue_script( 'jquery-tiptip' );
//			wp_enqueue_script( 'ea-quick-add' );
//			wp_enqueue_script( 'jquery-validation' );
			wp_enqueue_script( 'ea-backbone-modal' );
			wp_enqueue_script( 'eaccounting' );
			wp_enqueue_script( 'eaccounting-admin' );


			wp_enqueue_script( 'ea-quick-add' );
			wp_enqueue_script( 'ea-form' );

			wp_localize_script( 'eaccounting-admin', 'eaccounting_admin_i10n', array(
				'ajax_url'          => eaccounting()->ajax_url(),
				'global_currencies' => eaccounting_get_global_currencies()
			) );

			if(eaccounting_is_admin_page('ea-settings')){
				wp_enqueue_media();
				wp_enqueue_script( 'eaccounting-settings' );
			}

		}
	}
}

return new Admin_Assets();
