<?php
/**
 * Load EverAccounting assets.
 *
 * @version     1.0.2
 * @package     EverAccounting
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
		self::register_style( 'ea-admin-styles', 'css/admin.css' );
		self::register_style( 'ea-release-styles', 'css/release.css' );
		self::register_style( 'jquery-ui-styles', 'css/jquery-ui.css' );
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
		self::register_script( 'jquery-blockui', 'js/jquery.blockUI.js', array( 'jquery' ), false );
		self::register_script( 'jquery-select2', 'js/select2.full.js', array( 'jquery' ), false );
		self::register_script( 'jquery-inputmask', 'js/jquery.inputmask.js', array( 'jquery' ), false );
		self::register_script( 'chartjs', 'js/chart.bundle.js', array(), false );
		self::register_script( 'chartjs-labels', 'chartjs-plugin-labels.js', array( 'chartjs' ), false );
		self::register_script( 'jquery-print-this', 'js/printThis.js', array( 'jquery' ), false );

		// Core plugins.
		self::register_script( 'ea-select', 'js/ea-select2.js', array( 'jquery', 'jquery-select2' ), false );
		self::register_script(
			'ea-creatable',
			'js/ea-creatable.js',
			array(
				'jquery',
				'ea-select',
				'wp-util',
				'ea-modal',
				'jquery-blockui',
			),
			false
		);
		self::register_script( 'ea-modal', 'js/ea-modal.js', array( 'jquery' ), false );
		self::register_script( 'ea-form', 'js/ea-form.js', array( 'jquery' ), false );
		self::register_script( 'ea-exporter', 'js/ea-exporter.js', array( 'jquery' ), false );
		self::register_script( 'ea-importer', 'js/ea-importer.js', array( 'jquery' ), false );

		// Core script.
		self::register_script( 'ea-helper', 'js/ea-helper.js', array( 'jquery', 'jquery-blockui' ), false );
		self::register_script(
			'ea-overview',
			'ea-overview.js',
			array(
				'jquery',
				'jquery-daterange',
				'chartjs',
			),
			false
		);
		self::register_script( 'ea-settings', 'js/ea-settings.js', array( 'jquery' ), false );
		self::register_script( 'ea-admin', 'js/ea-admin.js', array( 'jquery' ), false );

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
					'asset_url'  => eaccounting()->plugin_url( '/assets/dist' ),
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
		self::register_style( 'ea-public-styles', 'css/public.css', [ 'common', 'button' ] );
		wp_print_styles( 'ea-public-styles' );
	}

	/**
	 * Register style.
	 *
	 * @param string $style_handle style handler.
	 * @param string $style_path style file path.
	 * @param array $dependencies style dependencies.
	 * @param bool $has_rtl support RTL?
	 */
	public static function register_style( $style_handle, $style_path, $dependencies = array(), $has_rtl = true ) {
		$style_asset_path = untrailingslashit( plugin_dir_path( EACCOUNTING_PLUGIN_FILE ) ) . '/assets/dist/' . ltrim( substr_replace( $style_path, '.asset.php', - strlen( '.css' ) ), '/' );
		$style_asset_url  = untrailingslashit( plugin_dir_url( EACCOUNTING_PLUGIN_FILE ) ) . '/assets/dist/' . ltrim( $style_path, '/' );
		$version          = EACCOUNTING_VERSION;
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$version = time();
		}
		if ( file_exists( $style_asset_path ) ) {
			$asset        = require $style_asset_path;
			$dependencies = isset( $asset['dependencies'] ) ? array_merge( $asset['dependencies'], $dependencies ) : $dependencies;
			$version      = ! empty( $asset['version'] ) ? $asset['version'] : $version;
		}

		$added = wp_register_style( $style_handle, $style_asset_url, $dependencies, $version );

		if ( $has_rtl && function_exists( 'wp_style_add_data' ) ) {
			wp_style_add_data( $style_handle, 'rtl', 'replace' );
		}

		return $added;
	}

	/**
	 * Registers a script according to `wp_register_script`, additionally loading the translations for the file.
	 *
	 * @param string $script_handle Name of the script. Should be unique.
	 * @param string $script_path file path from dist directory
	 * @param array $dependencies Optional. An array of registered script handles this script depends on. Default empty array.
	 * @param bool $has_i18n Optional. Whether to add a script translation call to this file. Default 'true'.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Add $has_i18n parameter.
	 *
	 * @return bool
	 */
	public static function register_script( $script_handle, $script_path, $dependencies = array(), $has_i18n = false ) {
		$script_asset_path = untrailingslashit( plugin_dir_path( EACCOUNTING_PLUGIN_FILE ) ) . '/assets/dist/' . ltrim( substr_replace( $script_path, '.asset.php', - strlen( '.js' ) ), '/' );
		$script_asset_url  = untrailingslashit( plugin_dir_url( EACCOUNTING_PLUGIN_FILE ) ) . '/assets/dist/' . ltrim( $script_path, '/' );
		$version           = EACCOUNTING_VERSION;
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$version = time();
		}
		if ( file_exists( $script_asset_path ) ) {
			$asset        = require $script_asset_path;
			$dependencies = isset( $asset['dependencies'] ) ? array_merge( $asset['dependencies'], $dependencies ) : $dependencies;
			$version      = ! empty( $asset['version'] ) ? $asset['version'] : $version;
		}

		$added = wp_register_script( $script_handle, $script_asset_url, $dependencies, $version, true );

		if ( $has_i18n && function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( $script_handle, 'wp-ever-accounting', dirname( __DIR__ ) . '/languages' );
		}

		return $added;
	}
}

new Assets();
