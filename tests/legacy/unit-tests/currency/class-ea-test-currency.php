<?php

use EverAccounting\Currency;

/**
 * Class EverAccounting_Tests_Currency.
 * @package EverAccounting\Tests\Currency
 */
class EverAccounting_Tests_Currency extends EverAccounting_Unit_Test_Case {
	public function test_create_currency() {
		$currency = eaccounting_insert_currency( array(
			'name'               => 'Canadian Dollar',
			'code'               => 'CAD',
			'rate'               => 1,
			'precision'          => 2,
			'symbol'             => '$',
			'position'           => 'before',
			'decimal_separator'  => ',',
			'thousand_separator' => ','
		) );
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

		$currency = eaccounting_insert_currency( array(
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

		$currency = eaccounting_insert_currency( array(
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
		$currency = eaccounting_insert_currency( array(
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
		$this->assertNotFalse( $currency->exists() );

		$this->assertEquals( 'Indian Rupee', $currency->get_name() );
		$this->assertNotNull( $currency->get_id() );
		$this->assertEquals( 'INR', $currency->get_code() );
		$this->assertEquals( 1.75, $currency->get_rate() );
		$this->assertEquals( 2, $currency->get_precision() );
		$this->assertEquals( 'before', $currency->get_position() );
		$this->assertEquals( '₹', $currency->get_symbol() );
		$this->assertEquals( '.', $currency->get_decimal_separator() );
		$this->assertEquals( ',', $currency->get_thousand_separator() );
		$this->assertNotNull( $currency->get_date_created() );

		$currency = eaccounting_insert_currency( array(
			'id'                 => $currency_id,
			'name'               => 'Pakistani Rupee',
			'code'               => 'PKR',
			'rate'               => 2,
			'symbol'             => 'RS',
			'position'           => 'after',
			'decimal_separator'  => 'D',
			'thousand_separator' => 'T'
		) );

		$this->assertEquals( 'Pakistani Rupee', $currency->get_name() );
		$this->assertEquals( 'PKR', $currency->get_code() );
		$this->assertEquals( 2, $currency->get_rate() );
		$this->assertEquals( 2, $currency->get_precision() );
		$this->assertEquals( 'after', $currency->get_position() );
		$this->assertEquals( 'RS', $currency->get_symbol() );
		$this->assertEquals( 'D', $currency->get_decimal_separator() );
		$this->assertEquals( 'T', $currency->get_thousand_separator() );
		$this->assertNotNull( $currency->get_date_created() );
	}

	public function test_delete_currency(){
		$currency = EverAccounting_Helper_Currency::create_currency();
		$this->assertNotEquals( 0, $currency->get_id() );
		$this->assertNotFalse( eaccounting_delete_currency( $currency->get_id() ) );
	}

	public function test_exception_currency(){
		$currency = eaccounting_insert_currency(array(
			'code' => ''
		));
		$this->assertEquals('Currency code is required.',$currency->get_error_message());

		$currency = eaccounting_insert_currency(array(
			'code' => 'AUD',
			'rate' => ''
		));
		$this->assertEquals('Currency rate is required.',$currency->get_error_message());

		$currency = eaccounting_insert_currency(array(
			'code' => 'AUD',
			'rate' => 1.12
		));
		$this->assertNotFalse($currency->exists());

		$currency = eaccounting_insert_currency(array(
			'code' => 'EUR',
			'rate' => 1.12
		));
		$this->assertEquals('Duplicate currency code.',$currency->get_error_message());
	}
}
