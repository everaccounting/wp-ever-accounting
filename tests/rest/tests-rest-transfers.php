<?php

use EverAccounting\REST\Transfers_Controller;
use EverAccounting\Tests\Framework\Helpers\Transfer_Helper;
use EverAccounting\Tests\Framework\REST_UnitTestCase;

class Tests_REST_Transfers extends REST_UnitTestCase {
	/**
	 * Setup our test server, endpoints, and user info.
	 */
	public function setUp() {
		parent::setUp();
		$this->endpoint = new Transfers_Controller();
	}

	/**
	 * Test route registration.
	 *
	 * @since 1.1.4
	 */
	public function test_register_routes() {
		$routes = $this->server->get_routes();
		$this->assertArrayHasKey( '/ea/v1/transfers', $routes );
		$this->assertArrayHasKey( '/ea/v1/transfers/(?P<id>[\d]+)', $routes );
	}

	/**
	 * Test getting transfers.
	 *
	 * @since 1.1.4
	 */
	public function test_get_transfers() {
		wp_set_current_user( $this->user->ID );
		$this->factory->transfer->create_many( 2 );
		$response  = $this->do_rest_get_request( '/ea/v1/transfers' );
		$transfers = $response->get_data();
		$this->assertEquals( 200, $response->get_status() );
		$this->assertEquals( 2, count( $transfers ) );
	}

	/**
	 * Test getting accounts without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_get_transfers_without_permission() {
		wp_set_current_user( 0 );
		$response = $this->do_rest_get_request( '/ea/v1/transfers' );
		$this->assertEquals( 401, $response->get_status() );
	}

	/**
	 * Test getting a single transfer.
	 *
	 * @since 1.1.4
	 */
	public function test_get_revenue() {
		wp_set_current_user( $this->user->ID );
		$expected_response_fields = array();

		foreach ( $this->endpoint->get_public_item_schema()['properties'] as $key => $value ) {
			if ( 'object' == $value['type'] && 'currency' != $key ) {
				$expected_response_fields[] = $key . '_id';
			} else {
				$expected_response_fields[] = $key;
			}
		}

		$transfer = Transfer_Helper::create_transfer();
		$response = $this->do_rest_get_request( '/ea/v1/transfers/' . $transfer->get_id() );
		$this->assertEquals( 200, $response->get_status(), var_export( $response, true ) );
		$response_fields = array();
		foreach ( $response->get_data() as $key => $value ) {
			if ( is_array( $value ) ) {
				$response_fields[] = $key . '_id';
			} else {
				$response_fields[] = $key;
			}
		}

		$this->assertEmpty( array_diff( $expected_response_fields, $response_fields ), 'These fields were expected but not present in API response: ' . print_r( array_diff( $expected_response_fields, $response_fields ), true ) );

		$this->assertEmpty( array_diff( $response_fields, $expected_response_fields ), 'These fields were not expected in the API response: ' . print_r( array_diff( $response_fields, $expected_response_fields ), true ) );

		// Test that all fields are returned when requested one by one.
		foreach ( $expected_response_fields as $field ) {
			$request = new WP_REST_Request( 'GET', '/ea/v1/transfers/' . $transfer->get_id() );
			$request->set_param( '_fields', $field );
			$response = $this->server->dispatch( $request );
			$this->assertEquals( 200, $response->get_status() );
			//$response_fields = array_keys( $response->get_data() );
			$this->assertContains( $field, $response_fields, "Field $field was expected but not present in order API response." );
		}
	}

	/**
	 * Test getting single transfer without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_get_transfer_without_permission() {
		wp_set_current_user( 0 );
		$transfer = Transfer_Helper::create_transfer();
		$response = $this->do_rest_get_request( '/ea/v1/transfers/' . $transfer->get_id() );
		$this->assertEquals( 401, $response->get_status() );
	}

	/**
	 * Test creating a single transfer.
	 *
	 * @since 1.1.4
	 */
	public function test_insert_transfer() {
		wp_set_current_user( $this->user->ID );
		$data                       = Transfer_Helper::create_transfer( false );
		$data['from_account']['id'] = $data['from_account_id'];
		unset( $data['from_account_id'] );
		$data['to_account']['id'] = $data['to_account_id'];
		unset( $data['to_account_id'] );

		$response = $this->do_rest_post_request( '/ea/v1/transfers', $data );
		$transfer = $response->get_data();

		$data['income_id']  = $transfer['income_id'];
		$data['expense_id'] = $transfer['expense_id'];

		$this->assertEquals( 201, $response->get_status() );
		unset( $transfer['id'] );

		foreach ( $transfer as $key => $value ) {
			if ( ! is_scalar( $value ) ) {
				continue;
			}
			$this->assertEquals( $value, $data[ $key ] );
		}

	}

	/**
	 * Test creating transfer without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_insert_transfer_without_permission() {
		wp_set_current_user( 0 );
		$data                       = Transfer_Helper::create_transfer( false );
		$data['from_account']['id'] = $data['from_account_id'];
		$data['to_account']['id']   = $data['to_account_id'];
		$response                   = $this->do_rest_post_request( '/ea/v1/transfers', $data );
		$this->assertEquals( 401, $response->get_status() );
	}

	/**
	 * Test editing a single transfer. Tests multiple transfer types.
	 *
	 * @since 1.1.4
	 */
	public function test_update_transfer() {
		wp_set_current_user( $this->user->ID );
		$transfer     = Transfer_Helper::create_transfer();
		$updated_data = [
			'amount'         => 500,
			'description'    => 'Update amount for the transfer',
			'payment_method' => 'bank_transfer',
			'date_created'   => date( 'Y-m-d 00:00:00' ),
		];
		$request      = $this->do_rest_put_request( '/ea/v1/transfers/' . $transfer->get_id(), $updated_data );
		$this->assertEquals( 200, $request->get_status() );
		$response = $this->do_rest_get_request( '/ea/v1/transfers/' . $transfer->get_id() );
		$data     = $response->get_data();
		$this->assertEquals( 200, $response->get_status() );
		foreach ( $updated_data as $key => $val ) {
			$this->assertEquals( $val, $data[ $key ] );
		}
	}

	/**
	 * Test updating a single transfer without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_update_transfer_without_permission() {
		wp_set_current_user( 0 );
		$transfer     = Transfer_Helper::create_transfer();
		$updated_data = [
			'description' => 'will not change',
		];
		$request      = $this->do_rest_put_request( '/ea/v1/transfers/' . $transfer->get_id(), $updated_data );
		$this->assertEquals( 401, $request->get_status() );
	}

	/**
	 * Test deleting a single transfer.
	 *
	 * @since 1.1.4
	 */
	public function test_delete_transfer() {
		wp_set_current_user( $this->user->ID );
		$transfer = Transfer_Helper::create_transfer();

		$response = $this->do_rest_request( '/ea/v1/transfers/' . $transfer->get_id(), 'DELETE' );
		$this->assertEquals( 200, $response->get_status() );

		$response = $this->do_rest_get_request( '/ea/v1/transfers' );
		$this->assertEquals( 7, $response->get_headers()['X-WP-Total'] );
	}

	/**
	 * Test deleting a single transfer without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_delete_transfer_without_permission() {
		wp_set_current_user( 0 );
		$transfer = Transfer_Helper::create_transfer();
		$response = $this->do_rest_request( '/ea/v1/transfers/' . $transfer->get_id(), 'DELETE' );
		$this->assertEquals( 401, $response->get_status() );
	}

}
