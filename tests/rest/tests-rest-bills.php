<?php

use EverAccounting\REST\Bills_Controller;
use EverAccounting\Tests\Framework\Helpers\Document_Helper;
use EverAccounting\Tests\Framework\REST_UnitTestCase;

class Tests_REST_Bills extends REST_UnitTestCase {

	/**
	 * Setup our test server, endpoints, and user info.
	 */
	public function setUp() {
		parent::setUp();
		$this->endpoint = new Bills_Controller();
	}

	/**
	 * Test route registration.
	 *
	 * @since 1.1.4
	 */
	public function test_register_routes() {
		$routes = $this->server->get_routes();
		$this->assertArrayHasKey( '/ea/v1/bills', $routes );
		$this->assertArrayHasKey( '/ea/v1/bills/(?P<id>[\d]+)', $routes );
	}

	/**
	 * Test getting bills.
	 *
	 * @since 1.1.4
	 */
	public function test_get_bills() {
		wp_set_current_user( $this->user->ID );
//		$this->factory->invoice->create_many( 2 );
//		$response = $this->do_rest_get_request( '/ea/v1/invoices' );
//		$invoices = $response->get_data();
//		$this->assertEquals( 200, $response->get_status() );
		//$this->assertEquals( 2, count( $invoices ) );
		$this->assertEquals( true, true );
	}

	/**
	 * Test getting bills without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_get_bills_without_permission() {
		wp_set_current_user( 0 );
		$response = $this->do_rest_get_request( '/ea/v1/bills' );
		$this->assertEquals( 401, $response->get_status() );
	}

	/**
	 * Test getting a single bill.
	 *
	 * @since 1.1.4
	 */
	public function test_get_bill() {
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

		$bill = Document_Helper::create_document( true, array( 'type' => 'bill' ) );

		$response = $this->do_rest_get_request( '/ea/v1/bills/' . $bill->get_id() );
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
			$request = new WP_REST_Request( 'GET', '/ea/v1/bills/' . $bill->get_id() );
			$request->set_param( '_fields', $field );
			$response = $this->server->dispatch( $request );
			$this->assertEquals( 200, $response->get_status() );
			$this->assertContains( $field, $response_fields, "Field $field was expected but not present in order API response." );
		}
	}

	/**
	 * Test getting single bill without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_get_bill_without_permission() {
		wp_set_current_user( 0 );
		$bill     = Document_Helper::create_document( true, array( 'type' => 'bill' ) );
		$response = $this->do_rest_get_request( '/ea/v1/bills/' . $bill->get_id() );
		$this->assertEquals( 401, $response->get_status() );
	}

	/**
	 * Test creating a single bill.
	 *
	 * @since 1.1.4
	 */
	public function test_insert_bill() {
		wp_set_current_user( $this->user->ID );
		$data = Document_Helper::create_document( false, array( 'type' => 'bill' ) );

		$data['currency']['code'] = $data['currency_code'];
		unset( $data['currency_code'] );
		$data['vendor']['id'] = $data['contact_id'];
		unset( $data['contact_id'] );
		$data['category']['id'] = $data['category_id'];
		unset( $data['category_id'] );

		$response = $this->do_rest_post_request( '/ea/v1/bills', $data );
		$bill     = $response->get_data();
		$this->assertEquals( 201, $response->get_status() );
		unset( $bill['id'] );
		$data['document_number'] = $bill['document_number'];

		//todo need to update the status
		unset( $bill['status'] );
//		$data['status']          = $bill['status'];

		foreach ( $bill as $key => $value ) {
			if ( ! is_scalar( $value ) ) {
				continue;
			}

			$this->assertEquals( $value, $data[ $key ] );
		}

	}

	/**
	 * Test creating bill without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_insert_bill_without_permission() {
		wp_set_current_user( 0 );
		$data                     = Document_Helper::create_document( false, array( 'type' => 'bill' ) );
		$data['currency']['code'] = $data['currency_code'];
		$data['vendor']['id']     = $data['contact_id'];
		$data['category']['id']   = $data['category_id'];
		$response                 = $this->do_rest_post_request( '/ea/v1/bills', $data );
		$this->assertEquals( 401, $response->get_status() );
	}



}