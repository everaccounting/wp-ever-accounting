<?php
defined( 'ABSPATH' ) || exit();


function eaccounting_activate_account( $data ) {
	if ( ! isset( $data['_wpnonce'] ) || ! wp_verify_nonce( $data['_wpnonce'], 'eaccounting_accounts_nonce' ) ) {
		wp_die( __( 'Trying to cheat or something?', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to update account', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	$account_id = absint( $data['account'] );
	if ( $account_id ) {
		eaccounting_insert_account( [
			'id'     => $account_id,
			'status' => 'active'
		] );
	}

	eaccounting_admin_notice( __( 'Account Activated.', 'wp-eaccounting' ) );

	wp_redirect( admin_url( 'admin.php?page=eaccounting-accounts' ) );
	exit;
}

add_action( 'eaccounting_admin_get_activate_account', 'eaccounting_activate_account' );


function eaccounting_deactivate_account( $data ) {
	if ( ! isset( $data['_wpnonce'] ) || ! wp_verify_nonce( $data['_wpnonce'], 'eaccounting_accounts_nonce' ) ) {
		wp_die( __( 'Trying to cheat or something?', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to update account', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	$account_id = absint( $data['account'] );

	if ( $account_id ) {
		eaccounting_insert_account( [
			'id'     => $account_id,
			'status' => 'inactive'
		] );
	}

	eaccounting_admin_notice( __( 'Account Deactivated', 'wp-eaccounting' ) );

	wp_redirect( admin_url( 'admin.php?page=eaccounting-accounts' ) );
	exit;
}

add_action( 'eaccounting_admin_get_deactivate_account', 'eaccounting_deactivate_account' );


function eaccounting_delete_account_handler( $data ) {

	if ( ! isset( $data['_wpnonce'] ) || ! wp_verify_nonce( $data['_wpnonce'], 'eaccounting_accounts_nonce' ) ) {
		wp_die( __( 'Trying to cheat or something?', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to update account', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( $account_id = absint( $data['account'] ) ) {
		eaccounting_delete_account( $account_id );
	}

	eaccounting_admin_notice( __( 'Account Deleted', 'wp-eaccounting' ) );

	wp_redirect( admin_url( 'admin.php?page=eaccounting-accounts' ) );
	exit;
}

add_action( 'eaccounting_admin_get_delete_account', 'eaccounting_delete_account_handler' );

function eaccounting_edit_account( $data ) {
	if ( ! isset( $data['_wpnonce'] ) || ! wp_verify_nonce( $data['_wpnonce'], 'eaccounting_account_nonce' ) ) {
		wp_die( __( 'Trying to cheat or something?', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to update account', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	$created = eaccounting_insert_account( array(
		'id'              => $data['id'],
		'name'            => $data['name'],
		'number'          => $data['number'],
		'bank_name'       => $data['bank_name'],
		'bank_phone'      => $data['bank_phone'],
		'bank_address'    => $data['bank_address'],
		'opening_balance' => $data['opening_balance'],
		'status'          => isset( $data['status'] ) ? $data['status'] : 'active',
	) );

	if ( is_wp_error( $created ) ) {
		eaccounting_admin_notice( $created->get_error_message(), 'error' );
		wp_redirect( admin_url( 'admin.php?page=eaccounting-accounts&eaccounting-action=add_account' ) );
		exit();
	}

	if ( empty( $data['id'] ) ) {
		$message = __( 'Account created successfully.', 'wp-eaccounting' );
	} else {
		$message = __( 'Account updated successfully.', 'wp-eaccounting' );
	}

	eaccounting_admin_notice( $message );

	wp_redirect( add_query_arg( [ 'eaccounting-action' => 'edit_account', 'account' => $created ] ) );
	exit();
}

add_action( 'eaccounting_admin_post_edit_account', 'eaccounting_edit_account' );
