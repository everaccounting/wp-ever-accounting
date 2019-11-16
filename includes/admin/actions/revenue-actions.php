<?php
defined( 'ABSPATH' ) || exit();

/**
 * Delete
 * since 1.0.0
 *
 * @param $data
 */
function eaccounting_action_delete_revenue( $data ) {

	if ( ! isset( $data['_wpnonce'] ) || ! wp_verify_nonce( $data['_wpnonce'], 'eaccounting_revenues_nonce' ) ) {
		wp_die( __( 'Trying to cheat or something?', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to update account', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( $revenue_id = absint( $data['revenue'] ) ) {
		eaccounting_delete_revenue( $revenue_id );
	}

	wp_redirect( add_query_arg( [ 'revenue' => $revenue_id ], admin_url( 'admin.php?page=eaccounting-revenues' ) ) );
	exit;
}

add_action( 'eaccounting_admin_get_delete_revenue', 'eaccounting_action_delete_revenue' );


function eaccounting_action_edit_revenue( $data ) {
	if ( ! isset( $data['_wpnonce'] ) || ! wp_verify_nonce( $data['_wpnonce'], 'eaccounting_revenue_nonce' ) ) {
		wp_die( __( 'Trying to cheat or something?', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to update account', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	$revenue_id = eaccounting_insert_revenue( array(
		'id'          => isset( $data['id'] ) ? absint( $data['id'] ) : '',
		'account_id'  => isset( $data['account_id'] ) ? absint( $data['account_id'] ) : '',
		'paid_at'     => isset( $data['paid_at'] ) ? eaccounting_sanitize_date( $data['paid_at'] ) : '',
		'amount'      => isset( $data['amount'] ) ? eaccounting_sanitize_price( $data['amount'] ) : '',
		'contact_id'  => isset( $data['contact_id'] ) ? absint( $data['contact_id'] ) : '',
		'description' => isset( $data['description'] ) ? sanitize_text_field( $data['description'] ) : '',
		'category_id' => isset( $data['category_id'] ) ? absint( $data['category_id'] ) : '',
		'reference'   => isset( $data['reference'] ) ? sanitize_text_field( $data['reference'] ) : '',
		'method_id'   => isset( $data['method_id'] ) ? absint( $data['method_id'] ) : '',
		'parent_id'   => isset( $data['parent_id'] ) ? absint( $data['parent_id'] ) : '',
		'reconciled'  => isset( $data['reconciled'] ) ? absint( $data['reconciled'] ) : '',
		'file_id'     => isset( $data['file_id'] ) ? absint( $data['file_id'] ) : '',
	) );

	$redirect = add_query_arg( [
		'eaccounting-action' => 'edit_revenue',
		'revenue'            => !is_wp_error($revenue_id)? $revenue_id:'',
	], admin_url( 'admin.php?page=eaccounting-revenues' ) );

	if ( is_wp_error( $revenue_id ) ) {
		eaccounting_admin_notice( $revenue_id->get_error_message(), 'error' );
		wp_redirect( $redirect );
		exit();
	}

	if ( empty( $data['id'] ) ) {
		$message = __( 'Revenue created successfully.', 'wp-eaccounting' );
	} else {
		$message = __( 'Revenue updated successfully.', 'wp-eaccounting' );
	}
	eaccounting_admin_notice( $message );
	wp_redirect( $redirect );
	exit();

}

add_action( 'eaccounting_admin_post_edit_revenue', 'eaccounting_action_edit_revenue' );
