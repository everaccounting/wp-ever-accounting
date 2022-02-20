<?php
/**
 * Ever_Accounting Revenue Class Handler
 *
 * @since    1.1.3
 * @package  Ever_Accounting\Tests
 * @class    Tests_Revenue_Class
 */

namespace Ever_Accounting\Tests;

use Ever_Accounting\Tests\Factories\Account_Factory;
use Ever_Accounting\Tests\Factories\Category_Factory;
use Ever_Accounting\Tests\Factories\Contact_Factory;
use Ever_Accounting\Tests\Factories\Revenue_Factory;

/**
 * Class Tests_Revenue_Class
 *
 * @since 1.1.3
 * @package  Ever_Accounting\Tests
 */
class Tests_Revenue_Class extends \WP_UnitTestCase {
	public function test_create_revenue() {
		$category = Category_Factory::create( 'Income Category', 'income' );
		$account  = Account_Factory::create();
		$customer   = Contact_Factory::create_customer();

		$revenue = \Ever_Accounting\Transactions::insert_revenue(
			array(
				'account_id' => $account->get_id(),
				'payment_date' => '2020-08-31',
				'amount' => 50,
				'customer_id' => $customer->get_id(),
				'category_id' => $category->get_id(),
				'payment_method' => 'cash',
			)
		);
		$this->assertNotFalse( $revenue->exists() );
		$this->assertTrue( $revenue->exists() );
		$this->assertNotNull( $revenue->get_id() );
		$this->assertEquals( '2020-08-31', date('Y-m-d',strtotime($revenue->get_payment_date())));
		$this->assertEquals( 50, $revenue->get_amount());
		$this->assertEquals( 'cash', $revenue->get_payment_method());
		$this->assertEquals('income', $revenue->get_type());
	}

	public function test_update_revenue(){
		$category = Category_Factory::create( 'Income', 'income' );
		$account  = Account_Factory::create();
		$customer   = Contact_Factory::create_customer();

		$revenue = \Ever_Accounting\Transactions::insert_revenue(
			array(
				'account_id' => $account->get_id(),
				'payment_date' => '2020-08-31',
				'amount' => 50,
				'customer_id' => $customer->get_id(),
				'category_id' => $category->get_id(),
				'payment_method' => 'cash',
				'type' => 'income'
			)
		);
		$revenue_id = $revenue->get_id();
		$this->assertNotFalse( $revenue->exists() );
		$this->assertEquals( '2020-08-31', date('Y-m-d',strtotime($revenue->get_payment_date())));
		$this->assertEquals( 50, $revenue->get_amount());
		$this->assertEquals( 'cash', $revenue->get_payment_method());
		$this->assertEquals('income', $revenue->get_type());

		$error = \Ever_Accounting\Transactions::insert_revenue(array(
			'id' => $revenue_id,
			'account_id' => $account->get_id(),
			'payment_date' => '2020-09-01',
			'amount' => 100,
			'customer_id' => $customer->get_id(),
			'category_id' => $category->get_id(),
			'payment_method' => 'bank_transfer',
			'type' => 'income'
		));

		$this->assertNotWPError( $error );
		$revenue = \Ever_Accounting\Transactions::get_revenue($revenue_id); // so we can read fresh copies from the DB
		$this->assertEquals( 100, $revenue->get_amount());
		$this->assertEquals( 'bank_transfer', $revenue->get_payment_method());
		$this->assertEquals('income', $revenue->get_type());
	}

	public function test_delete_revenue(){
		$revenue = Revenue_Factory::create(50,'cash','income');
		$this->assertNotEquals( 0, $revenue->get_id() );
		$this->assertNotFalse( \Ever_Accounting\Transactions::delete_revenue( $revenue->get_id() ) );
	}

	public function test_exception_revenue(){
		$revenue = \Ever_Accounting\Transactions::insert_revenue(array(
			'type' => 'income',
			'payment_date' => '',
			'account_id' => '2',
			'category_id' => '3',
			'payment_method' => 'cash'
		));
		$this->assertEquals( 'Transaction payment_date is required', $revenue->get_error_message() );

		$revenue = \Ever_Accounting\Transactions::insert_revenue(array(
			'payment_date' => '2020-09-01',
			'type' => 'income',
			'account_id' => ''
		));
		$this->assertEquals( 'Transaction account_id is required', $revenue->get_error_message() );

		$revenue = \Ever_Accounting\Transactions::insert_revenue(array(
			'payment_date' => '2020-09-01',
			'type' => 'income',
			'account_id' => '2',
			'category_id' => '',
		));
		$this->assertEquals( 'Transaction category_id is required', $revenue->get_error_message() );

		$revenue = \Ever_Accounting\Transactions::insert_revenue(array(
			'payment_date' => '2020-09-01',
			'type' => 'income',
			'category_id' => 53,
			'account_id' => '2',
			'payment_method' => ''
		));
		$this->assertEquals( 'Transaction payment_method is required', $revenue->get_error_message() );

		$account = Account_Factory::create();
		$category = Category_Factory::create();

		$revenue = \Ever_Accounting\Transactions::insert_revenue(array(
			'account_id' => $account->get_id(),
			'payment_date' => '2020-09-01',
			'type' => 'income',
			'payment_method' => 'cash',
			'category_id' => $category->get_id()
		));

		$this->assertEquals( 'Transaction type and category type does not match.', $revenue->get_error_message() );

		$contact = Contact_Factory::create_vendor();
		$category = Category_Factory::create('Income', 'income');
		$revenue = \Ever_Accounting\Transactions::insert_revenue(array(
			'account_id' => $account->get_id(),
			'payment_date' => '2020-09-01',
			'type' => 'income',
			'payment_method' => 'cash',
			'category_id' => $category->get_id(),
			'contact_id' => $contact->get_id()
		));

		$this->assertNotFalse( $revenue->exists() );

	}
}
