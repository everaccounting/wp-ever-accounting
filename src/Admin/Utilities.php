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
//				'tabs'       => array(
//					'items'      => __( 'Items', 'wp-ever-accounting' ),
//					'categories' => __( 'Categories', 'wp-ever-accounting' ),
//				),
			)
		);

		return apply_filters( 'starter_plugin_admin_menus', $menus );
	}

	/**
	 * Get page ids.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public static function get_screen_ids() {
		$screen_ids = array();

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
}
