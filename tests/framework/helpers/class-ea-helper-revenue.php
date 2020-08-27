<?php

/**
 * Class EAccounting_Helper_Revenue.
 *
 * This helper class should ONLY be used for unit tests!.
 */

class EAccounting_Helper_Revenue {
	/**
	 * Creates an revenue in test db
	 *
	 */
	public static function create_revenue( $type = 'income', $amount = 1000, $paid_at = '2020-08-26', $category = 'income', $payment_method = 'cash' ) {
		$account    = EAccounting_Helper_Account::create_account();
		$account_id = $account->get_id();

		$category    = EAccounting_Helper_Category::create_category( 'Income', 'income' );
		$category_id = $category->get_id();

		$revenue = eaccounting_insert_transaction( array(
			'type'           => $type,
			'amount'         => $amount,
			'paid_at'        => $paid_at,
			'category'       => $category,
			'category_id'    => $category_id,
			'payment_method' => $payment_method,
			'account_id'     => $account_id
		) );

		return $revenue;

	}
}
