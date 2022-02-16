<?php
namespace Ever_Accounting\Tests\Factories;

class Currency_Factory {
	/**
	 * Creates a currency in the tests DB.
	 */
	public static function create( $code = 'CAD', $rate = 1 ) {
		return \Ever_Accounting\Currencies::insert( array(
			'name'      => 'Canadian Dollar',
			'code'      => $code,
			'rate'      => $rate,
			'position'  => 'before',
			'precision' => 2,
			'symbol'    => '$'
		) );
	}

}
