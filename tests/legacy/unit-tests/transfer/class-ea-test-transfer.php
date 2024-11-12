<?php

use EAccounting\Transfer;

/**
 * Class EverAccounting_Tests_transfer.
 * @package EAccounting\Tests\Category
 */
class EverAccounting_Tests_transfer extends EverAccounting_Unit_Test_Case {
	public function test_create_transfer() {
		$from_account_id = EverAccounting_Helper_Account::create_account( 'From Account', '001' )->get_id();
		$to_account_id   = EverAccounting_Helper_Account::create_account( 'To Account', '002' )->get_id();
		$transfer        = eaccounting_insert_transfer( array(
			'from_account_id' => $from_account_id,
			'to_account_id'   => $to_account_id,
			'date'            => '2020-08-26',
			'amount'          => 50,
			'method'  => 'cash',
			'reference'       => 'Test Reference',
			'description'     => "Testing transfer for unit testing"
		) );
		$this->assertNotFalse( $transfer->exists() );

		$this->assertEquals( '2020-08-26', date( 'Y-m-d', strtotime( $transfer->get_date() ) ) );
		$this->assertEquals( 50, $transfer->get_amount() );
		$this->assertEquals( 'cash', $transfer->get_method() );
		$this->assertEquals( 'Test Reference', $transfer->get_reference() );
		$this->assertEquals( 'Testing transfer for unit testing', $transfer->get_description() );

	}

	public function test_update_transfer() {
		$from_account_id = EverAccounting_Helper_Account::create_account( 'From Account', '001' )->get_id();
		$to_account_id   = EverAccounting_Helper_Account::create_account( 'To Account', '002' )->get_id();
		$transfer        = eaccounting_insert_transfer( array(
			'from_account_id' => $from_account_id,
			'to_account_id'   => $to_account_id,
			'date'            => '2020-08-26',
			'amount'          => 50,
			'method'  => 'cash',
		) );
		$transfer_id     = $transfer->get_id();
		$this->assertNotFalse( $transfer->exists() );

		$this->assertEquals( '2020-08-26', date( 'Y-m-d', strtotime( $transfer->get_date() ) ) );
		$this->assertEquals( 50, $transfer->get_amount() );
		$this->assertEquals( 'cash', $transfer->get_method() );

		$error = eaccounting_insert_transfer( array(
			'id'              => $transfer_id,
			'from_account_id' => $to_account_id,
			'to_account_id'   => $from_account_id,
			'date'            => '2020-08-24',
			'amount'          => 60,
			'method'  => 'cash',
		) );
		$this->assertNotWPError( $error );

		$transfer = eaccounting_get_transfer( $transfer_id ); // so we can read fresh copies from the DB

		$this->assertEquals( '2020-08-24', date( 'Y-m-d', strtotime( $transfer->get_date() ) ) );
		$this->assertEquals( 60, $transfer->get_amount() );
		$this->assertEquals( 'cash', $transfer->get_method() );
	}

	public function test_delete_transfer() {
		$transfer = EverAccounting_Helper_Transfer::create_transfer();
		$this->assertNotEquals( 0, $transfer->get_id() );
		$this->assertNotFalse( eaccounting_delete_transfer( $transfer->get_id() ) );
	}

	public function test_exception_transfer() {
		$transfer = eaccounting_insert_transfer( array(
			'from_account_id' => ''
		) );
		$this->assertEquals( 'From account is required', $transfer->get_error_message() );

		$from_account_id = EverAccounting_Helper_Account::create_account( 'Sender Account ID', '001' )->get_id();
		$transfer        = eaccounting_insert_transfer( array(
			'from_account_id' => $from_account_id,
			'to_account_id'   => '',
		) );
		$this->assertEquals( 'To account is required', $transfer->get_error_message() );

		$to_account_id = EverAccounting_Helper_Account::create_account( 'Receiver Account ID', '002' )->get_id();

		$transfer = eaccounting_insert_transfer( array(
			'from_account_id' => $from_account_id,
			'to_account_id'   => $from_account_id,
		) );
		$this->assertEquals( 'Source & Target account can not be same.', $transfer->get_error_message() );

		$transfer = eaccounting_insert_transfer( array(
			'from_account_id' => $from_account_id,
			'to_account_id'   => $to_account_id,
			'amount'          => ''
		) );
		$this->assertEquals( 'Transfer amount is required', $transfer->get_error_message() );

		$transfer = eaccounting_insert_transfer( array(
			'from_account_id' => $from_account_id,
			'to_account_id'   => $to_account_id,
			'amount'          => 50,
			'date'            => '',

		) );
		$this->assertEquals( 'Transfer date is required', $transfer->get_error_message() );

		$transfer = eaccounting_insert_transfer( array(
			'from_account_id' => $from_account_id,
			'to_account_id'   => $to_account_id,
			'amount'          => 50,
			'date'            => '2020-08-31',
			'method'  => '',

		) );
		$this->assertEquals( 'Payment method is required', $transfer->get_error_message() );

		$transfer = eaccounting_insert_transfer( array(
			'from_account_id' => $from_account_id,
			'to_account_id'   => $to_account_id,
			'amount'          => 50,
			'date'            => '2020-08-31',
			'method'  => 'cash',

		) );
		$this->assertNotFalse( $transfer->exists() );

	}
}
