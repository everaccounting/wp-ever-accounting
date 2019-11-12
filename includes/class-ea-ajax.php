<?php
defined( 'ABSPATH' ) || exit();

class EAccounting_Ajax {

	/**
	 * EAccounting_Ajax constructor.
	 */
	public function __construct() {
		add_action( 'wp_ajax_eaccounting_edit_contact', array( $this, 'edit_contact' ) );
	}


	public function edit_contact() {
		$this->verify_nonce( 'eaccounting_edit_contact' );
		$this->check_permission();

		$data = array(
			'id'         => isset( $_REQUEST['id'] ) ? absint( $_REQUEST['id'] ) : null,
			'first_name' => isset( $_REQUEST['first_name'] ) ? sanitize_text_field( $_REQUEST['first_name'] ) : '',
			'last_name'  => isset( $_REQUEST['last_name'] ) ? sanitize_text_field( $_REQUEST['last_name'] ) : '',
			'email'      => isset( $_REQUEST['email'] ) ? sanitize_email( $_REQUEST['email'] ) : '',
			'phone'      => isset( $_REQUEST['phone'] ) ? sanitize_text_field( $_REQUEST['phone'] ) : '',
			'tax_number' => isset( $_REQUEST['tax_number'] ) ? sanitize_text_field( $_REQUEST['tax_number'] ) : '',
			'address'    => isset( $_REQUEST['address'] ) ? sanitize_text_field( $_REQUEST['address'] ) : '',
			'city'       => isset( $_REQUEST['city'] ) ? sanitize_text_field( $_REQUEST['city'] ) : '',
			'state'      => isset( $_REQUEST['state'] ) ? sanitize_text_field( $_REQUEST['state'] ) : '',
			'postcode'   => isset( $_REQUEST['postcode'] ) ? sanitize_text_field( $_REQUEST['postcode'] ) : '',
			'country'    => isset( $_REQUEST['country'] ) ? sanitize_text_field( $_REQUEST['country'] ) : '',
			'website'    => isset( $_REQUEST['website'] ) ? sanitize_text_field( $_REQUEST['website'] ) : '',
			'status'     => isset( $_REQUEST['status'] ) ? sanitize_key( $_REQUEST['status'] ) : '',
			'note'       => isset( $_REQUEST['id'] ) ? sanitize_textarea_field( $_REQUEST['id'] ) : '',
			'types'      => isset( $_REQUEST['types'] ) ? $_REQUEST['types']  : [],
		);

		$contact_id = eaccounting_insert_contact( $data );
		if ( is_wp_error( $contact_id ) ) {
			$this->send_error( $contact_id->get_error_message() );
		}

		$this->send_success([
			'id' => $contact_id,
			'message' => __('Contact saved successfully', 'wp-ever-accounting'),
			'redirect' => add_query_arg(['eaccounting-action' => 'edit_contact', 'contact' => $contact_id ], admin_url('admin.php?page=eaccounting-contacts')),
		]);

	}

	/**
	 * Check permission
	 *
	 * since 1.0.0
	 */
	public function check_permission() {
		if ( ! current_user_can( 'manage_options' ) ) {
			$this->send_error( __( 'Error: You are not allowed to do this.', 'wp-ever-accounting' ) );
		}
	}

	/**
	 * Verify nonce request
	 * since 1.0.0
	 *
	 * @param $action
	 */
	public function verify_nonce( $action ) {
		if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], $action ) ) {
			$this->send_error( __( 'Error: Nonce verification failed', 'wp-ever-accounting' ) );
		}
	}

	/**
	 * Wrapper function for sending success response
	 * since 1.0.0
	 *
	 * @param null $data
	 */
	public function send_success( $data = null ) {
		wp_send_json_success( $data );
	}

	/**
	 * Wrapper function for sending error
	 * since 1.0.0
	 *
	 * @param null $data
	 */
	public function send_error( $data = null ) {
		wp_send_json_error( $data );
	}

}

new EAccounting_Ajax();
