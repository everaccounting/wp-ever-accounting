<?php
defined( 'ABSPATH' ) || exit();
function eaccounting_activate_tax_rate( $data ) {
	if ( ! isset( $data['_wpnonce'] ) || ! wp_verify_nonce( $data['_wpnonce'], 'eaccounting_tax_rates_nonce' ) ) {
		wp_die( __( 'Trying to cheat or something?', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to update account', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	$tax_rate_id = absint( $data['tax_rate'] );
	if ( $tax_rate_id ) {
		eaccounting_insert_tax_rate( [
			'id'     => $tax_rate_id,
			'status' => 'active'
		] );
	}

	eaccounting_admin_notice( __( 'Tax Rate Activated.', 'wp-eaccounting' ) );

	wp_redirect( admin_url( 'admin.php?page=eaccounting-misc&tab=tax_rates&notice=1' ) );
}

add_action( 'eaccounting_admin_get_activate_tax_rate', 'eaccounting_activate_tax_rate' );


function eaccounting_deactivate_tax_rate( $data ) {
	if ( ! isset( $data['_wpnonce'] ) || ! wp_verify_nonce( $data['_wpnonce'], 'eaccounting_tax_rates_nonce' ) ) {
		wp_die( __( 'Trying to cheat or something?', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to update account', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	$tax_rate_id = absint( $data['tax_rate'] );

	if ( $tax_rate_id ) {
		eaccounting_insert_tax_rate( [
			'id'     => $tax_rate_id,
			'status' => 'inactive'
		] );
	}

	eaccounting_admin_notice( __( 'Tax Rate Deactivated', 'wp-eaccounting' ) );

	wp_redirect( admin_url( 'admin.php?page=eaccounting-misc&tab=tax_rates&notice=1' ) );
	error_log( 'eaccounting_deactivate_tax_rate' );
}

add_action( 'eaccounting_admin_get_deactivate_tax_rate', 'eaccounting_deactivate_tax_rate' );


function eaccounting_delete_tax_rate_handler( $data ) {

	if ( ! isset( $data['_wpnonce'] ) || ! wp_verify_nonce( $data['_wpnonce'], 'eaccounting_tax_rates_nonce' ) ) {
		wp_die( __( 'Trying to cheat or something?', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to update account', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( $tax_rate_id = absint( $data['tax_rate'] ) ) {
		eaccounting_delete_tax_rate( $tax_rate_id );
	}

	eaccounting_admin_notice( __( 'Tax Rate Deleted', 'wp-eaccounting' ) );

	wp_redirect( admin_url( 'admin.php?page=eaccounting-misc&tab=tax_rates&notice=1' ) );
}

add_action( 'eaccounting_admin_get_delete_tax_rate', 'eaccounting_delete_tax_rate_handler' );

function eaccounting_edit_tax_rate( $posted ) {
	if ( ! isset( $posted['_wpnonce'] ) || ! wp_verify_nonce( $posted['_wpnonce'], 'eaccounting_tax_rate_nonce' ) ) {
		wp_die( __( 'Trying to cheat or something?', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to update account', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	$created = eaccounting_insert_tax_rate( $posted );
	if ( is_wp_error( $posted ) ) {
		eaccounting_admin_notice( $created->get_error_message(), 'error');
		wp_redirect(add_query_arg(['notice' => '1']));
	}

	eaccounting_admin_notice( __('Tax Rate saved', 'wp-eaccounting'));
	wp_redirect(add_query_arg(['notice' => '1', 'eaccounting-action' => 'edit_tax_rate', 'tax_rate' => $created]));
}

add_action( 'eaccounting_admin_post_edit_tax_rate', 'eaccounting_edit_tax_rate' );
