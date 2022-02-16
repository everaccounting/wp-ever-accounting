<?php

class Account_Factory {
	/**
	 * Creates a currency in the tests DB.
	 */
	public static function create( $code = 'CAD', $rate = 1 ) {
		return \Ever_Accounting\Currencies::insert( array(
			'name'      => 'Canadian Dollar',
			'code'      => 'CAD',
			'rate'      => 1,
			'position'  => 'before',
			'precision' => 2,
			'symbol'    => '$'
		) );
	}

}
