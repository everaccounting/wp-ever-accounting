<?php
defined( 'ABSPATH' ) || exit();
/**
 * Activate
 * since 1.0.0
 *
 * @param $data
 */
function eaccounting_action_activate_product( $data ) {
	if ( ! isset( $data['_wpnonce'] ) || ! wp_verify_nonce( $data['_wpnonce'], 'eaccounting_products_nonce' ) ) {
		wp_die( __( 'Trying to cheat or something?', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to update account', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	$product_id = absint( $data['product'] );
	if ( $product_id ) {
		eaccounting_insert_product( [
			'id'     => $product_id,
			'status' => 'active'
		] );
	}
	eaccounting_admin_notice( __( 'Product status updated to active.', 'wp-eaccounting' ) );
	wp_redirect( admin_url( 'admin.php?page=eaccounting-products' ) );
	exit;
}

add_action( 'eaccounting_admin_get_activate_product', 'eaccounting_action_activate_product' );

/**
 * Deactivate
 *
 * since 1.0.0
 *
 * @param $data
 */
function eaccounting_action_deactivate_product( $data ) {
	if ( ! isset( $data['_wpnonce'] ) || ! wp_verify_nonce( $data['_wpnonce'], 'eaccounting_products_nonce' ) ) {
		wp_die( __( 'Trying to cheat or something?', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to update account', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	$product_id = absint( $data['product'] );
	if ( $product_id ) {
		eaccounting_insert_product( [
			'id'     => $product_id,
			'status' => 'inactive'
		] );
	}

	eaccounting_admin_notice( __( 'Product status updated to inactive.', 'wp-eaccounting' ) );
	wp_redirect( admin_url( 'admin.php?page=eaccounting-products' ) );
	exit;
}

add_action( 'eaccounting_admin_get_deactivate_product', 'eaccounting_action_deactivate_product' );


/**
 * Delete
 * since 1.0.0
 *
 * @param $data
 */
function eaccounting_action_delete_product( $data ) {

	if ( ! isset( $data['_wpnonce'] ) || ! wp_verify_nonce( $data['_wpnonce'], 'eaccounting_products_nonce' ) ) {
		wp_die( __( 'Trying to cheat or something?', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to update account', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( $product_id = absint( $data['product'] ) ) {
		eaccounting_delete_product( $product_id );
	}
	eaccounting_admin_notice( __( 'Product deleted successfully.', 'wp-eaccounting' ) );
	wp_redirect( admin_url( 'admin.php?page=eaccounting-products' ) );
	exit;
}

add_action( 'eaccounting_admin_get_delete_product', 'eaccounting_action_delete_product' );


function eaccounting_action_edit_product( $data ) {
	if ( ! isset( $data['_wpnonce'] ) || ! wp_verify_nonce( $data['_wpnonce'], 'eaccounting_product_nonce' ) ) {
		return;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to update account', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	$args = array(
		'id'             => absint( $data['id'] ),
		'name'           => sanitize_text_field( $data['name'] ),
		'sku'            => sanitize_text_field( $data['sku'] ),
		'description'    => sanitize_textarea_field( $data['description'] ),
		'sale_price'     => $data['sale_price'],
		'purchase_price' => $data['purchase_price'],
		'quantity'       => absint( $data['quantity'] ),
		'category_id'    => absint( $data['category_id'] ),
		'status'         => isset( $data['status'] ) ? $data['status'] : 'inactive',
	);

	$product_id = eaccounting_insert_product( $args );

	if ( is_wp_error( $product_id ) ) {
		eaccounting_admin_notice( $product_id->get_error_message(), 'warning' );
		wp_redirect( add_query_arg( [ 'eaccounting-action' => 'add_product' ] ) );
		exit();
	}

	if ( ! $product_id ) {
		eaccounting_admin_notice( __( 'Could not create product, please try again' ), 'warning' );
		wp_redirect( add_query_arg( [ 'eaccounting-action' => 'add_product' ] ) );
		exit();
	}


	if ( empty( $data['id'] ) ) {
		$message = __( 'Product created successfully.', 'wp-eaccounting' );
	} else {
		$message = __( 'Product updated successfully.', 'wp-eaccounting' );
	}

	eaccounting_admin_notice( $message );
	wp_redirect( add_query_arg( [ 'eaccounting-action' => 'edit_product', 'product' => $product_id ] ) );
	exit();

}

add_action( 'eaccounting_admin_post_edit_product', 'eaccounting_action_edit_product' );
