<?php
defined( 'ABSPATH' ) || exit();

/**
 * Delete
 * since 1.0.0
 *
 * @param $data
 */
function eaccounting_action_delete_transfer( $data ) {

	if ( ! isset( $data['_wpnonce'] ) || ! wp_verify_nonce( $data['_wpnonce'], 'eaccounting_transfers_nonce' ) ) {
		wp_die( __( 'Trying to cheat or something?', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to update account', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( $transfer_id = absint( $data['transfer'] ) ) {
		eaccounting_delete_transfer( $transfer_id );
	}

	wp_redirect( add_query_arg( [ 'transfer' => $transfer_id ], admin_url( 'admin.php?page=eaccounting-transfers' ) ) );
	exit;
}

add_action( 'eaccounting_admin_get_delete_transfer', 'eaccounting_action_delete_transfer' );


function eaccounting_action_edit_transfer( $data ) {
	if ( ! isset( $data['_wpnonce'] ) || ! wp_verify_nonce( $data['_wpnonce'], 'eaccounting_transfer_nonce' ) ) {
		wp_die( __( 'Trying to cheat or something?', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to update transfer', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	$transfer_id = eaccounting_insert_transfer( array(
		'id'              => isset( $data['id'] ) ? absint( $data['id'] ) : '',
		'from_account_id' => isset( $data['from_account_id'] ) ? absint( $data['from_account_id'] ) : '',
		'to_account_id'   => isset( $data['to_account_id'] ) ? absint( $data['to_account_id'] ) : '',
		'amount'          => isset( $data['amount'] ) ? eaccounting_sanitize_price( $data['amount'] ) : '',
		'transferred_at'  => isset( $data['transferred_at'] ) ? eaccounting_sanitize_date( $data['amount'] ) : '',
		'description'     => isset( $data['description'] ) ? sanitize_textarea_field( $data['description'] ) : '',
		'payment_method'  => isset( $data['payment_method'] ) ? sanitize_text_field( $data['payment_method'] ) : '',
		'reference'       => isset( $data['reference'] ) ? sanitize_text_field( $data['reference'] ) : '',
	) );

	$redirect = add_query_arg( [
		'eaccounting-action' => 'edit_transfer',
		'transfer'           => ! is_wp_error( $transfer_id ) ? $transfer_id : '',
	], admin_url( 'admin.php?page=eaccounting-transfers' ) );

	if ( is_wp_error( $transfer_id ) ) {
		eaccounting_admin_notice( $transfer_id->get_error_message(), 'error' );
		wp_redirect( $redirect );
		exit();
	}

	if ( empty( $data['id'] ) ) {
		$message = __( 'Transfer created successfully.', 'wp-ever-accounting' );
	} else {
		$message = __( 'Transfer updated successfully.', 'wp-ever-accounting' );
	}
	eaccounting_admin_notice( $message );
	wp_redirect( $redirect );
	exit();

}

add_action( 'eaccounting_admin_post_edit_transfer', 'eaccounting_action_edit_transfer' );
