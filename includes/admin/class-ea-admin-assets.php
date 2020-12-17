<?php
/**
 * Load assets.
 *
 * @package     EverAccounting
 * @subpackage  Admin
 * @version     1.0.2
 */

namespace EverAccounting\Admin;

use EverAccounting\Core\DateTime;

defined( 'ABSPATH' ) || exit();

class Admin_Assets {
	/**
	 * Hook in tabs.
	 *
	 * @version 1.0.2
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
	}

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
		wp_register_style( 'ea-admin-styles', eaccounting()->plugin_url() . '/assets/css/admin.css', array(), $version );
		wp_register_style( 'jquery-ui-style', eaccounting()->plugin_url() . '/assets/css/jquery-ui/jquery-ui.min.css', array(), $version );

		// Add RTL support for admin styles.
		wp_style_add_data( 'ea-admin-styles', 'rtl', 'replace' );

		// Admin styles for Accounting pages only.
		if ( in_array( $screen_id, eaccounting_get_screen_ids(), true ) ) {
			wp_enqueue_style( 'ea-admin-styles' );
			wp_enqueue_style( 'jquery-ui-style' );
		}

		// React Styles
		// wp_register_style( 'ea-components', self::get_asset_dist_url( 'components', 'css' ), array( 'wp-components' ), $version );
		// wp_register_style( 'ea-invoice', self::get_asset_dist_url( 'invoice', 'css' ), array( 'ea-components', 'wp-components' ), $version );
		// wp_register_style( 'ea-app', self::get_asset_dist_url( 'app', 'css' ), array( 'ea-components', 'wp-components' ), $version );
		// wp_enqueue_style( 'ea-invoice' );
		// wp_enqueue_style( 'ea-app' );
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
		//$suffix                = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$suffix  = '';
		$version = eaccounting()->get_version();

		// 3rd parties
		wp_register_script( 'jquery-blockui', eaccounting()->plugin_url( '/assets/js/jquery-blockui/jquery.blockUI' . $suffix . '.js' ), array( 'jquery' ), '2.70', true );
		wp_register_script( 'jquery-tiptip', eaccounting()->plugin_url( '/assets/js/jquery-tiptip/jquery.tipTip' . $suffix . '.js' ), array( 'jquery' ), $version, true );
		wp_register_script( 'jquery-select2', eaccounting()->plugin_url( '/assets/js/select2/select2.full' . $suffix . '.js' ), array( 'jquery' ), $version );
		wp_register_script( 'jquery-inputmask', eaccounting()->plugin_url( '/assets/js/inputmask/jquery.inputmask' . $suffix . '.js' ), array( 'jquery' ), '1.0.2' );
		wp_register_script( 'jquery-chartjs', eaccounting()->plugin_url( '/assets/js/chartjs/chart.bundle' . $suffix . '.js' ), array( 'jquery' ), '1.0.2' );

		// core plugins
		wp_register_script( 'ea-select', eaccounting()->plugin_url( '/assets/js/eaccounting/ea-select2' . $suffix . '.js' ), array( 'jquery', 'jquery-select2' ), $version );
		wp_register_script( 'ea-creatable', eaccounting()->plugin_url( '/assets/js/eaccounting/ea-creatable' . $suffix . '.js' ), array( 'jquery', 'ea-select', 'wp-util', 'ea-modal', 'jquery-blockui' ), $version );
		wp_register_script( 'ea-modal', eaccounting()->plugin_url( '/assets/js/eaccounting/ea-modal' . $suffix . '.js' ), array( 'jquery' ), $version );
		wp_register_script( 'ea-notice', eaccounting()->plugin_url( '/assets/js/eaccounting/ea-notice' . $suffix . '.js' ), array( 'jquery' ), $version );
		wp_register_script( 'ea-form', eaccounting()->plugin_url( '/assets/js/eaccounting/ea-form' . $suffix . '.js' ), array( 'jquery' ), $version );
		wp_register_script( 'ea-exporter', eaccounting()->plugin_url( '/assets/js/eaccounting/ea-exporter' . $suffix . '.js' ), array( 'jquery' ), $version );
		wp_register_script( 'ea-importer', eaccounting()->plugin_url( '/assets/js/eaccounting/ea-importer' . $suffix . '.js' ), array( 'jquery' ), $version );

		// core script
		wp_register_script( 'ea-helper', eaccounting()->plugin_url( '/assets/js/eaccounting/ea-helper' . $suffix . '.js' ), array( 'jquery', 'jquery-blockui' ), $version );
		wp_register_script( 'ea-overview', eaccounting()->plugin_url( '/assets/js/eaccounting/ea-overview' . $suffix . '.js' ), array( 'jquery', 'jquery-daterange', 'jquery-chartjs' ), $version );
		wp_register_script( 'ea-settings', eaccounting()->plugin_url( '/assets/js/eaccounting/ea-settings' . $suffix . '.js' ), array( 'jquery' ), $version );
		wp_register_script( 'ea-admin', eaccounting()->plugin_url( '/assets/js/eaccounting/ea-admin' . $suffix . '.js' ), array( 'jquery' ), $version );

		// Admin scripts for Accounting pages only.
		if ( in_array( $screen_id, eaccounting_get_screen_ids(), true ) ) {
			// globally needed scripts
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_script( 'jquery-tiptip' );
			wp_enqueue_script( 'jquery-select2' );
			wp_enqueue_script( 'jquery-inputmask' );
			wp_enqueue_script( 'jquery-blockui' );
			wp_enqueue_script( 'ea-notice' );
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


			//
			//          wp_localize_script(
			//              'ea-admin',
			//              'eaccounting_admin_i10n',
			//              array(
			//                  'ajaxurl'           => eaccounting()->ajax_url(),
			//                  'global_currencies' => eaccounting_get_global_currencies(),
			//                  'nonce'             => array(
			//                      'get_account'  => wp_create_nonce( 'ea_get_account' ),
			//                      'get_currency' => wp_create_nonce( 'ea_get_currency' ),
			//                  ),
			//                  'datepicker'        => array(
			//                      'locale' => array(
			//                          'format'           => 'D MMM YY',
			//                          'separator'        => '  >>  ',
			//                          'applyLabel'       => __( 'Apply', 'wp-ever-accounting' ),
			//                          'cancelLabel'      => __( 'Cancel', 'wp-ever-accounting' ),
			//                          'fromLabel'        => __( 'From', 'wp-ever-accounting' ),
			//                          'toLabel'          => __( 'To', 'wp-ever-accounting' ),
			//                          'customRangeLabel' => __( 'Custom', 'wp-ever-accounting' ),
			//                          'daysOfWeek'       => array( 'Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa' ),
			//                          'monthNames'       => array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' ),
			//                          'firstDay'         => get_option( 'start_of_week' ),
			//                      ),
			//                  ),
			//              )
			//          );
			//
			//          // all admin page
			//
			//          // overview page
			//          if ( 'toplevel_page_eaccounting' === $screen_id ) {
			//              $financial_start    = eaccounting_get_financial_start();
			//              $financial_start_dt = new DateTime( $financial_start );
			//              $today_dt           = new DateTime();
			//              $date_format        = 'Y-m-d';
			//              wp_localize_script(
			//                  'ea-overview',
			//                  'eaccounting_overview_i10n',
			//                  array(
			//                      'datepicker' => array(
			//                          'locale' => array(
			//                              'format'           => 'YYYY-MM-DD',
			//                              'separator'        => '  >>  ',
			//                              'applyLabel'       => __( 'Apply', 'wp-ever-accounting' ),
			//                              'cancelLabel'      => __( 'Cancel', 'wp-ever-accounting' ),
			//                              'fromLabel'        => __( 'From', 'wp-ever-accounting' ),
			//                              'toLabel'          => __( 'To', 'wp-ever-accounting' ),
			//                              'customRangeLabel' => __( 'Custom', 'wp-ever-accounting' ),
			//                              'daysOfWeek'       => array( 'Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa' ),
			//                              'monthNames'       => array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' ),
			//                              'firstDay'         => get_option( 'start_of_week' ),
			//                          ),
			//                          'ranges' => array(
			//                              __( 'This Year', 'wp-ever-accounting' )      => array(
			//                                  $financial_start,
			//                                  $financial_start_dt->copy()->add( new \DateInterval( 'P1Y' ) )->sub( new \DateInterval( 'P1D' ) )->format( $date_format ),
			//                              ),
			//                              __( 'Last Year', 'wp-ever-accounting' )      => array(
			//                                  $financial_start_dt->copy()->sub( new \DateInterval( 'P1Y' ) )->format( $date_format ),
			//                                  $financial_start_dt->copy()->sub( new \DateInterval( 'P1D' ) )->format( $date_format ),
			//                              ),
			//                              __( 'Last 12 Months', 'wp-ever-accounting' ) => array(
			//                                  $today_dt->copy()->sub( new \DateInterval( 'P1Y' ) )->sub( new \DateInterval( 'P1D' ) )->format( $date_format ),
			//                                  $today_dt->copy()->format( $date_format ),
			//                              ),
			//                          ),
			//                      ),
			//                  )
			//              );
			//
			//              wp_enqueue_script( 'ea-overview' );
		}
		//
		//          // export page
		//          if ( eaccounting_is_admin_page( 'ea-tools' ) && isset( $_GET['tab'] ) && 'export' === $_GET['tab'] ) {
		//              wp_enqueue_script( 'ea-exporter' );
		//          }
		//
		//          // import page
		//          if ( eaccounting_is_admin_page( 'ea-tools' ) && isset( $_GET['tab'] ) && 'import' === $_GET['tab'] ) {
		//              wp_localize_script(
		//                  'ea-importer',
		//                  'eaccounting_importer_i10n',
		//                  array(
		//                      'uploaded_file_not_found' => esc_html__( 'Could not find the uploaded file, please try again', 'wp-ever-accounting' ),
		//                      'select_field_to_preview' => esc_html__( '  - Select field to preview data -', 'wp-ever-accounting' ),
		//                      'required'                => esc_html__( '(Required)', 'wp-ever-accounting' ),
		//                  )
		//              );
		//              wp_enqueue_script( 'ea-importer' );
		//          }
		//
		//          // settings page
		//          if ( eaccounting_is_admin_page( 'ea-settings' ) ) {
		//              wp_enqueue_media();
		//              wp_enqueue_script( 'ea-settings' );
		//          }
		//
		//          // report page
		//          if ( eaccounting_is_admin_page( 'ea-reports' ) ) {
		//              wp_enqueue_script( 'jquery-chartjs' );
		//          }
		//      }

		// React scripts
		//self::register_react_script( 'ea-data', self::get_asset_dist_url( 'data' ) );
		//self::register_react_script( 'ea-components', self::get_asset_dist_url( 'components' ) );
		//self::register_react_script( 'ea-invoice', self::get_asset_dist_url( 'invoice' ), array( 'ea-data', 'ea-components' ) );

		//      wp_localize_script(
		//          'ea-data',
		//          'eaccountingi10n',
		//          array(
		//              'site_url'   => site_url(),
		//              'admin_url'  => admin_url(),
		//              'asset_url'  => eaccounting()->plugin_url( '/assets' ),
		//              'plugin_url' => eaccounting()->plugin_url(),
		//          )
		//      );
		      wp_enqueue_media();
		//      wp_enqueue_script( 'ea-invoice' );

		//print scripts
		wp_register_script( 'ea-print', eaccounting()->plugin_url( '/assets/js/printjs/printThis' . $suffix . '.js' ), array( 'jquery' ), $version,true );

	}


	/**
	 * Registers a script according to `wp_register_script`, additionally loading the translations for the file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $src          Full URL of the script, or path of the script relative to the WordPress root directory.
	 * @param array  $dependencies Optional. An array of registered script handles this script depends on. Default empty array.
	 * @param bool   $has_i18n     Optional. Whether to add a script translation call to this file. Default 'true'.
	 *
	 * @param string $handle       Name of the script. Should be unique.
	 */
	protected static function register_react_script( $handle, $src, $dependencies = array(), $has_i18n = true ) {
		$relative_src = str_replace( plugins_url( '/', EACCOUNTING_PLUGIN_FILE ), '', $src );
		$asset_path   = str_replace( '.js', '.asset.php', eaccounting()->plugin_path( $relative_src ) );

		$version = eaccounting()->get_version();
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$version = time();
		}
		if ( file_exists( $asset_path ) ) {
			$asset        = require $asset_path;
			$dependencies = isset( $asset['dependencies'] ) ? array_merge( $asset['dependencies'], $dependencies ) : $dependencies;
			$version      = ! empty( $asset['version'] ) ? $asset['version'] : $version;
		}

		wp_register_script( $handle, $src, $dependencies, $version, true );

		if ( $has_i18n && function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( $handle, 'wp-ever-accounting', dirname( __DIR__ ) . '/languages' );
		}
	}

	/**
	 * Returns the appropriate asset url
	 *
	 * @param string $filename Filename for asset url (without extension).
	 * @param string $type     File type (.css or .js).
	 *
	 * @return  string The generated path.
	 */
	protected static function get_asset_dist_url( $filename, $type = 'js' ) {
		return eaccounting()->plugin_url( "assets/dist/$filename.$type" );
	}
}

return new Admin_Assets();
