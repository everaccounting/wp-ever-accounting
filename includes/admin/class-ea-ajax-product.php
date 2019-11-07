<?php

defined( 'ABSPATH' ) || exit();

class EAccounting_Ajax_Products extends EAccounting_Ajax {

	/**
	 * EAccounting_Ajax_Products constructor.
	 */
	public function __construct() {
		$this->action( 'eaccounting_edit_product', 'update_product' );
	}

	/**
	 * Validate product sku
	 * @since 1.0.0
	 */
	public function update_product() {
		if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'eaccounting_product_nonce' ) ) {
			$this->error( '' );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			$this->error( __( 'You do not have permission to create product', 'wp-ever-accounting' ) );
		}

		$args = array(
			'id'             => absint( $_REQUEST['id'] ),
			'name'           => sanitize_text_field( $_REQUEST['name'] ),
			'sku'            => sanitize_text_field( $_REQUEST['sku'] ),
			'description'    => sanitize_textarea_field( $_REQUEST['description'] ),
			'sale_price'     => eaccounting_sanitize_price( $_REQUEST['sale_price'] ),
			'purchase_price' => eaccounting_sanitize_price( $_REQUEST['purchase_price'] ),
			'quantity'       => absint( $_REQUEST['quantity'] ),
			'category_id'    => absint( $_REQUEST['category_id'] ),
			'tax_id'         => absint( $_REQUEST['tax_id'] ),
			'status'         => isset( $_REQUEST['status'] ) ? 1 : 0,
		);

		$created = eaccounting_insert_product($args);

		if(is_wp_error($created)){
			$this->error($created->get_error_message());
		}
		eaccounting()->notices->add(sprintf(__('Product <strong>%s</strong> saved successfully', 'wp-ever-accounting'), $args['name']));
		$this->success(__('Product created successfully', 'wp-ever-accounting'), $created);
	}

}

new EAccounting_Ajax_Products();
