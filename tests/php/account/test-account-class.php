<?php
/**
 * Ever_Accounting Account Class Handler
 *
 * @since    1.1.3
 * @package  Ever_Accounting\Tests
 * @class    Tests_Account_Class
 */

namespace Ever_Accounting\Tests;

use Ever_Accounting\Accounts;
use Ever_Accounting\Tests\Factories\Account_Factory;

/**
 * Class Tests_Account_Class
 *
 * @since 1.1.3
 * @package  Ever_Accounting\Tests
 */
class Tests_Account_Class extends \WP_UnitTestCase {
	public function test_create_account() {
		$account = Accounts::insert(
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
		$account    = Accounts::insert(
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

		$error = Accounts::insert(
			array(
				'id'              => $account_id,
				'name'            => 'New Account',
				'number'          => '123456789',
				'currency_code'   => 'USD',
				'opening_balance' => '100.0000',
			)
		);
		$this->assertNotWPError( $error );

		$account = Accounts::get( $account_id ); //get account info from db
		$this->assertEquals( 'New Account', $account->get_name() );
		$this->assertEquals( '123456789', $account->get_number() );
		$this->assertEquals( 'USD', $account->get_currency_code() );
		$this->assertEquals( 100.0000, $account->get_opening_balance() );
	}

	public function test_delete_account() {
		$account = Account_Factory::create();
		$this->assertNotEquals( 0, $account->get_id() );
		$this->assertNotFalse( Accounts::delete( $account->get_id() ) );
	}

	public function test_exception_account() {
		$account = Accounts::insert(
			array(
				'name' => '',
			)
		);
		$this->assertEquals( 'Account name is required.', $account->get_error_message() );

		$account = Accounts::insert(
			array(
				'name'   => 'New Account',
				'number' => '',
			)
		);
		$this->assertEquals( 'Account number is required.', $account->get_error_message() );

		$account = Accounts::insert(
			array(
				'name'          => 'New Account',
				'number'        => '12345678',
				'currency_code' => '',
			)
		);
		$this->assertEquals( 'Account currency_code is required.', $account->get_error_message() );

		$account = Accounts::insert(
			array(
				'name'            => 'Test Bank Account',
				'number'          => '123456789',
				'currency_code'   => 'USD',
				'opening_balance' => 100.0000,
			)
		);

		// check this when duplicate account will be added on the database.
		/*
		$account = Accounts::insert(
			array(
				'name'            => 'Ever Bank Account',
				'number'          => '123456789',
				'currency_code'   => 'USD',
				'opening_balance' => 100.0000,
			)
		);
		$this->assertEquals( 'Could not insert item into the database.', $account->get_error_message() );
		*/
	}
}
