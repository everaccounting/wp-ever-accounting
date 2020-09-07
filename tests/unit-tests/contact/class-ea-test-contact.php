<?php

use EverAccounting\Contact;

/**
 * Class EAccounting_Tests_contact.
 * @package EAccounting\Tests\contact
 */
class EAccounting_Tests_contact extends EAccounting_Unit_Test_Case {

	public function test_create_contact() {
		$contact = eaccounting_insert_contact( array(
			'name'          => 'John Doe',
			'email'         => 'john@doe.com',
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

		$this->assertNotFalse( $contact->exists() );

		$this->assertEquals( 'John Doe', $contact->get_name() );
		$this->assertNotNull( $contact->get_id() );
		$this->assertEquals( 'john@doe.com', $contact->get_email() );
		$this->assertEquals( '+12345678', $contact->get_phone() );
		$this->assertEquals( '+12345678', $contact->get_fax() );
		$this->assertEquals( '1995-08-15', date( 'Y-m-d', strtotime( $contact->get_birth_date() ) ) );
		$this->assertEquals( 'Test Address', $contact->get_address() );
		$this->assertEquals( 'US', $contact->get_country() );
		$this->assertEquals( 'http://www.test.com', $contact->get_website() );
		$this->assertEquals( '12', $contact->get_tax_number() );
		$this->assertEquals( 'USD', $contact->get_currency_code() );
		$this->assertEquals( 'customer', $contact->get_type() );
		$this->assertEquals( 'Test Note', $contact->get_note() );
		$this->assertEquals( 1, $contact->get_enabled() );
		$this->assertNotNull( $contact->get_date_created() );
	}

	public function test_update_contact() {
		$contact = eaccounting_insert_contact( array(
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

		$error = eaccounting_insert_contact( array(
			'id'            => $contact_id,
			'name'          => 'John Doe Updated',
			'email'         => 'john12@doe.com',
			'currency_code' => 'USD',
			'type'          => 'vendor',
		) );

		$this->assertNotWPError( $error );

		$contact = eaccounting_get_contact( $contact_id ); // so we can read fresh copies from the DB

		$this->assertEquals( 'John Doe Updated', $contact->get_name() );
		$this->assertEquals( 'john12@doe.com', $contact->get_email() );
		$this->assertEquals( 'USD', $contact->get_currency_code() );
		$this->assertEquals( 'vendor', $contact->get_type() );
	}

	public function test_delete_contact() {
		$contact = EAccounting_Helper_Contact::create_contact();
		$this->assertNotEquals( 0, $contact->get_id() );
		$this->assertNotFalse( eaccounting_delete_contact( $contact->get_id() ) );
	}

	public function test_exception_contact() {

		//currency_code check
		$contact = eaccounting_insert_contact( array(
			'currency_code' => '',
		) );
		$this->assertNotEquals( 'Currency code is required', $contact->get_error_message() );

		//contact_name check
		$contact = eaccounting_insert_contact( array(
			'currency_code' => 'USD',
			'name'          => '',
		) );
		$this->assertNotEquals( 'Contact Name is required', $contact->get_error_message() );

		//currency_type check
		$contact = eaccounting_insert_contact( array(
			'currency_code' => 'USD',
			'name'          => 'John Doe',
			'type'          => ''
		) );
		$this->assertNotEquals( 'Contact Type is required', $contact->get_error_message() );

		//contact user_id check
		$contact = eaccounting_insert_contact( array(
			'currency_code' => 'USD',
			'name'          => 'John Doe New',
			'type'          => 'vendor',
			'user_id'       => 10,
		) );
		$this->assertNotEquals( 'Invalid User ID', $contact->get_error_message() );

		//insert contact with valid data
		$contact = eaccounting_insert_contact( array(
			'currency_code' => 'USD',
			'name'          => 'John Doe',
			'type'          => 'vendor',
			'email'         => 'john@doe.com'
		) );

		$this->assertNotFalse( $contact->exists() );

		//duplicate email check
		$newContact = eaccounting_insert_contact( array(
			'currency_code' => 'USD',
			'name'          => 'John Doe New',
			'type'          => 'vendor',
			'email'         => 'john@doe.com'
		) );
		$this->assertNotEquals( 'The email address is already in used', $newContact->get_error_message() );

	}

//
//	public function test_with_required_params() {
//		$contact = eaccounting_insert_contact( [
//			'name' => 'John Doe',
//			'type' => 'customer',
//		] );
//
//		$this->assertNotFalse( $contact->exists() );
//	}
//
//	public function test_duplicate_email() {
//		$contact = eaccounting_insert_contact( [
//			'name'  => 'John Doe',
//			'type'  => 'customer',
//			'email' => 'john@doe.com',
//		] );
//
//		$this->assertNotFalse( $contact->exists() );
//
//		$contact = eaccounting_insert_contact( [
//			'name'  => 'John Deo',
//			'type'  => 'customer',
//			'email' => 'john@doe.com',
//		] );
//
//		var_dump($contact);
//
////		$this->assertWPError('The email address is already in used', $contact->get_error_message());
//	}


//	public function test_create_contact() {
//		$contact = eaccounting_insert_contact( array(
//			'name'  => 'Test contact',
//			'type'  => 'expense',
//			'color' => 'red',
//		) );
//
//		$this->assertNotFalse( $contact->exists() );
//
//		$this->assertEquals( 'Test contact', $contact->get_name() );
//		$this->assertNotNull( $contact->get_id() );
//		$this->assertEquals( 'expense', $contact->get_type() );
//		$this->assertEquals( 'red', $contact->get_color() );
//		$this->assertNotNull( $contact->get_date_created() );
//	}


//	public function test_update_contact() {
//		$contact    = eaccounting_insert_contact( array(
//			'name'  => 'New contact',
//			'type'  => 'expense',
//			'color' => 'red',
//		) );
//		$contact_id = $contact->get_id();
//		$this->assertNotFalse( $contact->exists() );
//		$this->assertEquals( 'New contact', $contact->get_name() );
//		$this->assertEquals( 'expense', $contact->get_type() );
//		$error = eaccounting_insert_contact( array(
//			'id'    => $contact_id,
//			'name'  => 'Updated contact',
//			'type'  => 'income',
//			'color' => 'blue',
//		) );
//
//		$this->assertNotWPError($error);
//
//		$contact = eaccounting_get_contact( $contact_id ); // so we can read fresh copies from the DB
//
//		$this->assertEquals( 'Updated contact', $contact->get_name() );
//		$this->assertEquals( 'income', $contact->get_type() );
//		$this->assertEquals( 'blue', $contact->get_color() );
//	}

//
//	public function test_delete_contact() {
//		$contact = EAccounting_Helper_contact::create_contact();
//		$this->assertNotEquals( 0, $contact->get_id() );
//		$this->assertNotFalse( eaccounting_delete_contact( $contact->get_id() ) );
//	}


//	public function test_exception_contact_number() {
//		$contact = EAccounting_Helper_contact::create_contact( 'Another contact 1', 'income' );
//		try {
//			EAccounting_Helper_contact::create_contact( 'Another contact 1', 'income' );
//		} catch ( Exception $e ) {
//			$this->assertEquals( "Duplicate contact name.", $e->getMessage() );
//		}
//
//		//name check
//		try {
//			$contact = new contact();
//			$contact->set_name( '' );
//			$contact->save();
//		} catch ( Exception $e ) {
//			$this->assertEquals( "contact name is required", $e->getMessage() );
//		}
//
//		//type check
//		try {
//			$contact = new contact();
//			$contact->set_name( 'Exception account' );
//			$contact->set_type( '' );
//			$contact->save();
//		} catch ( Exception $e ) {
//			$this->assertEquals( "contact type is required", $e->getMessage() );
//		}
//
//		try {
//			$contact = new contact();
//			$contact->set_name( 'Exception account' );
//			$contact->set_type( 'income' );
//			$contact->save();
//		} catch ( Exception $e ) {
//			$this->throwAnException( $e->getMessage() );
//		}
//
//	}
//
//	public function test_contact_functions() {
//		$contact = eaccounting_insert_contact( array(
//			'name'  => 'Income contact',
//			'type'  => 'income',
//			'color' => 'red'
//		) );
//
//		$this->assertNotNull( $contact->get_id() );
//
//		$updated = eaccounting_insert_contact( array(
//			'id'   => $contact->get_id(),
//			'name' => 'contact Updated again'
//		) );
//		$this->assertEquals( "contact Updated again", $updated->get_name() );
//		$this->assertEquals( "income", $updated->get_type() );
//		$this->assertEquals( "red", $updated->get_color() );
//
//		$this->assertEquals( true, eaccounting_delete_contact( $contact->get_id() ) );
//	}
}
