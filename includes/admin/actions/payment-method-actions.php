<?php
defined( 'ABSPATH' ) || exit();


function eaccounting_activate_payment_method( $data ) {
	if ( ! isset( $data['_wpnonce'] ) || ! wp_verify_nonce( $data['_wpnonce'], 'eaccounting_payment_methods_nonce' ) ) {
		wp_die( __( 'Trying to cheat or something?', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to update account', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	$payment_method_id = absint( $data['payment_method'] );
	if ( $payment_method_id ) {
		eaccounting_insert_payment_method( [
			'id'     => $payment_method_id,
			'status' => 'active'
		] );
	}

	eaccounting_admin_notice( __( 'Payment Method Activated.', 'wp-eaccounting' ) );

	wp_redirect( admin_url( 'admin.php?page=eaccounting-misc&tab=payment_methods' ) );
	exit;
}

add_action( 'eaccounting_admin_get_activate_payment_method', 'eaccounting_activate_payment_method' );


function eaccounting_deactivate_payment_method( $data ) {
	if ( ! isset( $data['_wpnonce'] ) || ! wp_verify_nonce( $data['_wpnonce'], 'eaccounting_payment_methods_nonce' ) ) {
		wp_die( __( 'Trying to cheat or something?', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to update account', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	$payment_method_id = absint( $data['payment_method'] );

	if ( $payment_method_id ) {
		eaccounting_insert_payment_method( [
			'id'     => $payment_method_id,
			'status' => 'inactive'
		] );
	}

	eaccounting_admin_notice( __( 'Payment Method Deactivated', 'wp-eaccounting' ) );

	wp_redirect( admin_url( 'admin.php?page=eaccounting-misc&tab=payment_methods' ) );
	exit;
}

add_action( 'eaccounting_admin_get_deactivate_payment_method', 'eaccounting_deactivate_payment_method' );


function eaccounting_delete_payment_method_handler( $data ) {

	if ( ! isset( $data['_wpnonce'] ) || ! wp_verify_nonce( $data['_wpnonce'], 'eaccounting_payment_methods_nonce' ) ) {
		wp_die( __( 'Trying to cheat or something?', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to update account', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( $payment_method_id = absint( $data['payment_method'] ) ) {
		eaccounting_delete_payment_method( $payment_method_id );
	}

	eaccounting_admin_notice( __( 'Payment Method Deleted', 'wp-eaccounting' ) );

	wp_redirect( admin_url( 'admin.php?page=eaccounting-misc&tab=payment_methods' ) );
	exit;
}

add_action( 'eaccounting_admin_get_delete_payment_method', 'eaccounting_delete_payment_method_handler' );

function eaccounting_edit_payment_method( $posted ) {
	if ( ! isset( $posted['_wpnonce'] ) || ! wp_verify_nonce( $posted['_wpnonce'], 'eaccounting_payment_method_nonce' ) ) {
		wp_die( __( 'Trying to cheat or something?', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to update account', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	$created = eaccounting_insert_payment_method( array(
		'id'     => $posted['id'],
		'name'   => $posted['name'],
		'code'   => $posted['code'],
		'order'  => $posted['order'],
		'description'  => $posted['description'],
		'status' => isset( $posted['status'] ) ? $posted['status'] : 'active',
	) );

	if ( is_wp_error( $posted ) ) {
		eaccounting_admin_notice( $created->get_error_message(), 'error' );
		wp_redirect( admin_url( 'admin.php?page=eaccounting-misc&tab=payment_methods' ) );
		exit();
	}

	eaccounting_admin_notice( __( 'Payment Method saved', 'wp-eaccounting' ) );
	wp_redirect( add_query_arg( [ 'eaccounting-action' => 'edit_payment_method', 'payment_method' => $created ] ) );
	exit();
}

add_action( 'eaccounting_admin_post_edit_payment_method', 'eaccounting_edit_payment_method' );
