<?php

namespace EverAccounting\Tests\Framework\Helpers;

class Account_Helper {

	public static function create_account( $save = true, $props = array() ) {
		$default = array(
			'name'            => 'Bank of america',
			'number'          => '1000',
			'currency_code'   => 'USD',
			'opening_balance' => '100',
			'bank_name'       => 'Bank of america',
			'bank_phone'      => '0123456789',
			'bank_address'    => '',
			'thumbnail_id'    => null,
			'enabled'         => true,
			'date_created'    => date('Y-m-d'),
		);
		$props = array_merge( $default, $props );

		if ( $save ) {
			return eaccounting_insert_account( $props, false  );
		}

		return $props;
	}

}
