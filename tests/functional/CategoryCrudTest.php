<?php

use EverAccounting\Models\Category;

class CategoryCrudTest extends TestCase\WPTestCase {

	public function testCreate() {
		$category = $this->factory()->category->create_and_get();
		// Check if the category was created.
		$this->assertInstanceOf( Category::class, $category );

		// Check if the category has an ID.
		$this->assertNotEmpty( $category->id );
		$this->assertIsInt( $category->id );

		// Name and type is required.
		$category->name = '';
		$this->assertWPError( $category->save() );
		$category->type = '';
		$this->assertWPError( $category->save() );
	}
}
