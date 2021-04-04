<?php

namespace EverAccounting\Tests\Framework\Helpers;

class Currency_Helper {

	public static function create_currency(  $save = true, $props = array()  ) {
		$default = array(
			'name'               => 'USD',
			'code'               => 'USD',
			'rate'               => 1.0,
			'symbol'             => '$',
			'position'           => 'before',
			'precision'          => 4,
			'decimal_separator'  => '.',
			'thousand_separator' => ','
		);

		$props = array_merge( $default, $props );
		if ( $save ) {
			return eaccounting_insert_currency( $props, false  );
		}

		return $props;
	}

}
