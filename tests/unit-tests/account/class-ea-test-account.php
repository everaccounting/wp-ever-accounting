<?php

/**
 * Class EAccounting_Tests_Account.
 * @package EAccounting\Tests\Account
 */
class EAccounting_Tests_Account extends EAccounting_Unit_Test_Case {

	public function test_create_account() {
		$account = new EAccounting_Account();
		$account->set_name( 'Test Account' );
		$account->set_number( '000001' );
		$account->set_opening_balance( 5000 );
		$account->set_currency_code( 'USD' );
		$account->set_bank_name( 'ABC Bank LTD.' );
		$account->set_bank_phone( '1234567890' );
		$account->set_bank_address( '123 Steet, Redwood City, California' );
		$account->save();

		$this->assertEquals( 'Test Account', $account->get_name() );
		$this->assertNotNull( $account->get_id() );
		$this->assertEquals( '000001', $account->get_number() );
		$this->assertEquals( '5000', $account->get_opening_balance() );
		$this->assertEquals( 'USD', $account->get_currency_code() );
		$this->assertEquals( 'ABC Bank LTD.', $account->get_bank_name() );
		$this->assertEquals( '1234567890', $account->get_bank_phone() );
		$this->assertEquals( '123 Steet, Redwood City, California', $account->get_bank_address() );
		$this->assertEquals( 1, $account->get_company_id() );
		$this->assertNotNull( $account->get_date_created() );
		$this->assertNotNull( $account->get_date_created() );
	}

	public function test_update_account() {
		$account    = EAccounting_Helper_Account::create_account( 'New Account', '0101010' );
		$account_id = $account->get_id();

		$this->assertEquals( 'New Account', $account->get_name() );
		$this->assertEquals( '0101010', $account->get_number() );
		$account->set_name( 'Updated Account' );
		$account->set_number( '0202020' );
		$account->set_opening_balance( 10000 );
		$account->set_currency_code( 'BDT' );
		$account->save();

		$account = new EAccounting_Account( $account_id ); // so we can read fresh copies from the DB
		$this->assertEquals( 'Updated Account', $account->get_name() );
		$this->assertEquals( '0202020', $account->get_number() );
		$this->assertEquals( '10000.0000', $account->get_opening_balance() );
		$this->assertEquals( 'BDT', $account->get_currency_code() );
	}


	public function test_delete_account() {
		$account = EAccounting_Helper_Account::create_account();
		$this->assertNotEquals( 0, $account->get_id() );
		$account->delete();
		$this->assertEquals( 0, $account->get_id() );
	}


	public function test_exception_account_number() {
		$account = EAccounting_Helper_Account::create_account( 'Another Account 1', '1000' );

		try {
			EAccounting_Helper_Account::create_account( 'Another Account 2', '1000' );
		} catch ( Exception $e ) {
			$this->assertEquals( "Duplicate account number.", $e->getMessage() );
		}

		//name check
		try {
			$account = new EAccounting_Account();
			$account->set_name( '' );
			$account->save();
		} catch ( Exception $e ) {

			$this->assertEquals( "Account Name is required", $e->getMessage() );
		}

		//number check
		try {
			$account = new EAccounting_Account();
			$account->set_name( 'Exception account' );
			$account->set_number( '' );
			$account->save();
		} catch ( Exception $e ) {
			$this->assertEquals( "Account Number is required", $e->getMessage() );
		}

		try {
			$account = new EAccounting_Account();
			$account->set_name( 'Exception account' );
			$account->set_number( '090909' );
			$account->set_currency_code( '' );
			$account->save();
		} catch ( Exception $e ) {
			$this->assertEquals( "Currency code is required", $e->getMessage() );
		}

		try {
			$account = new EAccounting_Account();
			$account->set_name( 'Exception account' );
			$account->set_number( '090909' );
			$account->set_currency_code( 'AUD' );
			$account->save();
		} catch ( Exception $e ) {
			$this->throwAnException( $e->getMessage() );
		}

	}

}
