<?php

use EverAccounting\Transaction;
use EverAccounting\Account;

/**
 * Class EAccounting_Tests_Revenue.
 * @package EAccounting\Tests\Revenue
 */
class EAccounting_Tests_Revenue extends EAccounting_Unit_Test_Case {
	public function test_create_revenue() {

		//create account for revenue
		$account    = eaccounting_insert_account(
			array(
				'name'            => 'John Doe',
				'number'          => '12345678',
				'currency_code'   => 'USD',
				'opening_balance' => '10000.0000',
				'bank_name'       => 'Standard Chartered Bank',
				'bank_phone'      => '+12345678',
				'bank_address'    => 'Test Address',
			)
		);
		$account_id = $account->get_id();
		$this->assertNotFalse( $account->exists() );

		$this->assertEquals( 'John Doe', $account->get_name() );
		$this->assertNotNull( $account->get_id() );
		$this->assertEquals( '12345678', $account->get_number() );
		$this->assertEquals( 'USD', $account->get_currency_code() );
		$this->assertEquals( '10000.0000', $account->get_opening_balance() );
		$this->assertEquals( 'Standard Chartered Bank', $account->get_bank_name() );
		$this->assertEquals( '+12345678', $account->get_bank_phone() );
		$this->assertEquals( 'Test Address', $account->get_bank_address() );
		$this->assertNotNull( $account->get_date_created() );

		//create category for revenue
		$category    = eaccounting_insert_category( array(
			'name'  => 'Testing Category',
			'type'  => 'income',
			'color' => 'green',
		) );
		$category_id = $category->get_id();
		$this->assertNotFalse( $category->exists() );

		$this->assertEquals( 'Testing Category', $category->get_name() );
		$this->assertNotNull( $category->get_id() );
		$this->assertEquals( 'income', $category->get_type() );
		$this->assertEquals( 'green', $category->get_color() );
		$this->assertEquals( 1, $category->get_company_id() );
		$this->assertNotNull( $category->get_date_created() );

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

	public function test_update_revenue(){
		$account    = eaccounting_insert_account(
			array(
				'name'            => 'John Doe',
				'number'          => '12345678',
				'currency_code'   => 'USD',
				'opening_balance' => '10000.0000',
				'bank_name'       => 'Standard Chartered Bank',
				'bank_phone'      => '+12345678',
				'bank_address'    => 'Test Address',
			)
		);
		$account_id = $account->get_id();
		$this->assertNotFalse( $account->exists() );

		$this->assertEquals( 'John Doe', $account->get_name() );
		$this->assertNotNull( $account->get_id() );
		$this->assertEquals( '12345678', $account->get_number() );
		$this->assertEquals( 'USD', $account->get_currency_code() );
		$this->assertEquals( '10000.0000', $account->get_opening_balance() );
		$this->assertEquals( 'Standard Chartered Bank', $account->get_bank_name() );
		$this->assertEquals( '+12345678', $account->get_bank_phone() );
		$this->assertEquals( 'Test Address', $account->get_bank_address() );
		$this->assertNotNull( $account->get_date_created() );

		//create category for revenue
		$category    = eaccounting_insert_category( array(
			'name'  => 'Testing Category',
			'type'  => 'income',
			'color' => 'green',
		) );
		$category_id = $category->get_id();
		$this->assertNotFalse( $category->exists() );

		$this->assertEquals( 'Testing Category', $category->get_name() );
		$this->assertNotNull( $category->get_id() );
		$this->assertEquals( 'income', $category->get_type() );
		$this->assertEquals( 'green', $category->get_color() );
		$this->assertEquals( 1, $category->get_company_id() );
		$this->assertNotNull( $category->get_date_created() );

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

		$error = eaccounting_insert_transaction(array(
			'id' => $revenue_id,
			'type' => 'income',
			'amount' => 1500,
			'paid_at' => '2020-08-15',
			'category' => 'income',
			'category_id' => $category_id,
			'payment_method' => 'check',
			'account_id' => $account_id
		));
		$this->assertNotWPError( $error );

		$revenue = eaccounting_get_transaction($revenue_id);
		var_dump($revenue);

		$this->assertEquals( 'income', $revenue->get_type() );
		$this->assertNotNull( $revenue->get_id() );
		$this->assertEquals( '2020-08-15', date( 'Y-m-d', strtotime( $revenue->get_paid_at() ) ) );
		$this->assertEquals( 1500, $revenue->get_amount() );
		$this->assertEquals( 'check', $revenue->get_payment_method() );
		$this->assertNotNull( $revenue->get_account_id() );
		$this->assertNotNull( $revenue->get_category_id() );
		$this->assertNotNull( $revenue->get_date_created() );

	}
}
