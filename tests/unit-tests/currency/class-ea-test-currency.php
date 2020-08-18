<?php

/**
 * Class EAccounting_Tests_Category.
 * @package EAccounting\Tests\Category
 */
class EAccounting_Tests_Currency extends EAccounting_Unit_Test_Case {

	public function test_create_currency() {
		$currency = eaccounting_insert_currency( array(
			'code' => 'USD',
			'rate' => '1',
		) );

		$this->assertNotFalse( $currency->exists() );

		$this->assertEquals( 'US Dollar', $currency->get_name() );
		$this->assertNotNull( $currency->get_id() );
		$this->assertEquals( 'USD', $currency->get_code() );
		$this->assertEquals( '2', $currency->get_precision() );
		$this->assertEquals( '$', $currency->get_symbol() );
		$this->assertEquals( 'before', $currency->get_position() );
		$this->assertEquals( '.', $currency->get_decimal_separator() );
		$this->assertEquals( ',', $currency->get_thousand_separator() );
		$this->assertNotNull( $currency->get_date_created() );

		$currency = eaccounting_insert_currency( array(
			'code' => 'BDT',
			'rate' => '1',
		) );

		$this->assertNotFalse( $currency->exists() );

		$this->assertEquals( 'Taka', $currency->get_name() );
		$this->assertNotNull( $currency->get_id() );
		$this->assertEquals( 'BDT', $currency->get_code() );
		$this->assertEquals( '2', $currency->get_precision() );
		$this->assertEquals( '৳', $currency->get_symbol() );
		$this->assertEquals( 'before', $currency->get_position() );
		$this->assertEquals( '.', $currency->get_decimal_separator() );
		$this->assertEquals( ',', $currency->get_thousand_separator() );
		$this->assertNotNull( $currency->get_date_created() );
	}

	public function test_update_currency() {
		$currency    = eaccounting_insert_currency( array(
			'name'               => 'Bulgarian Lev',
			'code'               => 'BGN',
			'precision'          => 2,
			'symbol'             => 'лв',
			'position'           => 'after',
			'decimal_separator'  => '.',
			'thousand_separator' => ',',
			'rate'               => '92',
		) );
		$currency_id = $currency->get_id();
		$this->assertNotFalse( $currency->exists() );
		$this->assertEquals( 'Bulgarian Lev', $currency->get_name() );
		$this->assertEquals( 'BGN', $currency->get_code() );
		$this->assertEquals( '2', $currency->get_precision() );
		$this->assertEquals( 'лв', $currency->get_symbol() );
		$this->assertEquals( 'after', $currency->get_position() );
		$this->assertEquals( '.', $currency->get_decimal_separator() );
		$this->assertEquals( ',', $currency->get_thousand_separator() );
		$this->assertEquals( '92', $currency->get_rate() );
		$currency = eaccounting_insert_currency( array(
			'id'                 => $currency_id,
			'name'               => 'Bulgarian Lev Update',
			'code'               => 'BDT',
			'precision'          => 1,
			'symbol'             => '$',
			'position'           => 'before',
			'decimal_separator'  => 'D',
			'thousand_separator' => 'T',
			'rate'               => '90',
		) );
		$this->assertEquals( 'Bulgarian Lev Update', $currency->get_name() );
		$this->assertEquals( 'BDT', $currency->get_code() );
		$this->assertEquals( '1', $currency->get_precision() );
		$this->assertEquals( '$', $currency->get_symbol() );
		$this->assertEquals( 'before', $currency->get_position() );
		$this->assertEquals( 'D', $currency->get_decimal_separator() );
		$this->assertEquals( 'T', $currency->get_thousand_separator() );
		$this->assertEquals( '90', $currency->get_rate() );
	}


	public function test_delete_currency() {
		$currency = eaccounting_insert_currency( array(
			'code' => 'BHD',
			'rate' => '1',
		) );
		$this->assertNotNull( $currency->get_id() );
		$this->assertNotFalse( eaccounting_delete_currency( $currency->get_id() ) );
	}


//	public function test_exception_category_number() {
//		$currency = EAccounting_Helper_Category::create_category( 'Another category 1', 'income' );
//		try {
//			EAccounting_Helper_Category::create_category( 'Another category 1', 'income' );
//		} catch ( Exception $e ) {
//			$this->assertEquals( "Duplicate category name.", $e->getMessage() );
//		}
//
//		//name check
//		try {
//			$currency = new Category();
//			$currency->set_name( '' );
//			$currency->save();
//		} catch ( Exception $e ) {
//			$this->assertEquals( "Category name is required", $e->getMessage() );
//		}
//
//		//type check
//		try {
//			$currency = new Category();
//			$currency->set_name( 'Exception account' );
//			$currency->set_type( '' );
//			$currency->save();
//		} catch ( Exception $e ) {
//			$this->assertEquals( "Category type is required", $e->getMessage() );
//		}
//
//		try {
//			$currency = new Category();
//			$currency->set_name( 'Exception account' );
//			$currency->set_type( 'income' );
//			$currency->save();
//		} catch ( Exception $e ) {
//			$this->throwAnException( $e->getMessage() );
//		}
//
//	}
//
//	public function test_category_functions() {
//		$currency = eaccounting_insert_category( array(
//			'name'  => 'Income category',
//			'type'  => 'income',
//			'color' => 'red'
//		) );
//
//		$this->assertNotNull( $currency->get_id() );
//
//		$updated = eaccounting_insert_category( array(
//			'id'   => $currency->get_id(),
//			'name' => 'Category Updated again'
//		) );
//		$this->assertEquals( "Category Updated again", $updated->get_name() );
//		$this->assertEquals( "income", $updated->get_type() );
//		$this->assertEquals( "red", $updated->get_color() );
//
//		$this->assertEquals( true, eaccounting_delete_category( $currency->get_id() ) );
//	}
}
