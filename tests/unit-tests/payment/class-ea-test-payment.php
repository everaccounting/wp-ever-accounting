<?php
/**
 * Handle the transaction test case.
 *
 * @package     EverAccounting\Test
 * @class       EverAccounting_Tests_Payment
 * @version     1.0.2
 */


use EverAccounting\Models\Transaction;

defined( 'ABSPATH' ) || exit;

/**
 * Class EverAccounting_Tests_Payment.
 * @package EverAccounting\Tests\Payment
 */
class EverAccounting_Tests_Payment extends EverAccounting_Unit_Test_Case {
	public function test_create_payment() {
		$category = EverAccounting_Helper_Category::create_category( 'Expense', 'expense' );
		$account  = EverAccounting_Helper_Account::create_account();
		$vendor   = EverAccounting_Helper_Contact::create_contact( 'John Doe', 'john@doe.com', 'vendor', 'USD' );
		
		$payment = eaccounting_insert_payment(
			array(
				'account_id'     => $account->get_id(),
				'payment_date'   => '2020-08-31',
				'amount'         => 50,
				'vendor_id'      => $vendor->get_id(),
				'category_id'    => $category->get_id(),
				'payment_method' => 'cash',
				'type' 			 => 'expense'
			)
		);
		$this->assertNotFalse( $payment->exists() );
		$this->assertNotNull( $payment->get_id() );
		$this->assertEquals( '2020-08-31', date('Y-m-d',strtotime($payment->get_payment_date())));
		$this->assertEquals( 50, $payment->get_amount());
		$this->assertEquals( 'cash', $payment->get_payment_method());
		$this->assertEquals('expense', $payment->get_type());
	}

	public function test_update_payment(){
		$category = EverAccounting_Helper_Category::create_category( 'Expense', 'expense' );
		$account  = EverAccounting_Helper_Account::create_account();
		$vendor   = EverAccounting_Helper_Contact::create_contact( 'John Doe', 'john@doe.com', 'vendor', 'USD' );

		$payment = eaccounting_insert_payment(
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

		$error = eaccounting_insert_payment(array(
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
		$payment = eaccounting_get_payment($payment_id); // so we can read fresh copies from the DB
		$this->assertEquals( 100, $payment->get_amount());
		$this->assertEquals( 'bank_transfer', $payment->get_payment_method());
		$this->assertEquals('expense', $payment->get_type());
	 }

	public function test_delete_payment(){
		$payment = EverAccounting_Helper_Transaction::create_transaction(50,'cash','expense');
		$this->assertNotEquals( 0, $payment->get_id() );
		$this->assertNotFalse( eaccounting_delete_payment( $payment->get_id() ) );
	}

	public function test_exception_payment(){
		$payment = eaccounting_insert_payment(array(
			'payment_date' => '',
		    'type'    => 'expense'
		));
		$this->assertEquals( 'Payment Date is required', $payment->get_error_message() );

		$payment = eaccounting_insert_payment(array(
			'payment_date' => '2020-09-01',
			'account_id'   => '',
			'type' => 'expense'
		));
		$this->assertEquals( 'Account ID is required', $payment->get_error_message() );

		$account  = EverAccounting_Helper_Account::create_account();
		$payment = eaccounting_insert_payment(array(
			'payment_date' => '2020-09-01',
			'type' 		   => 'expense',
			'account_id'   => $account->get_id(),
			'category_id'  => ''
		));
		$this->assertEquals( 'Category ID is required', $payment->get_error_message() );

		$category = EverAccounting_Helper_Category::create_category( 'Expense', 'expense' );
		$payment = eaccounting_insert_payment(array(
			'payment_date' => '2020-09-01',
			'type' 		   => 'expense',
			'account_id'   => $account->get_id(),
			'category_id'  => $category->get_id(),
			
		));
		$this->assertEquals( 'Payment Method is required', $payment->get_error_message() );


		$contact = EverAccounting_Helper_Contact::create_contact();
		$payment = eaccounting_insert_payment(array(
			'account_id'     => $account->get_id(),
			'payment_date'   => '2020-09-01',
			'type'           => 'expense',
			'payment_method' => 'cash',
			'category_id'    => $category->get_id(),
			'contact_id'     => $contact->get_id()
		));
		$this->assertNotFalse( $payment->exists() );

	}
}
