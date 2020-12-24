<?php
/**
 * Admin Banking Page
 *
 * Functions used for displaying banking related pages.
 *
 * @author      EverAccounting
 * @category    Admin
 * @package     EverAccounting\Admin
 * @version     1.1.10
 */

defined( 'ABSPATH' ) || exit();

class EAccounting_Admin_Banking {
	/**
	 * EAccounting_Admin_Banking constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_page' ), 20 );
		add_action( 'eaccounting_banking_page_tab_accounts', array( $this, 'render_accounts_page' ), 20 );
	}

	/**
	 * Registers the reports page.
	 *
	 */
	public function register_page() {
		add_submenu_page(
			'eaccounting',
			__( 'Banking', 'wp-ever-accounting' ),
			__( 'Banking', 'wp-ever-accounting' ),
			'manage_eaccounting',
			'ea-banking',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Get banking page tabs.
	 *
	 * @return array
	 * @since 1.1.0
	 */
	public function get_tabs() {
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

	/**
	 * Render page.
	 *
	 * @since 1.1.0
	 */
	public function render_page() {
		$tabs        = $this->get_tabs();
		$first_tab   = current( array_keys( $tabs ) );
		$current_tab = ! empty( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $tabs ) ? sanitize_title( $_GET['tab'] ) : $first_tab;
		include dirname( __FILE__ ) . '/views/admin-page-banking.php';
	}

	/**
	 * Render accounts page.
	 *
	 * @since 1.1.0
	 */
	public function render_accounts_page() {
		$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
		if ( in_array( $requested_view, array( 'view' ), true ) && ! empty( $_GET['account_id'] ) ) {
			$account_id = isset( $_GET['account_id'] ) ? absint( $_GET['account_id'] ) : null;
			include dirname( __FILE__ ) . '/views/accounts/view-account.php';
		} elseif ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
			$account_id = isset( $_GET['account_id'] ) ? absint( $_GET['account_id'] ) : null;
			include dirname( __FILE__ ) . '/views/accounts/edit-account.php';
		} else {
			include dirname( __FILE__ ) . '/views/accounts/list-account.php';
		}
	}

}

new EAccounting_Admin_Banking();
