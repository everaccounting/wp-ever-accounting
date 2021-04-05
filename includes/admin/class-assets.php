<?php
/**
 * Load assets.
 *
 * @package     EverAccounting
 * @subpackage  Admin
 * @version     1.0.2
 */

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit();

/**
 * Class Assets
 * @package EverAccounting\Admin
 * @since   1.0.2
 */

class Assets extends \EverAccounting\Abstracts\Assets {

	/**
	 * Enqueue admin styles.
	 *
	 * @version 1.0.3
	 */
	public function admin_styles() {
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';
		// Register admin styles.
		$this->register_style('ea-admin-styles', 'admin.min.css' );
		$this->register_style('ea-release-styles', 'release.min.css' );
		$this->register_style('jquery-ui-styles', 'jquery-ui.min.css' );

		// Admin styles for Accounting pages only.
		if ( in_array( $screen_id, eaccounting_get_screen_ids(), true ) ) {
			wp_enqueue_style( 'ea-admin-styles' );
			wp_enqueue_style( 'jquery-ui-style' );
		}
	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @version 1.0.3
	 */
	public function admin_scripts() {
		$screen                = get_current_screen();
		$screen_id             = $screen ? $screen->id : '';

		// 3rd parties
		$this->register_script( 'jquery-blockui', 'jquery.blockUI.min.js', array( 'jquery' ), false);
		$this->register_script( 'jquery-select2', 'select2.full.js', array( 'jquery' ), false);
		$this->register_script( 'jquery-inputmask', 'jquery.inputmask.js', array( 'jquery' ), false);
		$this->register_script( 'chartjs', 'chart.bundle.js', array(), false);
		$this->register_script( 'chartjs-labels', 'chartjs-plugin-labels.js', array('chartjs'), false);
		$this->register_script( 'jquery-print-this', 'printThis.js', array('jquery'), false);

		// core plugins
		$this->register_script( 'ea-select', 'ea-select2.js', array( 'jquery', 'jquery-select2' ), false );
		$this->register_script( 'ea-creatable', 'ea-creatable.js', array( 'jquery', 'ea-select', 'wp-util', 'ea-modal', 'jquery-blockui' ), false );
		$this->register_script( 'ea-modal', 'ea-modal.js', array( 'jquery' ), false );
		$this->register_script( 'ea-form', 'ea-form.js', array( 'jquery' ), false );
		$this->register_script( 'ea-exporter', 'ea-exporter.js', array( 'jquery' ), false );
		$this->register_script( 'ea-importer', 'ea-importer.js', array( 'jquery' ), false );

		// core script
		$this->register_script( 'ea-helper',  'ea-helper.js', array( 'jquery', 'jquery-blockui' ), false );
		$this->register_script( 'ea-overview',  'ea-overview.js', array( 'jquery', 'jquery-daterange', 'chartjs' ), false );
		$this->register_script( 'ea-settings',  'ea-settings.js', array( 'jquery' ), false );
		$this->register_script( 'ea-admin',  'ea-admin.js', array( 'jquery' ), false );

		// Admin scripts for Accounting pages only.
		if ( in_array( $screen_id, eaccounting_get_screen_ids(), true ) ) {
			// globally needed scripts
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
				wp_enqueue_script( 'chartjs' );
				//wp_enqueue_script( 'chartjs-labels' );
			}

			$default_currency = eaccounting()->settings->get( 'default_currency', 'USD' );
			wp_localize_script(
				'ea-admin',
				'eaccountingi10n',
				array(
					'site_url'   => site_url(),
					'admin_url'  => admin_url(),
					'asset_url'  => eaccounting()->plugin_url( '/assets' ),
					'plugin_url' => eaccounting()->plugin_url(),
					'currency'   => eaccounting_get_currency( $default_currency )->get_data(),
					'currencies' => eaccounting_get_currencies(array('return' => 'raw', 'number' => -1 )) //phpcs:ignore
				)
			);
			wp_enqueue_media();
		}
	}
}

return new \EverAccounting\Admin\Assets();
