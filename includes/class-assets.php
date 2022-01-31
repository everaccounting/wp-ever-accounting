<?php
/**
 * Load EverAccounting assets.
 *
 * @package     EverAccounting
 * @version     1.0.2
 */

namespace EverAccounting;

defined( 'ABSPATH' ) || exit();

/**
 * Class Assets
 *
 * @since 1.0.2
 */
class Assets {

	/**
	 * Assets constructor.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'public_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'public_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

		add_action( 'eaccounting_head', array( $this, 'eaccounting_styles' ) );
		add_action( 'eaccounting_footer', array( $this, 'eaccounting_scripts' ) );
	}

	/**
	 * Enqueue public styles.
	 *
	 * @version 1.0.3
	 */
	public function public_styles() {

	}

	/**
	 * Enqueue public scripts.
	 *
	 * @version 1.0.3
	 */
	public function public_scripts() {

	}

	/**
	 * Enqueue admin styles.
	 *
	 * @version 1.0.3
	 */
	public function admin_styles() {
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';
		$this->register_style( 'ea-admin-styles', 'admin.min.css' );
		$this->register_style( 'ea-release-styles', 'release.min.css' );
		$this->register_style( 'jquery-ui-styles', 'jquery-ui.min.css' );
		// Admin styles for Accounting pages only.
		if ( in_array( $screen_id, eaccounting_get_screen_ids(), true ) ) {
			wp_enqueue_style( 'ea-admin-styles' );
			wp_enqueue_style( 'jquery-ui-styles' );
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
		$eaccounting_screen_id = sanitize_title( __( 'Accounting', 'wp-ever-accounting' ) );
		// 3rd parties.
		$this->register_script( 'jquery-blockui', 'jquery.blockUI.min.js', array( 'jquery' ), false );
		$this->register_script( 'jquery-select2', 'select2.full.js', array( 'jquery' ), false );
		$this->register_script( 'jquery-inputmask', 'jquery.inputmask.js', array( 'jquery' ), false );
		$this->register_script( 'chartjs', 'chart.bundle.js', array(), false );
		$this->register_script( 'chartjs-labels', 'chartjs-plugin-labels.js', array( 'chartjs' ), false );
		$this->register_script( 'jquery-print-this', 'printThis.js', array( 'jquery' ), false );

		// Core plugins.
		$this->register_script( 'ea-select', 'ea-select2.js', array( 'jquery', 'jquery-select2' ), false );
		$this->register_script(
			'ea-creatable',
			'ea-creatable.js',
			array(
				'jquery',
				'ea-select',
				'wp-util',
				'ea-modal',
				'jquery-blockui',
			),
			false
		);
		$this->register_script( 'ea-modal', 'ea-modal.js', array( 'jquery' ), false );
		$this->register_script( 'ea-form', 'ea-form.js', array( 'jquery' ), false );
		$this->register_script( 'ea-exporter', 'ea-exporter.js', array( 'jquery' ), false );
		$this->register_script( 'ea-importer', 'ea-importer.js', array( 'jquery' ), false );

		// Core script.
		$this->register_script( 'ea-helper', 'ea-helper.js', array( 'jquery', 'jquery-blockui' ), false );
		$this->register_script(
			'ea-overview',
			'ea-overview.js',
			array(
				'jquery',
				'jquery-daterange',
				'chartjs',
			),
			false
		);
		$this->register_script( 'ea-settings', 'ea-settings.js', array( 'jquery' ), false );
		$this->register_script( 'ea-admin', 'ea-admin.js', array( 'jquery' ), false );

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
			// Export page.
			$tab = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_STRING );
			if ( eaccounting_is_admin_page( 'ea-tools' ) && 'export' === $tab ) {
				wp_enqueue_script( 'ea-exporter' );
			}

			// Import page.
			if ( eaccounting_is_admin_page( 'ea-tools' ) && 'import' === $tab ) {
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
					'currency'   => \EverAccounting\Currencies::get_currency_by_code( $default_currency )->get_data(),
					'currencies' => \EverAccounting\Currencies::get_currencies(
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

	/**
	 * Load public styles.
	 *
	 * @since 1.1.0
	 */
	public function eaccounting_styles() {
		$version = eaccounting()->get_version();
		wp_register_style(
			'ea-public-styles',
			eaccounting()->plugin_url() . '/dist/css/public.min.css',
			array(
				'common',
				'buttons',
			),
			$version
		);
		wp_print_styles( 'ea-public-styles' );
	}

	/**
	 * Load public scripts
	 *
	 * @since 1.1.0
	 */
	public function eaccounting_scripts() {
		$suffix  = '';
		$version = eaccounting()->get_version();
	}

	/**
	 * Register style.
	 *
	 * @param string $handle style handler.
	 * @param string $file_path style file path.
	 * @param array  $dependencies style dependencies.
	 * @param bool   $has_rtl support RTL?
	 */
	protected function register_style( $handle, $file_path, $dependencies = array(), $has_rtl = true ) {
		$filename = is_null( $file_path ) ? $handle : $file_path;
		$file_url = $this->get_asset_dist_url( $filename, 'css' );
		$version  = EACCOUNTING_VERSION;
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$version = time();
		}

		wp_register_style( $handle, $file_url, $dependencies, $version );

		if ( $has_rtl && function_exists( 'wp_style_add_data' ) ) {
			wp_style_add_data( $handle, 'rtl', 'replace' );
		}
	}

	/**
	 * Registers a script according to `wp_register_script`, additionally loading the translations for the file.
	 *
	 * @param string $handle Name of the script. Should be unique.
	 * @param string $file_path file path from dist directory
	 * @param array  $dependencies Optional. An array of registered script handles this script depends on. Default empty array.
	 * @param bool   $has_i18n Optional. Whether to add a script translation call to this file. Default 'true'.
	 *
	 * @since 1.0.0
	 */
	protected function register_script( $handle, $file_path = null, $dependencies = array(), $has_i18n = true ) {
		$filename             = is_null( $file_path ) ? $handle : $file_path;
		$file_url             = $this->get_asset_dist_url( $filename );
		$filename             = str_replace( [ '.min', '.js' ], '', $filename );
		$dependency_file_path = $this->get_asset_dist_path( $filename . '.asset', 'php' );
		$version              = EACCOUNTING_VERSION;
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$version = time();
		}
		if ( file_exists( $dependency_file_path ) ) {
			$asset        = require $dependency_file_path;
			$dependencies = isset( $asset['dependencies'] ) ? array_merge( $asset['dependencies'], $dependencies ) : $dependencies;
			$version      = ! empty( $asset['version'] ) ? $asset['version'] : $version;
		}
		wp_register_script( $handle, $file_url, $dependencies, $version, true );

		if ( $has_i18n && function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( $handle, 'wp-ever-accounting', dirname( __DIR__ ) . '/languages' );
		}
	}

	/**
	 * Returns the appropriate asset url
	 *
	 * @param string $filename Filename for asset url (without extension).
	 * @param string $type File type (.css or .js).
	 *
	 * @return  string The generated path.
	 */
	protected function get_asset_dist_url( $filename, $type = 'js' ) {
		return plugins_url( "/dist/$type/$filename", EACCOUNTING_PLUGIN_FILE );
	}

	/**
	 * Returns the appropriate asset url
	 *
	 * @param string $filename Filename for asset url (without extension).
	 * @param string $type File type (.css or .js).
	 *
	 * @return  string The generated path.
	 */
	protected function get_asset_dist_path( $filename, $type = 'js' ) {
		$plugin_path = untrailingslashit( plugin_dir_path( EACCOUNTING_PLUGIN_FILE ) );

		return $plugin_path . "/dist/$type/$filename";
	}
}

new Assets();
