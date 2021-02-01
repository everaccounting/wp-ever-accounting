<?php

/**
 * Class EverAccounting_Helper_Currency.
 *
 * This helper class should ONLY be used for unit tests!.
 */
class EverAccounting_Helper_Currency {
	/**
	 * Creates a currency in the tests DB.
	 */
	public static function create_currency( $code = 'CAD', $rate = 1 ) {
		return $currency = eaccounting_insert_currency( array(
			'name' => 'Canadian Dollar',
			'code' => 'CAD',
			'rate' => 1,
			'position' => 'before',
			'precision' => 2,
			'symbol' => '$'
		) );
	}

}
