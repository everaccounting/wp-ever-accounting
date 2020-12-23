<?php
/**
 * Page : Expenses
 * Tab: Vendor
 *
 * @var array $tabs
 * @var string $current_tab
 */

defined( 'ABSPATH' ) || exit;

$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
if ( in_array( $requested_view, array( 'view' ), true ) && ! empty( $_GET['vendor_id'] ) ) {
	$vendor_id = isset( $_GET['vendor_id'] ) ? absint( $_GET['vendor_id'] ) : null;
	include dirname( __FILE__ ) . '/vendors/view-vendor.php';
} elseif ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
	$vendor_id = isset( $_GET['vendor_id'] ) ? absint( $_GET['vendor_id'] ) : null;
	include dirname( __FILE__ ) . '/vendors/edit-vendor.php';
} else {
	include dirname( __FILE__ ) . '/vendors/list-vendor.php';
}

