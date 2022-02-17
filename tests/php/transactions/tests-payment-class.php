<?php
/**
 * Ever_Accounting Payment Class Handler
 *
 * @since    1.1.3
 * @package  Ever_Accounting\Tests
 * @class    Tests_Payment_Class
 */

namespace Ever_Accounting\Tests;

use Ever_Accounting\Tests\Factories\Account_Factory;
use Ever_Accounting\Tests\Factories\Category_Factory;
use Ever_Accounting\Tests\Factories\Contact_Factory;
use Ever_Accounting\Tests\Factories\Payment_Factory;

/**
 * Class Tests_Payment_Class
 *
 * @since 1.1.3
 * @package  Ever_Accounting\Tests
 */
class Tests_Payment_Class extends \WP_UnitTestCase {
	public function test_create_payment() {
		$category = Category_Factory::create( 'Expense', 'expense' );
		$account  = Account_Factory::create();
		$vendor   = Contact_Factory::create_vendor();

		$payment = \Ever_Accounting\Transactions::insert_payment(
			array(
				'account_id' => $account->get_id(),
				'payment_date' => '2020-08-31',
				'amount' => 50,
				'vendor_id' => $vendor->get_id(),
				'category_id' => $category->get_id(),
				'payment_method' => 'cash',
			)
		);
		$this->assertNotFalse( $payment->exists() );
		$this->assertTrue( $payment->exists() );
		$this->assertNotNull( $payment->get_id() );
		$this->assertEquals( '2020-08-31', date('Y-m-d',strtotime($payment->get_payment_date())));
		$this->assertEquals( 50, $payment->get_amount());
		$this->assertEquals( 'cash', $payment->get_payment_method());
		$this->assertEquals('expense', $payment->get_type());
	}

	public function test_update_payment(){
		$category = Category_Factory::create( 'Expense', 'expense' );
		$account  = Account_Factory::create();
		$vendor   = Contact_Factory::create_vendor();

		$payment = \Ever_Accounting\Transactions::insert_payment(
			array(
				'account_id' => $account->get_id(),
				'payment_date' => '2020-08-31',
				'amount' => 50,
				'vendor_id' => $vendor->get_id(),
				'category_id' => $category->get_id(),
				'payment_method' => 'cash',
				'type' => 'expense'
			)
		);
		$payment_id = $payment->get_id();
		$this->assertNotFalse( $payment->exists() );
		$this->assertEquals( '2020-08-31', date('Y-m-d',strtotime($payment->get_payment_date())));
		$this->assertEquals( 50, $payment->get_amount());
		$this->assertEquals( 'cash', $payment->get_payment_method());
		$this->assertEquals('expense', $payment->get_type());

		$error = \Ever_Accounting\Transactions::insert_payment(array(
			'id' => $payment_id,
			'account_id' => $account->get_id(),
			'payment_date' => '2020-09-01',
			'amount' => 100,
			'vendor_id' => $vendor->get_id(),
			'category_id' => $category->get_id(),
			'payment_method' => 'bank_transfer',
			'type' => 'expense'
		));

		$this->assertNotWPError( $error );
		$payment = \Ever_Accounting\Transactions::get_payment($payment_id); // so we can read fresh copies from the DB
		$this->assertEquals( 100, $payment->get_amount());
		$this->assertEquals( 'bank_transfer', $payment->get_payment_method());
		$this->assertEquals('expense', $payment->get_type());
	}

	public function test_delete_payment(){
		$payment = Payment_Factory::create(50,'cash','expense');
		$this->assertNotEquals( 0, $payment->get_id() );
		$this->assertNotFalse( \Ever_Accounting\Transactions::delete_payment( $payment->get_id() ) );
	}

	public function test_exception_payment(){
//		$payment = \Ever_Accounting\Transactions::insert_payment(array(
//			'type' => 'expense',
//			'payment_date' => '2020-09-01',
//			'account_id' => '2',
//			'category_id' => '3',
//			'payment_method' => 'cash'
//		));
//		$this->assertEquals( 'Transaction type is required', $payment->get_error_message() );

		$payment = \Ever_Accounting\Transactions::insert_payment(array(
			'type' => 'expense',
			'payment_date' => '',
			'account_id' => '2',
			'category_id' => '3',
			'payment_method' => 'cash'
		));
		$this->assertEquals( 'Transaction payment_date is required', $payment->get_error_message() );

		$payment = \Ever_Accounting\Transactions::insert_payment(array(
			'payment_date' => '2020-09-01',
			'type' => 'expense',
			'account_id' => ''
		));
		$this->assertEquals( 'Transaction account_id is required', $payment->get_error_message() );

		$payment = \Ever_Accounting\Transactions::insert_payment(array(
			'payment_date' => '2020-09-01',
			'type' => 'expense',
			'account_id' => '2',
			'category_id' => '',
		));
		$this->assertEquals( 'Transaction category_id is required', $payment->get_error_message() );

		$payment = \Ever_Accounting\Transactions::insert_payment(array(
			'payment_date' => '2020-09-01',
			'type' => 'expense',
			'category_id' => 53,
			'account_id' => '2',
			'payment_method' => ''
		));
		$this->assertEquals( 'Transaction payment_method is required', $payment->get_error_message() );

		$account = Account_Factory::create();
		$category = Category_Factory::create('Income', 'income');

		$payment = \Ever_Accounting\Transactions::insert_payment(array(
			'account_id' => $account->get_id(),
			'payment_date' => '2020-09-01',
			'type' => 'expense',
			'payment_method' => 'cash',
			'category_id' => $category->get_id()
		));

		$this->assertEquals( 'Transaction type and category type does not match.', $payment->get_error_message() );

		$contact = Contact_Factory::create_vendor();
		$category = Category_Factory::create();
		$payment = \Ever_Accounting\Transactions::insert_payment(array(
			'account_id' => $account->get_id(),
			'payment_date' => '2020-09-01',
			'type' => 'expense',
			'payment_method' => 'cash',
			'category_id' => $category->get_id(),
			'contact_id' => $contact->get_id()
		));

		$this->assertNotFalse( $payment->exists() );

	}
}
