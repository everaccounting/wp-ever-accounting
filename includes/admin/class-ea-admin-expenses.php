<?php
/**
 * EverAccounting Admin Expenses Page.
 *
 * @package     EverAccounting
 * @subpackage  Admin
 * @version     1.1.0
 */

defined( 'ABSPATH' ) || exit();

class EAccounting_Admin_Expenses {

	/**
	 * EAccounting_Admin_Expenses constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_page' ), 20 );
		//add_action( 'eaccounting_expenses_page_tab_bills', array( $this, 'render_bills_tab' ), 20 );
		add_action( 'eaccounting_expenses_page_tab_payments', array( $this, 'render_payments_tab' ), 20 );
		add_action( 'eaccounting_expenses_page_tab_vendors', array( $this, 'render_vendors_tab' ), 20 );
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
		if ( current_user_can( 'ea_manage_bill' ) ) {
			$tabs['bills'] = __( 'Bills', 'wp-ever-accounting' );
		}
		if ( current_user_can( 'ea_manage_payment' ) ) {
			$tabs['payments'] = __( 'Payments', 'wp-ever-accounting' );
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
		include dirname( __FILE__ ) . '/views/admin-page-expenses.php';
	}

	/**
	 * Render customer tab.
	 *
	 * @since 1.1.0
	 */
	public function render_vendors_tab() {
		$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
		if ( in_array( $requested_view, array( 'view' ), true ) && ! empty( $_GET['vendor_id'] ) ) {
			$vendor_id = isset( $_GET['vendor_id'] ) ? absint( $_GET['vendor_id'] ) : null;
			include dirname( __FILE__ ) . '/views/vendors/view-vendor.php';
		} elseif ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
			$vendor_id = isset( $_GET['vendor_id'] ) ? absint( $_GET['vendor_id'] ) : null;
			include dirname( __FILE__ ) . '/views/vendors/edit-vendor.php';
		} else {
			include dirname( __FILE__ ) . '/views/vendors/list-vendor.php';
		}
	}

	/**
	 * Render customer tab.
	 *
	 * @since 1.1.0
	 */
	public function render_payments_tab() {
		$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
		if ( in_array( $requested_view, array( 'view' ), true ) && ! empty( $_GET['payment_id'] ) ) {
			$payment_id = isset( $_GET['payment_id'] ) ? absint( $_GET['payment_id'] ) : null;
			include dirname( __FILE__ ) . '/views/payments/view-payment.php';
		} elseif ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
			$payment_id = isset( $_GET['payment_id'] ) ? absint( $_GET['payment_id'] ) : null;
			include dirname( __FILE__ ) . '/views/payments/edit-payment.php';
		} else {
			include dirname( __FILE__ ) . '/views/payments/list-payment.php';
		}
	}
}

new EAccounting_Admin_Expenses();
