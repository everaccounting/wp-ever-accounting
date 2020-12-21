<?php
/**
 * Admin Revenues Page.
 *
 * @package     EverAccounting
 * @subpackage  Admin/Sales/Revenues
 * @since       1.0.2
 */
defined( 'ABSPATH' ) || exit();

function eaccounting_sales_tab_incomes() {
	if ( ! current_user_can( 'ea_manage_revenue' ) ) {
		wp_die( __( 'Sorry you are not allowed to access this page.', 'wp-ever-accounting' ) );
	}
	$requested_view = isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : null;
	if ( in_array( $requested_view, array( 'view' ), true ) && ! empty( $_GET['income_id'] ) ) {
		$income_id = isset( $_GET['income_id'] ) ? absint( $_GET['income_id'] ) : null;
		include dirname( __FILE__ ) . '/view-income.php';
	} elseif ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
		$income_id = isset( $_GET['income_id'] ) ? absint( $_GET['income_id'] ) : null;
		include dirname( __FILE__ ) . '/edit-income.php';
	} else {
		include dirname( __FILE__ ) . '/list-income.php';
	}
}

add_action( 'eaccounting_sales_tab_incomes', 'eaccounting_sales_tab_incomes' );
