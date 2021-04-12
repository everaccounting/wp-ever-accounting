<?php

namespace EverAccounting\Tests\Framework\Helpers;

class Vendor_Helper {

	public static function create_vendor( $save = true, $props = array() ) {
		$default = array(
			'name'          => 'Matt Mullenweg',
			'company'       => 'Automattic',
			'email'         => 'mat@email.com',
			'phone'         => '+12340974',
			'fax'           => '+245679',
			'birth_date'    => '1995-02-03',
			'street'        => '6th',
			'state'         => 'New York',
			'postcode'      => '1216',
			'country'       => 'US',
			'website'       => 'http://mat@local.com',
			'vat_number'    => 'vat-12345',
			'currency_code' => 'USD',
			'enabled'       => true,
			'note'          => 'Matt',
			'thumbnail_id'  => null,
			'date_created'  => date( 'Y-m-d' ),
		);
		$props   = array_merge( $default, $props );

		if ( $save ) {
			return eaccounting_insert_vendor( $props, false );
		}

		return $props;
	}

}
