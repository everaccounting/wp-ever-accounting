<?php

namespace EverAccounting\Tests\Framework\Helpers;

class Currency_Helper {

	public static function create_currency( $code = 'USD', $symbol = '$' ) {
		return eaccounting_insert_account(
			array(
				'name'               => $code,
				'code'               => $code,
				'rate'               => 1.0,
				'symbol'             => $symbol,
				'position'           => 'before',
				'precision'          => 4,
				'decimal_separator'  => '.',
				'thousand_separator' => ','
			),
			false
		);
	}

}
