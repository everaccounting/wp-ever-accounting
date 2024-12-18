<?php

use EAccounting\Transaction;

/**
 * Class EverAccounting_Tests_Payment.
 * @package EAccounting\Tests\Payment
 */
class EverAccounting_Tests_Payment extends EverAccounting_Unit_Test_Case {
	public function test_create_payment() {
		$category = EverAccounting_Helper_Category::create_category( 'Expense', 'expense' );
		$account  = EverAccounting_Helper_Account::create_account();
		$vendor   = EverAccounting_Helper_Contact::create_contact( 'John Doe', 'john@doe.com', 'vendor', 'USD' );

		$payment = eaccounting_insert_transaction(
			array(
				'account_id' => $account->get_id(),
				'paid_at' => '2020-08-31',
				'amount' => 50,
				'vendor_id' => $vendor->get_id(),
				'category_id' => $category->get_id(),
				'method' => 'cash',
				'type' => 'expense'
			)
		);
		$this->assertNotFalse( $payment->exists() );
		$this->assertNotNull( $payment->get_id() );
		$this->assertEquals( '2020-08-31', date('Y-m-d',strtotime($payment->get_payment_date())));
		$this->assertEquals( 50, $payment->get_amount());
		$this->assertEquals( 'cash', $payment->get_method());
		$this->assertEquals('expense', $payment->get_type());
	}

	public function test_update_payment(){
		$category = EverAccounting_Helper_Category::create_category( 'Expense', 'expense' );
		$account  = EverAccounting_Helper_Account::create_account();
		$vendor   = EverAccounting_Helper_Contact::create_contact( 'John Doe', 'john@doe.com', 'vendor', 'USD' );

		$payment = eaccounting_insert_transaction(
			array(
				'account_id' => $account->get_id(),
				'paid_at' => '2020-08-31',
				'amount' => 50,
				'vendor_id' => $vendor->get_id(),
				'category_id' => $category->get_id(),
				'method' => 'cash',
				'type' => 'expense'
			)
		);
		$payment_id = $payment->get_id();
		$this->assertNotFalse( $payment->exists() );
		$this->assertEquals( '2020-08-31', date('Y-m-d',strtotime($payment->get_payment_date())));
		$this->assertEquals( 50, $payment->get_amount());
		$this->assertEquals( 'cash', $payment->get_method());
		$this->assertEquals('expense', $payment->get_type());

		$error = eaccounting_insert_transaction(array(
			'id' => $payment_id,
			'account_id' => $account->get_id(),
			'paid_at' => '2020-09-01',
			'amount' => 100,
			'vendor_id' => $vendor->get_id(),
			'category_id' => $category->get_id(),
			'method' => 'bank_transfer',
			'type' => 'expense'
		));

		$this->assertNotWPError( $error );
		$payment = eaccounting_get_transaction($payment_id); // so we can read fresh copies from the DB
		$this->assertEquals( 100, $payment->get_amount());
		$this->assertEquals( 'bank_transfer', $payment->get_method());
		$this->assertEquals('expense', $payment->get_type());
	}

	public function test_delete_payment(){
		$payment = EverAccounting_Helper_Transaction::create_transaction(50,'cash','expense');
		$this->assertNotEquals( 0, $payment->get_id() );
		$this->assertNotFalse( eaccounting_delete_transaction( $payment->get_id() ) );
	}

	public function test_exception_payment(){
		$payment = eaccounting_insert_transaction(array(
			'paid_at' => '',
		));
		$this->assertEquals( 'Transaction date is required', $payment->get_error_message() );

		$payment = eaccounting_insert_transaction(array(
			'paid_at' => '2020-09-01',
			'type' => ''
		));
		$this->assertEquals( 'Transaction type is required', $payment->get_error_message() );

		$payment = eaccounting_insert_transaction(array(
			'paid_at' => '2020-09-01',
			'type' => 'expense',
			'category_id' => ''
		));
		$this->assertEquals( 'Category is required', $payment->get_error_message() );

		$payment = eaccounting_insert_transaction(array(
			'paid_at' => '2020-09-01',
			'type' => 'expense',
			'category_id' => 53,
			'method' => ''
		));
		$this->assertEquals( 'Payment method is required', $payment->get_error_message() );

		$payment = eaccounting_insert_transaction(array(
			'paid_at' => '2020-09-01',
			'type' => 'expense',
			'category_id' => 53,
			'method' => 'cash',
			'account_id' => ''
		));
		$this->assertEquals( 'Account is required.', $payment->get_error_message() );

		$account = EverAccounting_Helper_Account::create_account();
		$category = EverAccounting_Helper_Category::create_category();

		$payment = eaccounting_insert_transaction(array(
			'account_id' => $account->get_id(),
			'paid_at' => '2020-09-01',
			'type' => 'income',
			'method' => 'cash',
			'category_id' => $category->get_id()
		));
		$this->assertEquals( 'Transaction type and category type does not match.', $payment->get_error_message() );

		$contact = EverAccounting_Helper_Contact::create_contact();
		$payment = eaccounting_insert_transaction(array(
			'account_id' => $account->get_id(),
			'paid_at' => '2020-09-01',
			'type' => 'expense',
			'method' => 'cash',
			'category_id' => $category->get_id(),
			'contact_id' => $contact->get_id()
		));
		$this->assertNotFalse( $payment->exists() );

	}
}
