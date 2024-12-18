<?php

/**
 * Class EverAccounting_Helper_Revenue.
 *
 * This helper class should ONLY be used for unit tests!.
 */

class EverAccounting_Helper_Revenue {
	/**
	 * Creates an revenue in test db
	 *
	 */
	public static function create_revenue( $type = 'income', $amount = 1000, $payment_date = '2020-08-26', $category = 'income', $method = 'cash' ) {
		$account    = EverAccounting_Helper_Account::create_account();
		$account_id = $account->get_id();

		$category    = EverAccounting_Helper_Category::create_category( 'Income', 'income' );
		$category_id = $category->get_id();

		$revenue = eaccounting_insert_transaction( array(
			'type'           => $type,
			'amount'         => $amount,
			'payment_date'        => $payment_date,
			'category'       => $category,
			'category_id'    => $category_id,
			'method' => $method,
			'account_id'     => $account_id
		) );

		return $revenue;

	}
}
