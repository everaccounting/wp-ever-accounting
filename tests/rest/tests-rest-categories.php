<?php

use EverAccounting\REST\Categories_Controller;
use EverAccounting\Tests\Framework\Helpers\Category_Helper;
use EverAccounting\Tests\Framework\REST_UnitTestCase;

class Tests_REST_Categories extends REST_UnitTestCase {
	/**
	 * Setup our test server, endpoints, and user info.
	 */
	public function setUp() {
		parent::setUp();
		$this->endpoint = new Categories_Controller();
	}

	/**
	 * Test route registration.
	 *
	 * @since 1.1.4
	 */
	public function test_register_routes() {
		$routes = $this->server->get_routes();
		$this->assertArrayHasKey( '/ea/v1/categories', $routes );
		$this->assertArrayHasKey( '/ea/v1/categories/(?P<id>[\d]+)', $routes );

	}

	/**
	 * Test getting categories.
	 *
	 * @since 1.1.4
	 */
	public function test_get_categories() {
		wp_set_current_user( $this->user->ID );

		/** Check categories*/
		$types = eaccounting_get_category_types();
		foreach ( $types as $key => $value ) {
			$this->factory->category->create_many( 5, [ 'type' => $key ] );
			$response   = $this->do_rest_get_request( '/ea/v1/categories', array( 'type' => $key, 'per_page' => 20 ) );
			$categories = $response->get_data();
			$this->assertEquals( 200, $response->get_status() );
			if ( 'income' === $key ) {
				$this->assertEquals( 10, count( $categories ) );
			} else if ( 'item' === $key ) {
				$this->assertEquals( 6, count( $categories ) );
			} else if('expense' === $key){
				$this->assertEquals( 7, count( $categories ) );
			} else {
				$this->assertEquals( 6, count( $categories ) );
			}
		}
	}

	/**
	 * Test getting single category without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_get_categories_without_permission() {
		wp_set_current_user( 0 );
		$response = $this->do_rest_get_request( '/ea/v1/categories/' );
		$this->assertEquals( 404, $response->get_status() );
	}

	/**
	 * Test getting a single category.
	 *
	 * @since 1.1.4
	 */
	public function test_get_category() {
		wp_set_current_user( $this->user->ID );
		$expected_response_fields = array_keys( $this->endpoint->get_public_item_schema()['properties'] );
		$category                 = Category_Helper::create_category();
		$response                 = $this->do_rest_get_request( '/ea/v1/categories/' . $category->get_id() );
		$this->assertEquals( 200, $response->get_status(), var_export( $response, true ) );
		$response_fields = array_keys( $response->get_data() );

		$this->assertEmpty( array_diff( $expected_response_fields, $response_fields ), 'These fields were expected but not present in API response: ' . print_r( array_diff( $expected_response_fields, $response_fields ), true ) );

		$this->assertEmpty( array_diff( $response_fields, $expected_response_fields ), 'These fields were not expected in the API response: ' . print_r( array_diff( $response_fields, $expected_response_fields ), true ) );

		// Test that all fields are returned when requested one by one.
		foreach ( $expected_response_fields as $field ) {
			$request = new WP_REST_Request( 'GET', '/ea/v1/categories/' . $category->get_id() );
			$request->set_param( '_fields', $field );
			$response = $this->server->dispatch( $request );
			$this->assertEquals( 200, $response->get_status() );
			$response_fields = array_keys( $response->get_data() );
			$this->assertContains( $field, $response_fields, "Field $field was expected but not present in order API response." );
		}
	}

	/**
	 * Test getting single category without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_get_category_without_permission() {
		wp_set_current_user( 0 );
		$category = Category_Helper::create_category();
		$response = $this->do_rest_get_request( '/ea/v1/categories/' . $category->get_id() );
		$this->assertEquals( 401, $response->get_status() );
	}

	/**
	 * Test creating a single category.
	 *
	 * @since 1.1.4
	 */
	public function test_insert_category() {
		wp_set_current_user( $this->user->ID );
		$data     = Category_Helper::create_category( false );
		$response = $this->do_rest_post_request( '/ea/v1/categories', $data );
		$category = $response->get_data();
		$this->assertEquals( 201, $response->get_status() );
		unset( $category['id'] );
		foreach ( $category as $key => $value ) {
			if ( ! is_scalar( $value ) ) {
				continue;
			}
			$this->assertEquals( $value, $data[ $key ] );
		}
	}

	/**
	 * Test getting single category without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_insert_category_without_permission() {
		wp_set_current_user( 0 );
		$data = Category_Helper::create_category( false );
		$response = $this->do_rest_post_request( '/ea/v1/categories/' , $data );
		$this->assertEquals( 404, $response->get_status() );
	}

	/**
	 * Test editing a single category. Tests multiple category types.
	 *
	 * @since 1.1.4
	 */
	public function test_update_category() {
		wp_set_current_user( $this->user->ID );
		// check update income category
		$category     = Category_Helper::create_category();
		$updated_data = [
			'name'    => 'Income Category',
			'color'   => '#e3e3e3',
			'enabled' => 0,
		];

		$request = $this->do_rest_put_request( '/ea/v1/categories/' . $category->get_id(), $updated_data );
		$this->assertEquals( 200, $request->get_status() );
		$response = $this->do_rest_get_request( '/ea/v1/categories/' . $category->get_id() );
		$data     = $response->get_data();
		$this->assertEquals( 200, $response->get_status() );

		foreach ( $updated_data as $key => $val ) {
			$this->assertEquals( $val, $data[ $key ] );
		}

		// check update expense category
		$category     = Category_Helper::create_category( true, [ 'name' => 'Expense', 'type' => 'expense', 'color' => '#1a0dab' ] );
		$updated_data = [
			'name'    => 'Expense Category',
			'color'   => '#322a94',
			'enabled' => 0,
		];

		$request = $this->do_rest_put_request( '/ea/v1/categories/' . $category->get_id(), $updated_data );
		$this->assertEquals( 200, $request->get_status() );
		$response = $this->do_rest_get_request( '/ea/v1/categories/' . $category->get_id() );
		$data     = $response->get_data();
		$this->assertEquals( 200, $response->get_status() );

		foreach ( $updated_data as $key => $val ) {
			$this->assertEquals( $val, $data[ $key ] );
		}

		// check update item category
		$category     = Category_Helper::create_category( true, [ 'name' => 'Item Category', 'type' => 'item', 'color' => '#9caf80', 'enabled' => 0 ] );
		$updated_data = [
			'name'    => 'Item Category Updated',
			'color'   => '#8480af',
			'enabled' => 1,
		];

		$request = $this->do_rest_put_request( '/ea/v1/categories/' . $category->get_id(), $updated_data );
		$this->assertEquals( 200, $request->get_status() );
		$response = $this->do_rest_get_request( '/ea/v1/categories/' . $category->get_id() );
		$data     = $response->get_data();
		$this->assertEquals( 200, $response->get_status() );

		foreach ( $updated_data as $key => $val ) {
			$this->assertEquals( $val, $data[ $key ] );
		}

		//check update other category
		$category     = Category_Helper::create_category( true, [ 'name' => 'Other', 'type' => 'other', 'color' => '#af80a2', 'enabled' => 0 ] );
		$updated_data = [
			'name'    => 'Other Category',
			'color'   => '#8480af',
			'enabled' => 1,
		];

		$request = $this->do_rest_put_request( '/ea/v1/categories/' . $category->get_id(), $updated_data );
		$this->assertEquals( 200, $request->get_status() );
		$response = $this->do_rest_get_request( '/ea/v1/categories/' . $category->get_id() );
		$data     = $response->get_data();
		$this->assertEquals( 200, $response->get_status() );

		foreach ( $updated_data as $key => $val ) {
			$this->assertEquals( $val, $data[ $key ] );
		}
	}

	/**
	 * Test updating a single account without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_update_account_without_permission() {
		wp_set_current_user( 0 );
		$category     = Category_Helper::create_category();
		$updated_data = [
			'name' => 'will not change',
		];
		$request      = $this->do_rest_put_request( '/ea/v1/categories/' . $category->get_id(), $updated_data );
		$this->assertEquals( 401, $request->get_status() );
	}

	/**
	 * Test deleting a single category.
	 *
	 * @since 1.1.4
	 */
	public function test_delete_category() {
		wp_set_current_user( $this->user->ID );
		$category = Category_Helper::create_category();

		$response = $this->do_rest_request( '/ea/v1/categories/' . $category->get_id(), 'DELETE' );
		$this->assertEquals( 200, $response->get_status() );

		$response = $this->do_rest_get_request( '/ea/v1/categories' );
		$this->assertEquals( 9, $response->get_headers()['X-WP-Total'] );
	}

	/**
	 * Test deleting a single category without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_delete_category_without_permission() {
		wp_set_current_user( 0 );
		$category = Category_Helper::create_category();
		$response = $this->do_rest_request( '/ea/v1/categories/' . $category->get_id(), 'DELETE' );
		$this->assertEquals( 401, $response->get_status() );
	}
}