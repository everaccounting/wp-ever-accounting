<?php

namespace EverAccounting\Tests\Framework\Helpers;

class Customer_Helper {

	public static function create_customer( $save = true, $props = array() ) {
		$default = array(
			'name'          => 'John DOe',
			'company'       => 'Automattic',
			'email'         => 'john@email.com',
			'phone'         => '+12340974',
			'vat_number'    => 'vt-1234',
			'birth_date'    => '1995-02-03',
			'street'        => '6th',
			'state'         => 'New York',
			'postcode'      => '1216',
			'country'       => 'US',
			'website'       => 'http://john@local.com',
			'currency_code' => 'USD',
			'enabled'       => true,
			'date_created'  => date( 'Y-m-d' ),
		);
		$props   = array_merge( $default, $props );

		if ( $save ) {
			return eaccounting_insert_customer( $props, false );
		}

		return $props;
	}

}