<?php

namespace EverAccounting\Tests\Framework\Helpers;

class Contact_Helper {
	public static function create_customer( $save = true, $props = array() ) {
		$default = array(
			'name'          => 'John Doe',
			'email'         => 'john@customer.com',
			'currency_code' => 'USD',
			'phone'         => '609-972-6928',
			'fax'           => '609-377-7111',
			'address'       => '3167  Whiteman Street',
			'country'       => 'US',
			'website'       => 'http://john.com',
			'tax_number'    => '3738 24315 73209',
			'note'          => 'Avid food scholar. Certified coffee evangelist. Wannabe pop culture lover.',
			'creator_id'    => '',
			'date_created'    => date('Y-m-d'),
		);
		$props = array_merge( $default, $props );

		if ( $save ) {
			return eaccounting_insert_customer( $props, false  );
		}

		return $props;
	}

	public static function create_vendor( $save = true, $props = array() ) {
		$default = array(
			'name'          => 'John Doe',
			'email'         => 'john@vendor.com',
			'currency_code' => 'USD',
			'phone'         => '609-972-6928',
			'fax'           => '609-377-7111',
			'address'       => '3167  Whiteman Street',
			'country'       => 'US',
			'website'       => 'http://john.com',
			'tax_number'    => '3738 24315 73209',
			'note'          => 'Avid food scholar. Certified coffee evangelist. Wannabe pop culture lover.',
			'creator_id'    => '',
			'date_created'    => date('Y-m-d'),
		);
		$props = array_merge( $default, $props );

		if ( $save ) {
			return eaccounting_insert_vendor( $props, false  );
		}

		return $props;
	}
}
