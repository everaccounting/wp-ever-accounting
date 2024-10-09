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
				'capability' => 'manage_options',
				'menu_slug'  => 'eac-items',
				'menu_title' => __( 'Items', 'wp-ever-accounting' ),
				'page_title' => __( 'Items', 'wp-ever-accounting' ),
				'tabs'       => array(
					'items' => __( 'Items', 'wp-ever-accounting' ),
				),
			),
			array(
				'page_title' => __( 'Sales', 'wp-ever-accounting' ),
				'menu_title' => __( 'Sales', 'wp-ever-accounting' ),
				'capability' => 'manage_options',
				'menu_slug'  => 'eac-sales',
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
				'tabs'       => array(
					'accounts'     => __( 'Accounts', 'wp-ever-accounting' ),
					'transfers'    => __( 'Transfers', 'wp-ever-accounting' ),
					'transactions' => __( 'Transactions', 'wp-ever-accounting' ),
				),
			),
			array(
				'page_title' => __( 'Tools', 'wp-ever-accounting' ),
				'menu_title' => __( 'Tools', 'wp-ever-accounting' ),
				'capability' => 'manage_options',
				'menu_slug'  => 'eac-tools',
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
				'tabs'       => array(
					'sales'    => __( 'Sales', 'wp-ever-accounting' ),
					'expenses' => __( 'Expenses', 'wp-ever-accounting' ),
					'profits'  => __( 'Profit & Loss', 'wp-ever-accounting' ),
					'taxes'    => __( 'Taxes', 'wp-ever-accounting' ),
				),
			),
			array(
				'page_title' => __( 'Settings', 'wp-ever-accounting' ),
				'menu_title' => __( 'Settings', 'wp-ever-accounting' ),
				'capability' => 'manage_options',
				'menu_slug'  => 'eac-settings',
			),
		);

		return apply_filters( 'eac_admin_menus', $menus );
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
		);

		foreach ( self::get_menus() as $page ) {
			$screen_ids[] = Menus::PARENT_SLUG . '_page_' . $page['menu_slug'];
		}

		return $screen_ids;
	}

	/**
	 * Do meta boxes.
	 *
	 * @since 1.0.0
	 * @param string $screen Screen type.
	 * @param string $position Position.
	 * @param mixed  $item Item object.
	 *
	 * @return void
	 */
	public static function do_meta_boxes( $screen, $position, $item ) {
		if ( ! empty( $position ) ) {
			/**
			 * Fires action to add meta boxes to the given screen.
			 *
			 * @param mixed $object object.
			 *
			 * @since 1.0.0
			 */
			do_action( 'eac_do_meta_boxes_' . $screen . '_' . $position, $item );
		}

		/**
		 * Fires after all built-in meta boxes have been added, contextually for the given object.
		 *
		 * @param mixed $object object.
		 *
		 * @since 1.0.0
		 */
		do_action( 'eac_do_meta_boxes_' . $screen, $item );
	}
}
