<?php
/**
 * EverAccounting Admin Sales Page.
 *
 * @package     EverAccounting
 * @subpackage  Admin
 * @version     1.1.0
 */

defined( 'ABSPATH' ) || exit();

class EverAccounting_Admin_Sales {

	/**
	 * EverAccounting_Admin_Sales constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_page' ), 30 );
	}

	/**
	 * Registers the reports page.
	 *
	 */
	public function register_page() {
		add_submenu_page(
			'eaccounting',
			__( 'Sales', 'wp-ever-accounting' ),
			__( 'Sales', 'wp-ever-accounting' ),
			'manage_eaccounting',
			'ea-sales',
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
		if ( current_user_can( 'ea_manage_invoice' ) ) {
			$tabs['invoices'] = __( 'Invoices', 'wp-ever-accounting' );
		}
		if ( current_user_can( 'ea_manage_revenue' ) ) {
			$tabs['revenues'] = __( 'Revenues', 'wp-ever-accounting' );
		}
		if ( current_user_can( 'ea_manage_customer' ) ) {
			$tabs['customers'] = __( 'Customers', 'wp-ever-accounting' );
		}

		return apply_filters( 'eaccounting_sales_tabs', $tabs );
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
		include dirname( __FILE__ ) . '/views/admin-page-sales.php';
	}
}

new EverAccounting_Admin_Sales();