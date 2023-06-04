<?php

class ProductTest extends \Codeception\TestCase\WPTestCase {
	/**
	 * @var \WpunitTester
	 */
	protected $tester;

	// Create product
	public function testCreateProduct() {
		$data = $this->tester->create_product([], false);
		$product = eac_insert_product( $data );
		$this->assertNotEmpty( $product );
		$this->assertEquals( $data['name'], $product->get_name() );
		$this->assertEquals( $data['price'], $product->get_price() );
		$this->assertEquals( $data['unit'], $product->get_unit() );
		$this->assertEquals( $data['description'], $product->get_description() );
		$this->assertEquals( $data['category_id'], $product->get_category_id() );
		$this->assertEquals( $data['taxable'], $product->get_taxable() );
		$this->assertEquals( $data['tax_ids'], $product->get_tax_ids() );
		$this->assertEquals( $data['status'], $product->get_status() );

		// When creating a product, the created_at will be set automatically and updated_at will be null.
		$this->assertNotEmpty( $product->get_created_at() );
		$this->assertNull( $product->get_updated_at() );
	}

	// Update product
	public function testUpdateProduct() {
		$data = $this->tester->create_product([], false);
		$product = eac_insert_product( $data );
		$this->assertNotEmpty( $product );
		$this->assertEquals( $data['name'], $product->get_name() );
		$this->assertEquals( $data['price'], $product->get_price() );
		$this->assertEquals( $data['unit'], $product->get_unit() );
		$this->assertEquals( $data['description'], $product->get_description() );
		$this->assertEquals( $data['category_id'], $product->get_category_id() );
		$this->assertEquals( $data['taxable'], $product->get_taxable() );
		$this->assertEquals( $data['tax_ids'], $product->get_tax_ids() );
		$this->assertEquals( $data['status'], $product->get_status() );
		$this->assertEquals( $data['updated_at'], $product->get_updated_at() );
		$this->assertNotEmpty( $product->get_created_at() );

		$data = array(
			'id'          => $product->get_id(),
			'name'        => 'Test Product 2',
			'price'       => 200,
			'unit'        => 'box',
			'description' => 'Test Product Description 2',
			'category_id' => 2,
			'taxable'     => 'no',
			'tax_ids'     => '2,3',
			'status'      => 'inactive',
			'updated_at'  => null,
			'created_at'  => null,
		);

		$updated = eac_insert_product( $data );

		$this->assertNotEmpty( $updated );
		$this->assertEquals( $product->get_id(), $updated->get_id() );
		$this->assertEquals( $data['name'], $updated->get_name() );
		$this->assertEquals( $data['price'], $updated->get_price() );
		$this->assertEquals( $data['unit'], $updated->get_unit() );
		$this->assertEquals( $data['description'], $updated->get_description() );
		$this->assertEquals( $data['category_id'], $updated->get_category_id() );
		$this->assertEquals( $data['taxable'], $updated->get_taxable() );
		$this->assertEquals( $data['tax_ids'], $updated->get_tax_ids() );
		$this->assertEquals( $data['status'], $updated->get_status() );
	}

	// Delete product
	public function testDeleteProduct() {
		$product = $this->tester->create_product();
		$deleted = eac_delete_product( $product->get_id() );
		$this->assertNotFalse( $deleted );
	}

	// Query products
	public function testQueryProducts() {
		$created_products = $this->tester->create_products(50);
		$this->assertNotEmpty( $created_products );
		// count products
//		$count = eac_get_products([], true);
//		$this->assertEquals( 50, $count );
//
//		// get products
//		$products = eac_get_products(array(
//			'limit' => -1,
//		));
//		$this->assertNotEmpty( $products );
//		$this->assertEquals( 50, count($products) );
	}
}
