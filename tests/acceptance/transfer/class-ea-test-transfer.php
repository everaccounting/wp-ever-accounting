<?php
/**
 * Handle the transfer test case.
 *
 * @package     EverAccounting\Test
 * @class       EverAccounting_Tests_transfer
 * @version     1.0.2
 */

use EverAccounting\Models\Transfer;
use EverAccounting\Models\Account;

defined( 'ABSPATH' ) || exit;

/**
 * Class EverAccounting_Tests_transfer.
 * @package EverAccounting\Tests\Category
 */
class EverAccounting_Tests_transfer extends EverAccounting_Unit_Test_Case {
	public function test_create_transfer() {
		$from_account_id = eaccounting_insert_account( array(
			'name'  		=> 'Account 1',
			'number'		=> '1000',
			'currency_code '=> 'USD'

		));
		$to_account_id   = eaccounting_insert_account( array(
			'name'  		=> 'Account 2',
			'number'		=> '200',
			'currency_code '=> 'USD'

		));
		$transfer        = eaccounting_insert_transfer( array(
			'from_account_id' => $from_account_id->get_id(),
			'to_account_id'   => $to_account_id->get_id(),
			'date'            => '2020-08-26',
			'amount'          => 50,
			'payment_method'  => 'cash',
			'reference'       => 'Test Reference',
			'description'     => "Testing transfer for unit testing"
		) );
		$this->assertNotFalse( $transfer->exists() );

		$this->assertEquals( '2020-08-26', date( 'Y-m-d', strtotime( $transfer->get_date() ) ) );
		$this->assertEquals( 50, $transfer->get_amount() );
		$this->assertEquals( 'cash', $transfer->get_payment_method() );
		$this->assertEquals( 'Test Reference', $transfer->get_reference() );
		$this->assertEquals( 'Testing transfer for unit testing', $transfer->get_description() );

	}

	public function test_update_transfer() {
		$from_account_id = EverAccounting_Helper_Account::create_account( 'update Account', '003' )->get_id();
		$to_account_id   = EverAccounting_Helper_Account::create_account( 'target Account', '004' )->get_id();
		$transfer        = eaccounting_insert_transfer( array(
			'from_account_id' => $from_account_id,
			'to_account_id'   => $to_account_id,
			'date'            => '2020-08-26',
			'amount'          => 50,
			'payment_method'  => 'cash',
		) );
		$transfer_id     = $transfer->get_id();
		$this->assertNotFalse( $transfer->exists() );

		$this->assertEquals( '2020-08-26', date( 'Y-m-d', strtotime( $transfer->get_date() ) ) );
		$this->assertEquals( 50, $transfer->get_amount() );
		$this->assertEquals( 'cash', $transfer->get_payment_method() );

		$error = eaccounting_insert_transfer( array(
			'id'              => $transfer_id,
			'from_account_id' => $to_account_id,
			'to_account_id'   => $from_account_id,
			'date'            => '2020-08-24',
			'amount'          => 60,
			'payment_method'  => 'cash',
		) );
		$this->assertNotWPError( $error );

		$transfer = eaccounting_get_transfer( $transfer_id ); // so we can read fresh copies from the DB

		$this->assertEquals( '2020-08-24', date( 'Y-m-d', strtotime( $transfer->get_date() ) ) );
		$this->assertEquals( 60, $transfer->get_amount() );
		$this->assertEquals( 'cash', $transfer->get_payment_method() );
	}

	public function test_delete_transfer() {
		$transfer = EverAccounting_Helper_Transfer::create_transfer();
		$this->assertNotEquals( 0, $transfer->get_id() );
		$this->assertNotFalse( eaccounting_delete_transfer( $transfer->get_id() ) );
	}

	public function test_exception_transfer() {
		$to_account_id = EverAccounting_Helper_Account::create_account( 'Receiver Account ID', '005' )->get_id();
		$transfer = eaccounting_insert_transfer( array(
			'from_account_id' => '',
			'to_account_id'   => $to_account_id
		) );
		$this->assertEquals( 'Transfer from and to account can not be same.', $transfer->get_error_message() );

		$from_account_id = EverAccounting_Helper_Account::create_account( 'Sender Account ID', '006' )->get_id();
		$transfer        = eaccounting_insert_transfer( array(
			'from_account_id' => $from_account_id,
			'to_account_id'   => '',
		) );
		$this->assertEquals( 'Transfer from and to account can not be same.', $transfer->get_error_message() );

		$to_account_id = EverAccounting_Helper_Account::create_account( 'Receiver Account ID', '007' )->get_id();

		$transfer = eaccounting_insert_transfer( array(
			'from_account_id' => $from_account_id,
			'to_account_id'   => $from_account_id,
		) );
		$this->assertEquals( "Source and Destination account can't be same.", $transfer->get_error_message() );

		$transfer = eaccounting_insert_transfer( array(
			'from_account_id' => $from_account_id,
			'to_account_id'   => $to_account_id,
			'amount'          => ''
		) );
		$this->assertEquals( 'Transfer Date is required', $transfer->get_error_message() );

		$transfer = eaccounting_insert_transfer( array(
			'from_account_id' => $from_account_id,
			'to_account_id'   => $to_account_id,
			'amount'          => 50,
			'date'            => '',

		) );
		$this->assertEquals( 'Transfer Date is required', $transfer->get_error_message() );

		$transfer = eaccounting_insert_transfer( array(
			'from_account_id' => $from_account_id,
			'to_account_id'   => $to_account_id,
			'amount'          => 50,
			'date'            => '2020-08-31',
			'payment_method'  => '',

		) );
		$this->assertEquals( 'Payment method is required', $transfer->get_error_message() );

		$transfer = eaccounting_insert_transfer( array(
			'from_account_id' => $from_account_id,
			'to_account_id'   => $to_account_id,
			'amount'          => 50,
			'date'            => '2020-08-31',
			'payment_method'  => 'cash',

		) );
		$this->assertNotFalse( $transfer->exists() );

	}
}
