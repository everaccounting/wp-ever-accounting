<?php
/**
 * Handle the vendor test case.
 *
 * @package     EverAccounting\Test
 * @class       EverAccounting_Tests_Vendor
 * @version     1.0.2
 */

use EverAccounting\Models\Vendor;

/**
 * Class EverAccounting_Tests_Vendor.
 * @package EverAccounting\Tests\Contact
 */

class EverAccounting_Tests_Vendor extends EverAccounting_Unit_Test_Case {
	public function test_create_vendor() {
		$vendors = eaccounting_insert_vendor(
			array(
				'name'          => 'Test Vendor',
				'email'         => 'vendor@email.com',
				'phone'         => +12345678,
				'fax'           => +12345678,
				'birth_date'    => '1984-05-20',
				'street'       => 'Test Address',
				'country'       => 'US',
				'website'       => 'http://www.test.com',
				'currency_code' => 'USD',
				'type' 			=> 'vendor'
			)
		);
		$this->assertNotFalse( $vendors->exists() );
		$this->assertEquals( 'Test Vendor', $vendors->get_name() );
		$this->assertNotNull( $vendors->get_id() );
		$this->assertEquals( 'vendor@email.com', $vendors->get_email() );
		$this->assertEquals( '12345678', $vendors->get_phone() );
		$this->assertEquals( '1984-05-20', date( 'Y-m-d', strtotime( $vendors->get_birth_date() ) ) );
		$this->assertEquals( 'US', $vendors->get_country() );
		$this->assertEquals( 'http://www.test.com', $vendors->get_website() );
		$this->assertEquals( 'USD', $vendors->get_currency_code() );
		$this->assertEquals( 'Test Address', $vendors->get_street());
		$this->assertEquals( 'vendor', $vendors->get_type() );
		$this->assertEquals( 1, $vendors->get_enabled() );
		$this->assertNotNull( $vendors->get_date_created() );
	}

	public function test_update_vendor(){
		$vendors = eaccounting_insert_vendor(array(
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

		$errors = eaccounting_insert_vendor(array(
			'id' => $vendor_id,
			'name' => 'John Doe',
			'email' => 'vendor@email.com',
			'country' => 'US',
			'type' => 'vendor',
			'currency_code' => 'EUR'
		));
		$this->assertNotWPError( $errors );

		$vendors = eaccounting_get_vendor( $vendor_id );
		$this->assertEquals( 'John Doe', $vendors->get_name() );
		$this->assertEquals( 'vendor@email.com', $vendors->get_email() );
		$this->assertEquals( 'EUR', $vendors->get_currency_code() );
		$this->assertEquals( 'vendor', $vendors->get_type() );
		$this->assertEquals( 'US', $vendors->get_country() );

	}
	public function test_delete_vendor(){
		$vendor = EverAccounting_Helper_Contact::create_contact();
		$this->assertNotEquals( 0, $vendor->get_id() );
		$this->assertFalse(eaccounting_delete_vendor($vendor->get_id()));
	}

	public function test_exception_vendor() {

		//currency_code check
		$vendor = eaccounting_insert_vendor( array(
			'currency_code' => '',
			'name'			=> 'Sylverster Stallon'
		) );
		$this->assertNotEquals( 'Currency code is required', $vendor->get_error_message() );

		//contact_name check
		$vendor = eaccounting_insert_vendor( array(
			'currency_code' => 'USD',
			'name'          => '',
		) );
		$this->assertNotEquals( 'Contact Name is required', $vendor->get_error_message() );

		//insert contact with valid data
		$vendor = eaccounting_insert_vendor( array(
			'currency_code' => 'USD',
			'name'          => 'John Doe',
			'type'          => 'vendor',
			'email'         => 'john@doe.com'
		) );

		$this->assertNotFalse( $vendor->exists() );

	}
}
