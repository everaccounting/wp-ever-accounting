<?php

use EverAccounting\REST\Accounts_Controller;
use EverAccounting\Tests\Framework\Helpers\Account_Helper;
use EverAccounting\Tests\Framework\REST_UnitTestCase;

class Tests_REST_Accounts extends REST_UnitTestCase {
	/**
	 * Setup our test server, endpoints, and user info.
	 */
	public function setUp() {
		parent::setUp();
		$this->endpoint = new Accounts_Controller();
	}

	/**
	 * Test route registration.
	 *
	 * @since 1.1.4
	 */
	public function test_register_routes() {
		$routes = $this->server->get_routes();
		$this->assertArrayHasKey( '/ea/v1/accounts', $routes );
		$this->assertArrayHasKey( '/ea/v1/accounts/(?P<id>[\d]+)', $routes );
	}

	/**
	 * Test getting accounts.
	 *
	 * @since 1.1.4
	 */
	public function test_get_accounts() {
		wp_set_current_user( $this->user->ID );
		$this->factory->account->create_many( 10 );
		$response = $this->do_rest_get_request( '/ea/v1/accounts' );
		$accounts = $response->get_data();
		// One created by default.
		$this->assertEquals( 11, count( $accounts ) );
		$this->assertEquals( 200, $response->get_status() );
	}

	/**
	 * Test getting accounts without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_get_accounts_without_permission() {
		wp_set_current_user( 0 );
		$response = $this->do_rest_get_request( '/ea/v1/accounts' );
		$this->assertEquals( 401, $response->get_status() );
	}


	/**
	 * Test getting a single account.
	 *
	 * @since 1.1.4
	 */
	public function test_get_account() {
		wp_set_current_user( $this->user->ID );
		$expected_response_fields = array(
			'id',
			'name',
			'number',
			'opening_balance',
			'currency',
			'bank_name',
			'bank_phone',
			'bank_address',
			'enabled',
			'date_created',
		);
		$account                  = Account_Helper::create_account();
		$response                 = $this->do_rest_get_request( '/ea/v1/accounts/' . $account->get_id() );
		$this->assertEquals( 200, $response->get_status(), var_export( $response, true ) );
		$response_fields = array_keys( $response->get_data() );

		$this->assertEmpty( array_diff( $expected_response_fields, $response_fields ), 'These fields were expected but not present in API response: ' . print_r( array_diff( $expected_response_fields, $response_fields ), true ) );

		$this->assertEmpty( array_diff( $response_fields, $expected_response_fields ), 'These fields were not expected in the API response: ' . print_r( array_diff( $response_fields, $expected_response_fields ), true ) );

		// Test that all fields are returned when requested one by one.
		foreach ( $expected_response_fields as $field ) {
			$request = new WP_REST_Request( 'GET', '/ea/v1/accounts/' . $account->get_id() );
			$request->set_param( '_fields', $field );
			$response = $this->server->dispatch( $request );
			$this->assertEquals( 200, $response->get_status() );
			$response_fields = array_keys( $response->get_data() );
			$this->assertContains( $field, $response_fields, "Field $field was expected but not present in order API response." );
		}
	}

	/**
	 * Test getting single account without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_get_account_without_permission() {
		wp_set_current_user( 0 );
		$account  = Account_Helper::create_account();
		$response = $this->do_rest_get_request( '/ea/v1/accounts/' . $account->get_id() );
		$this->assertEquals( 401, $response->get_status() );
	}

	/**
	 * Test creating a single account.
	 *
	 * @since 1.1.4
	 */
	public function test_insert_account() {
		wp_set_current_user( $this->user->ID );
		$data                     = Account_Helper::create_account( false );
		$data['currency']['code'] = $data['currency_code'];
		unset( $data['currency_code'] );
		$response = $this->do_rest_post_request( '/ea/v1/accounts', $data );
		$account  = $response->get_data();
		$this->assertEquals( 201, $response->get_status() );
		unset( $account['id'] );
		foreach ( $account as $key => $value ) {
			if ( ! is_scalar( $value ) ) {
				continue;
			}
			$this->assertEquals( $value, $data[ $key ] );
		}
	}

	/**
	 * Test creating account without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_insert_account_without_permission() {
		wp_set_current_user( 0 );
		$data                     = Account_Helper::create_account( false );
		$data['currency']['code'] = $data['currency_code'];
		$response                 = $this->do_rest_post_request( '/ea/v1/accounts', $data );
		$this->assertEquals( 401, $response->get_status() );
	}

	/**
	 * Test editing a single account. Tests multiple account types.
	 *
	 * @since 1.1.4
	 */
	public function test_update_account() {
		wp_set_current_user( $this->user->ID );
		$account      = Account_Helper::create_account();
		$updated_data = [
			'name'            => 'new account',
//			'number'          => '10000',
			'opening_balance' => '100000',
			'bank_name'       => 'New bank',
			'bank_phone'      => '5487487',
			'bank_address'    => 'Somewhere in',
			'enabled'         => 0,
			'date_created'    => date( 'Y-m-d 00:00:00' ),
		];
		$request      = $this->do_rest_put_request( '/ea/v1/accounts/' . $account->get_id(), $updated_data );
		$this->assertEquals( 200, $request->get_status() );
		$response = $this->do_rest_get_request( '/ea/v1/accounts/' . $account->get_id() );
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
		$account      = Account_Helper::create_account();
		$updated_data = [
			'name'            => 'will not change account',
		];
		$request      = $this->do_rest_put_request( '/ea/v1/accounts/' . $account->get_id(), $updated_data );
		$this->assertEquals( 401, $request->get_status() );
	}

	/**
	 * Test deleting a single account.
	 *
	 * @since 1.1.4
	 */
	public function test_delete_account() {
		wp_set_current_user( $this->user->ID );
		$account = Account_Helper::create_account();

		$response = $this->do_rest_request( '/ea/v1/accounts/' . $account->get_id(), 'DELETE' );
		$this->assertEquals( 200, $response->get_status() );

		$response = $this->do_rest_get_request( '/ea/v1/accounts' );
		$this->assertEquals( 1, $response->get_headers()['X-WP-Total'] );
	}

	/**
	 * Test deleting a single account without permission.
	 *
	 * @since 1.1.4
	 */
	public function test_delete_account_without_permission() {
		wp_set_current_user( 0 );
		$account = Account_Helper::create_account();

		$response = $this->do_rest_request( '/ea/v1/accounts/' . $account->get_id(), 'DELETE' );
		$this->assertEquals( 401, $response->get_status() );
	}
}
