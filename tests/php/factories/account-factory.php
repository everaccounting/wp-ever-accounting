<?php

namespace Ever_Accounting\Tests\Factories;

class Account_Factory {
	/**
	 * Creates an account in the tests DB.
	 */
	public static function create( $name = 'Test Account', $number = '000001', $opening_balance = 1000, $currency_code = 'USD' ) {
		return \Ever_Accounting\Accounts::insert( array(
			'name'            => $name,
			'currency_code'   => $currency_code,
			'number'          => $number,
			'opening_balance' => $opening_balance,
			'bank_name'       => 'ABC Bank LTD.',
			'bank_phone'      => '1234567890',
			'bank_address'    => '123 South Street, Redwood City, California',
		) );
	}

}
