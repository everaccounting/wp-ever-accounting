<?php

namespace Ever_Accounting\Tests\Factories;

class Revenue_Factory {
	/**
	 * Creates a revenue in the tests DB.
	 */
	public static function create( $type = 'income', $amount = 1000, $payment_date = '2020-08-26', $category = 'income', $payment_method = 'cash' ) {

		$account    = Account_Factory::create();
		$account_id = $account->get_id();

		$category    = Category_Factory::create( 'Income', 'income' );
		$category_id = $category->get_id();

		return \Ever_Accounting\Transactions::insert_revenue( array(
			'type'           => $type,
			'amount'         => $amount,
			'payment_date'   => $payment_date,
			'category'       => $category,
			'category_id'    => $category_id,
			'payment_method' => $payment_method,
			'account_id'     => $account_id
		) );
	}

}
