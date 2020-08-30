<?php

use EverAccounting\Transaction;
use EverAccounting\Account;

/**
 * Class EAccounting_Tests_Revenue.
 * @package EAccounting\Tests\Revenue
 */
class EAccounting_Tests_Revenue extends EAccounting_Unit_Test_Case {
	public function test_create_revenue() {

		$account    = EAccounting_Helper_Account::create_account();
		$account_id = $account->get_id();
		$this->assertNotFalse( $account->exists() );

		$category    = EAccounting_Helper_Category::create_category( 'Income Category', 'income' );
		$category_id = $category->get_id();
		$this->assertNotFalse( $category->exists() );

		$revenue = eaccounting_insert_transaction(
			array(
				'type'           => 'income',
				'paid_at'        => '2020-08-15',
				'amount'         => 500,
				'category'       => 'expense',
				'category_id'    => $category_id,
				'payment_method' => 'cash',
				'account_id'     => $account_id
			)
		);
		$this->assertNotFalse( $revenue->exists() );

		$this->assertEquals( 'income', $revenue->get_type() );
		$this->assertNotNull( $revenue->get_id() );
		$this->assertEquals( '2020-08-15', date( 'Y-m-d', strtotime( $revenue->get_paid_at() ) ) );
		$this->assertEquals( 500, $revenue->get_amount() );
		$this->assertEquals( 'cash', $revenue->get_payment_method() );
		$this->assertNotNull( $revenue->get_account_id() );
		$this->assertNotNull( $revenue->get_category_id() );
		$this->assertNotNull( $revenue->get_date_created() );
	}

	public function test_update_revenue() {
		$account    = EAccounting_Helper_Account::create_account();
		$account_id = $account->get_id();
		$this->assertNotFalse( $account->exists() );

		//create category for revenue
		$category    = EAccounting_Helper_Category::create_category( 'Income Category', 'income' );
		$category_id = $category->get_id();
		$this->assertNotFalse( $category->exists() );

		$revenue    = eaccounting_insert_transaction(
			array(
				'type'           => 'income',
				'paid_at'        => '2020-08-15',
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
		$this->assertEquals( '2020-08-15', date( 'Y-m-d', strtotime( $revenue->get_paid_at() ) ) );
		$this->assertEquals( 500, $revenue->get_amount() );
		$this->assertEquals( 'cash', $revenue->get_payment_method() );
		$this->assertNotNull( $revenue->get_account_id() );
		$this->assertNotNull( $revenue->get_category_id() );
		$this->assertNotNull( $revenue->get_date_created() );

		$error = eaccounting_insert_transaction( array(
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

		$revenue = eaccounting_get_transaction( $revenue_id );

		$this->assertEquals( 'income', $revenue->get_type() );
		$this->assertNotNull( $revenue->get_id() );
		$this->assertEquals( '2020-08-15', date( 'Y-m-d', strtotime( $revenue->get_paid_at() ) ) );
		$this->assertEquals( 1500, $revenue->get_amount() );
		$this->assertEquals( 'check', $revenue->get_payment_method() );
		$this->assertNotNull( $revenue->get_account_id() );
		$this->assertNotNull( $revenue->get_category_id() );
		$this->assertNotNull( $revenue->get_date_created() );
	}

	public function test_delete_revenue(){
		$revenue = EAccounting_Helper_Revenue::create_revenue();
		$this->assertNotEquals( 0, $revenue->get_id() );
		$this->assertNotFalse( eaccounting_delete_transaction( $revenue->get_id() ) );
	}


}
