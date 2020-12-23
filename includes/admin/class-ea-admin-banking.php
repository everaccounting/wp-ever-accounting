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
	 * EAccounting_Admin_Banking constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_page' ), 20 );
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
	 * @since 1.1.0
	 * @return array
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

}

new EAccounting_Admin_Banking();
