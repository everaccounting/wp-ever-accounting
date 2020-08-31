<?php

use EverAccounting\Contact;

/**
 * Class EAccounting_Tests_Vendor.
 * @package EAccounting\Tests\Contact
 */

class EAccounting_Tests_Vendor extends EAccounting_Unit_Test_Case {
	public function test_create_vendor() {
		$vendors = eaccounting_insert_contact(
			array(
				'name'          => 'Test Vendor',
				'email'         => 'vendor@email.com',
				'phone'         => +12345678,
				'fax'           => +12345678,
				'birth_date'    => '1984-05-20',
				'address'       => 'Test Address',
				'country'       => 'US',
				'website'       => 'http://www.test.com',
				'currency_code' => 'USD',
				'type' => 'vendor'
			)
		);

		$this->assertNotFalse( $vendors->exists() );

		$this->assertEquals( 'Test Vendor', $vendors->get_name() );
		$this->assertNotNull( $vendors->get_id() );
		$this->assertEquals( 'vendor@email.com', $vendors->get_email() );
		$this->assertEquals( '12345678', $vendors->get_phone() );
		$this->assertEquals( '12345678', $vendors->get_fax() );
		$this->assertEquals( '1984-05-20', date( 'Y-m-d', strtotime( $vendors->get_birth_date() ) ) );
		$this->assertEquals( 'Test Address', $vendors->get_address() );
		$this->assertEquals( 'US', $vendors->get_country() );
		$this->assertEquals( 'http://www.test.com', $vendors->get_website() );
		$this->assertEquals( '', $vendors->get_tax_number() );
		$this->assertEquals( 'USD', $vendors->get_currency_code() );
		$this->assertEquals( 'vendor', $vendors->get_type() );
		$this->assertEquals( 1, $vendors->get_enabled() );
		$this->assertEquals( 1, $vendors->get_company_id() );
		$this->assertNotNull( $vendors->get_date_created() );
	}

	public function test_update_vendor(){
		$vendors = eaccounting_insert_contact(array(
			'name' => 'Test Vendor',
			'email' => 'vendor@email.com',
			'country' => 'US',
			'type' => 'vendor',
			'currency_code' => 'USD'
		));
		$vendor_id = $vendors->get_id();

		$this->assertNotFalse( $vendors->exists() );
		$this->assertEquals( 'Test Vendor', $vendors->get_name() );
		$this->assertEquals( 'vendor@email.com', $vendors->get_email() );
		$this->assertEquals( 'US', $vendors->get_country() );
		$this->assertEquals( 'vendor', $vendors->get_type() );
		$this->assertEquals( 'USD', $vendors->get_currency_code() );

		$errors = eaccounting_insert_contact(array(
			'id' => $vendor_id,
			'name' => 'John Doe',
			'email' => 'vendor@email.com',
			'country' => 'US',
			'type' => 'vendor',
			'currency_code' => 'EUR'
		));
		$this->assertNotWPError( $errors );

		$vendors = eaccounting_get_contact( $vendor_id );
		$this->assertEquals( 'John Doe', $vendors->get_name() );
		$this->assertEquals( 'vendor@email.com', $vendors->get_email() );
		$this->assertEquals( 'EUR', $vendors->get_currency_code() );
		$this->assertEquals( 'vendor', $vendors->get_type() );
		$this->assertEquals( 'US', $vendors->get_country() );

	}
	public function test_delete_vendor(){
		$vendor = EAccounting_Helper_Contact::create_contact();
		$this->assertNotEquals( 0, $vendor->get_id() );
		$this->assertNotFalse( eaccounting_delete_contact( $vendor->get_id() ) );
	}

	public function test_exception_vendor() {

		//currency_code check
		$vendor = eaccounting_insert_contact( array(
			'currency_code' => '',
		) );
		$this->assertNotEquals( 'Currency code is required', $vendor->get_error_message() );

		//contact_name check
		$vendor = eaccounting_insert_contact( array(
			'currency_code' => 'USD',
			'name'          => '',
		) );
		$this->assertNotEquals( 'Contact Name is required', $vendor->get_error_message() );

		//currency_type check
		$vendor = eaccounting_insert_contact( array(
			'currency_code' => 'USD',
			'name'          => 'John Doe',
			'type'          => ''
		) );
		$this->assertNotEquals( 'Contact Type is required', $vendor->get_error_message() );

		//contact user_id check
		$vendor = eaccounting_insert_contact( array(
			'currency_code' => 'USD',
			'name'          => 'John Doe New',
			'type'          => 'vendor',
			'user_id'       => 10,
		) );
		$this->assertNotEquals( 'Invalid User ID', $vendor->get_error_message() );

		//insert contact with valid data
		$vendor = eaccounting_insert_contact( array(
			'currency_code' => 'USD',
			'name'          => 'John Doe',
			'type'          => 'vendor',
			'email'         => 'john@doe.com'
		) );

		$this->assertNotFalse( $vendor->exists() );

		//duplicate email check
		$newContact = eaccounting_insert_contact( array(
			'currency_code' => 'USD',
			'name'          => 'John Doe New',
			'type'          => 'vendor',
			'email'         => 'john@doe.com'
		) );
		$this->assertNotEquals( 'The email address is already in used', $newContact->get_error_message() );

	}
}
