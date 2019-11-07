<?php
defined( 'ABSPATH' ) || exit();

function eaccounting_edit_product( $data ) {
	if ( ! isset( $data['_wpnonce'] ) || ! wp_verify_nonce( $data['_wpnonce'], 'eaccounting_product_nonce' ) ) {
		return ;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to update account', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	$args = array(
		'id'             => absint( $data['id'] ),
		'name'           => sanitize_text_field( $data['name'] ),
		'sku'            => sanitize_text_field( $data['sku'] ),
		'description'    => sanitize_textarea_field( $data['description'] ),
		'sale_price'     => doubleval( $data['sale_price'] ),
		'purchase_price' => doubleval( $data['purchase_price'] ),
		'quantity'       => absint( $data['quantity'] ),
		'category_id'    => absint( $data['category_id'] ),
		'tax_id'         => absint( $data['tax_id'] ),
		'status'         => isset( $data['status'] ) ? 1 : 0,
	);

	$product_id = eaccounting_insert_product( $args );
	if(is_wp_error($product_id)){
		eaccounting()->notices->add($product_id->get_error_message(), 'warning');
	}

	eaccounting()->notices->add(sprintf(__('Product <strong>%s</strong> saved successfully', 'wp-ever-accounting'), $args['name']));

	wp_redirect(add_query_arg(['eaccounting-action' => 'add_product']));
}
add_action( 'eaccounting_admin_post_edit_product', 'eaccounting_edit_product' );


function eaccounting_delete_product_handler( $data ) {

	if ( ! isset( $data['_wpnonce'] ) || ! wp_verify_nonce( $data['_wpnonce'], 'eaccounting_products_nonce' ) ) {
		wp_die( __( 'Trying to cheat or something?', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to update account', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
	}

	if ( $product_id = absint( $data['product'] ) ) {
		eaccounting_delete_product( $product_id );
	}

	wp_redirect( admin_url( 'admin.php?page=eaccounting-products' ) );
}

add_action( 'eaccounting_admin_get_delete_product', 'eaccounting_delete_product_handler' );


function eaccounting_deactivate_product( $data ) {
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

	wp_redirect( admin_url( 'admin.php?page=eaccounting-products' ) );
}

add_action( 'eaccounting_admin_get_deactivate_product', 'eaccounting_deactivate_product' );


function eaccounting_activate_product( $data ) {
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

	wp_redirect( admin_url( 'admin.php?page=eaccounting-products' ) );
}

add_action( 'eaccounting_admin_get_activate_product', 'eaccounting_activate_product' );
