<?php
/**
 * EverAccounting Admin Expenses Page.
 *
 * @package     EverAccounting
 * @subpackage  Admin
 * @version     1.1.0
 */

defined( 'ABSPATH' ) || exit();

class EverAccounting_Admin_Expenses {

	/**
	 * EverAccounting_Admin_Expenses constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_page' ), 40 );
	}

	/**
	 * Registers the reports page.
	 *
	 */
	public function register_page() {
		add_submenu_page(
			'eaccounting',
			__( 'Expenses', 'wp-ever-accounting' ),
			__( 'Expenses', 'wp-ever-accounting' ),
			'manage_eaccounting',
			'ea-expenses',
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
		if ( current_user_can( 'ea_manage_payment' ) ) {
			$tabs['payments'] = __( 'Payments', 'wp-ever-accounting' );
		}
		if ( current_user_can( 'ea_manage_bill' ) ) {
			$tabs['bills'] = __( 'Bills', 'wp-ever-accounting' );
		}
		if ( current_user_can( 'ea_manage_vendor' ) ) {
			$tabs['vendors'] = __( 'Vendors', 'wp-ever-accounting' );
		}

		return apply_filters( 'eaccounting_expenses_tabs', $tabs );
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
		if ( empty( $_GET['tab'] ) ) {
			wp_redirect(
				add_query_arg(
					array(
						'page' => 'ea-expenses',
						'tab'  => $current_tab,
					),
					admin_url( 'admin.php' )
				)
			);
			exit();
		}
		include dirname( __FILE__ ) . '/views/admin-page-expenses.php';
	}
}

new EverAccounting_Admin_Expenses();
