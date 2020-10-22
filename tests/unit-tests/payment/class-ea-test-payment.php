<?php

use EverAccounting\Transaction;

/**
 * Class EAccounting_Tests_Payment.
 * @package EAccounting\Tests\Payment
 */
class EAccounting_Tests_Payment extends EAccounting_Unit_Test_Case {
	public function test_create_payment() {
		$category = EAccounting_Helper_Category::create_category( 'Expense', 'expense' );
		$account  = EAccounting_Helper_Account::create_account();
		$vendor   = EAccounting_Helper_Contact::create_contact( 'John Doe', 'john@doe.com', 'vendor', 'USD' );

		$payment = eaccounting_insert_transaction(
			array(
				'account_id' => $account->get_id(),
				'paid_at' => '2020-08-31',
				'amount' => 50,
				'vendor_id' => $vendor->get_id(),
				'category_id' => $category->get_id(),
				'payment_method' => 'cash',
				'type' => 'expense'
			)
		);
		$this->assertNotFalse( $payment->exists() );
		$this->assertNotNull( $payment->get_id() );
		$this->assertEquals( '2020-08-31', date('Y-m-d',strtotime($payment->get_paid_at())));
		$this->assertEquals( 50, $payment->get_amount());
		$this->assertEquals( 'cash', $payment->get_payment_method());
		$this->assertEquals('expense', $payment->get_type());
	}

	public function test_update_payment(){
		$category = EAccounting_Helper_Category::create_category( 'Expense', 'expense' );
		$account  = EAccounting_Helper_Account::create_account();
		$vendor   = EAccounting_Helper_Contact::create_contact( 'John Doe', 'john@doe.com', 'vendor', 'USD' );

		$payment = eaccounting_insert_transaction(
			array(
				'account_id' => $account->get_id(),
				'paid_at' => '2020-08-31',
				'amount' => 50,
				'vendor_id' => $vendor->get_id(),
				'category_id' => $category->get_id(),
				'payment_method' => 'cash',
				'type' => 'expense'
			)
		);
		$payment_id = $payment->get_id();
		$this->assertNotFalse( $payment->exists() );
		$this->assertEquals( '2020-08-31', date('Y-m-d',strtotime($payment->get_paid_at())));
		$this->assertEquals( 50, $payment->get_amount());
		$this->assertEquals( 'cash', $payment->get_payment_method());
		$this->assertEquals('expense', $payment->get_type());

		$error = eaccounting_insert_transaction(array(
			'id' => $payment_id,
			'account_id' => $account->get_id(),
			'paid_at' => '2020-09-01',
			'amount' => 100,
			'vendor_id' => $vendor->get_id(),
			'category_id' => $category->get_id(),
			'payment_method' => 'bank_transfer',
			'type' => 'expense'
		));

		$this->assertNotWPError( $error );
		$payment = eaccounting_get_transaction($payment_id); // so we can read fresh copies from the DB
		$this->assertEquals( 100, $payment->get_amount());
		$this->assertEquals( 'bank_transfer', $payment->get_payment_method());
		$this->assertEquals('expense', $payment->get_type());
	}

	public function test_delete_payment(){
		$payment = EAccounting_Helper_Transaction::create_transaction(50,'cash','expense');
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
			'payment_method' => ''
		));
		$this->assertEquals( 'Payment method is required', $payment->get_error_message() );

		$payment = eaccounting_insert_transaction(array(
			'paid_at' => '2020-09-01',
			'type' => 'expense',
			'category_id' => 53,
			'payment_method' => 'cash',
			'account_id' => ''
		));
		$this->assertEquals( 'Account is required.', $payment->get_error_message() );

		$account = EAccounting_Helper_Account::create_account();
		$category = EAccounting_Helper_Category::create_category();

		$payment = eaccounting_insert_transaction(array(
			'account_id' => $account->get_id(),
			'paid_at' => '2020-09-01',
			'type' => 'income',
			'payment_method' => 'cash',
			'category_id' => $category->get_id()
		));
		$this->assertEquals( 'Transaction type and category type does not match.', $payment->get_error_message() );

		$contact = EAccounting_Helper_Contact::create_contact();
		$payment = eaccounting_insert_transaction(array(
			'account_id' => $account->get_id(),
			'paid_at' => '2020-09-01',
			'type' => 'expense',
			'payment_method' => 'cash',
			'category_id' => $category->get_id(),
			'contact_id' => $contact->get_id()
		));
		$this->assertNotFalse( $payment->exists() );

	}
}
