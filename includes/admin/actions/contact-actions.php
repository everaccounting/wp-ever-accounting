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
		wp_die( __( 'You do not have permission to update contact', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
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
		wp_die( __( 'You do not have permission to update contact', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
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
		wp_die( __( 'You do not have permission to update contact', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
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
		wp_die( __( 'You do not have permission to update contact', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	$contact_id = eaccounting_insert_contact( array(
		'id'         => isset( $data['id'] ) ? absint( $data['id'] ) : '',
		'first_name' => isset( $data['first_name'] ) ? sanitize_text_field( $data['first_name'] ) : '',
		'last_name'  => isset( $data['last_name'] ) ? sanitize_text_field( $data['last_name'] ) : '',
		'email'      => isset( $data['email'] ) ? sanitize_email( $data['email'] ) : '',
		'phone'      => isset( $data['phone'] ) ? sanitize_text_field( $data['phone'] ) : '',
		'tax_number' => isset( $data['tax_number'] ) ? sanitize_text_field( $data['tax_number'] ) : '',
		'address'    => isset( $data['address'] ) ? sanitize_text_field( $data['address'] ) : '',
		'city'       => isset( $data['city'] ) ? sanitize_text_field( $data['city'] ) : '',
		'state'      => isset( $data['state'] ) ? sanitize_text_field( $data['state'] ) : '',
		'postcode'   => isset( $data['postcode'] ) ? sanitize_text_field( $data['postcode'] ) : '',
		'country'    => isset( $data['country'] ) ? sanitize_key( $data['country'] ) : '',
		'website'    => isset( $data['website'] ) ? esc_url( $data['website'] ) : '',
		'note'       => isset( $data['note'] ) ? sanitize_text_field( $data['note'] ) : '',
		'avatar_url' => isset( $data['avatar_url'] ) ? esc_url( $data['avatar_url'] ) : '',
		'types'      => isset( $data['types'] ) && is_array( $data['types'] ) ? $data['types'] : [ 'customer' ],
	) );

	$redirect = add_query_arg( [
		'eaccounting-action' => 'edit_contact',
		'contact'            => ! is_wp_error( $contact_id ) ? $contact_id : '',
	], admin_url( 'admin.php?page=eaccounting-contacts' ) );

	if ( is_wp_error( $contact_id ) ) {
		eaccounting_admin_notice( $contact_id->get_error_message(), 'error' );
		wp_redirect( $redirect );
		exit();
	}

	if ( empty( $data['id'] ) ) {
		$message = __( 'Contact created successfully.', 'wp-ever-accounting' );
	} else {
		$message = __( 'Contact updated successfully.', 'wp-ever-accounting' );
	}
	eaccounting_admin_notice( $message );
	wp_redirect( $redirect );
	exit();
}

add_action( 'eaccounting_admin_post_edit_contact', 'eaccounting_action_edit_contact' );
