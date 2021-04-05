<?php
/**
 * Handle the account test case.
 *
 * @package     EverAccounting\Test
 * @class       EverAccounting_Tests_account
 * @version     1.1.0
 */

use EverAccounting\Models\Account;

defined( 'ABSPATH' ) || exit;

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
		$this->assertTrue( $account->exists() ); 
		$this->assertEquals( 'Ever Account', $account->get_name() );
		$this->assertEquals( 'USD', $account->get_currency_code() );
		$this->assertEquals( '10000.0000', $account->get_opening_balance() );
		$this->assertEquals( 'Standard Chartered Bank', $account->get_bank_name() );
		$this->assertEquals( '+12375896', $account->get_bank_phone() );
		$this->assertEquals( 'Liverpool, United Kingdom', $account->get_bank_address() );
	}

	public function test_update_account() {
	  	$account = eaccounting_insert_account(
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

		$account_update = eaccounting_insert_account(
			array(
				'id'               => $account_id,
				'name'   		   => 'New Account',
				'number' 		   => '123456789',
				'currency_code'    => 'USD',
				'opening_balance'  => 100.0000
			)
		);

		$this->assertNotEquals( 'New Account', $account->get_name() ); // Updated Account Name is not equal to Old Account Name
		$this->assertNotEquals( '123456789', $account->get_number() ); // Updated Account Number is not equal to Old Account Number
		$this->assertEquals( 'USD', $account->get_currency_code() ); // Updated Currency Code is Equal to Old Currency Code
		$this->assertNotEquals( 100.0000, $account->get_opening_balance() ); // Updated Opening Balance is not equal to Old Opening Balance
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
		$this->assertEquals( 'Account name is required', $account->get_error_message() );

		$account = eaccounting_insert_account(
			array(
				'name'   => 'New Account',
				'number' => '',
			)
		);
		$this->assertEquals( 'Account number is required', $account->get_error_message() );

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
