<?php

namespace Ever_Accounting\Tests\Factories;

class Transfer_Factory {
	/**
	 * Creates a transfer in the tests DB.
	 */
	public static function create( $amount = 500 ) {
		Category_Factory::create('Transfer', 'other' );
		return \Ever_Accounting\Transfers::insert( array(
			'from_account_id' => Account_Factory::create( 'From Account', 10000 )->get_id(),
			'to_account_id'   => Account_Factory::create( 'To Account' )->get_id(),
			'date'            => '2021-08-25',
			'amount'          => $amount,
			'payment_method'  => 'cash'
		) );
	}

}
