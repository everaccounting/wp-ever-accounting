<?php
defined( 'ABSPATH' ) || exit();

function eaccounting_action_edit_tax( $data ) {
	if ( ! isset( $data['_wpnonce'] ) || ! wp_verify_nonce( $data['_wpnonce'], 'eaccounting_tax_nonce' ) ) {
		wp_die( __( 'Trying to cheat or something?', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to update tax', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	$tax_id = eaccounting_insert_tax( array(
		'id'     => isset( $data['id'] ) ? absint( $data['id'] ) : '',
		'name'   => ! isset( $data['name'] ) ? '' : sanitize_text_field( $data['name'] ),
		'rate'   => ! isset( $data['rate'] ) ? '' : (double) $data['rate'],
		'type'   => ! isset( $data['type'] ) ? '' : sanitize_key( $data['type'] ),
	) );

	$redirect = add_query_arg( [
		'eaccounting-action' => 'edit_tax',
		'tax'                => ! is_wp_error( $tax_id ) ? $tax_id : '',
	], admin_url( 'admin.php?page=eaccounting-misc&tab=taxes' ) );

	if ( is_wp_error( $tax_id ) ) {
		eaccounting_admin_notice( $tax_id->get_error_message(), 'error' );
		wp_redirect( $redirect );
		exit();
	}

	if ( empty( $data['id'] ) ) {
		$message = __( 'Tax rate created successfully.', 'wp-ever-accounting' );
	} else {
		$message = __( 'Tax rate updated successfully.', 'wp-ever-accounting' );
	}
	eaccounting_admin_notice( $message );
	wp_redirect( $redirect );
	exit();

}

add_action( 'eaccounting_admin_post_edit_tax', 'eaccounting_action_edit_tax' );


/**
 * Delete
 * since 1.0.0
 *
 * @param $data
 */
function eaccounting_action_delete_tax( $data ) {

	if ( ! isset( $data['_wpnonce'] ) || ! wp_verify_nonce( $data['_wpnonce'], 'eaccounting_taxes_nonce' ) ) {
		wp_die( __( 'Trying to cheat or something?', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to update transfer', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( $transfer_id = absint( $data['tax'] ) ) {
		eaccounting_delete_tax( $transfer_id );
	}

	wp_redirect( admin_url( 'admin.php?page=eaccounting-misc&tab=taxes' ) );
	exit;
}

add_action( 'eaccounting_admin_get_delete_tax', 'eaccounting_action_delete_tax' );

/**
 * @since 1.0.0
 * @param $data
 */
function eaccounting_activate_tax( $data ) {
	if ( ! isset( $data['_wpnonce'] ) || ! wp_verify_nonce( $data['_wpnonce'], 'eaccounting_taxes_nonce' ) ) {
		wp_die( __( 'Trying to cheat or something?', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to update account', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	$tax = absint( $data['tax'] );
	if ( $tax ) {
		eaccounting_insert_tax( [
			'id'     => $tax,
		] );
	}

	eaccounting_admin_notice( __( 'Tax rate activated', 'wp-ever-accounting' ) );

	wp_redirect( admin_url( 'admin.php?page=eaccounting-misc&tab=taxes' ) );
	exit;
}

add_action( 'eaccounting_admin_get_activate_tax', 'eaccounting_activate_tax' );

/**
 * @since 1.0.0
 * @param $data
 */
function eaccounting_deactivate_tax( $data ) {
	if ( ! isset( $data['_wpnonce'] ) || ! wp_verify_nonce( $data['_wpnonce'], 'eaccounting_taxes_nonce' ) ) {
		wp_die( __( 'Trying to cheat or something?', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to update account', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	$tax = absint( $data['tax'] );

	if ( $tax ) {
		eaccounting_insert_tax( [
			'id'     => $tax,
		] );
	}

	eaccounting_admin_notice( __( 'Tax rate deactivated', 'wp-ever-accounting' ) );

	wp_redirect( admin_url( 'admin.php?page=eaccounting-misc&tab=taxes' ) );
	exit;
}

add_action( 'eaccounting_admin_get_deactivate_tax', 'eaccounting_deactivate_tax' );


