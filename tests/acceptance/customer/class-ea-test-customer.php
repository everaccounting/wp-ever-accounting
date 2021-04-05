<?php
/**
 * Handle the customer test case.
 *
 * @package     EverAccounting\Test
 * @class       EverAccounting_Tests_customer
 * @version     1.0.2
 */

use EverAccounting\Models\Customer;

defined( 'ABSPATH' ) || exit;

/**
 * Class EverAccounting_Tests_customer.
 * @package EverAccounting\Tests\Customer
 */
class EverAccounting_Tests_customer extends EverAccounting_Unit_Test_Case {
	public function test_create_customer() {

		$customer = eaccounting_insert_customer( array(
			'user_id'		=> 1,
			'name'          => 'Ever Customer',
			'company'		=> 'Byteever',
			'email'         => 'ever@email.com',
			'phone'         => '+12345678',
			'birth_date'    => '1995-08-15',
			'country'       => 'US',
			'website'       => 'http://www.test.com',
			'vat_number'    => '12',
			'currency_code' => 'USD',
			'street'       => 'Test Street',
			'type'          => 'customer',
			'enabled'       => 1,
			'creator_id'    => '',
		) );
		$this->assertNotFalse( $customer->exists() );
		$this->assertEquals( 'Ever Customer', $customer->get_name() );
		$this->assertNotNull( $customer->get_id() );
		$this->assertEquals( 'ever@email.com', $customer->get_email() );
		$this->assertEquals( '+12345678', $customer->get_phone() );
		$this->assertEquals( '1995-08-15', date( 'Y-m-d', strtotime( $customer->get_birth_date() ) ) );
		$this->assertEquals( 'US', $customer->get_country() );
		$this->assertEquals( 'http://www.test.com', $customer->get_website() );
		$this->assertEquals( 'USD', $customer->get_currency_code() );
		$this->assertEquals( 'customer', $customer->get_type() );
		$this->assertEquals( 1, $customer->get_enabled() );
		$this->assertNotNull( $customer->get_date_created() );
		$this->assertEquals( 'Test Street', $customer->get_street() );
		$this->assertNotFalse( $customer->get_enabled() );
	}

	public function test_update_customer(){
		$customer = eaccounting_insert_customer( array(
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

		$error = eaccounting_insert_customer( array(
			'id'            => $contact_id,
			'name'          => 'Ever Customer',
			'email'         => 'ever@email.com',
			'currency_code' => 'USD',
			'type'          => 'vendor',
		) );
	
		$this->assertNotWPError( $error );
		$customer = eaccounting_get_vendor( $contact_id); // so we can read fresh copies from the DB
			
		$this->assertEquals( 'Ever Customer', $customer->get_name() );
		$this->assertEquals( 'ever@email.com', $customer->get_email() );
		$this->assertEquals( 'USD', $customer->get_currency_code() );
		$this->assertEquals( 'vendor', $customer->get_type() );
	}

	public function test_delete_customer(){
		$customer = EverAccounting_Helper_Contact::create_contact();
		$this->assertNotEquals( 0, $customer->get_id() );
		$this->assertNotFalse( eaccounting_delete_customer( $customer->get_id() ) );
	}

	public function test_exception_customer(){

		//currency_code check
		$customer = eaccounting_insert_customer( array(
			'name' 			=> '',
			'currency_code' => 'USD'
		) );
		$this->assertEquals( 'Name is required', $customer->get_error_message() );

		$customer = eaccounting_insert_customer(array(
			'name' => 'John Doe',
			'currency_code' => ''
			
		));
		$this->assertEquals( 'Currency Code is required', $customer->get_error_message() );

		//insert customer with valid data
		$customer = eaccounting_insert_customer( array(
			'name'          => 'John Doe',
			'currency_code' => 'USD',
			'type'          => 'customer',
			'email'         => 'john@doe.com'
		) );
		$this->assertNotFalse( $customer->exists() );
	}

}
