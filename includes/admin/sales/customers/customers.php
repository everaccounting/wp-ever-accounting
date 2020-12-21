<?php
/**
 * Admin Revenues Page.
 *
 * @since       1.0.2
 * @subpackage  Admin/Sales/Revenues
 * @package     EverAccounting
 */
defined( 'ABSPATH' ) || exit();


function eaccounting_render_customers_tab() {
	$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
	if ( in_array( $requested_view, array( 'view' ), true ) && ! empty( $_GET['customer_id'] ) ) {
		$customer_id = isset( $_GET['customer_id'] ) ? absint( $_GET['customer_id'] ) : null;
		include dirname( __FILE__ ) . '/view-customer.php';
	} elseif ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
		$customer_id = isset( $_GET['customer_id'] ) ? absint( $_GET['customer_id'] ) : null;
		include dirname( __FILE__ ) . '/edit-customer.php';
	} else {
		include dirname( __FILE__ ) . '/list-customer.php';
	}
}

add_action( 'eaccounting_sales_tab_customers', 'eaccounting_render_customers_tab' );


function eaccounting_render_customer_profile_top($customer){
	include dirname( __FILE__ ) . '/customer-profile-top.php';
}
add_action('eaccounting_customer_profile_top', 'eaccounting_render_customer_profile_top');

function eaccounting_customer_profile_aside($customer){
	include dirname( __FILE__ ) . '/customer-profile-aside.php';
}
add_action('eaccounting_customer_profile_aside', 'eaccounting_customer_profile_aside');


function eaccounting_customer_profile_content_transactions($customer){
	include dirname( __FILE__ ) . '/customer-profile-transactions.php';
}
add_action('eaccounting_customer_profile_content_transactions', 'eaccounting_customer_profile_content_transactions');

function eaccounting_customer_profile_content_invoices($customer) {
	include dirname( __FILE__ ) . '/customer-profile-invoices.php';
}
add_action('eaccounting_customer_profile_content_invoices','eaccounting_customer_profile_content_invoices');

function eaccounting_customer_profile_content_notes($customer) {
	include dirname( __FILE__ ) . '/customer-profile-notes.php';
}
add_action('eaccounting_customer_profile_content_notes','eaccounting_customer_profile_content_notes');
