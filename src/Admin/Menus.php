<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Menus.
 *
 * @since   1.0.0
 * @package EverAccounting\Admin
 */
class Menus extends \EverAccounting\Singleton {

	/**
	 * Menus constructor.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {
		add_action( 'wp_loaded', array( $this, 'save_settings' ) );
		add_action( 'admin_menu', array( $this, 'register_settings_page' ), 100 );
	}

	/**
	 * Save settings.
	 *
	 * @since 1.0.0
	 */
	public function save_settings() {
		global $current_tab, $current_section;

		// We should only save on the settings page.
		if ( ! is_admin() || ! isset( $_GET['page'] ) || 'ea-settings' !== $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		Settings::get_tabs();
		// Get current tab/section.
		$current_tab     = empty( $_GET['tab'] ) ? 'general' : sanitize_title( wp_unslash( $_GET['tab'] ) ); // WPCS: input var okay, CSRF ok.
		$current_section = empty( $_REQUEST['section'] ) ? '' : sanitize_title( wp_unslash( $_REQUEST['section'] ) ); // WPCS: input var okay, CSRF ok.

		// Save settings if data has been posted.
		if ( '' !== $current_section && apply_filters( "ever_accounting_save_settings_{$current_tab}_{$current_section}", ! empty( $_POST['save'] ) ) ) { // WPCS: input var okay, CSRF ok.
			Settings::save();
		} elseif ( '' === $current_section && apply_filters( "ever_accounting_save_settings_{$current_tab}", ! empty( $_POST['save'] ) ) ) { // WPCS: input var okay, CSRF ok.
			Settings::save();
		}
	}

	/**
	 * Register settings page.
	 *
	 * @since 1.0.0
	 */
	public function register_settings_page() {
		$hook = add_submenu_page(
			'eaccounting',
			__( 'Settings', 'wp-ever-accounting' ),
			__( 'Settings', 'wp-ever-accounting' ),
			'manage_eaccounting',
			'ea-settings',
			array( Settings::class, 'output' )
		);

		add_action( "load-$hook", array( $this, 'settings_page_load' ) );
	}

	/**
	 * Settings page load.
	 *
	 * @since 1.0.0
	 */
	public function settings_page_load() {
		do_action( 'ever_accounting_settings_page_load' );
	}
}
