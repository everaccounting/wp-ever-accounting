<?php

/**
 * Class EAccounting_Helper_Transaction.
 *
 * This helper class should ONLY be used for unit tests!.
 */
class EAccounting_Helper_Transaction {

	/**
	 * Create a mock account for testing purposes.
	 *
	 * @return array|\EverAccounting\Transaction|WP_Error
	 */
	public static function create_transaction( $amount = 50, $payment_method = 'cash', $type = 'expense' ) {
		$category = EAccounting_Helper_Category::create_category( 'Expense', 'expense' );
		$account  = EAccounting_Helper_Account::create_account();
		$vendor   = EAccounting_Helper_Contact::create_contact( 'John Doe', 'john@doe.com', 'vendor', 'USD' );

		$transaction = eaccounting_insert_transaction( array(
			'account_id'     => $account->get_id(),
			'paid_at'        => '2020-09-01',
			'amount'         => $amount,
			'vendor_id'      => $vendor->get_id(),
			'category_id'    => $category->get_id(),
			'payment_method' => $payment_method,
			'type'           => $type
		) );

		return $transaction;
	}


}
