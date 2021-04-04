<?php
namespace EverAccounting\Tests\REST\V1;

use EverAccounting\REST\Accounts_Controller;
use EverAccounting\Tests\Framework\REST_Unit_Test_Case;

class Accounts_Controller_Test extends REST_Unit_Test_Case {
	/**
	 * Setup our test server, endpoints, and user info.
	 */
	public function setUp() {
		parent::setUp();
		$this->endpoint = new Accounts_Controller();
		$this->user     = $this->factory->user->create(
			array(
				'role' => 'administrator',
			)
		);
	}

	/**
	 * Get all expected fields.
	 */
	public function get_expected_response_fields() {
		return array(
			'id',
		);
	}

	public function test_account_api_get_all_fields(){
		wp_set_current_user( $this->user );

		$response = $this->do_rest_request( '/ea/v1/accounts' );
		$response = $this->server->dispatch( new \WP_REST_Request( 'GET', '/ea/v1/accounts' ));
		$this->assertEquals( 200, $response->get_status() );

		$response_fields = array_keys( $response->get_data() );
		var_dump($response_fields);
	}

}
