<?php

use EverAccounting\REST\Vendors_Controller;
use EverAccounting\Tests\Framework\Helpers\Vendor_Helper;
use EverAccounting\Tests\Framework\REST_UnitTestCase;

class Tests_REST_Vendors extends REST_UnitTestCase {
	/**
	 * Setup our test server, endpoints, and user info.
	 */
	public function setUp() {
		parent::setUp();
		$this->endpoint = new Vendors_Controller();
	}

	/**
	 * Test route registration.
	 *
	 * @since 1.1.4
	 */
	public function test_register_routes() {
		$routes = $this->server->get_routes();
		$this->assertArrayHasKey( '/ea/v1/vendors', $routes );
		$this->assertArrayHasKey( '/ea/v1/vendors/(?P<id>[\d]+)', $routes );

	}

	/**
	 * Test getting vendors.
	 *
	 * @since 1.1.4
	 */
	public function test_get_vendors() {
		wp_set_current_user( $this->user->ID );

		$this->factory->vendor->create_many( 5 );
		$response = $this->do_rest_get_request( '/ea/v1/vendors' );
		$vendors  = $response->get_data();
		$this->assertEquals( 200, $response->get_status() );
		$this->assertEquals( 10, count( $vendors ) );
	}

	/**
	 * Test getting customers without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_get_vendors_without_permission() {
		wp_set_current_user( 0 );
		$response = $this->do_rest_get_request( '/ea/v1/vendors/' );
		$this->assertEquals( 404, $response->get_status() );
	}

	/**
	 * Test getting a single vendor.
	 *
	 * @since 1.1.4
	 */
	public function test_get_vendor() {
		wp_set_current_user( $this->user->ID );
		$expected_response_fields = array_keys( $this->endpoint->get_public_item_schema()['properties'] );
		$vendor                   = Vendor_Helper::create_vendor();
		$response                 = $this->do_rest_get_request( '/ea/v1/vendors/' . $vendor->get_id() );
		$this->assertEquals( 200, $response->get_status(), var_export( $response, true ) );
		$response_fields = array_keys( $response->get_data() );

		$this->assertEmpty( array_diff( $expected_response_fields, $response_fields ), 'These fields were expected but not present in API response: ' . print_r( array_diff( $expected_response_fields, $response_fields ), true ) );

		$this->assertEmpty( array_diff( $response_fields, $expected_response_fields ), 'These fields were not expected in the API response: ' . print_r( array_diff( $response_fields, $expected_response_fields ), true ) );

		// Test that all fields are returned when requested one by one.
		foreach ( $expected_response_fields as $field ) {
			$request = new WP_REST_Request( 'GET', '/ea/v1/vendors/' . $vendor->get_id() );
			$request->set_param( '_fields', $field );
			$response = $this->server->dispatch( $request );
			$this->assertEquals( 200, $response->get_status() );
			$response_fields = array_keys( $response->get_data() );
			$this->assertContains( $field, $response_fields, "Field $field was expected but not present in order API response." );
		}

	}

	/**
	 * Test getting single vendor without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_get_vendor_without_permission() {
		wp_set_current_user( 0 );
		$vendor   = Vendor_Helper::create_vendor();
		$response = $this->do_rest_get_request( '/ea/v1/vendors/' . $vendor->get_id() );
		$this->assertEquals( 401, $response->get_status() );
	}

	/**
	 * Test creating a single vendor.
	 *
	 * @since 1.1.4
	 */
	public function test_insert_vendor() {
		wp_set_current_user( $this->user->ID );
		$data                     = Vendor_Helper::create_vendor( false );
		$data['currency']['code'] = $data['currency_code'];
		$response                 = $this->do_rest_post_request( '/ea/v1/vendors', $data );
		$vendor                   = $response->get_data();
		$this->assertEquals( 201, $response->get_status() );
		unset( $vendor['id'] );
		foreach ( $vendor as $key => $value ) {
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
	public function test_insert_vendor_without_permission() {
		wp_set_current_user( 0 );
		$data                     = Vendor_Helper::create_vendor( false );
		$data['currency']['code'] = $data['currency_code'];
		$response                 = $this->do_rest_post_request( '/ea/v1/vendors', $data );
		$this->assertEquals( 401, $response->get_status() );
	}

	/**
	 * Test editing a single vendor. Tests multiple vendor types.
	 *
	 * @since 1.1.4
	 */
	public function test_update_vendor() {
		wp_set_current_user( $this->user->ID );
		$vendor       = Vendor_Helper::create_vendor();
		$updated_data = [
			'name'       => 'Matt Mullenweg SR',
			'email'      => 'mattsr@email.com',
			'country'    => 'AE',
			'vat_number' => 'vt-124578',
			'enabled'    => 0,
		];
		$request      = $this->do_rest_put_request( '/ea/v1/vendors/' . $vendor->get_id(), $updated_data );
		$this->assertEquals( 200, $request->get_status() );
		$response = $this->do_rest_get_request( '/ea/v1/vendors/' . $vendor->get_id() );
		$data     = $response->get_data();
		$this->assertEquals( 200, $response->get_status() );

		foreach ( $updated_data as $key => $val ) {
			$this->assertEquals( $val, $data[ $key ] );
		}
	}

	/**
	 * Test updating a single vendor without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_update_vendor_without_permission() {
		wp_set_current_user( 0 );
		$vendor       = Vendor_Helper::create_vendor();
		$updated_data = [
			'name' => 'Can not change',
		];
		$request      = $this->do_rest_put_request( '/ea/v1/vendors/' . $vendor->get_id(), $updated_data );
		$this->assertEquals( 401, $request->get_status() );
	}

	/**
	 * Test deleting a single vendor.
	 *
	 * @since 1.1.4
	 */
	public function test_delete_vendor() {
		wp_set_current_user( $this->user->ID );
		$vendor = Vendor_Helper::create_vendor();

		$response = $this->do_rest_request( '/ea/v1/vendors/' . $vendor->get_id(), 'DELETE' );
		$this->assertEquals( 200, $response->get_status() );

		$response = $this->do_rest_get_request( '/ea/v1/vendors' );
		$this->assertEquals( 18, $response->get_headers()['X-WP-Total'] );
	}

	/**
	 * Test deleting a single vendor without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_delete_vendor_without_permission() {
		wp_set_current_user( 0 );
		$vendor   = Vendor_Helper::create_vendor();
		$response = $this->do_rest_request( '/ea/v1/vendors/' . $vendor->get_id(), 'DELETE' );
		$this->assertEquals( 401, $response->get_status() );
	}
}