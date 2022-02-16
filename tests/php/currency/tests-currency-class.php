<?php
/**
 * Ever_Accounting SampleTest Handler
 *
 * @since    1.1.3
 * @package  Ever_Accounting\Tests
 * @class    SampleTest
 */

namespace Ever_Accounting\Tests;

use Ever_Accounting\Currencies;

defined( 'ABSPATH' ) || exit();

/**
 * Class SampleTest
 *
 * @since 1.1.3
 * @package  Ever_Accounting\Tests
 */
class CurrencyTest extends \WP_UnitTestCase {

	public function test_create_currency() {
		$data = array(
			'name'               => 'Canadian Dollar',
			'code'               => 'CAD',
			'rate'               => 1,
			'precision'          => 2,
			'symbol'             => '$',
			'position'           => 'before',
			'decimal_separator'  => ',',
			'thousand_separator' => ','
		);

		$currency = Currencies::insert( $data );
		$this->assertTrue( $currency->exists() );


		$this->assertNotFalse( $currency->exists() );

		$this->assertEquals( 'Canadian Dollar', $currency->get_name() );
		$this->assertNotNull( $currency->get_id() );
		$this->assertEquals( 'CAD', $currency->get_code() );
		$this->assertEquals( 1, $currency->get_rate() );
		$this->assertEquals( 2, $currency->get_precision() );
		$this->assertEquals( '$', $currency->get_symbol() );
		$this->assertEquals( 'before', $currency->get_position() );
		$this->assertEquals( ',', $currency->get_decimal_separator() );
		$this->assertEquals( ',', $currency->get_thousand_separator() );
		$this->assertNotNull( $currency->get_date_created() );

		$currency = Currencies::insert( array(
			'name'               => 'Bangladeshi Taka',
			'code'               => 'BDT',
			'rate'               => 1,
			'precision'          => 2,
			'position'           => 'before',
			'symbol'             => '৳',
			'decimal_separator'  => '.',
			'thousand_separator' => ','
		) );
		$this->assertNotFalse( $currency->exists() );

		$this->assertEquals( 'Bangladeshi Taka', $currency->get_name() );
		$this->assertNotNull( $currency->get_id() );
		$this->assertEquals( 'BDT', $currency->get_code() );
		$this->assertEquals( 1, $currency->get_rate() );
		$this->assertEquals( 'before', $currency->get_position() );
		$this->assertEquals( '৳', $currency->get_symbol() );
		$this->assertEquals( '.', $currency->get_decimal_separator() );
		$this->assertEquals( ',', $currency->get_thousand_separator() );
		$this->assertNotNull( $currency->get_date_created() );

		$currency = Currencies::insert( array(
			'name'               => 'Bulgarian Lev',
			'code'               => 'BGN',
			'rate'               => 100,
			'precision'          => 3,
			'position'           => 'after',
			'symbol'             => 'лв',
			'decimal_separator'  => 'D',
			'thousand_separator' => 'T'
		) );
		$this->assertNotFalse( $currency->exists() );

		$this->assertEquals( 'Bulgarian Lev', $currency->get_name() );
		$this->assertNotNull( $currency->get_id() );
		$this->assertEquals( 'BGN', $currency->get_code() );
		$this->assertEquals( 100, $currency->get_rate() );
		$this->assertEquals( 3, $currency->get_precision() );
		$this->assertEquals( 'after', $currency->get_position() );
		$this->assertEquals( 'лв', $currency->get_symbol() );
		$this->assertEquals( 'D', $currency->get_decimal_separator() );
		$this->assertEquals( 'T', $currency->get_thousand_separator() );
		$this->assertNotNull( $currency->get_date_created() );
	}

	public function test_update_currency() {
		$currency = Currencies::insert( array(
			'name'               => 'Indian Rupee',
			'code'               => 'INR',
			'rate'               => 1.75,
			'precision'          => 2,
			'symbol'             => '₹',
			'position'           => 'before',
			'decimal_separator'  => '.',
			'thousand_separator' => ',',
		) );

		$currency_id = $currency->get_id();
		$this->assertNotFalse( $currency->exists(), 'Currency does not exist' );

		$this->assertEquals( 'Indian Rupee', $currency->get_name(), 'Currency name does not match' );
		$this->assertNotNull( $currency->get_id(), 'Currency ID is null' );
		$this->assertEquals( 'INR', $currency->get_code(), 'Currency code does not match' );
		$this->assertEquals( 1.75, $currency->get_rate(), 'Currency rate does not match' );
		$this->assertEquals( 2, $currency->get_precision(), 'Currency precision does not match' );
		$this->assertEquals( 'before', $currency->get_position(), 'Currency position does not match' );
		$this->assertEquals( '₹', $currency->get_symbol(), 'Currency symbol does not match' );
		$this->assertEquals( '.', $currency->get_decimal_separator(), 'Currency decimal separator does not match' );
		$this->assertEquals( ',', $currency->get_thousand_separator(), 'Currency thousand separator does not match' );
		$this->assertNotNull( $currency->get_date_created(), 'Currency date created is null' );

		$currency = Currencies::insert( array(
			'id'                 => $currency_id,
			'name'               => 'Pakistani Rupee',
			'code'               => 'PKR',
			'rate'               => 2,
			'symbol'             => 'RS',
			'position'           => 'after',
			'decimal_separator'  => 'D',
			'thousand_separator' => 'T'
		) );

		$this->assertNotFalse( $currency->exists(), 'Currency does not exist' );
		$this->assertEquals( 'Pakistani Rupee', $currency->get_name(), 'Currency name does not match' );
		$this->assertEquals( 'PKR', $currency->get_code(), 'Currency code does not match' );
		$this->assertEquals( 2, $currency->get_rate(), 'Currency rate does not match' );
		$this->assertEquals( 2, $currency->get_precision(), 'Currency precision does not match' );
		$this->assertEquals( 'after', $currency->get_position(), 'Currency position does not match' );
		$this->assertEquals( 'RS', $currency->get_symbol(), 'Currency symbol does not match' );
		$this->assertEquals( 'D', $currency->get_decimal_separator(), 'Currency decimal separator does not match' );
		$this->assertEquals( 'T', $currency->get_thousand_separator(), 'Currency thousand separator does not match' );
		$this->assertNotNull( $currency->get_date_created(), 'Currency date created is null' );
	}

	public function test_delete_currency() {
		$currency = \Currency_Factory::create();
		$this->assertNotEquals( 0, $currency->get_id() );
		$this->assertNotFalse( Currencies::delete( $currency->get_id() ) );
	}

	public function test_exception_currency() {
		$currency = Currencies::insert( array(
			'code' => ''
		) );
		$this->assertEquals( 'Currency code is required.', $currency->get_error_message() );

		$currency = Currencies::insert( array(
			'code' => 'AUD',
			'rate' => ''
		) );
		$this->assertEquals( 'Currency rate is required.', $currency->get_error_message() );

		$currency = Currencies::insert( array(
			'code' => 'AUD',
			'rate' => 1.12
		) );

		$this->assertNotFalse( $currency->exists() );

		\Currency_Factory::create( array(
			'code' => 'EUR',
			'rate' => 1.12
		) );
		$currency = \Currency_Factory::create( array(
			'code' => 'EUR',
			'rate' => 1.12
		) );

		$this->assertEquals( 'Currency already exists', $currency->get_error_message() );
	}
}
