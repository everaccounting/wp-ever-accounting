<?php

/**
 * Class EverAccounting_Helper_Transfer.
 *
 * This helper class should ONLY be used for unit tests!.
 */

class EverAccounting_Helper_Transfer {
	/**
	 * Create a mock transfer for testing purposes.
	 *
	 * @return array
	 */
	public static function create_transfer($amount = 500) {
		$transfer = eaccounting_insert_transfer(array(
			'from_account_id' => EverAccounting_Helper_Account::create_account('From Account',10000)->get_id(),
			'to_account_id' => EverAccounting_Helper_Account::create_account('To Account')->get_id(),
			'date' => '2020-08-25',
			'amount' => $amount,
			'payment_method' => 'cash'
		));
		return $transfer;
	}
}
