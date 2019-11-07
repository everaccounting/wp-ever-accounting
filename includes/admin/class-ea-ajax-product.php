<?php

defined( 'ABSPATH' ) || exit();

class EAccounting_Ajax_Products extends EAccounting_Ajax {

	/**
	 * EAccounting_Ajax_Products constructor.
	 */
	public function __construct() {
		$this->action( 'eaccounting_add_product', 'validate_insert_product' );
	}

	/**
	 * Validate product sku
	 * @since 1.0.0
	 */
	public function validate_insert_product() {
		if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( $_REQUEST['nonce'], 'eaccounting_product_nonce' ) ) {
			$this->error( '' );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			$this->error( __( 'You do not have permission to create product', 'wp-ever-accounting' ) );
		}

		$id = empty($_REQUEST['id'])? false : absint($_REQUEST['id']);
		$sku = empty($_REQUEST['sku'])? false : sanitize_text_field($_REQUEST['sku']);

		if(empty($sku)){
			$this->error( __( 'Product SKU is required', 'wp-ever-accounting' ) );
		}

		if ( ! eaccounting_is_sku_available( $sku, $id ) ) {
			return new WP_Error( 'invalid_content', __( 'SKU is taken by other product', 'wp-ever-accounting' ) );
		}

		$this->success('');
	}

}

new EAccounting_Ajax_Products();
