<?php
defined( 'ABSPATH' ) || exit();


function eaccounting_activate_category( $data ) {
	if ( ! isset( $data['_wpnonce'] ) || ! wp_verify_nonce( $data['_wpnonce'], 'eaccounting_categories_nonce' ) ) {
		wp_die( __( 'Trying to cheat or something?', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to update account', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	$category_id = absint( $data['category'] );
	if ( $category_id ) {
		eaccounting_insert_category( [
			'id'     => $category_id,
			'status' => 'active'
		] );
	}

	eaccounting_admin_notice( __( 'Category Activated.', 'wp-eaccounting' ) );

	wp_redirect( admin_url( 'admin.php?page=eaccounting-categories' ) );
	exit;
}

add_action( 'eaccounting_admin_get_activate_category', 'eaccounting_activate_category' );


function eaccounting_deactivate_category( $data ) {
	if ( ! isset( $data['_wpnonce'] ) || ! wp_verify_nonce( $data['_wpnonce'], 'eaccounting_categories_nonce' ) ) {
		wp_die( __( 'Trying to cheat or something?', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to update account', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	$category_id = absint( $data['category'] );

	if ( $category_id ) {
		eaccounting_insert_category( [
			'id'     => $category_id,
			'status' => 'inactive'
		] );
	}

	eaccounting_admin_notice( __( 'Category Deactivated', 'wp-eaccounting' ) );

	wp_redirect( admin_url( 'admin.php?page=eaccounting-categories' ) );
	exit;
}

add_action( 'eaccounting_admin_get_deactivate_category', 'eaccounting_deactivate_category' );


function eaccounting_delete_category_handler( $data ) {

	if ( ! isset( $data['_wpnonce'] ) || ! wp_verify_nonce( $data['_wpnonce'], 'eaccounting_categories_nonce' ) ) {
		wp_die( __( 'Trying to cheat or something?', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to update account', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( $category_id = absint( $data['category'] ) ) {
		eaccounting_delete_category( $category_id );
	}

	eaccounting_admin_notice( __( 'Category Deleted', 'wp-eaccounting' ) );

	wp_redirect( admin_url( 'admin.php?page=eaccounting-categories' ) );
	exit;
}

add_action( 'eaccounting_admin_get_delete_category', 'eaccounting_delete_category_handler' );

function eaccounting_edit_category( $posted ) {
	if ( ! isset( $posted['_wpnonce'] ) || ! wp_verify_nonce( $posted['_wpnonce'], 'eaccounting_category_nonce' ) ) {
		wp_die( __( 'Trying to cheat or something?', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to update account', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	$created = eaccounting_insert_category( array(
		'id'     => $posted['id'],
		'name'   => $posted['name'],
		'type'   => $posted['type'],
		'color'  => $posted['color'],
		'status' => isset( $posted['status'] ) ? $posted['status'] : 'inactive',
	) );

	if ( is_wp_error( $posted ) ) {
		eaccounting_admin_notice( $created->get_error_message(), 'error' );
		wp_redirect( admin_url( 'admin.php?page=eaccounting-categories' ) );
		exit();
	}

	eaccounting_admin_notice( __( 'Category saved', 'wp-eaccounting' ) );
	wp_redirect( add_query_arg( [ 'eaccounting-action' => 'edit_category', 'category' => $created ] ) );
	exit();
}

add_action( 'eaccounting_admin_post_edit_category', 'eaccounting_edit_category' );
