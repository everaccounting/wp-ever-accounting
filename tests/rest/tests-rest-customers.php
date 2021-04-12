<?php

use EverAccounting\REST\Customers_Controller;
use EverAccounting\Tests\Framework\Helpers\Customer_Helper;
use EverAccounting\Tests\Framework\REST_UnitTestCase;

class Tests_REST_Customers extends REST_UnitTestCase {
	/**
	 * Setup our test server, endpoints, and user info.
	 */
	public function setUp() {
		parent::setUp();
		$this->endpoint = new Customers_Controller();
	}

	/**
	 * Test route registration.
	 *
	 * @since 1.1.4
	 */
	public function test_register_routes() {
		$routes = $this->server->get_routes();
		$this->assertArrayHasKey( '/ea/v1/customers', $routes );
		$this->assertArrayHasKey( '/ea/v1/customers/(?P<id>[\d]+)', $routes );

	}

	/**
	 * Test getting customers.
	 *
	 * @since 1.1.4
	 */
	public function test_get_customers() {
		wp_set_current_user( $this->user->ID );

		wp_set_current_user( $this->user->ID );
		$this->factory->customer->create_many( 10 );
		$response  = $this->do_rest_get_request( '/ea/v1/customers' );
		$customers = $response->get_data();
		$this->assertEquals( 200, $response->get_status() );
		$this->assertEquals( 10, count( $customers ) );
	}

	/**
	 * Test getting customers without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_get_customers_without_permission() {
		wp_set_current_user( 0 );
		$response = $this->do_rest_get_request( '/ea/v1/customers/' );
		$this->assertEquals( 404, $response->get_status() );
	}

	/**
	 * Test getting a single customer.
	 *
	 * @since 1.1.4
	 */
	public function test_get_customer() {
		wp_set_current_user( $this->user->ID );
		$expected_response_fields = array_keys( $this->endpoint->get_public_item_schema()['properties'] );
		$customer                 = Customer_Helper::create_customer();
		$response                 = $this->do_rest_get_request( '/ea/v1/customers/' . $customer->get_id() );
		$this->assertEquals( 200, $response->get_status(), var_export( $response, true ) );
		$response_fields = array_keys( $response->get_data() );

		$this->assertEmpty( array_diff( $expected_response_fields, $response_fields ), 'These fields were expected but not present in API response: ' . print_r( array_diff( $expected_response_fields, $response_fields ), true ) );

		$this->assertEmpty( array_diff( $response_fields, $expected_response_fields ), 'These fields were not expected in the API response: ' . print_r( array_diff( $response_fields, $expected_response_fields ), true ) );

		// Test that all fields are returned when requested one by one.
		foreach ( $expected_response_fields as $field ) {
			$request = new WP_REST_Request( 'GET', '/ea/v1/customers/' . $customer->get_id() );
			$request->set_param( '_fields', $field );
			$response = $this->server->dispatch( $request );
			$this->assertEquals( 200, $response->get_status() );
			$response_fields = array_keys( $response->get_data() );
			$this->assertContains( $field, $response_fields, "Field $field was expected but not present in order API response." );
		}
	}

	/**
	 * Test getting single customer without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_get_customer_without_permission() {
		wp_set_current_user( 0 );
		$customer  = Customer_Helper::create_customer();
		$response = $this->do_rest_get_request( '/ea/v1/customers/' . $customer->get_id() );
		$this->assertEquals( 401, $response->get_status() );
	}


	/**
	 * Test creating a single customer.
	 *
	 * @since 1.1.4
	 */
	public function test_insert_customer() {
		wp_set_current_user( $this->user->ID );
		$data                     = Customer_Helper::create_customer( false );
		$data['currency']['code'] = $data['currency_code'];
		unset( $data['currency_code'] );
		$response = $this->do_rest_post_request( '/ea/v1/customers', $data );
		$customer  = $response->get_data();
		$this->assertEquals( 201, $response->get_status() );
		unset( $customer['id'] );
		foreach ( $customer as $key => $value ) {
			if ( ! is_scalar( $value ) ) {
				continue;
			}
			$this->assertEquals( $value, $data[ $key ] );
		}
	}

	/**
	 * Test creating customer without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_insert_customer_without_permission() {
		wp_set_current_user( 0 );
		$data                     = Customer_Helper::create_customer( false );
		$data['currency']['code'] = $data['currency_code'];
		$response                 = $this->do_rest_post_request( '/ea/v1/customers', $data );
		$this->assertEquals( 401, $response->get_status() );
	}


	/**
	 * Test editing a single customer. Tests multiple customer types.
	 *
	 * @since 1.1.4
	 */
	public function test_update_customer() {
		wp_set_current_user( $this->user->ID );
		$customer     = Customer_Helper::create_customer();
		$updated_data = [
			'name'         => 'John Michel',
			'email'        => 'johnmh@email.com',
			'vat_number'   => 'vt-124578',
			'enabled'      => 0,
		];

		$request = $this->do_rest_put_request( '/ea/v1/customers/' . $customer->get_id(), $updated_data );
		$this->assertEquals( 200, $request->get_status() );
		$response = $this->do_rest_get_request( '/ea/v1/customers/' . $customer->get_id() );
		$data     = $response->get_data();
		$this->assertEquals( 200, $response->get_status() );

		foreach ( $updated_data as $key => $val ) {
			$this->assertEquals( $val, $data[ $key ] );
		}

	}

	/**
	 * Test updating a single customer without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_update_customer_without_permission() {
		wp_set_current_user( 0 );
		$customer     = Customer_Helper::create_customer();
		$updated_data = [
			'name' => 'will not change',
		];
		$request      = $this->do_rest_put_request( '/ea/v1/customers/' . $customer->get_id(), $updated_data );
		$this->assertEquals( 401, $request->get_status() );
	}

	/**
	 * Test deleting a single customer.
	 *
	 * @since 1.1.4
	 */
	public function test_delete_customer() {
		wp_set_current_user( $this->user->ID );
		$customer     = Customer_Helper::create_customer();

		$response = $this->do_rest_request( '/ea/v1/customers/' . $customer->get_id(), 'DELETE' );
		$this->assertEquals( 200, $response->get_status() );

		$response = $this->do_rest_get_request( '/ea/v1/customers' );
		$this->assertEquals( 2, $response->get_headers()['X-WP-Total'] );
	}

	/**
	 * Test deleting a single customer without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_delete_customer_without_permission() {
		wp_set_current_user( 0 );
		$customer     = Customer_Helper::create_customer();
		$response = $this->do_rest_request( '/ea/v1/customers/' . $customer->get_id(), 'DELETE' );
		$this->assertEquals( 401, $response->get_status() );
	}
}