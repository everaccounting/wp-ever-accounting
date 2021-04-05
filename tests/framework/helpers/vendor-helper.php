<?php

namespace EverAccounting\Tests\Framework\Helpers;

class Vendor_Helper {

	public static function create_vendor( $save = true, $props = array() ) {
		$default = array(
			'name'          => 'Matt Mullenweg',
			'email'         => 'mat@email.com',
			'phone'         => '+12340974',
			'fax'           => '+245679',
			'birth_date'    => '1995-02-03',
			'address'       => 'New York City',
			'country'       => 'USA',
			'website'       => 'mat@local.com',
			'tax_number'    => 'tx-12345',
			'currency_code' => 'USD',
			'note'          => 'Matt',
			'date_created'  => date( 'Y-m-d' ),
		);
		$props   = array_merge( $default, $props );

		if ( $save ) {
			return eaccounting_insert_vendor( $props, false );
		}

		return $props;
	}

}
