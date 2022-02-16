<?php

namespace Ever_Accounting\Tests\Factories;

class Contact_Factory {
	/**
	 * Creates a customers in the tests DB.
	 */
	public static function create_customer( $name = 'John Doe', $email = 'john@doe.com', $type = 'customer', $currency_code = 'USD' ) {

		$currency_code = \Ever_Accounting\Currencies::get($currency_code ) ? \Ever_Accounting\Currencies::get($currency_code ) : Currency_Factory::create( 'USD' );

		return \Ever_Accounting\Contacts::insert_customer( array(
			'name'          => $name,
			'email'         => $email,
			'type'          => $type,
			'currency_code' => $currency_code->get_code(),
			'phone'         => '609-972-6928',
			'fax'           => '609-377-7111',
			'address'       => '3167  Whiteman Street',
			'website'       => 'http://john.com',
			'tax_number'    => '3738 24315 73209',
			'creator_id'    => '',
			'date_created'  => '',
		) );
	}

	/**
	 * Creates a vendor in the tests DB.
	 */
	public static function create_vendor( $name = 'John Doe', $email = 'john@doe.com', $type = 'vendor', $currency_code = 'USD' ) {
		$currency_code = \Ever_Accounting\Currencies::get($currency_code ) ? \Ever_Accounting\Currencies::get($currency_code ) : Currency_Factory::create( 'USD' );
		return \Ever_Accounting\Contacts::insert_vendor( array(
			'name'          => $name,
			'email'         => $email,
			'type'          => $type,
			'currency_code' => $currency_code->get_code(),
			'phone'         => '609-972-6928',
			'fax'           => '609-377-7111',
			'address'       => '3167  Whiteman Street',
			'website'       => 'http://john.com',
			'tax_number'    => '3738 24315 73209',
			'creator_id'    => '',
			'date_created'  => '',
		) );
	}

}
