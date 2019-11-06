<?php

defined( 'ABSPATH' ) || exit();

class EAccounting_Ajax_Taxes extends EAccounting_Ajax{

	/**
	 * EAccounting_Ajax_Accounts constructor.
	 */
	public function __construct() {
		$this->action('eaccounting_add_tax', 'add_tax');
	}

	/**
	 *
	 * @since 1.0.0
	 */
	public function add_tax(){
		if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( $_REQUEST['nonce'], 'eaccounting_tax_nonce' ) ) {
			$this->error('');
		}

		if( ! current_user_can( 'manage_options' ) ) {
			$this->error(__( 'You do not have permission to create discount codes', 'wp-ever-accounting' ));
		}

		$created = eaccounting_insert_tax($_REQUEST);
		if(is_wp_error($created)){
			$this->error($created->get_error_message());
		}

		$this->success(__('New Tax Rate has been created', 'wp-ever-accounting'));


	}

}

new EAccounting_Ajax_Taxes();
