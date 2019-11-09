<?php
defined( 'ABSPATH' ) || exit();
/**
 * Activate
 * since 1.0.0
 *
 * @param $data
 */
function eaccounting_action_activate_contact( $data ) {
	if ( ! isset( $data['_wpnonce'] ) || ! wp_verify_nonce( $data['_wpnonce'], 'eaccounting_contacts_nonce' ) ) {
		wp_die( __( 'Trying to cheat or something?', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to update account', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	$contact_id = absint( $data['contact'] );
	if ( $contact_id ) {
		eaccounting_insert_contact( [
			'id'     => $contact_id,
			'status' => 'active'
		] );
	}

	wp_redirect( admin_url( 'admin.php?page=eaccounting-contacts' ) );
}

add_action( 'eaccounting_admin_get_activate_contact', 'eaccounting_action_activate_contact' );

/**
 * Deactivate
 *
 * since 1.0.0
 *
 * @param $data
 */
function eaccounting_action_deactivate_contact( $data ) {
	if ( ! isset( $data['_wpnonce'] ) || ! wp_verify_nonce( $data['_wpnonce'], 'eaccounting_contacts_nonce' ) ) {
		wp_die( __( 'Trying to cheat or something?', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to update account', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	$contact_id = absint( $data['contact'] );
	if ( $contact_id ) {
		eaccounting_insert_contact( [
			'id'     => $contact_id,
			'status' => 'inactive'
		] );
	}

	wp_redirect( admin_url( 'admin.php?page=eaccounting-contacts' ) );
}

add_action( 'eaccounting_admin_get_deactivate_contact', 'eaccounting_action_deactivate_contact' );


/**
 * Delete
 * since 1.0.0
 *
 * @param $data
 */
function eaccounting_action_delete_contact( $data ) {

	if ( ! isset( $data['_wpnonce'] ) || ! wp_verify_nonce( $data['_wpnonce'], 'eaccounting_contacts_nonce' ) ) {
		wp_die( __( 'Trying to cheat or something?', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to update account', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( $contact_id = absint( $data['contact'] ) ) {
		eaccounting_delete_contact( $contact_id );
	}

	wp_redirect( admin_url( 'admin.php?page=eaccounting-contacts' ) );
}

add_action( 'eaccounting_admin_get_delete_contact', 'eaccounting_action_delete_contact' );


function eaccounting_action_edit_contact( $data ) {

	if ( ! isset( $data['_wpnonce'] ) || ! wp_verify_nonce( $data['_wpnonce'], 'eaccounting_contact_nonce' ) ) {
		return;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to update account', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	$contact_id = eaccounting_insert_contact( $data );
	if ( is_wp_error( $contact_id ) ) {
		wp_redirect( add_query_arg( [ 'eaccounting-action' => 'add_contact',  'feedback' => $contact_id->get_error_code() ] ) );
		exit();
	}

	wp_redirect( add_query_arg( [ 'eaccounting-action' => 'add_contact',  'feedback' => 'success' ] ) );
}

add_action( 'eaccounting_admin_post_edit_contact', 'eaccounting_action_edit_contact' );
