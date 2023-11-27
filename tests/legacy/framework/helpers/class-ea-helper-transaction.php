<?php

/**
 * Class EverAccounting_Helper_Transaction.
 *
 * This helper class should ONLY be used for unit tests!.
 */
class EverAccounting_Helper_Transaction {

	/**
	 * Create a mock account for testing purposes.
	 *
	 * @return array|\EverAccounting\Transaction|WP_Error
	 */
	public static function create_transaction( $amount = 50, $payment_method = 'cash', $type = 'expense' ) {
		$category = EverAccounting_Helper_Category::create_category( 'Expense', 'expense' );
		$account  = EverAccounting_Helper_Account::create_account();
		$vendor   = EverAccounting_Helper_Contact::create_contact( 'John Doe', 'john@doe.com', 'vendor', 'USD' );

		$transaction = eaccounting_insert_transaction( array(
			'account_id'     => $account->get_id(),
			'payment_date'        => '2020-09-01',
			'amount'         => $amount,
			'vendor_id'      => $vendor->get_id(),
			'category_id'    => $category->get_id(),
			'payment_method' => $payment_method,
			'type'           => $type
		) );

		return $transaction;
	}


}
