<?php
defined( 'ABSPATH' ) || exit();


function eaccounting_delete_revenue_handler( $data ) {

	if ( ! isset( $data['_wpnonce'] ) || ! wp_verify_nonce( $data['_wpnonce'], 'eaccounting_revenues_nonce' ) ) {
		wp_die( __( 'Trying to cheat or something?', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to update account', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( $revenue_id = absint( $data['revenue'] ) ) {
		eaccounting_delete_revenue( $revenue_id );
	}

	eaccounting_admin_notice( __( 'Revenue Deleted', 'wp-eaccounting' ) );

	wp_redirect( admin_url( 'admin.php?page=eaccounting-income&tab=revenues' ) );
	exit;
}

add_action( 'eaccounting_admin_get_delete_revenue', 'eaccounting_delete_revenue_handler' );

function eaccounting_edit_revenue( $posted ) {
	error_log(print_r($posted, true));
	if ( ! isset( $posted['_wpnonce'] ) || ! wp_verify_nonce( $posted['_wpnonce'], 'eaccounting_revenue_nonce' ) ) {
		wp_die( __( 'Trying to cheat or something?', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to update account', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	$created = eaccounting_insert_revenue( array(
		'id'     => $posted['id'],
		'paid_at'   => $posted['date'],
		'amount'   => $posted['amount'],
		'account_id'  => $posted['account_id'],
		'contact_id'  => $posted['contact_id'],
		'description'  => $posted['description'],
		'category_id'  => $posted['category_id'],
		'payment_method_id'  => $posted['payment_method_id'],
		'reference'  => $posted['reference'],
	) );

	error_log(print_r($created, true));

	if ( is_wp_error( $posted ) ) {
		eaccounting_admin_notice( $created->get_error_message(), 'error' );
		wp_redirect( admin_url( 'admin.php?page=eaccounting-income&tab=revenues' ) );
		exit();
	}

	eaccounting_admin_notice( __( 'Revenue saved', 'wp-eaccounting' ) );
	wp_redirect( add_query_arg( [ 'eaccounting-action' => 'edit_revenue', 'revenue' => $created ] ) );
	exit();
}

add_action( 'eaccounting_admin_post_edit_revenue', 'eaccounting_edit_revenue' );
