<?php

class CategoryTest extends \Codeception\TestCase\WPTestCase {
	/**
	 * @var \FunctionalTester
	 */
	protected $tester;


	public function testCreate() {
		$category = EAC()->categories->insert( array(
			'name' => 'Test Category',
			'type' => 'expense',
		) );
		$this->assertNotNull( $category );
		$this->assertNotFalse( $category->exists() );
		$this->assertEquals( 'Test Category', $category->name );
		$this->assertNotNull( $category->id );
		$this->assertEquals( 'expense', $category->type );
	}

	public function testUpdate() {
		$category    = EAC()->categories->insert( array(
			'name' => 'New Category',
			'type' => 'expense',
		) );
		$category_id = $category->id;
		$this->assertNotFalse( $category->exists() );
		$this->assertEquals( 'New Category', $category->name );
		$error = EAC()->categories->insert( array(
			'id'   => $category_id,
			'name' => 'Updated category',
			'type' => 'payment',
		) );
		$this->assertNotWPError( $error );
		$category = EAC()->categories->get( $category_id );
		$this->assertEquals( 'Updated category', $category->name );
		$this->assertEquals( 'payment', $category->type );
	}

	public function testDelete() {
		$category = EAC()->categories->insert( array(
			'name' => 'Test Category',
			'type' => 'expense',
		) );
		$this->assertNotEquals( 0, $category->id );
		$this->assertNotFalse( $category->delete() );
	}

	public function testValidation() {
		$category = EAC()->categories->insert( array(
			'name' => '',
		) );
		$this->assertWPError( $category );
//		$this->assertEquals( 'missing_required', $category->get_error_code() );
//		$category = EAC()->categories->insert( array(
//			'name' => 'Test Category',
//			'type' => ''
//		) );
//		codecept_debug($category);
//		$this->assertEquals( 'missing_required', $category->get_error_code() );
//		$types = EAC()->categories->get_types();
//		$category = EAC()->categories->insert( array(
//			'name' => 'Test Category',
//			'type' => 'invalid'
//		) );
//		$this->assertEquals( 'missing_required', $category->get_error_code() );
//		$type = $this->tester->getRandomElement( $types );
//		$category = EAC()->categories->insert( array(
//			'name' => 'Test Category',
//			'type' => $type
//		) );
//		$this->assertNotFalse( $category->exists() );
//		$this->assertEquals( $type, $category->type );
//		$category = EAC()->categories->insert( array(
//			'name' => 'Test Category',
//			'type' => $type
//		) );
//		$this->assertEquals( 'duplicate', $category->get_error_code() );
	}
}
