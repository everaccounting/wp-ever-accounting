<?php
/**
 * Load Admin Assets.
 *
 * @package     EverAccounting
 * @subpackage  Admin
 * @version     1.0.2
 */

namespace EverAccounting\Admin;

use EverAccounting\Abstracts\Assets;

defined( 'ABSPATH' ) || exit();

/**
 * Class Admin_Assets
 *
 * @since   1.0.2
 * @package EverAccounting\Admin
 */
class Admin_Assets extends Assets {
	/**
	 * Enqueue styles.
	 *
	 * @version 1.0.2
	 */
	public function admin_styles() {
		$version   = eaccounting()->get_version();
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';
		// Register admin styles.
		$this->register_style( 'ea-admin-styles', 'admin.min.css' );
		// wp_register_style( 'ea-admin-styles', eaccounting()->plugin_url() . '/dist/css/admin.min.css', array(), $version );
		// wp_register_style( 'ea-release-styles', eaccounting()->plugin_url() . '/dist/css/release.min.css', array(), $version );
		// wp_register_style( 'jquery-ui-style', eaccounting()->plugin_url() . '/dist/css/jquery-ui.min.css', array(), $version );
		//
		// Add RTL support for admin styles.
		// wp_style_add_data( 'ea-admin-styles', 'rtl', 'replace' );
		//
		// Admin styles for Accounting pages only.
		if ( in_array( $screen_id, eaccounting_get_screen_ids(), true ) ) {
			wp_enqueue_style( 'ea-admin-styles' );
			wp_enqueue_style( 'jquery-ui-style' );
		}
	}


	/**
	 * Enqueue scripts.
	 *
	 * @version 1.0.2
	 */
	public function admin_scripts() {
		$screen                = get_current_screen();
		$screen_id             = $screen ? $screen->id : '';
		$eaccounting_screen_id = sanitize_title( __( 'Accounting', 'wp-ever-accounting' ) );

		// 3rd parties
		$this->register_script( 'jquery-blockui', 'jquery.blockUI.min.js', [ 'jquery' ], false );
		$this->register_script( 'jquery-tiptip', 'jquery.tipTip.js', [ 'jquery' ], false );
		$this->register_script( 'jquery-select2', 'select2.full.js', [ 'jquery' ], false );
		$this->register_script( 'jquery-inputmask', 'jquery.inputmask.js', [ 'jquery' ], false );
		$this->register_script( 'jquery-chartjs', 'chart.bundle.js', [ 'jquery' ], false );
		$this->register_script( 'jquery-chartjs-labels', 'chartjs-plugin-labels.js', [ 'jquery' ], false );
		$this->register_script( 'ea-print', 'printThis.js', [ 'jquery' ], false );

		// core plugins
		$this->register_script( 'ea-select', 'ea-select2.js', [ 'jquery', 'jquery-select2' ], false );
		$this->register_script(
			'ea-creatable',
			'ea-creatable.js',
			[
				'jquery',
				'ea-select',
				'wp-util',
				'ea-modal',
				'jquery-blockui',
			],
			false
		);
		$this->register_script( 'ea-modal', 'ea-modal.js', [ 'jquery' ], false );
		$this->register_script( 'ea-form', 'ea-form.js', [ 'jquery' ], false );
		$this->register_script( 'ea-exporter', 'ea-exporter.js', [ 'jquery' ], false );
		$this->register_script( 'ea-importer', 'ea-importer.js', [ 'jquery' ], false );

		// core script
		$this->register_script( 'ea-helper', 'ea-helper.js', [ 'jquery' ], false );
		$this->register_script(
			'ea-overview',
			'ea-overview.js',
			[
				'jquery',
				'jquery-daterange',
				'jquery-chartjs',
			],
			false
		);
		$this->register_script( 'ea-settings', 'ea-settings.js', [ 'jquery' ], false );
		$this->register_script( 'ea-admin', 'ea-admin.js', [ 'jquery' ], false );

		// Admin scripts for Accounting pages only.
		if ( in_array( $screen_id, eaccounting_get_screen_ids(), true ) ) {
			// Globally needed scripts.
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_script( 'jquery-ui-tooltip' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_script( 'jquery-select2' );
			wp_enqueue_script( 'jquery-inputmask' );
			wp_enqueue_script( 'jquery-blockui' );
			wp_enqueue_script( 'ea-modal' );
			wp_enqueue_script( 'ea-select' );
			wp_enqueue_script( 'ea-creatable' );
			wp_enqueue_script( 'ea-helper' );
			wp_enqueue_script( 'ea-admin' );
			wp_enqueue_script( 'ea-form' );

			wp_localize_script(
				'ea-select',
				'eaccounting_select_i10n',
				array(
					'ajaxurl' => eaccounting()->ajax_url(),
				)
			);

			wp_localize_script(
				'ea-form',
				'eaccounting_form_i10n',
				array(
					'ajaxurl'           => eaccounting()->ajax_url(),
					'global_currencies' => eaccounting_get_global_currencies(),
					'nonce'             => array(
						'get_account'  => wp_create_nonce( 'ea_get_account' ),
						'get_currency' => wp_create_nonce( 'ea_get_currency' ),
					),
				)
			);

			// export page
			if ( eaccounting_is_admin_page( 'ea-tools' ) && isset( $_GET['tab'] ) && 'export' === $_GET['tab'] ) {
				wp_enqueue_script( 'ea-exporter' );
			}

			// import page
			if ( eaccounting_is_admin_page( 'ea-tools' ) && isset( $_GET['tab'] ) && 'import' === $_GET['tab'] ) {
				wp_localize_script(
					'ea-importer',
					'eaccounting_importer_i10n',
					array(
						'uploaded_file_not_found' => esc_html__( 'Could not find the uploaded file, please try again', 'wp-ever-accounting' ),
						'select_field_to_preview' => esc_html__( '  - Select field to preview data -', 'wp-ever-accounting' ),
						'required'                => esc_html__( '(Required)', 'wp-ever-accounting' ),
					)
				);
				wp_enqueue_script( 'ea-importer' );
			}

			// settings page
			if ( eaccounting_is_admin_page( 'ea-settings' ) ) {
				wp_enqueue_media();
				wp_enqueue_script( 'ea-settings' );
			}

			// report page
			if ( eaccounting_is_admin_page( 'ea-reports' ) ) {
				wp_enqueue_script( 'jquery-chartjs' );
			}

			$default_currency = eaccounting()->settings->get( 'default_currency', 'USD' );
			wp_localize_script(
				'ea-admin',
				'eaccountingi10n',
				array(
					'site_url'   => site_url(),
					'admin_url'  => admin_url(),
					'asset_url'  => eaccounting()->plugin_url( '/assets' ),
					'dist_url'   => eaccounting()->plugin_url( '/dist' ),
					'plugin_url' => eaccounting()->plugin_url(),
					'currency'   => eaccounting_get_currency( $default_currency )->get_data(),
					'currencies' => eaccounting_get_currencies(
						array(
							'return' => 'raw',
							'number' => - 1,
						)
					),
					//phpcs:ignore
				)
			);
			wp_enqueue_media();
		}
	}
}

return new Admin_Assets();
