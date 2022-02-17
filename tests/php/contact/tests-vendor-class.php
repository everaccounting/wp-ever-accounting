<?php
/**
 * Ever_Accounting VendorTest Handler
 *
 * @since    1.1.3
 * @package  Ever_Accounting\Tests
 * @class    Tests_Vendor
 */

namespace Ever_Accounting\Tests;

use Ever_Accounting\Contacts;
use Ever_Accounting\Tests\Factories\Contact_Factory;
use Ever_Accounting\Tests\Factories\Currency_Factory;

defined( 'ABSPATH' ) || exit();

/**
 * Class Tests_Vendor_Class.
 * @package EverAccounting\Tests\Vendor
 */
class Tests_Vendor_Class extends \WP_UnitTestCase {
	public function test_create_vendor() {

		$currency = Currency_Factory::create('USD' );

		$vendor = Contacts::insert_vendor( array(
			'name'          => 'Ever Customer',
			'email'         => 'ever@email.com',
			'phone'         => '12345678',
			'birth_date'    => '1995-08-15',
			'country'       => 'US',
			'website'       => 'http://www.test.com',
			'vat_number'    => '12',
			'currency_code' => $currency->get_code(),
			'type'          => 'vendor',
			'thumbnail_id'  => null,
		) );

		$this->assertNotFalse( $vendor->exists() );

		$this->assertEquals( 'Ever Customer', $vendor->get_name() );
		$this->assertNotNull( $vendor->get_id() );
		$this->assertEquals( 'ever@email.com', $vendor->get_email() );
		$this->assertEquals( '12345678', $vendor->get_phone() );
		$this->assertEquals( '1995-08-15', date( 'Y-m-d', strtotime( $vendor->get_birth_date() ) ) );
		$this->assertEquals( 'US', $vendor->get_country() );
		$this->assertEquals( 'http://www.test.com', $vendor->get_website() );
		$this->assertEquals( '12', $vendor->get_vat_number() );
		$this->assertEquals( 'USD', $vendor->get_currency_code() );
		$this->assertEquals( 'vendor', $vendor->get_type() );
		$this->assertEquals( 1, $vendor->get_enabled() );
		$this->assertNotNull( $vendor->get_date_created() );

	}

	public function test_update_vendor(){
		$currency = Currency_Factory::create('USD' );

		$vendor = Contacts::insert_vendor( array(
			'name'          => 'John Doe',
			'email'         => 'john@doe.com',
			'currency_code' => $currency->get_code(),
			'type'          => 'vendor',
		) );

		$contact_id = $vendor->get_id();
		$this->assertNotFalse( $vendor->exists() );

		$this->assertEquals( 'John Doe', $vendor->get_name() );
		$this->assertEquals( 'john@doe.com', $vendor->get_email() );
		$this->assertEquals( 'USD', $vendor->get_currency_code() );
		$this->assertEquals( 'vendor', $vendor->get_type() );

		$error = Contacts::insert_vendor( array(
			'id'            => $contact_id,
			'name'          => 'Ever Customer',
			'email'         => 'ever@email.com',
			'currency_code' => 'USD',
			'type' => 'customer'
		) );

		$this->assertNotWPError( $error );

		$vendor = Contacts::get( $contact_id ); // so we can read fresh copies from the DB

		$this->assertEquals( 'Ever Customer', $vendor->get_name() );
		$this->assertEquals( 'ever@email.com', $vendor->get_email() );
		$this->assertEquals( 'USD', $vendor->get_currency_code() );
		$this->assertEquals( 'customer', $vendor->get_type() );
	}

	public function test_delete_customer(){
		$vendor = Contact_Factory::create_vendor();
		$this->assertNotEquals( 0, $vendor->get_id() );
		$this->assertNotFalse( Contacts::delete_vendor( $vendor->get_id() ) );
	}

	public function test_exception_customer(){

		//currency_code check
		$vendor = Contacts::insert_vendor( array(
			'name' => 'John Doe',
			'currency_code' => '',
		) );
		$this->assertEquals( 'Contact currency_code is required.', $vendor->get_error_message() );

		$vendor = Contacts::insert_vendor(array(
			'name' => '',
			'currency_code' => 'USD',
		));
		$this->assertEquals('Contact name is required.',$vendor->get_error_message());


		// contact user_id check
		// @todo will need to check for wp user id
//		$vendor = Contacts::insert_customer( array(
//			'name'          => 'John Doe New',
//			'currency_code' => 'USD',
//			'type'          => 'vendor',
//			'user_id'       => 10,
//		) );
//		$this->assertEquals( 'Invalid WP User ID', $vendor->get_error_message() );

		//insert customer with valid data
		$currency = Currency_Factory::create('USD' );
		$vendor = Contacts::insert_vendor( array(
			'name'          => 'John Doe',
			'currency_code' => $currency->get_code(),
			'type'          => 'vendor',
			'email'         => 'john@doe.com'
		) );
		$this->assertNotFalse( $vendor->exists() );

//		$customer = Contacts::insert_vendor(array(
//			'name' => 'Ever Customer',
//			'currency_code' => $currency->get_code(),
//			'type' => 'vendor',
//			'email' => 'john@doe.com'
//		));
//		$this->assertEquals('The email address is already in used.',$customer->get_error_message());
	}

}
