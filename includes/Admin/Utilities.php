<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Utilities class.
 *
 * @since 1.0.0
 * @package EverAccounting\Admin
 */
class Utilities {
	/**
	 * Get admin menus.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public static function get_menus() {
		$menus = array(
			array(
				'page_title' => __( 'Items', 'wp-ever-accounting' ),
				'menu_title' => __( 'Items', 'wp-ever-accounting' ),
				'capability' => 'manage_options',
				'menu_slug'  => 'eac-items',
				'page_hook'  => 'items',
			),
			array(
				'page_title' => __( 'Sales', 'wp-ever-accounting' ),
				'menu_title' => __( 'Sales', 'wp-ever-accounting' ),
				'capability' => 'manage_options',
				'menu_slug'  => 'eac-sales',
				'page_hook'  => 'sales',
				'tabs'       => array(
					'payments'  => __( 'Payments', 'wp-ever-accounting' ),
					'invoices'  => __( 'Invoices', 'wp-ever-accounting' ),
					'customers' => __( 'Customers', 'wp-ever-accounting' ),
				),
			),
			array(
				'page_title' => __( 'Purchases', 'wp-ever-accounting' ),
				'menu_title' => __( 'Purchases', 'wp-ever-accounting' ),
				'capability' => 'manage_options',
				'menu_slug'  => 'eac-purchases',
				'page_hook'  => 'purchases',
				'tabs'       => array(
					'expenses' => __( 'Expenses', 'wp-ever-accounting' ),
					'bills'    => __( 'Bills', 'wp-ever-accounting' ),
					'vendors'  => __( 'Vendors', 'wp-ever-accounting' ),
				),
			),
			array(
				'page_title' => __( 'Banking', 'wp-ever-accounting' ),
				'menu_title' => __( 'Banking', 'wp-ever-accounting' ),
				'capability' => 'manage_options',
				'menu_slug'  => 'eac-banking',
				'page_hook'  => 'banking',
				'tabs'       => array(
					'accounts'     => __( 'Accounts', 'wp-ever-accounting' ),
					'transactions' => __( 'Transactions', 'wp-ever-accounting' ),
					'transfers'    => __( 'Transfers', 'wp-ever-accounting' ),
				),
			),
			array(
				'page_title' => __( 'Misc', 'wp-ever-accounting' ),
				'menu_title' => __( 'Misc', 'wp-ever-accounting' ),
				'capability' => 'manage_options',
				'menu_slug'  => 'eac-misc',
				'page_hook'  => 'misc',
				'tabs'       => array(
					'categories' => __( 'Categories', 'wp-ever-accounting' ),
					'currencies' => __( 'Currencies', 'wp-ever-accounting' ),
					'taxes'      => __( 'Taxes', 'wp-ever-accounting' ),
				),
			),
			array(
				'page_title' => __( 'Tools', 'wp-ever-accounting' ),
				'menu_title' => __( 'Tools', 'wp-ever-accounting' ),
				'capability' => 'manage_options',
				'menu_slug'  => 'eac-tools',
				'page_hook'  => 'tools',
				'tabs'       => array(
					'import' => __( 'Import', 'wp-ever-accounting' ),
					'export' => __( 'Export', 'wp-ever-accounting' ),
				),
			),
			array(
				'page_title' => __( 'Reports', 'wp-ever-accounting' ),
				'menu_title' => __( 'Reports', 'wp-ever-accounting' ),
				'capability' => 'manage_options',
				'menu_slug'  => 'eac-reports',
				'page_hook'  => 'reports',
				'tabs'       => array(
					'payments' => __( 'payments', 'wp-ever-accounting' ),
					'expenses' => __( 'Expenses', 'wp-ever-accounting' ),
					'profits'  => __( 'Profits', 'wp-ever-accounting' ),
					'taxes'    => __( 'Taxes', 'wp-ever-accounting' ),
				),
			),
			array(
				'page_title' => __( 'Settings', 'wp-ever-accounting' ),
				'menu_title' => __( 'Settings', 'wp-ever-accounting' ),
				'capability' => 'manage_options',
				'menu_slug'  => 'eac-settings',
				'callback'   => array( Settings::class, 'output' ),
				'load_hook'  => array( Settings::class, 'get_tabs' ),
			),
		);

		return apply_filters( 'ever_accounting_admin_menus', $menus );
	}

	/**
	 * Get page ids.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public static function get_screen_ids() {
		$screen_ids = array(
			'toplevel_page_' . Menus::PARENT_SLUG,
			Menus::PARENT_SLUG . '_page_dashboard',
			Menus::PARENT_SLUG . '_page_settings',
		);

		foreach ( self::get_menus() as $page ) {
			$screen_ids[] = Menus::PARENT_SLUG . '_page_' . $page['menu_slug'];
		}

		return $screen_ids;
	}

	/**
	 * Determine if current page is add screen.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public static function is_add_screen() {
		return isset( $_GET['add'] ) ? true : false; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	/**
	 * Determine if current page is edit screen.
	 *
	 * @since 1.0.0
	 * @return false|int False if not edit screen, id if edit screen.
	 */
	public static function is_edit_screen() {
		return filter_input( INPUT_GET, 'edit', FILTER_VALIDATE_INT );
	}

	/**
	 * Determine if current page is view screen.
	 *
	 * @since 1.0.0
	 * @return false|int False if not view screen, id if view screen.
	 */
	public static function is_view_screen() {
		return filter_input( INPUT_GET, 'view', FILTER_VALIDATE_INT );
	}
}
