<?php

class CategoryTest extends \Codeception\TestCase\WPTestCase {
	/**
	 * @var \FunctionalTester
	 */
	protected $tester;


	public function testCreate(){
		$category = eac_insert_category( array(
			'name'  => 'Test Category',
			'type'  => 'expense',
			'color' => 'red',
		) );
		$this->assertNotNull( $category );
		$this->assertNotFalse( $category->exists() );
		$this->assertEquals( 'Test Category', $category->name );
		$this->assertNotNull( $category->id );
		$this->assertEquals( 'expense', $category->type );
		$this->assertEquals( 'red', $category->color );
		$this->assertNotNull( $category->date_created );
	}

	public function testUpdate(){
		$category    = eac_insert_category( array(
			'name'  => 'New Category',
			'type'  => 'expense',
			'color' => 'red',
		) );
		$category_id = $category->id;
		$this->assertNotFalse( $category->exists() );
		$this->assertEquals( 'New Category', $category->name );
		$error = eac_insert_category( array(
			'id'    => $category_id,
			'name'  => 'Updated category',
			'type'  => 'income',
			'color' => 'blue',
		) );
		$this->assertNotWPError( $error );
		$category = eac_get_category( $category_id );
		$this->assertEquals( 'Updated category', $category->name );
		$this->assertEquals( 'income', $category->type );
		$this->assertEquals( 'blue', $category->color );
	}

	public function testDelete(){
		$category = eac_insert_category( array(
			'name'  => 'Test Category',
			'type'  => 'expense',
			'color' => 'red',
		) );
		$this->assertNotEquals( 0, $category->id );
		$this->assertNotFalse( $category->delete() );
	}

	public function testValidation(){
		$category = eac_insert_category( array(
			'name' => '',
		) );
		$this->assertEquals( 'missing_required', $category->get_error_code() );
		$category = eac_insert_category( array(
			'name' => 'Test Category',
			'type' => ''
		) );
		$this->assertEquals( 'missing_required', $category->get_error_code() );
		$types = eac_get_category_types();
		$category = eac_insert_category( array(
			'name' => 'Test Category',
			'type' => 'invalid'
		) );
		$this->assertEquals( 'missing_required', $category->get_error_code() );
		$type = $this->tester->getRandomElement( $types );
		$category = eac_insert_category( array(
			'name' => 'Test Category',
			'type' => $type
		) );
		$this->assertNotFalse( $category->exists() );
		$this->assertEquals( $type, $category->type );
		$category = eac_insert_category( array(
			'name' => 'Test Category',
			'type' => $type
		) );
		$this->assertEquals( 'duplicate', $category->get_error_code() );
	}
}
