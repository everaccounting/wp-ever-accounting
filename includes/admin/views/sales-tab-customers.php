<?php
/**
 * Admin View: Page - Sales Tab - Customers
 *
 * @var array  $tabs
 * @var string $current_tab
 */
defined( 'ABSPATH' ) || exit;

$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
if ( in_array( $requested_view, array( 'view' ), true ) && ! empty( $_GET['customer_id'] ) ) {
	$customer_id = isset( $_GET['customer_id'] ) ? absint( $_GET['customer_id'] ) : null;
	include dirname( __FILE__ ) . '/customers/view-customer.php';
} elseif ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
	$customer_id = isset( $_GET['customer_id'] ) ? absint( $_GET['customer_id'] ) : null;
	include dirname( __FILE__ ) . '/customers/edit-customer.php';
} else {
	include dirname( __FILE__ ) . '/customers/list-customer.php';
}
