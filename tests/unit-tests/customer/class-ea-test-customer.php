<?php

use EAccounting\Contact;

/**
 * Class EverAccounting_Tests_customer.
 * @package EAccounting\Tests\Customer
 */
class EverAccounting_Tests_customer extends EverAccounting_Unit_Test_Case {
	public function test_create_customer() {

		$customer = eaccounting_insert_contact( array(
			'name'          => 'Ever Customer',
			'email'         => 'ever@email.com',
			'phone'         => '+12345678',
			'fax'           => '+12345678',
			'birth_date'    => '1995-08-15',
			'address'       => 'Test Address',
			'country'       => 'US',
			'website'       => 'http://www.test.com',
			'tax_number'    => '12',
			'currency_code' => 'USD',
			'type'          => 'customer',
			'note'          => 'Test Note',
			'enabled'       => 1,
			'creator_id'    => '',
		) );
		$this->assertNotFalse( $customer->exists() );

		$this->assertEquals( 'Ever Customer', $customer->get_name() );
		$this->assertNotNull( $customer->get_id() );
		$this->assertEquals( 'ever@email.com', $customer->get_email() );
		$this->assertEquals( '+12345678', $customer->get_phone() );
		$this->assertEquals( '+12345678', $customer->get_fax() );
		$this->assertEquals( '1995-08-15', date( 'Y-m-d', strtotime( $customer->get_birth_date() ) ) );
		$this->assertEquals( 'Test Address', $customer->get_address() );
		$this->assertEquals( 'US', $customer->get_country() );
		$this->assertEquals( 'http://www.test.com', $customer->get_website() );
		$this->assertEquals( '12', $customer->get_tax_number() );
		$this->assertEquals( 'USD', $customer->get_currency_code() );
		$this->assertEquals( 'customer', $customer->get_type() );
		$this->assertEquals( 'Test Note', $customer->get_note() );
		$this->assertEquals( 1, $customer->get_enabled() );
		$this->assertNotNull( $customer->get_date_created() );

	}

	public function test_update_customer(){
		$customer = eaccounting_insert_contact( array(
			'name'          => 'John Doe',
			'email'         => 'john@doe.com',
			'currency_code' => 'USD',
			'type'          => 'customer',
		) );

		$contact_id = $customer->get_id();
		$this->assertNotFalse( $customer->exists() );

		$this->assertEquals( 'John Doe', $customer->get_name() );
		$this->assertEquals( 'john@doe.com', $customer->get_email() );
		$this->assertEquals( 'USD', $customer->get_currency_code() );
		$this->assertEquals( 'customer', $customer->get_type() );

		$error = eaccounting_insert_contact( array(
			'id'            => $contact_id,
			'name'          => 'Ever Customer',
			'email'         => 'ever@email.com',
			'currency_code' => 'USD',
			'type'          => 'vendor',
		) );

		$this->assertNotWPError( $error );

		$customer = eaccounting_get_contact( $contact_id ); // so we can read fresh copies from the DB

		$this->assertEquals( 'Ever Customer', $customer->get_name() );
		$this->assertEquals( 'ever@email.com', $customer->get_email() );
		$this->assertEquals( 'USD', $customer->get_currency_code() );
		$this->assertEquals( 'vendor', $customer->get_type() );
	}

	public function test_delete_customer(){
		$customer = EverAccounting_Helper_Contact::create_contact();
		$this->assertNotEquals( 0, $customer->get_id() );
		$this->assertNotFalse( eaccounting_delete_contact( $customer->get_id() ) );
	}

	public function test_exception_customer(){

		//currency_code check
		$customer = eaccounting_insert_contact( array(
			'currency_code' => '',
		) );
		$this->assertEquals( 'Currency is required', $customer->get_error_message() );

		$customer = eaccounting_insert_contact(array(
			'currency_code' => 'USD',
			'name' => ''
		));
		$this->assertEquals('Name is required',$customer->get_error_message());


		$customer = eaccounting_insert_contact( array(
			'currency_code' => 'USD',
			'name'          => 'John Doe',
			'type'          => ''
		) );
		$this->assertEquals( 'Type is required', $customer->get_error_message() );

		//contact user_id check
		$customer = eaccounting_insert_contact( array(
			'currency_code' => 'USD',
			'name'          => 'John Doe New',
			'type'          => 'customer',
			'user_id'       => 10,
		) );
		$this->assertEquals( 'Invalid WP User ID', $customer->get_error_message() );

		//insert customer with valid data
		$customer = eaccounting_insert_contact( array(
			'currency_code' => 'USD',
			'name'          => 'John Doe',
			'type'          => 'customer',
			'email'         => 'john@doe.com'
		) );
		$this->assertNotFalse( $customer->exists() );

		$customer = eaccounting_insert_contact(array(
			'currency_code' => 'USD',
			'name' => 'Ever Customer',
			'type' => 'customer',
			'email' => 'john@doe.com'
		));
		$this->assertEquals('The email address is already in used.',$customer->get_error_message());
	}

}
