<?php
/**
 * Load assets.
 *
 * @package     EverAccounting
 * @subpackage  Admin
 * @version     1.0.2
 */

namespace EverAccounting\Admin;

use EverAccounting\DateTime;

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
		if ( in_array( $screen_id, eaccounting_get_screen_ids() ) ) {
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
		$suffix                = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$version               = eaccounting()->get_version();

		//3rd parties
		wp_register_script( 'jquery-blockui', eaccounting()->plugin_url() . '/assets/js/jquery-blockui/jquery.blockUI' . $suffix . '.js', array( 'jquery' ), '2.70', true );
		wp_register_script( 'jquery-tiptip', eaccounting()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip' . $suffix . '.js', array( 'jquery' ), $version, true );
		//wp_register_script( 'jquery-pace', eaccounting()->plugin_url() . '/assets/js/pace/pace' . $suffix . '.js', array( 'jquery' ), '1.0.2' );
		wp_register_script( 'select2', eaccounting()->plugin_url() . '/assets/js/select2/select2.full' . $suffix . '.js', array( 'jquery' ), $version );
		wp_register_script( 'ea-backbone-modal', eaccounting()->plugin_url() . '/assets/js/eaccounting/ea-backbone-modal' . $suffix . '.js', array( 'underscore', 'backbone', 'wp-util' ), $version );
		wp_register_script( 'ea-notice', eaccounting()->plugin_url() . '/assets/js/eaccounting/ea-notice' . $suffix . '.js', array( 'jquery' ), '1.0.2' );
		wp_register_script( 'jquery-inputmask', eaccounting()->plugin_url() . '/assets/js/inputmask/jquery.inputmask' . $suffix . '.js', array( 'jquery' ), '1.0.2' );
		wp_register_script( 'ea-chartjs', eaccounting()->plugin_url() . '/assets/js/chartjs/chart.bundle' . $suffix . '.js', array( 'jquery' ), '1.0.2' );
		wp_register_script( 'moment-js', eaccounting()->plugin_url() . '/assets/js/moment/moment' . $suffix . '.js', array( 'jquery' ), '1.0.2' );
		wp_register_script( 'ea-daterange', eaccounting()->plugin_url() . '/assets/js/daterange/daterangepicker' . $suffix . '.js', array( 'jquery', 'moment-js' ), '1.0.2' );

		//core js
		wp_register_script( 'eaccounting', eaccounting()->plugin_url() . '/assets/js/eaccounting/eaccounting' . $suffix . '.js', array( 'jquery', 'ea-backbone-modal' ), $version );
		wp_register_script( 'ea-settings', eaccounting()->plugin_url() . '/assets/js/admin/admin-settings' . $suffix . '.js', array( 'jquery' ), $version );
		wp_register_script( 'ea-admin', eaccounting()->plugin_url() . '/assets/js/admin/eaccounting-admin' . $suffix . '.js', array( 'jquery' ), $version );
		wp_register_script( 'ea-dashboard', eaccounting()->plugin_url() . '/assets/js/admin/admin-dashboard' . $suffix . '.js', array( 'jquery', 'ea-daterange' ), $version );
		wp_register_script( 'ea-exporter', eaccounting()->plugin_url() . '/assets/js/admin/ea-exporter' . $suffix . '.js', array( 'jquery', 'backbone', 'wp-util' ), $version );

		// Admin scripts for Accounting pages only.
		if ( in_array( $screen_id, eaccounting_get_screen_ids() ) ) {
			wp_enqueue_script( 'ea-chartjs' );
			wp_enqueue_script( 'jquery-pace' );
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_script( 'ea-notice' );
			wp_enqueue_script( 'jquery-tiptip' );
			wp_enqueue_script( 'select2' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_script( 'jquery-inputmask' );
			wp_enqueue_script( 'jquery-blockui' );
			wp_enqueue_script( 'jquery-tiptip' );
			wp_enqueue_script( 'ea-backbone-modal' );
			wp_enqueue_script( 'eaccounting' );
			wp_enqueue_script( 'ea-admin' );
			wp_enqueue_script( 'ea-batch' );
			wp_enqueue_script( 'ea-dashboard' );

			wp_localize_script( 'ea-admin', 'eaccounting_admin_i10n', array(
				'ajaxurl'           => eaccounting()->ajax_url(),
				'global_currencies' => eaccounting_get_global_currencies()
			) );


			$financial_start    = eaccounting_get_financial_start();
			$financial_start_dt = new DateTime( $financial_start );
			$today_dt           = new DateTime();
			$date_format        = 'Y-m-d';
			wp_localize_script( 'ea-dashboard', 'eaccounting_dashboard_i10n', array(
				'datepicker' => array(
					'locale' => array(
						'format'           => 'YYYY-MM-DD',
						'separator'        => '  >>  ',
						'applyLabel'       => __( 'Apply', 'wp-ever-accounting' ),
						'cancelLabel'      => __( 'Cancel', 'wp-ever-accounting' ),
						'fromLabel'        => __( 'From', 'wp-ever-accounting' ),
						'toLabel'          => __( 'To', 'wp-ever-accounting' ),
						'customRangeLabel' => __( 'Custom', 'wp-ever-accounting' ),
						'daysOfWeek'       => [ 'Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa' ],
						'monthNames'       => [ 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' ],
						'firstDay'         => get_option( 'start_of_week' ),
					),
					'ranges' => array(
						__( 'This Year', 'wp-ever-accounting' )      => array(
							$financial_start,
							$financial_start_dt->clone()->add( new \DateInterval( 'P1Y' ) )->sub( new \DateInterval( 'P1D' ) )->format( $date_format )
						),
						__( 'Last Year', 'wp-ever-accounting' )      => array(
							$financial_start_dt->clone()->sub( new \DateInterval( 'P1Y' ) )->format( $date_format ),
							$financial_start_dt->clone()->sub( new \DateInterval( 'P1D' ) )->format( $date_format ),
						),
//						__( 'This Quarter', 'wp-ever-accounting' )   => array(
//							$financial_start,
//							$financial_start_dt->clone()->add( new \DateInterval( 'P1Y' ) )->sub( new \DateInterval( 'P1D' ) )->format( $date_format )
//						),
//						__( 'Last Quarter', 'wp-ever-accounting' )   => array(
//							$financial_start_dt->clone()->sub( new \DateInterval( 'P1Y' ) )->format( $date_format ),
//							$financial_start_dt->clone()->sub( new \DateInterval( 'P1D' ) )->format( $date_format ),
//						),
						__( 'Last 12 Months', 'wp-ever-accounting' ) => array(
							$today_dt->clone()->sub( new \DateInterval( 'P1Y' ) )->sub( new \DateInterval( 'P1D' ) )->format( $date_format ),
							$today_dt->clone()->format( $date_format ),
						)
					)
				)
			) );


			wp_localize_script( 'eaccounting', 'eaccounting_i10n', array(
				'ajaxurl'           => eaccounting()->ajax_url(),
				'global_currencies' => eaccounting_get_global_currencies(),
				'nonce'             => array(
					'get_account'  => wp_create_nonce( 'ea_get_account' ),
					'get_currency' => wp_create_nonce( 'ea_get_currency' ),
				),
				'datepicker'        => array(
					'locale' => array(
						'format'           => 'D MMM YY',
						'separator'        => '  >>  ',
						'applyLabel'       => __( 'Apply', 'wp-ever-accounting' ),
						'cancelLabel'      => __( 'Cancel', 'wp-ever-accounting' ),
						'fromLabel'        => __( 'From', 'wp-ever-accounting' ),
						'toLabel'          => __( 'To', 'wp-ever-accounting' ),
						'customRangeLabel' => __( 'Custom', 'wp-ever-accounting' ),
						'daysOfWeek'       => [ 'Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa' ],
						'monthNames'       => [ 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' ],
						'firstDay'         => get_option( 'start_of_week' ),
					),
				)
			) );

			if ( eaccounting_is_admin_page( 'ea-settings' ) ) {
				wp_enqueue_media();
				wp_enqueue_script( 'ea-settings' );
			}

			if ( eaccounting_is_admin_page( 'ea-tools' ) && isset( $_GET['tab'] ) && 'export' == $_GET['tab'] ) {
				wp_enqueue_script( 'ea-exporter' );
			}

		}
	}
}

return new Admin_Assets();