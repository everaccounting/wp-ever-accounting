<?php

use EverAccounting\REST\Payments_Controller;
use EverAccounting\Tests\Framework\Helpers\Payment_Helper;
use EverAccounting\Tests\Framework\REST_UnitTestCase;

class Tests_REST_Payments extends REST_UnitTestCase {
	/**
	 * Setup our test server, endpoints, and user info.
	 */
	public function setUp() {
		parent::setUp();
		$this->endpoint = new Payments_Controller();
	}

	/**
	 * Test route registration.
	 *
	 * @since 1.1.4
	 */
	public function test_register_routes() {
		$routes = $this->server->get_routes();
		$this->assertArrayHasKey( '/ea/v1/payments', $routes );
		$this->assertArrayHasKey( '/ea/v1/payments/(?P<id>[\d]+)', $routes );
	}

	/**
	 * Test getting payments.
	 *
	 * @since 1.1.4
	 */
	public function test_get_payments() {
		wp_set_current_user( $this->user->ID );
		$this->factory->payment->create_many( 5 );
		$response = $this->do_rest_get_request( '/ea/v1/payments', [ 'per_page' => 20 ] );
		$payments = $response->get_data();
		$this->assertEquals( 200, $response->get_status() );
		$this->assertEquals( 5, count( $payments ) );
	}

	/**
	 * Test getting payments without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_get_payments_without_permission() {
		wp_set_current_user( 0 );
		$response = $this->do_rest_get_request( '/ea/v1/payments' );
		$this->assertEquals( 401, $response->get_status() );
	}

	/**
	 * Test getting a single payment.
	 *
	 * @since 1.1.4
	 */
	public function test_get_payment() {
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

		$payment  = Payment_Helper::create_payment();
		$response = $this->do_rest_get_request( '/ea/v1/payments/' . $payment->get_id() );
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
			$request = new WP_REST_Request( 'GET', '/ea/v1/payments/' . $payment->get_id() );
			$request->set_param( '_fields', $field );
			$response = $this->server->dispatch( $request );
			$this->assertEquals( 200, $response->get_status() );
			//$response_fields = array_keys( $response->get_data() );
			$this->assertContains( $field, $response_fields, "Field $field was expected but not present in order API response." );
		}

	}

	/**
	 * Test getting single payment without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_get_payment_without_permission() {
		wp_set_current_user( 0 );
		$payment  = Payment_Helper::create_payment();
		$response = $this->do_rest_get_request( '/ea/v1/payments/' . $payment->get_id() );
		$this->assertEquals( 401, $response->get_status() );
	}

	/**
	 * Test creating a single payment.
	 *
	 * @since 1.1.4
	 */
	public function test_insert_payment() {
		wp_set_current_user( $this->user->ID );
		$data                     = Payment_Helper::create_payment( false );
		$data['currency']['code'] = $data['currency_code'];
		unset( $data['currency_code'] );
		$data['account']['id'] = $data['account_id'];
		unset( $data['account_id'] );
		$data['customer']['id'] = $data['contact_id'];
		unset( $data['contact_id'] );
		$data['category']['id'] = $data['category_id'];
		unset( $data['category_id'] );

		$response = $this->do_rest_post_request( '/ea/v1/payments', $data );
		$payment  = $response->get_data();

		$this->assertEquals( 201, $response->get_status() );
		unset( $payment['id'] );

		foreach ( $payment as $key => $value ) {
			if ( ! is_scalar( $value ) ) {
				continue;
			}
			$this->assertEquals( $value, $data[ $key ] );
		}
	}

	/**
	 * Test creating payment without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_insert_payment_without_permission() {
		wp_set_current_user( 0 );
		$data                     = Payment_Helper::create_payment( false );
		$data['currency']['code'] = $data['currency_code'];
		$data['account']['id']    = $data['account_id'];
		$data['customer']['id']   = $data['contact_id'];
		$data['category']['id']   = $data['category_id'];
		$response                 = $this->do_rest_post_request( '/ea/v1/payments', $data );
		$this->assertEquals( 401, $response->get_status() );
	}

	/**
	 * Test editing a single payment. Tests multiple payment types.
	 *
	 * @since 1.1.4
	 */
	public function test_update_payment() {
		wp_set_current_user( $this->user->ID );
		$payment      = Payment_Helper::create_payment();
		$updated_data = [
			'amount'            => '500',
			'description' => 'Update amount from the customer',
			'payment_method'       => 'bank_transfer',
			'date_created'    => date( 'Y-m-d 00:00:00' ),
		];
		$request      = $this->do_rest_put_request( '/ea/v1/payments/' . $payment->get_id(), $updated_data );
		$this->assertEquals( 200, $request->get_status() );
		$response = $this->do_rest_get_request( '/ea/v1/payments/' . $payment->get_id() );
		$data     = $response->get_data();
		$this->assertEquals( 200, $response->get_status() );

		foreach ( $updated_data as $key => $val ) {
			$this->assertEquals( $val, $data[ $key ] );
		}
	}

	/**
	 * Test updating a single payment without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_update_payment_without_permission() {
		wp_set_current_user( 0 );
		$payment      = Payment_Helper::create_payment();
		$updated_data = [
			'description' => 'will not change',
		];
		$request      = $this->do_rest_put_request( '/ea/v1/payments/' . $payment->get_id(), $updated_data );
		$this->assertEquals( 401, $request->get_status() );
	}

	/**
	 * Test deleting a single payment.
	 *
	 * @since 1.1.4
	 */
	public function test_delete_payment() {
		wp_set_current_user( $this->user->ID );
		$payment      = Payment_Helper::create_payment();

		$response = $this->do_rest_request( '/ea/v1/payments/' . $payment->get_id(), 'DELETE' );
		$this->assertEquals( 200, $response->get_status() );

		$response = $this->do_rest_get_request( '/ea/v1/payments' );
		$this->assertEquals( 0, $response->get_headers()['X-WP-Total'] );
	}

	/**
	 * Test deleting a single payment without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_delete_payment_without_permission() {
		wp_set_current_user( 0 );
		$payment      = Payment_Helper::create_payment();
		$response = $this->do_rest_request( '/ea/v1/payments/' . $payment->get_id(), 'DELETE' );
		$this->assertEquals( 401, $response->get_status() );
	}



}