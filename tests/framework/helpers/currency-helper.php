<?php

namespace EverAccounting\Tests\Framework\Helpers;

class Account_Helper {

	public static function create_account( $currency = 'USD' ) {
		return eaccounting_insert_account(
			array(
				'name'            => 'John Doe',
				'number'          => '000001',
				'currency_code'   => $currency,
				'opening_balance' => '100',
				'bank_name'       => 'Bank of america',
				'bank_phone'      => '0123456789',
				'bank_address'    => '',
				'thumbnail_id'    => null,
				'enabled'         => true,
				'date_created'    => date('Y-m-d'),
			),
			false
		);
	}

}
