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
		$contact = new EAccounting_Contact( $contact_id );
		$contact->activate();
	}
	wp_redirect( admin_url( 'admin.php?page=eaccounting-contacts' ) );
	exit;
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
		$contact = new EAccounting_Contact( $contact_id );
		$contact->deactivate();
	}
	wp_redirect( admin_url( 'admin.php?page=eaccounting-contacts' ) );
	exit;
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

	if ( ! $contact_id ) {
		wp_redirect( add_query_arg( [ 'eaccounting-action' => 'add_contact' ] ) );
		exit();
	}

	wp_redirect( add_query_arg( [ 'contact' => $contact_id ], admin_url( 'admin.php?page=eaccounting-contacts' ) ) );
	exit;
}

add_action( 'eaccounting_admin_get_delete_contact', 'eaccounting_action_delete_contact' );


function eaccounting_action_edit_contact( $data ) {
	if ( ! isset( $data['_wpnonce'] ) || ! wp_verify_nonce( $data['_wpnonce'], 'eaccounting_edit_contact' ) ) {
		return;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to update account', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	$contact_id = eaccounting_insert_contact( array(
		'id'         => isset( $data['id'] ) ? $data['id'] : '',
		'first_name' => isset( $data['first_name'] ) ? $data['first_name'] : '',
		'last_name'  => isset( $data['last_name'] ) ? $data['last_name'] : '',
		'email'      => isset( $data['email'] ) ? $data['email'] : '',
		'phone'      => isset( $data['phone'] ) ? $data['phone'] : '',
		'tax_number' => isset( $data['tax_number'] ) ? $data['tax_number'] : '',
		'address'    => isset( $data['address'] ) ? $data['address'] : '',
		'city'       => isset( $data['city'] ) ? $data['city'] : '',
		'state'      => isset( $data['state'] ) ? $data['state'] : '',
		'postcode'   => isset( $data['postcode'] ) ? $data['postcode'] : '',
		'country'    => isset( $data['country'] ) ? $data['country'] : '',
		'website'    => isset( $data['website'] ) ? $data['website'] : '',
		'note'       => isset( $data['note'] ) ? $data['note'] : '',
		'status'     => isset( $data['status'] ) ? $data['status'] : 'inactive',
		'types'      => isset( $data['types'] ) && is_array($data['types']) ? $data['types'] : [ 'customer' ],
	) );

	$redirect = add_query_arg( [
		'eaccounting-action' => 'edit_contact',
		'contact'            => !is_wp_error($contact_id)? $contact_id:'',
	], admin_url( 'admin.php?page=eaccounting-contacts' ) );

	if ( is_wp_error( $contact_id ) ) {
		eaccounting_admin_notice( $contact_id->get_error_message(), 'error' );
		wp_redirect( $redirect );
		exit();
	}

	if ( empty( $data['id'] ) ) {
		$message = __( 'Contact created successfully.', 'wp-eaccounting' );
	} else {
		$message = __( 'Contact updated successfully.', 'wp-eaccounting' );
	}
	eaccounting_admin_notice( $message );
	wp_redirect( $redirect );
	exit();
}

add_action( 'eaccounting_admin_post_edit_contact', 'eaccounting_action_edit_contact' );