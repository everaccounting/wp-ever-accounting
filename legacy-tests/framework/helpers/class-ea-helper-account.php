<?php

/**
 * Class EverAccounting_Helper_Account.
 *
 * This helper class should ONLY be used for unit tests!.
 */
class EverAccounting_Helper_Account {

	/**
	 * Create a mock account for testing purposes.
	 *
	 * @return array
	 */
	public static function create_mock_account() {
		$account_data = array(
			'id'              => 0,
			'date_modified'   => null,
			'name'            => 'Test Account',
			'number'          => '000001',
			'opening_balance' => 1000.01,
			'currency_code'   => 'USD',
			'bank_name'       => 'ABC Bank LTD.',
			'bank_phone'      => '1234567890',
			'bank_address'    => '123 South Street, Redwood City, California',
		);

		return $account_data;
	}

	/**
	 * Creates a account in the tests DB.
	 */
	public static function create_account( $name = 'Test Account', $number = '000001', $opening_balance = 1000, $currency_code = 'USD' ) {
		$account = new \EAccounting\Account(null);
		$account->set_name( $name );
		$account->set_number( $number );
		$account->set_opening_balance( $opening_balance );
		$account->set_currency_code( $currency_code );
		$account->set_bank_name( 'ABC Bank LTD.' );
		$account->set_bank_phone( '1234567890' );
		$account->set_bank_address( '123 Steet, Redwood City, California' );
		$account->save();

		return $account;
	}

}
