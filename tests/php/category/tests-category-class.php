<?php
/**
 * Ever_Accounting Category Class Handler
 *
 * @since    1.1.3
 * @package  Ever_Accounting\Tests
 * @class    Tests_Category_Class
 */

namespace Ever_Accounting\Tests;

use Ever_Accounting\Categories;
use Ever_Accounting\Tests\Factories\Category_Factory;

/**
 * Class Tests_Category_Class
 *
 * @since 1.1.3
 * @package  Ever_Accounting\Tests
 */
class Tests_Category_Class extends \WP_UnitTestCase {

	public function test_create_category() {
		$category = Categories::insert( array(
			'name'  => 'Test Category',
			'type'  => 'expense',
			'color' => 'red',
		) );

		$this->assertNotFalse( $category->exists() );

		$this->assertEquals( 'Test Category', $category->get_name() );
		$this->assertNotNull( $category->get_id() );
		$this->assertEquals( 'expense', $category->get_type() );
		$this->assertEquals( 'red', $category->get_color() );
		$this->assertNotNull( $category->get_date_created() );
	}


	public function test_update_category() {
		$category    = Categories::insert( array(
			'name'  => 'New Category',
			'type'  => 'expense',
			'color' => 'red',
		) );
		$category_id = $category->get_id();
		$this->assertNotFalse( $category->exists() );
		$this->assertEquals( 'New Category', $category->get_name() );
		$this->assertEquals( 'expense', $category->get_type() );
		$error = Categories::insert( array(
			'id'    => $category_id,
			'name'  => 'Updated category',
			'type'  => 'income',
			'color' => 'blue',
		) );

		$this->assertNotWPError( $error );

		$category = Categories::get( $category_id ); // so we can read fresh copies from the DB

		$this->assertEquals( 'Updated category', $category->get_name() );
		$this->assertEquals( 'income', $category->get_type() );
		$this->assertEquals( 'blue', $category->get_color() );
	}


	public function test_delete_category() {
		$category = Category_Factory::create();
		$this->assertNotEquals( 0, $category->get_id() );
		$this->assertNotFalse( Categories::delete( $category->get_id() ) );
	}

	public function test_exception_category() {
		$category = Categories::insert( array(
			'name' => '',
		) );
		$this->assertEquals( 'Category name is required.', $category->get_error_message() );

		$category = Categories::insert( array(
			'name' => 'Test Category',
			'type' => ''
		) );
		$this->assertEquals( 'Category type is required.', $category->get_error_message() );

		$category = Categories::insert( array(
			'name' => 'Expense',
			'type' => 'expense',
		) );
		$this->assertNotFalse( $category->exists() );

		// just use this when needs to test duplicate categories
//		$category = Categories::insert( array(
//			'name' => 'Expense',
//			'type' => 'expense',
//		) );
//		$this->assertEquals( 'Could not insert item into the database.', $category->get_error_message() );

	}
}
