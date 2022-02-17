<?php
/**
 * Ever_Accounting Item Class Handler
 *
 * @since    1.1.3
 * @package  Ever_Accounting\Tests
 * @class    Tests_Item_Class
 */

namespace Ever_Accounting\Tests;

use Ever_Accounting\Items;
use Ever_Accounting\Tests\Factories\Item_Factory;

/**
 * Class Tests_Item_Class
 *
 * @since 1.1.3
 * @package  Ever_Accounting\Tests
 */
class Tests_Item_Class extends \WP_UnitTestCase {
	public function test_create_item() {
		$item = Items::insert( array(
			'name'  => 'Apple',
			'sale_price'  => '15',
			'purchase_price' => '12',
		) );

		$this->assertNotFalse( $item->exists() );

		$this->assertEquals( 'Apple', $item->get_name() );
		$this->assertNotNull( $item->get_id() );
		$this->assertEquals( '15', $item->get_sale_price() );
		$this->assertEquals( '12', $item->get_purchase_price() );
		$this->assertNotNull( $item->get_date_created() );
	}

	public function test_update_item() {
		$item    = Items::insert( array(
			'name'  => 'Apple',
			'sale_price'  => '15',
			'purchase_price' => '12',
		) );
		$item_id = $item->get_id();
		$this->assertNotFalse( $item->exists() );
		$this->assertEquals( 'Apple', $item->get_name() );
		$this->assertEquals( '15', $item->get_sale_price() );
		$this->assertEquals( '12', $item->get_purchase_price() );
		$error = Items::insert( array(
			'id'    => $item_id,
			'name'  => 'Malta',
			'sale_price'  => '20',
		) );

		$this->assertNotWPError( $error );

		$item = Items::get( $item_id ); // so we can read fresh copies from the DB

		$this->assertEquals( 'Malta', $item->get_name() );
		$this->assertEquals( '20', $item->get_sale_price() );
	}

	public function test_delete_item() {
		$item = Item_Factory::create();
		$this->assertNotEquals( 0, $item->get_id() );
		$this->assertNotFalse( Items::delete( $item->get_id() ) );
	}

	public function test_exception_item() {
		$item = Items::insert( array(
			'name' => '',
		) );
		$this->assertEquals( 'Item name is required.', $item->get_error_message() );

		$item = Items::insert( array(
			'name' => 'Orange',
			'quantity' => ''
		) );
		$this->assertEquals( 'Item quantity is required.', $item->get_error_message() );

		$item = Items::insert( array(
			'name' => 'Orange',
			'quantity' => 1,
			'sale_price' => ''
		) );
		$this->assertEquals( 'Item sale_price is required.', $item->get_error_message() );

		$item = Items::insert( array(
			'name' => 'Orange',
			'quantity' => 1,
			'sale_price' => '15.000',
			'purchase_price' => ''
		) );
		$this->assertEquals( 'Item purchase_price is required.', $item->get_error_message() );

		$item = Items::insert( array(
			'name' => 'Orange',
			'quantity' => 1,
			'sale_price' => '15.000',
			'purchase_price' => '15.000'
		) );
		$this->assertEquals( 'Item sale price and purchase price can\'t be same.', $item->get_error_message() );


		// just use this when needs to test duplicate categories
//		$category = Categories::insert( array(
//			'name' => 'Expense',
//			'type' => 'expense',
//		) );
//		$this->assertEquals( 'Could not insert item into the database.', $category->get_error_message() );

	}
}
