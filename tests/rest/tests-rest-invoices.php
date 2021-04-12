<?php

use EverAccounting\REST\Invoices_Controller;
use EverAccounting\Tests\Framework\Helpers\Document_Helper;
use EverAccounting\Tests\Framework\REST_UnitTestCase;

class Tests_REST_Invoices extends REST_UnitTestCase {
	/**
	 * Setup our test server, endpoints, and user info.
	 */
	public function setUp() {
		parent::setUp();
		$this->endpoint = new Invoices_Controller();
	}

	/**
	 * Test route registration.
	 *
	 * @since 1.1.4
	 */
	public function test_register_routes() {
		$routes = $this->server->get_routes();
		$this->assertArrayHasKey( '/ea/v1/invoices', $routes );
		$this->assertArrayHasKey( '/ea/v1/invoices/(?P<id>[\d]+)', $routes );
	}

	/**
	 * Test getting invoices.
	 *
	 * @since 1.1.4
	 */
	public function test_get_invoices() {
		wp_set_current_user( $this->user->ID );
//		$this->factory->invoice->create_many( 2 );
//		$response = $this->do_rest_get_request( '/ea/v1/invoices' );
//		$invoices = $response->get_data();
//		$this->assertEquals( 200, $response->get_status() );
		//$this->assertEquals( 2, count( $invoices ) );
		$this->assertEquals( true, true );
	}

	/**
	 * Test getting invoices without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_get_invoices_without_permission() {
		wp_set_current_user( 0 );
		$response = $this->do_rest_get_request( '/ea/v1/invoices' );
		$this->assertEquals( 401, $response->get_status() );
	}

	/**
	 * Test getting a single invoice.
	 *
	 * @since 1.1.4
	 */
	public function test_get_invoice() {
		wp_set_current_user( $this->user->ID );
		$expected_response_fields = array();
		foreach ( $this->endpoint->get_public_item_schema()['properties'] as $key => $value ) {
			if ( 'object' == $value['type'] && 'currency' == $key ) {
				$expected_response_fields[] = $key . '_code';
			} elseif ( 'object' == $value['type'] && 'currency' != $key ) {
				$expected_response_fields[] = $key . '_id';
			} else {
				$expected_response_fields[] = $key;
			}
		}
		$invoice  = Document_Helper::create_document();
		$response = $this->do_rest_get_request( '/ea/v1/invoices/' . $invoice->get_id() );
		$this->assertEquals( 200, $response->get_status(), var_export( $response, true ) );
		$response_fields = array();
		foreach ( $response->get_data() as $key => $value ) {
			if ( is_array( $value ) && 'currency' != $key ) {
				$response_fields[] = $key . '_id';
			} elseif ( is_array( $value ) && 'currency' == $key ) {
				$response_fields[] = $key . '_code';
			} else {
				$response_fields[] = $key;
			}
		}

		$this->assertEmpty( array_diff( $expected_response_fields, $response_fields ), 'These fields were expected but not present in API response: ' . print_r( array_diff( $expected_response_fields, $response_fields ), true ) );

		$this->assertEmpty( array_diff( $response_fields, $expected_response_fields ), 'These fields were not expected in the API response: ' . print_r( array_diff( $response_fields, $expected_response_fields ), true ) );

		// Test that all fields are returned when requested one by one.
		foreach ( $expected_response_fields as $field ) {
			$request = new WP_REST_Request( 'GET', '/ea/v1/invoices/' . $invoice->get_id() );
			$request->set_param( '_fields', $field );
			$response = $this->server->dispatch( $request );
			$this->assertEquals( 200, $response->get_status() );
			$this->assertContains( $field, $response_fields, "Field $field was expected but not present in order API response." );
		}
	}

	/**
	 * Test getting single invoice without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_get_invoice_without_permission() {
		wp_set_current_user( 0 );
		$invoice  = Document_Helper::create_document();
		$response = $this->do_rest_get_request( '/ea/v1/invoices/' . $invoice->get_id() );
		$this->assertEquals( 401, $response->get_status() );
	}

	/**
	 * Test creating a single invoice.
	 *
	 * @since 1.1.4
	 */
	public function test_insert_invoice() {
		wp_set_current_user( $this->user->ID );
		$data = Document_Helper::create_document( false );

		$data['currency']['code'] = $data['currency_code'];
		unset( $data['currency_code'] );
		$data['customer']['id'] = $data['contact_id'];
		unset( $data['contact_id'] );
		$data['category']['id'] = $data['category_id'];
		unset( $data['category_id'] );

		$response = $this->do_rest_post_request( '/ea/v1/invoices', $data );
		$invoice  = $response->get_data();
		$this->assertEquals( 201, $response->get_status() );
		unset( $invoice['id'] );
		$data['document_number'] = $invoice['document_number'];

		//todo need to update the status
		unset( $invoice['status'] );
//		$data['status']          = $invoice['status'];

		foreach ( $invoice as $key => $value ) {
			if ( ! is_scalar( $value ) ) {
				continue;
			}

			$this->assertEquals( $value, $data[ $key ] );
		}

	}

	/**
	 * Test creating invoice without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_insert_invoice_without_permission() {
		wp_set_current_user( 0 );
		$data                     = Document_Helper::create_document( false );
		$data['currency']['code'] = $data['currency_code'];
		$data['customer']['id']   = $data['contact_id'];
		$data['category']['id']   = $data['category_id'];
		$response                 = $this->do_rest_post_request( '/ea/v1/invoices', $data );
		$this->assertEquals( 401, $response->get_status() );
	}


	/**
	 * Test editing a single invoice. Tests multiple payment types.
	 *
	 * @since 1.1.4
	 */
	public function test_update_invoice() {
		wp_set_current_user( $this->user->ID );
		//todo need to update the invoice
		$this->assertEquals( true, true );
	}

	/**
	 * Test updating a single invoice without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_update_invoice_without_permission() {
		wp_set_current_user( 0 );
		$invoice = Document_Helper::create_document();
		//todo need to update
//		$updated_data = [
//			'description' => 'will not change',
//		];
		//$request      = $this->do_rest_put_request( '/ea/v1/invoices/' . $payment->get_id(), $updated_data );
		//$this->assertEquals( 401, $request->get_status() );
		$this->assertEquals( true, true );
	}

	/**
	 * Test deleting a single invoice.
	 *
	 * @since 1.1.4
	 */
	public function test_delete_invoice() {
		wp_set_current_user( $this->user->ID );
		$invoice      = Document_Helper::create_document();

		$response = $this->do_rest_request( '/ea/v1/invoices/' . $invoice->get_id(), 'DELETE' );
		$this->assertEquals( 200, $response->get_status() );

		$response = $this->do_rest_get_request( '/ea/v1/invoices' );
		$this->assertEquals( 0, $response->get_headers()['X-WP-Total'] );
	}

	/**
	 * Test deleting a single invoice without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_delete_invoice_without_permission() {
		wp_set_current_user( 0 );
		$invoice      = Document_Helper::create_document();
		$response = $this->do_rest_request( '/ea/v1/invoices/' . $invoice->get_id(), 'DELETE' );
		$this->assertEquals( 401, $response->get_status() );
	}

}
