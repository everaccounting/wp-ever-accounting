<?php

use EverAccounting\REST\Items_Controller;
use EverAccounting\Tests\Framework\Helpers\Item_Helper;
use EverAccounting\Tests\Framework\REST_UnitTestCase;

class Tests_REST_Items extends REST_UnitTestCase {
	/**
	 * Setup our test server, endpoints, and user info.
	 */
	public function setUp() {
		parent::setUp();
		$this->endpoint = new Items_Controller();
	}

	/**
	 * Test route registration.
	 *
	 * @since 1.1.4
	 */
	public function test_register_routes() {
		$routes = $this->server->get_routes();
		$this->assertArrayHasKey( '/ea/v1/items', $routes );
		$this->assertArrayHasKey( '/ea/v1/items/(?P<id>[\d]+)', $routes );
	}

	/**
	 * Test getting items.
	 *
	 * @since 1.1.4
	 */
	public function test_get_items() {
		wp_set_current_user( $this->user->ID );
		$this->factory->item->create_many( 10 );
		$response = $this->do_rest_get_request( '/ea/v1/items', [ 'per_page' => 20 ] );
		$items    = $response->get_data();
		$this->assertEquals( 200, $response->get_status() );
		$this->assertEquals( 10, count( $items ) );

	}

	/**
	 * Test getting items without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_get_items_without_permission() {
		wp_set_current_user( 0 );
		$response = $this->do_rest_get_request( '/ea/v1/items' );
		$this->assertEquals( 401, $response->get_status() );
	}

	/**
	 * Test getting a single item.
	 *
	 * @since 1.1.4
	 */
	public function test_get_item() {
		wp_set_current_user( $this->user->ID );

		$expected_response_fields = array();
		foreach ( $this->endpoint->get_public_item_schema()['properties'] as $key => $value ) {
			if ( 'object' != $value['type'] ) {
				$expected_response_fields[] = $key;
			}
		}
		$item     = Item_Helper::create_item();
		$response = $this->do_rest_get_request( '/ea/v1/items/' . $item->get_id() );
		$this->assertEquals( 200, $response->get_status(), var_export( $response, true ) );

		$response_fields = array_keys( $response->get_data() );

		$this->assertEmpty( array_diff( $expected_response_fields, $response_fields ), 'These fields were expected but not present in API response: ' . print_r( array_diff( $expected_response_fields, $response_fields ), true ) );

		$this->assertEmpty( array_diff( $response_fields, $expected_response_fields ), 'These fields were not expected in the API response: ' . print_r( array_diff( $response_fields, $expected_response_fields ), true ) );

		// Test that all fields are returned when requested one by one.
		foreach ( $expected_response_fields as $field ) {
			$request = new WP_REST_Request( 'GET', '/ea/v1/items/' . $item->get_id() );
			$request->set_param( '_fields', $field );
			$response = $this->server->dispatch( $request );
			$this->assertEquals( 200, $response->get_status() );
			$response_fields = array_keys( $response->get_data() );
			$this->assertContains( $field, $response_fields, "Field $field was expected but not present in order API response." );
		}
	}

	/**
	 * Test getting single item without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_get_item_without_permission() {
		wp_set_current_user( 0 );
		$item     = Item_Helper::create_item();
		$response = $this->do_rest_get_request( '/ea/v1/items/' . $item->get_id() );
		$this->assertEquals( 401, $response->get_status() );
	}

	/**
	 * Test creating a single item.
	 *
	 * @since 1.1.4
	 */
	public function test_insert_item() {
		wp_set_current_user( $this->user->ID );
		$data                   = Item_Helper::create_item( false );
		$data['category']['id'] = $data['category_id'];
		unset( $data['category_id'] );
		unset( $data['thumbnail_id'] );
		$response = $this->do_rest_post_request( '/ea/v1/items', $data );
		$item     = $response->get_data();
		$this->assertEquals( 201, $response->get_status() );
		unset( $item['id'] );
		foreach ( $item as $key => $value ) {
			if ( ! is_scalar( $value ) ) {
				continue;
			}
			$this->assertEquals( $value, $data[ $key ] );
		}
	}

	/**
	 * Test creating item without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_insert_item_without_permission() {
		wp_set_current_user( 0 );
		$data                   = Item_Helper::create_item( false );
		$data['category']['id'] = $data['category_id'];
		$response               = $this->do_rest_post_request( '/ea/v1/items', $data );
		$this->assertEquals( 401, $response->get_status() );
	}

	/**
	 * Test editing a single item. Tests multiple item types.
	 *
	 * @since 1.1.4
	 */
	public function test_update_item() {
		wp_set_current_user( $this->user->ID );
		$item         = Item_Helper::create_item();
		$updated_data = [
			'name'           => 'Mac Book Air',
			'sku'            => 'mac-air',
			'description'    => 'Apple Mac Book Air with m1 chip',
			'sale_price'     => '999',
			'purchase_price' => '899',
		];
		$request      = $this->do_rest_put_request( '/ea/v1/items/' . $item->get_id(), $updated_data );
		$this->assertEquals( 200, $request->get_status() );
		$response = $this->do_rest_get_request( '/ea/v1/items/' . $item->get_id() );
		$data     = $response->get_data();
		$this->assertEquals( 200, $response->get_status() );

		foreach ( $updated_data as $key => $val ) {
			$this->assertEquals( $val, $data[ $key ] );
		}
	}

	/**
	 * Test updating a single item without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_update_item_without_permission() {
		wp_set_current_user( 0 );
		$item         = Item_Helper::create_item();
		$updated_data = [
			'name' => 'will not change',
		];
		$request      = $this->do_rest_put_request( '/ea/v1/items/' . $item->get_id(), $updated_data );
		$this->assertEquals( 401, $request->get_status() );
	}

	/**
	 * Test deleting a single item.
	 *
	 * @since 1.1.4
	 */
	public function test_delete_item() {
		wp_set_current_user( $this->user->ID );
		$item = Item_Helper::create_item();

		$response = $this->do_rest_request( '/ea/v1/items/' . $item->get_id(), 'DELETE' );
		$this->assertEquals( 200, $response->get_status() );

		$response = $this->do_rest_get_request( '/ea/v1/items' );
		$this->assertEquals( 0, $response->get_headers()['X-WP-Total'] );
	}

	/**
	 * Test deleting a single item without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_delete_item_without_permission() {
		wp_set_current_user( 0 );
		$item = Item_Helper::create_item();
		$response = $this->do_rest_request( '/ea/v1/items/' . $item->get_id(), 'DELETE' );
		$this->assertEquals( 401, $response->get_status() );
	}
}