<?php

namespace Ever_Accounting\Tests\Factories;

class Payment_Factory {
	/**
	 * Creates a payment in the tests DB.
	 */
	public static function create( $type = 'expense', $amount = 1000, $payment_date = '2020-08-26', $category = 'expense', $payment_method = 'cash' ) {

		$account    = Account_Factory::create();
		$account_id = $account->get_id();

		$category    = Category_Factory::create( 'Expense', 'expense' );
		$category_id = $category->get_id();

		return \Ever_Accounting\Transactions::insert_payment( array(
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
