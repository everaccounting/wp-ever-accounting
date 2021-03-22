<?php
/**
 * Handle the transaction test and account test
 *
 * @package     EverAccounting\Test
 * @class       EverAccounting_Tests_Revenue
 * @version     1.0.2
 */

use EverAccounting\Models\Transaction;
use EverAccounting\Models\Account;

defined( 'ABSPATH' ) || exit;

/**
 * Class EverAccounting_Tests_Revenue.
 * @package EverAccounting\Tests\Revenue
 */
class EverAccounting_Tests_Revenue extends EverAccounting_Unit_Test_Case {
	public function test_create_revenue() {

		$account    = EverAccounting_Helper_Account::create_account();
		$account_id = $account->get_id();
		$this->assertNotFalse( $account->exists() );

		$category    = EverAccounting_Helper_Category::create_category( 'Income Category', 'income' );
		$category_id = $category->get_id();
		$this->assertNotFalse( $category->exists() );

		$revenue = eaccounting_insert_revenue(
			array(
				'type'           => 'income',
				'payment_date'   => '2020-08-15',
				'amount'         => '500',
				'category'       => 'expense',
				'category_id'    => $category_id,
				'payment_method' => 'cash',
				'account_id'     => $account_id
			)
		);

		$this->assertNotFalse( $revenue->exists() );
		$this->assertEquals( 'income', $revenue->get_type() );
		$this->assertNotNull( $revenue->get_id() );
		$this->assertEquals( '2020-08-15', date( 'Y-m-d', strtotime( $revenue->get_payment_date() ) ) );
		$this->assertEquals( 500, $revenue->get_amount() );
		$this->assertEquals( 'cash', $revenue->get_payment_method() );
		$this->assertNotNull( $revenue->get_account_id() );
		$this->assertNotNull( $revenue->get_category_id() );
		$this->assertNotNull( $revenue->get_date_created() );
	}

	public function test_update_revenue() {
		$account    = EverAccounting_Helper_Account::create_account();
		$account_id = $account->get_id();
		$this->assertNotFalse( $account->exists() );

		//create category for revenue
		$category    = EverAccounting_Helper_Category::create_category( 'Income Category', 'income' );
		$category_id = $category->get_id();
		$this->assertNotFalse( $category->exists() );

		$revenue    = eaccounting_insert_revenue(
			array(
				'type'           => 'income',
				'payment_date'   => '2020-08-15',
				'amount'         => 500,
				'category'       => 'expense',
				'category_id'    => $category_id,
				'payment_method' => 'cash',
				'account_id'     => $account_id
			)
		);
		$revenue_id = $revenue->get_id();
		$this->assertNotFalse( $revenue->exists() );

		$this->assertEquals( 'income', $revenue->get_type() );
		$this->assertNotNull( $revenue->get_id() );
		$this->assertEquals( '2020-08-15', date( 'Y-m-d', strtotime( $revenue->get_payment_date() ) ) );
		$this->assertEquals( 500, $revenue->get_amount() );
		$this->assertEquals( 'cash', $revenue->get_payment_method() );
		$this->assertNotNull( $revenue->get_account_id() );
		$this->assertNotNull( $revenue->get_category_id() );
		$this->assertNotNull( $revenue->get_date_created() );

		$error = eaccounting_insert_revenue( array(
			'id'             => $revenue_id,
			'type'           => 'income',
			'amount'         => 1500,
			'paid_at'        => '2020-08-15',
			'category'       => 'income',
			'category_id'    => $category_id,
			'payment_method' => 'check',
			'account_id'     => $account_id
		) );
		$this->assertNotWPError( $error );

		$revenue = eaccounting_get_revenue( $revenue_id );
		$this->assertEquals( 'income', $revenue->get_type() );
		$this->assertNotNull( $revenue->get_id() );
		$this->assertEquals( '2020-08-15', date( 'Y-m-d', strtotime( $revenue->get_payment_date() ) ) );
		$this->assertEquals( 1500, $revenue->get_amount() );
		$this->assertEquals( 'check', $revenue->get_payment_method() );
		$this->assertNotNull( $revenue->get_account_id() );
		$this->assertNotNull( $revenue->get_category_id() );
		$this->assertNotNull( $revenue->get_date_created() );
	}

	public function test_delete_revenue() {
		$revenue = EverAccounting_Helper_Revenue::create_revenue();
		$this->assertNotEquals( 0, $revenue->get_id() );
		$this->assertNotFalse( eaccounting_delete_revenue( $revenue->get_id() ) );
	}

	public function test_exception_revenue() {
		$revenue = eaccounting_insert_revenue( array(
			'payment_date' => '',
		) );
		$this->assertEquals( 'Revenue Date is required', $revenue->get_error_message() );

		$revenue = eaccounting_insert_revenue( array(
			'payment_date' => '2020-09-01',
			'account_id'   => ''
		) );
		$this->assertEquals( 'Account ID is required', $revenue->get_error_message() );

		$revenue = eaccounting_insert_revenue( array(
			'payment_date' => '2020-09-01',
			'account_id'   =>  '2',
			'category_id'  =>  '' 

		) );
		$this->assertEquals( 'Category ID is required', $revenue->get_error_message() );

		$revenue = eaccounting_insert_revenue( array(
			'payment_date' => '2020-09-01',
			'account_id'   =>  '2',
			'category_id'  =>  '10', 
			'payment_method' => ''
		) );
		$this->assertEquals( 'Payment Method is required', $revenue->get_error_message() );

		$account  = EverAccounting_Helper_Account::create_account();
		$category = EverAccounting_Helper_Category::create_category();
		
		$revenue = eaccounting_insert_revenue( array(
			'account_id'     => $account->get_id(),
			'payment_date'   => '',
			'type'           => 'income',
			'payment_method' => 'cash',
			'category_id'    => $category->get_id()
		) );

		$this->assertEquals( 'Revenue Date is required', $revenue->get_error_message() );

		$contact = EverAccounting_Helper_Contact::create_contact();
		$category = EverAccounting_Helper_Category::create_category('Income','income');

		$revenue = eaccounting_insert_revenue( array(
			'account_id'     => $account->get_id(),
			'payment_date'   => '2020-09-01',
			'type'           => 'income',
			'payment_method' => 'cash',
			'category_id'    => $category->get_id(),
			'contact_id'     => $contact->get_id()
		) );
		$this->assertNotFalse( $revenue->exists() );

	}


}
