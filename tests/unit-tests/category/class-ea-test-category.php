<?php

use EverAccounting\Category;

/**
 * Class EAccounting_Tests_Category.
 * @package EAccounting\Tests\Category
 */
class EAccounting_Tests_Category extends EAccounting_Unit_Test_Case {

	public function test_create_category() {
		$category = eaccounting_insert_category( array(
			'name'  => 'Test Category',
			'type'  => 'expense',
			'color' => 'red',
		) );

		$this->assertNotFalse( $category->exists() );

		$this->assertEquals( 'Test Category', $category->get_name() );
		$this->assertNotNull( $category->get_id() );
		$this->assertEquals( 'expense', $category->get_type() );
		$this->assertEquals( 'red', $category->get_color() );
		$this->assertEquals( 1, $category->get_company_id() );
		$this->assertNotNull( $category->get_date_created() );
	}


	public function test_update_category() {
		$category    = eaccounting_insert_category( array(
			'name'  => 'New Category',
			'type'  => 'expense',
			'color' => 'red',
		) );
		$category_id = $category->get_id();
		$this->assertNotFalse( $category->exists() );
		$this->assertEquals( 'New Category', $category->get_name() );
		$this->assertEquals( 'expense', $category->get_type() );
		$error = eaccounting_insert_category( array(
			'id'    => $category_id,
			'name'  => 'Updated category',
			'type'  => 'income',
			'color' => 'blue',
		) );

		$this->assertNotWPError($error);

		$category = eaccounting_get_category( $category_id ); // so we can read fresh copies from the DB

		$this->assertEquals( 'Updated category', $category->get_name() );
		$this->assertEquals( 'income', $category->get_type() );
		$this->assertEquals( 'blue', $category->get_color() );
	}


	public function test_delete_category() {
		$category = EAccounting_Helper_Category::create_category();
		$this->assertNotEquals( 0, $category->get_id() );
		$this->assertNotFalse( eaccounting_delete_category( $category->get_id() ) );
	}


//	public function test_exception_category_number() {
//		$category = EAccounting_Helper_Category::create_category( 'Another category 1', 'income' );
//		try {
//			EAccounting_Helper_Category::create_category( 'Another category 1', 'income' );
//		} catch ( Exception $e ) {
//			$this->assertEquals( "Duplicate category name.", $e->getMessage() );
//		}
//
//		//name check
//		try {
//			$category = new Category();
//			$category->set_name( '' );
//			$category->save();
//		} catch ( Exception $e ) {
//			$this->assertEquals( "Category name is required", $e->getMessage() );
//		}
//
//		//type check
//		try {
//			$category = new Category();
//			$category->set_name( 'Exception account' );
//			$category->set_type( '' );
//			$category->save();
//		} catch ( Exception $e ) {
//			$this->assertEquals( "Category type is required", $e->getMessage() );
//		}
//
//		try {
//			$category = new Category();
//			$category->set_name( 'Exception account' );
//			$category->set_type( 'income' );
//			$category->save();
//		} catch ( Exception $e ) {
//			$this->throwAnException( $e->getMessage() );
//		}
//
//	}
//
//	public function test_category_functions() {
//		$category = eaccounting_insert_category( array(
//			'name'  => 'Income category',
//			'type'  => 'income',
//			'color' => 'red'
//		) );
//
//		$this->assertNotNull( $category->get_id() );
//
//		$updated = eaccounting_insert_category( array(
//			'id'   => $category->get_id(),
//			'name' => 'Category Updated again'
//		) );
//		$this->assertEquals( "Category Updated again", $updated->get_name() );
//		$this->assertEquals( "income", $updated->get_type() );
//		$this->assertEquals( "red", $updated->get_color() );
//
//		$this->assertEquals( true, eaccounting_delete_category( $category->get_id() ) );
//	}
}
