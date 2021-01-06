<?php

/**
 * Class EverAccounting_Helper_Contact.
 *
 * This helper class should ONLY be used for unit tests!.
 */
class EAccounting_Helper_Contact {
	/**
	 * Creates a account in the tests DB.
	 */
	public static function create_contact( $name = 'John Doe', $email = 'john@doe.com', $type = 'customer', $currency_code = 'USD' ) {
		$contact = new \EverAccounting\Contact();
		$contact->set_props( [
			'name'          => $name,
			'email'         => $email,
			'type'          => $type,
			'currency_code' => $currency_code,
			'phone'         => '609-972-6928',
			'fax'           => '609-377-7111',
			'address'       => '3167  Whiteman Street',
			'country'       => 'US',
			'website'       => 'http://john.com',
			'tax_number'    => '3738 24315 73209',
			'note'          => 'Avid food scholar. Certified coffee evangelist. Wannabe pop culture lover.',
			'creator_id'    => '',
			'date_created'  => '',
		] );
		$contact->set_birth_date('5/14/1966');
		$contact->save();

		return $contact;
	}

}
