<?php
/**
 * Admin Banking Page
 *
 * Functions used for displaying banking related pages.
 *
 * @author      EverAccounting
 * @category    Admin
 * @package     EverAccounting\Admin\Banking
 * @version     1.1.10
 */

defined( 'ABSPATH' ) || exit();

class EAccounting_Admin_Banking {
	/**
	 * Handles output of the reports page in admin.
	 */
	public static function output() {
		$tabs        = self::get_tabs();
		$first_tab   = current( array_keys( $tabs ) );
		$current_tab = ! empty( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $tabs ) ? sanitize_title( $_GET['tab'] ) : $first_tab;
		include dirname( __FILE__ ) . '/views/html-admin-page-banking.php';
	}

	/**
	 * Get banking page tabs.
	 *
	 * @since 1.1.0
	 * @return array
	 */
	public static function get_tabs() {
		$tabs = array();
		if ( current_user_can( 'ea_manage_payment' ) && current_user_can( 'ea_manage_revenue' ) ) {
			$tabs['transactions'] = __( 'Transactions', 'wp-ever-accounting' );
		}
		if ( current_user_can( 'ea_manage_account' ) ) {
			$tabs['accounts'] = __( 'Accounts', 'wp-ever-accounting' );
		}
		if ( current_user_can( 'ea_manage_transfer' ) ) {
			$tabs['transfers'] = __( 'Transfers', 'wp-ever-accounting' );
		}

		return apply_filters( 'eaccounting_banking_tabs', $tabs );
	}

	public static function accounts() {
		if ( ! current_user_can( 'ea_manage_account' ) ) {
			wp_die( __( 'Sorry you are not allowed to access this page.', 'wp-ever-accounting' ) );
		}

		$action = isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : null;
		if ( in_array( $action, array( 'edit', 'add' ), true ) ) {
			include dirname( __FILE__ ) . '/views/accounts/edit-account.php';
		} else {
			require_once dirname( __FILE__ ) . '/list-tables/class-ea-account-list-table.php';
			$list_table = new EAccounting_Account_List_Table();
			$list_table->prepare_items();
			include dirname( __FILE__ ) . '/views/accounts/list-account.php';
		}
	}

	public static function transactions() {
		if ( ! current_user_can( 'ea_manage_payment' ) || ! current_user_can( 'ea_manage_revenue' ) ) {
			wp_die( __( 'Sorry you are not allowed to access this page.', 'wp-ever-accounting' ) );
		}

		$action = isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : null;

	}

	public static function transfers() {
		if ( ! current_user_can( 'ea_manage_account' ) ) {
			wp_die( __( 'Sorry you are not allowed to access this page.', 'wp-ever-accounting' ) );
		}

		$action = isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : null;
		var_dump( $action );
	}
}
