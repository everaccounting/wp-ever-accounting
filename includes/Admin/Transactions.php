<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Transactions
 *
 * @package EverAccounting\Admin
 * @since 1.0.0
 */
class Transactions {
	/**
	 * Transactions constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_filter( 'eac_banking_page_tabs', array( __CLASS__, 'register_tabs' ) );
	}

	/**
	 * Register tab.
	 *
	 * @param array $tabs Tabs.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public static function register_tabs( $tabs ) {
		if ( current_user_can( 'eac_manage_payment' ) && current_user_can( 'eac_manage_expense' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown -- Custom capability.
			$tabs['transactions'] = __( 'Transactions', 'wp-ever-accounting' );
		}

		return $tabs;
	}

	/**
	 * Handle actions.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function handle_actions() {
		if ( isset( $_POST['action'] ) && 'eac_edit_transfer' === $_POST['action'] && check_admin_referer( 'eac_edit_transfer' ) && current_user_can( 'eac_manage_transfer' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown -- Custom capability.

		}
	}
}
