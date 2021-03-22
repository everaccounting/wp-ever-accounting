<?php
/**
 * Handle the contact test case.
 *
 * @package     EverAccounting\Test
 * @class       EverAccounting_Tests_contact
 * @version     1.0.2
 */

use EverAccounting\Models\Contact;

defined( 'ABSPATH' ) || exit;

/**
 * Class EverAccounting_Tests_contact.
 * @package EverAccounting\Tests\contact
 */
class EverAccounting_Tests_contact extends EverAccounting_Unit_Test_Case {

	public function test_create_contact() {
		$contact = eaccounting_insert_customer(
				array(
				'user_id' 		=> '1',
				'name'          => 'John Doe',
				'company'		=> 'Byteever',
				'email'         => 'john@doe.com',
				'phone'         => '+12345678',
				'birth_date'    => '1995-08-15',
				'street'        => 'Bourbon Street',
				'city'          => 'New York',
				'state'			=> 'california',
				'postcode'		=> '70076', 
				'country'       => 'US',
				'website'       => 'http://www.test.com',
				'vat_number'    => '12',
				'currency_code' => 'USD',
				'type'          => 'customer',
				'enabled'       => 1,
				'creator_id'    => '',
			) 
		);

		$this->assertNotFalse( $contact->exists() );
		$this->assertEquals( 'John Doe', $contact->get_name() );
		$this->assertNotNull( $contact->get_id() );
		$this->assertEquals( 'john@doe.com', $contact->get_email() );
		$this->assertEquals( '+12345678', $contact->get_phone() );
		$this->assertEquals( '1995-08-15', date( 'Y-m-d', strtotime( $contact->get_birth_date() ) ) );
		$this->assertEquals( 'US', $contact->get_country() );
		$this->assertEquals( 'http://www.test.com', $contact->get_website() );
		$this->assertEquals( '12', $contact->get_vat_number() );
		$this->assertEquals( 'USD', $contact->get_currency_code() );
		$this->assertEquals( 'customer', $contact->get_type() );
		$this->assertEquals( 1, $contact->get_enabled() );
		$this->assertNotNull( $contact->get_date_created() );
		$this->assertEquals( 'New York', $contact->get_city() );
		$this->assertEquals( 'california', $contact->get_state() );
		$this->assertEquals( 'Bourbon Street', $contact->get_street() );
	}

	public function test_update_contact() {
		$contact = eaccounting_insert_customer( array(
			'name'          => 'John Doe',
			'email'         => 'john@doe.com',
			'currency_code' => 'USD',
			'type'          => 'customer',
		) );

		$contact_id = $contact->get_id();
		$this->assertNotFalse( $contact->exists() );

		$this->assertEquals( 'John Doe', $contact->get_name() );
		$this->assertEquals( 'john@doe.com', $contact->get_email() );
		$this->assertEquals( 'USD', $contact->get_currency_code() );
		$this->assertEquals( 'customer', $contact->get_type() );

		$error = eaccounting_insert_customer( array(
			'id'            => $contact_id,
			'name'          => 'John Doe Updated',
			'email'         => 'john12@doe.com',
			'currency_code' => 'USD',
			'type'          => 'vendor',
		) );
	
		$this->assertNotWPError( $error );

		$contact = eaccounting_get_vendor( $contact_id ); // so we can read fresh copies from the DB

		$this->assertEquals( 'John Doe Updated', $contact->get_name() );
		$this->assertEquals( 'john12@doe.com', $contact->get_email() );
		$this->assertEquals( 'USD', $contact->get_currency_code() );
		$this->assertEquals( 'vendor', $contact->get_type() );
	}

	public function test_delete_customer() {
		$contact = EverAccounting_Helper_Contact::create_contact();
		$this->assertNotEquals( null, $contact->get_id() );
		$this->assertNotFalse( eaccounting_delete_customer( $contact->get_id() ) );
	}

	public function test_exception_contact() {

		//customer name check
		$contact = eaccounting_insert_customer( array(
			'name' => '',
		) );
		$this->assertEquals( 'Name is required', $contact->get_error_message() );
		
		//currency chekc
		$contact = eaccounting_insert_customer( array(
			'name'          => 'John Doe',
			'currency_code' => ''
		) );

		$this->assertEquals( 'Currency Code is required', $contact->get_error_message() );

		//insert contact with valid data
		$contact = eaccounting_insert_vendor( array(
			'currency_code' => 'USD',
			'name'          => 'John Doe',
			'type'          => 'vendor',
			'email'         => 'john@doe.com'
		) );

		$this->assertNotFalse( $contact->exists() );

	}

	public function test_with_required_params() {
		$contact = eaccounting_insert_customer( [
			'name' => 'John Doe',
			'type' => 'customer',
		] );
		$this->assertWPError( $contact );
	}
}
