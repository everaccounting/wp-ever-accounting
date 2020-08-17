<?php
use EverAccounting\Contact;
/**
 * Class EAccounting_Tests_Contact.
 * @package EAccounting\Tests\Contact
 */
class EAccounting_Tests_Contact extends EAccounting_Unit_Test_Case {

	public function test_create_contact() {
		$contact = new Contact();
		$contact->set_props( [
			'name'          => 'Marcy P Frazier',
			'email'         => 'ni4v3s21us@temporary-mail.net',
			'type'          => 'customer',
			'currency_code' => 'USD',
			'phone'         => '609-972-6928',
			'fax'           => '609-377-7111',
			'address'       => '3167  Whiteman Street',
			'country'       => 'US',
			'website'       => 'http://john.com',
			'tax_number'    => '3738 24315 73209',
			'note'          => 'Avid food scholar. Certified coffee evangelist. Wannabe pop culture lover.',
			'creator_id'    => '',
			'company_id'    => '',
			'date_created'  => '',
		] );
		$contact->set_birth_date( '5/14/1966' );
		$contact->save();

		$this->assertEquals( 'Marcy P Frazier', $contact->get_name() );
		$this->assertNotNull( $contact->get_id() );
		$this->assertEquals( 'ni4v3s21us@temporary-mail.net', $contact->get_email() );
		$this->assertEquals( 'customer', $contact->get_type() );
		$this->assertEquals( 'USD', $contact->get_currency_code() );
		$this->assertEquals( '609-972-6928', $contact->get_phone() );
		$this->assertEquals( '609-377-7111', $contact->get_fax() );
		$this->assertEquals( '3167  Whiteman Street', $contact->get_address() );
		$this->assertEquals( 'http://john.com', $contact->get_website() );
		$this->assertEquals( '3738 24315 73209', $contact->get_tax_number() );
		$this->assertEquals( 'Avid food scholar. Certified coffee evangelist. Wannabe pop culture lover.', $contact->get_note() );
		$this->assertEquals( 1, $contact->get_company_id() );
		$this->assertNotNull( $contact->get_date_created() );
		$this->assertNotNull( $contact->get_date_created() );
	}

	public function test_update_contact() {
		$contact    = EAccounting_Helper_Contact::create_contact( 'John Doe', 'john@doe.com' );
		$contact_id = $contact->get_id();

		$this->assertEquals( 'John Doe', $contact->get_name() );
		$this->assertEquals( 'john@doe.com', $contact->get_email() );
		$contact->set_props( [
			'name'          => 'Jane Doe',
			'email'         => 'jane@doe.com',
			'type'          => 'vendor',
			'currency_code' => 'BDT',
			'phone'         => '12342547',
			'fax'           => '86757643456',
			'address'       => '1001  Whiteman Street',
			'country'       => 'BD',
			'website'       => 'http://jane.com',
			'tax_number'    => '3738 24315',
			'note'          => 'lorem ipsum dolor',
		] );

		$contact->set_birth_date( '01/30/1988' );
		$contact->save();

		$contact = new Contact( $contact_id ); // so we can read fresh copies from the DB

//		$this->assertEquals( 'Jane Doe', $contact->get_name() );
//		$this->assertEquals( 'jane@doe.com', $contact->get_email() );
//		$this->assertEquals( '10000.0000', $contact->get_opening_balance() );
//		$this->assertEquals( 'BDT', $contact->get_currency_code() );
	}
//
//
//	public function test_delete_contact() {
//		$account = EAccounting_Helper_Contact::create_contact();
//		$this->assertNotEquals( 0, $account->get_id() );
//		$account->delete();
//		$this->assertEquals( 0, $account->get_id() );
//	}
//
//
//	public function test_exception_contact_number() {
//		$account = EAccounting_Helper_Account::create_contact( 'Another Account 1', '1000' );
//
//		try {
//			EAccounting_Helper_Account::create_contact( 'Another Account 2', '1000' );
//		} catch ( Exception $e ) {
//			$this->assertEquals( "Duplicate account number.", $e->getMessage() );
//		}
//
//		//name check
//		try {
//			$account = new EAccounting_Account();
//			$account->set_name( '' );
//			$account->save();
//		} catch ( Exception $e ) {
//
//			$this->assertEquals( "Account Name is required", $e->getMessage() );
//		}
//
//		//number check
//		try {
//			$account = new EAccounting_Account();
//			$account->set_name( 'Exception account' );
//			$account->set_number( '' );
//			$account->save();
//		} catch ( Exception $e ) {
//			$this->assertEquals( "Account Number is required", $e->getMessage() );
//		}
//
//		try {
//			$account = new EAccounting_Account();
//			$account->set_name( 'Exception account' );
//			$account->set_number( '090909' );
//			$account->set_currency_code( '' );
//			$account->save();
//		} catch ( Exception $e ) {
//			$this->assertEquals( "Currency code is required", $e->getMessage() );
//		}
//
//		try {
//			$account = new EAccounting_Account();
//			$account->set_name( 'Exception account' );
//			$account->set_number( '090909' );
//			$account->set_currency_code( 'AUD' );
//			$account->save();
//		} catch ( Exception $e ) {
//			$this->throwAnException( $e->getMessage() );
//		}
//
//	}

}
