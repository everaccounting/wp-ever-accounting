<?php
/**
 * Admin Vendors Page.
 *
 * @package     EverAccounting
 * @subpackage  Admin/Expenses/Vendors
 * @since       1.0.2
 */
defined( 'ABSPATH' ) || exit();

function eaccounting_expenses_tab_vendors() {
	$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
	if ( in_array( $requested_view, array( 'view' ), true ) && ! empty( $_GET['vendor_id'] ) ) {
		$vendor_id = isset( $_GET['vendor_id'] ) ? absint( $_GET['vendor_id'] ) : null;
		include dirname( __FILE__ ) . '/view-vendor.php';
	} elseif ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
		$vendor_id = isset( $_GET['vendor_id'] ) ? absint( $_GET['vendor_id'] ) : null;
		include dirname( __FILE__ ) . '/edit-vendor.php';
	} else {
		include dirname( __FILE__ ) . '/list-vendor.php';
	}
}

add_action( 'eaccounting_expenses_tab_vendors', 'eaccounting_expenses_tab_vendors' );

function eaccounting_vendor_profile_top( $vendor ) {
	include dirname( __FILE__ ) . '/vendor-profile-top.php';
}

add_action( 'eaccounting_vendor_profile_top', 'eaccounting_vendor_profile_top' );

function eaccounting_vendor_profile_aside( $vendor ) {
	include dirname( __FILE__ ) . '/vendor-profile-aside.php';
}

add_action( 'eaccounting_vendor_profile_aside', 'eaccounting_vendor_profile_aside' );

function eaccounting_vendor_profile_content_transactions( $vendor ) {
	include dirname( __FILE__ ) . '/vendor-profile-transactions.php';
}

add_action( 'eaccounting_vendor_profile_content_transactions', 'eaccounting_vendor_profile_content_transactions' );

function eaccounting_vendor_profile_content_bills( $vendor ) {
	include dirname( __FILE__ ) . '/vendor-profile-bills.php';
}

add_action( 'eaccounting_vendor_profile_content_bills', 'eaccounting_vendor_profile_content_bills' );

function eaccounting_vendor_profile_content_notes( $vendor ) {
	include dirname( __FILE__ ) . '/vendor-profile-notes.php';
}

add_action( 'eaccounting_vendor_profile_content_notes', 'eaccounting_vendor_profile_content_notes' );
