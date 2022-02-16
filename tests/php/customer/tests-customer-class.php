<?php
/**
 * Ever_Accounting CustomerTest Handler
 *
 * @since    1.1.3
 * @package  Ever_Accounting\Tests
 * @class    Tests_Customer
 */

namespace Ever_Accounting\Tests;

use Ever_Accounting\Contacts;
use Ever_Accounting\Tests\Factories\Contact_Factory;
use Ever_Accounting\Tests\Factories\Currency_Factory;

defined( 'ABSPATH' ) || exit();

/**
 * Class Tests_Currency_Class.
 * @package EverAccounting\Tests\Customer
 */
class Tests_Customer_Class extends \WP_UnitTestCase {
	public function test_create_customer() {

		$currency = Currency_Factory::create('USD' );

		$customer = Contacts::insert_customer( array(
			'name'          => 'Ever Customer',
			'email'         => 'ever@email.com',
			'phone'         => '12345678',
			'birth_date'    => '1995-08-15',
			'country'       => 'US',
			'website'       => 'http://www.test.com',
			'vat_number'    => '12',
			'currency_code' => $currency->get_code(),
			'type'          => 'customer',
			'thumbnail_id'  => null,
		) );

		$this->assertNotFalse( $customer->exists() );

		$this->assertEquals( 'Ever Customer', $customer->get_name() );
		$this->assertNotNull( $customer->get_id() );
		$this->assertEquals( 'ever@email.com', $customer->get_email() );
		$this->assertEquals( '12345678', $customer->get_phone() );
		$this->assertEquals( '1995-08-15', date( 'Y-m-d', strtotime( $customer->get_birth_date() ) ) );
		$this->assertEquals( 'US', $customer->get_country() );
		$this->assertEquals( 'http://www.test.com', $customer->get_website() );
		$this->assertEquals( '12', $customer->get_vat_number() );
		$this->assertEquals( 'USD', $customer->get_currency_code() );
		$this->assertEquals( 'customer', $customer->get_type() );
		$this->assertEquals( 1, $customer->get_enabled() );
		$this->assertNotNull( $customer->get_date_created() );

	}

	public function test_update_customer(){
		$currency = Currency_Factory::create('USD' );

		$customer = Contacts::insert_customer( array(
			'name'          => 'John Doe',
			'email'         => 'john@doe.com',
			'currency_code' => $currency->get_code(),
			'type'          => 'customer',
		) );

		$contact_id = $customer->get_id();
		$this->assertNotFalse( $customer->exists() );

		$this->assertEquals( 'John Doe', $customer->get_name() );
		$this->assertEquals( 'john@doe.com', $customer->get_email() );
		$this->assertEquals( 'USD', $customer->get_currency_code() );
		$this->assertEquals( 'customer', $customer->get_type() );

		$error = Contacts::insert_customer( array(
			'id'            => $contact_id,
			'name'          => 'Ever Customer',
			'email'         => 'ever@email.com',
			'currency_code' => 'USD',
			'type'          => 'vendor',
		) );

		$this->assertNotWPError( $error );

		$customer = Contacts::get( $contact_id ); // so we can read fresh copies from the DB

		$this->assertEquals( 'Ever Customer', $customer->get_name() );
		$this->assertEquals( 'ever@email.com', $customer->get_email() );
		$this->assertEquals( 'USD', $customer->get_currency_code() );
		$this->assertEquals( 'vendor', $customer->get_type() );
	}

	public function test_delete_customer(){
		$customer = Contact_Factory::create_customer();
		$this->assertNotEquals( 0, $customer->get_id() );
		$this->assertNotFalse( Contacts::delete_customer( $customer->get_id() ) );
	}

	public function test_exception_customer(){

		//currency_code check
		$customer = Contacts::insert_customer( array(
			'name' => 'John Doe',
			'currency_code' => '',
		) );
		$this->assertEquals( 'Contact currency_code is required.', $customer->get_error_message() );

		$customer = Contacts::insert_customer(array(
			'name' => '',
			'currency_code' => 'USD',
		));
		$this->assertEquals('Contact name is required.',$customer->get_error_message());


		// contact user_id check
		// @todo will need to check for wp user id
//		$customer = Contacts::insert_customer( array(
//			'name'          => 'John Doe New',
//			'currency_code' => 'USD',
//			'type'          => 'customer',
//			'user_id'       => 10,
//		) );
//		$this->assertEquals( 'Invalid WP User ID', $customer->get_error_message() );

		//insert customer with valid data
		$currency = Currency_Factory::create('USD' );
		$customer = Contacts::insert_customer( array(
			'name'          => 'John Doe',
			'currency_code' => $currency->get_code(),
			'type'          => 'customer',
			'email'         => 'john@doe.com'
		) );
		$this->assertNotFalse( $customer->exists() );

		$customer = Contacts::insert_customer(array(
			'name' => 'Ever Customer',
			'currency_code' => $currency->get_code(),
			'type' => 'customer',
			'email' => 'john@doe.com'
		));
		$this->assertEquals('The email address is already in used.',$customer->get_error_message());
	}

}
