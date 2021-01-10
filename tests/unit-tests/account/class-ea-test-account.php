<?php

use EverAccounting\Account;

/**
 * Class EverAccounting_Tests_account.
 * @package EverAccounting\Tests\Customer
 */
class EverAccounting_Tests_account extends EverAccounting_Unit_Test_Case {
	public function test_create_account() {
		$account = eaccounting_insert_account(
			array(
				'name'            => 'Ever Account',
				'number'          => '12345678',
				'currency_code'   => 'USD',
				'opening_balance' => '10000.000',
				'bank_name'       => 'Standard Chartered Bank',
				'bank_phone'      => '+12375896',
				'bank_address'    => 'Liverpool, United Kingdom',
			)
		);

		$this->assertNotFalse( $account->exists() );
		$this->assertEquals( 'Ever Account', $account->get_name() );
		$this->assertNotNull( $account->get_id() );
		$this->assertEquals( '12345678', $account->get_number() );
		$this->assertEquals( 'USD', $account->get_currency_code() );
		$this->assertEquals( '10000.0000', $account->get_opening_balance() );
		$this->assertEquals( 'Standard Chartered Bank', $account->get_bank_name() );
		$this->assertEquals( '+12375896', $account->get_bank_phone() );
		$this->assertEquals( 'Liverpool, United Kingdom', $account->get_bank_address() );
	}

	public function test_update_account() {
		$account    = eaccounting_insert_account(
			array(
				'name'          => 'Test Account',
				'number'        => '12345678',
				'currency_code' => 'USD',
			)
		);
		$account_id = $account->get_id();
		$this->assertNotFalse( $account->exists() );

		$this->assertEquals( 'Test Account', $account->get_name() );
		$this->assertNotNull( $account->get_id() );
		$this->assertEquals( '12345678', $account->get_number() );
		$this->assertEquals( 'USD', $account->get_currency_code() );
		$this->assertEquals( 0.0000, $account->get_opening_balance() );

		$error = eaccounting_insert_account(
			array(
				'id'              => $account_id,
				'name'            => 'New Account',
				'number'          => '123456789',
				'currency_code'   => 'USD',
				'opening_balance' => '100.0000',
			)
		);
		$this->assertNotWPError( $error );

		$account = eaccounting_get_account( $account_id ); //get account info from db
		$this->assertEquals( 'New Account', $account->get_name() );
		$this->assertEquals( '123456789', $account->get_number() );
		$this->assertEquals( 'USD', $account->get_currency_code() );
		$this->assertEquals( 100.0000, $account->get_opening_balance() );
	}

	public function test_delete_account() {
		$account = EverAccounting_Helper_Account::create_account();
		$this->assertNotEquals( 0, $account->get_id() );
		$this->assertNotFalse( eaccounting_delete_account( $account->get_id() ) );
	}

	public function test_exception_account() {
		$account = eaccounting_insert_account(
			array(
				'name' => '',
			)
		);
		$this->assertEquals( 'Account Name is required', $account->get_error_message() );

		$account = eaccounting_insert_account(
			array(
				'name'   => 'New Account',
				'number' => '',
			)
		);
		$this->assertEquals( 'Account Number is required', $account->get_error_message() );

		$account = eaccounting_insert_account(
			array(
				'name'          => 'New Account',
				'number'        => '12345678',
				'currency_code' => '',
			)
		);
		$this->assertEquals( 'Currency code is required', $account->get_error_message() );

		$account = eaccounting_insert_account(
			array(
				'name'            => 'New Account',
				'number'          => '12345678',
				'currency_code'   => 'CAD',
				'opening_balance' => 100.0000,
			)
		);
		$this->assertEquals( 'Currency with provided code does not not exist.', $account->get_error_message() );

		$account = eaccounting_insert_account(
			array(
				'name'            => 'Test Bank Account',
				'number'          => '123456789',
				'currency_code'   => 'USD',
				'opening_balance' => 100.0000,
			)
		);

		$account = eaccounting_insert_account(
			array(
				'name'            => 'Ever Bank Account',
				'number'          => '123456789',
				'currency_code'   => 'USD',
				'opening_balance' => 100.0000,
			)
		);
		$this->assertEquals( 'Duplicate account number.', $account->get_error_message() );
	}
}
