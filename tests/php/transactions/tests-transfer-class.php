<?php
/**
 * Ever_Accounting Transfer Class Handler
 *
 * @since    1.1.3
 * @package  Ever_Accounting\Tests
 * @class    Tests_Transfer_Class
 */

namespace Ever_Accounting\Tests;

use Ever_Accounting\Tests\Factories\Account_Factory;
use Ever_Accounting\Tests\Factories\Category_Factory;
use Ever_Accounting\Tests\Factories\Contact_Factory;
use Ever_Accounting\Tests\Factories\Revenue_Factory;
use Ever_Accounting\Tests\Factories\Transfer_Factory;

/**
 * Class Tests_Transfer_Class
 *
 * @since 1.1.3
 * @package  Ever_Accounting\Tests
 */
class Tests_Transfer_Class extends \WP_UnitTestCase {
	public function test_create_transfer() {
		$from_account_id = Account_Factory::create( 'From Account', '001' )->get_id();
		$to_account_id   = Account_Factory::create( 'To Account', '002' )->get_id();
		Category_Factory::create('Transfer', 'other' );

		$transfer        = \Ever_Accounting\Transfers::insert( array(
			'from_account_id' => $from_account_id,
			'to_account_id'   => $to_account_id,
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
		$from_account_id = Account_Factory::create( 'From Account', '001' )->get_id();
		$to_account_id   = Account_Factory::create( 'To Account', '002' )->get_id();
		Category_Factory::create('Transfer', 'other' );
		$transfer        = \Ever_Accounting\Transfers::insert( array(
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

		$error = \Ever_Accounting\Transfers::insert( array(
			'id'              => $transfer_id,
			'from_account_id' => $from_account_id,
			'to_account_id'   => $to_account_id,
			'date'            => '2020-08-24',
			'amount'          => 60,
			'payment_method'  => 'cash',
		) );

		$this->assertNotWPError( $error );

		$transfer = \Ever_Accounting\Transfers::get( $transfer_id ); // so we can read fresh copies from the DB

		$this->assertEquals( '2020-08-24', date( 'Y-m-d', strtotime( $transfer->get_date() ) ) );
		$this->assertEquals( 60, $transfer->get_amount() );
		$this->assertEquals( 'cash', $transfer->get_payment_method() );
	}

	public function test_delete_transfer() {
		$transfer = Transfer_Factory::create();
		$this->assertNotEquals( 0, $transfer->get_id() );
		$this->assertNotFalse( \Ever_Accounting\Transfers::delete( $transfer->get_id() ) );
	}

	public function test_exception_transfer() {
		$transfer = \Ever_Accounting\Transfers::insert( array(
			'from_account_id' => '',
			'to_account_id' => Account_Factory::create( 'To Account' )->get_id(),
			'date'            => '2021-08-25',
			'amount'          => '500',
			'payment_method'  => 'cash'
		) );
		$this->assertEquals( 'Transfer from_account_id is required.', $transfer->get_error_message() );

		$from_account_id = Account_Factory::create( 'Sender Account ID', '001' )->get_id();
		$transfer        = \Ever_Accounting\Transfers::insert( array(
			'from_account_id' => $from_account_id,
			'to_account_id'   => '',
			'date'            => '2021-08-25',
			'amount'          => '500',
			'payment_method'  => 'cash'
		) );
		$this->assertEquals( 'Transfer to_account_id is required.', $transfer->get_error_message() );

		$to_account_id = Account_Factory::create( 'Receiver Account ID', '002' )->get_id();

		$transfer = \Ever_Accounting\Transfers::insert( array(
			'from_account_id' => $from_account_id,
			'to_account_id'   => $from_account_id,
			'date'            => '2021-08-25',
			'amount'          => '500',
			'payment_method'  => 'cash'
		) );
		$this->assertEquals( 'Source and Destination account number can\'t be same.', $transfer->get_error_message() );

		$transfer = \Ever_Accounting\Transfers::insert( array(
			'from_account_id' => $from_account_id,
			'to_account_id'   => $to_account_id,
			'amount'          => '',
			'date'            => '2021-08-25',
			'payment_method'  => 'cash'
		) );
		$this->assertEquals( 'Transfer amount is required.', $transfer->get_error_message() );

		$transfer = \Ever_Accounting\Transfers::insert( array(
			'from_account_id' => $from_account_id,
			'to_account_id'   => $to_account_id,
			'amount'          => 50,
			'date'            => '',
			'payment_method'  => 'cash'
		) );
		$this->assertEquals( 'Transfer date is required.', $transfer->get_error_message() );

		$transfer = \Ever_Accounting\Transfers::insert( array(
			'from_account_id' => $from_account_id,
			'to_account_id'   => $to_account_id,
			'amount'          => 50,
			'date'            => '2020-08-31',
			'payment_method'  => '',

		) );
		$this->assertEquals( 'Transfer payment_method is required.', $transfer->get_error_message() );

		Category_Factory::create( 'Transfer', 'other' );
		$transfer = \Ever_Accounting\Transfers::insert( array(
			'from_account_id' => $from_account_id,
			'to_account_id'   => $to_account_id,
			'amount'          => 50,
			'date'            => '2020-08-31',
			'payment_method'  => 'cash',

		) );
		$this->assertNotFalse( $transfer->exists() );

	}
}
