<?php

use EverAccounting\Tests\Framework\REST_UnitTestCase;

class Tests_REST_Accounts extends REST_UnitTestCase {

	/**
	 * Get all expected fields.
	 */
	public function get_expected_response_fields() {
		return array(
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
	}

	public function test_account_fields(){
		$expected_response_fields = $this->get_expected_response_fields();
		$account = \EverAccounting\Tests\Framework\Helpers\Account_Helper::create_account(true );
		$response = $this->do_rest_get_request('/ea/v1/accounts/'.$account->get_id());
		$this->assertEquals( 200, $response->get_status(), $response );
		$response_fields = array_keys( $response->get_data() );

		$this->assertEmpty( array_diff( $expected_response_fields, $response_fields ), 'These fields were expected but not present in API response: ' . print_r( array_diff( $expected_response_fields, $response_fields ), true ) );

		$this->assertEmpty( array_diff( $response_fields, $expected_response_fields ), 'These fields were not expected in the API response: ' . print_r( array_diff( $response_fields, $expected_response_fields ), true ) );
	}
}
