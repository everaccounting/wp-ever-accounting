<?php
defined( 'ABSPATH' ) || die();

class EAccounting_Settings {
	/**
	 * Setting pages.
	 *
	 * @var array
	 */
	private static $settings = array();

	/**
	 * @return array|mixed|void
	 */
	public static function get_settings_pages() {
		if ( empty( self::$settings ) ) {
			include_once dirname( __FILE__ ) . '/class-ea-settings-page.php';

			$settings = array();
			$settings[] = include 'general-settings.php';
			$settings[] = include 'tax-settings.php';

			self::$settings = apply_filters( 'eaccounting_get_settings_pages', $settings );
		}

		return self::$settings;
	}


	/**
	 *
	 */
	public static function output() {
		global $current_section, $current_tab;
		$current_tab     = empty( $_GET['tab'] ) ? 'general' : sanitize_title( wp_unslash( $_GET['tab'] ) );
		$current_section = empty( $_REQUEST['section'] ) ? '' : sanitize_title( wp_unslash( $_REQUEST['section'] ) );

		self::get_settings_pages();
		$tabs = apply_filters( 'eaccounting_settings_tabs_array', array() );

		include dirname( __FILE__ ). '/html-settings.php';
	}


	/**
	 *
	 */
	public static function save() {
		if ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( sanitize_key( $_REQUEST['_wpnonce'] ), 'eaccounting-settings' ) ) {
			global $current_tab, $current_section;

			self::get_settings_pages();

			// Get current tab/section.
			$current_tab     = empty( $_REQUEST['tab'] ) ? 'general' : sanitize_title( wp_unslash( $_REQUEST['tab'] ) );
			$current_section = empty( $_REQUEST['section'] ) ? '' : sanitize_title( wp_unslash( $_REQUEST['section'] ) );

			// Trigger actions.
			do_action( 'eaccounting_settings_save_' . $current_tab );
			do_action( 'eaccounting_update_options_' . $current_tab );
			do_action( 'eaccounting_update_options' );

			do_action( 'eaccounting_settings_saved' );
		}
	}


}
