<?php

class ItemTest extends \Codeception\TestCase\WPTestCase {
	/**
	 * @var \FunctionalTester
	 */
	protected $tester;

	public function testCreate() {
		$item = eac_insert_item( array(
			'name'       => 'Item name',
			'quantity'   => 1,
			'sale_price' => '100.00',
		) );

		$this->assertNotNull( $item );
		$this->assertNotWPError( $item );

		$this->assertEquals( 'Item name', $item->name );
		$this->assertNotNull( $item->id );
		$this->assertEquals( 1, $item->quantity );
		$this->assertEquals( '100.00', $item->sale_price );

		$this->assertNotNull( $item->date_created );
		$this->assertNotNull( $item->purchase_price );
		//$this->assertNotNull( $item->creator_id );
	}

	public function testUpdate() {
		$item = eac_insert_item( array(
			'name'       => 'Item name',
			'quantity'   => 1,
			'sale_price' => '100.00',
		) );
		$this->assertNotNull( $item );
		$this->assertNotWPError( $item );
		$this->assertEquals( 'Item name', $item->name );
		$this->assertNotNull( $item->id );
		$item_id = $item->id;
		$this->assertEquals( 1, $item->quantity );
		$this->assertEquals( '100.00', $item->sale_price );
		$this->assertNotNull( $item->date_created );
		$this->assertNotNull( $item->purchase_price );

		$error = eac_insert_item( array(
			'id'         => $item_id,
			'name'       => 'Item updated',
			'quantity'   => 2,
			'sale_price' => '110.00',
		) );
		$this->assertNotWPError( $error );
		$item = eac_get_item( $item_id );
		$this->assertEquals( 'Item updated', $item->name );
		$this->assertEquals( 2, $item->quantity );
		$this->assertEquals( '110.00', $item->sale_price );
	}

	public function testDelete(){
		$item = eac_insert_item( array(
			'name'       => 'Item name',
			'quantity'   => 1,
			'sale_price' => '100.00',
		) );
		$this->assertNotNull( $item );
		$this->assertNotWPError( $item );
		$this->assertNotNull( $item->id );
		$this->assertNotEquals( 0, $item->id );
		$this->assertNotFalse( $item->delete() );
	}

	public function testValidation(){
		$item = eac_insert_item( array(
			'name' => '',
		) );
		$this->assertEquals( 'missing_required', $item->get_error_code() );

		$item = eac_insert_item( array(
			'name' => 'Item name',
			'quantity'   => null,
		) );
		$this->assertEquals( 'missing_required', $item->get_error_code() );

		$item = eac_insert_item( array(
			'name' => 'Item name',
			'quantity'   => 1,
			'sale_price' => null,
		) );
		$this->assertEquals( 'missing_required', $item->get_error_code() );
	}
}
