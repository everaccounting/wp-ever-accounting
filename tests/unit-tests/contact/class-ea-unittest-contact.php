<?php

use EverAccounting\Contact;

/**
 * Class EAccounting_Tests_contact.
 * @package EAccounting\Tests\contact
 */
class EAccounting_Tests_contact extends EAccounting_Unit_Test_Case {

	public function test_with_required_params() {
		$contact = eaccounting_insert_contact( [
			'name' => 'John Doe',
			'type' => 'customer',
		] );

		$this->assertNotFalse( $contact->exists() );
	}

	public function test_duplicate_email() {
		$contact = eaccounting_insert_contact( [
			'name'  => 'John Doe',
			'type'  => 'customer',
			'email' => 'john@doe.com',
		] );

		$this->assertNotFalse( $contact->exists() );

		$contact = eaccounting_insert_contact( [
			'name'  => 'John Deo',
			'type'  => 'customer',
			'email' => 'john@doe.com',
		] );

		var_dump($contact);

//		$this->assertWPError('The email address is already in used', $contact->get_error_message());
	}


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
//		$this->assertEquals( 1, $contact->get_company_id() );
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
