<?php

use EverAccounting\Models\DocumentItem;

class DocumentItemTest extends \Codeception\TestCase\WPTestCase {
	/**
	 * @var \FunctionalTester
	 */
	protected $tester;

	public function testCrud() {
		$data = array(
			'name'        => '',
			'price'       => 10,
			'quantity'    => 1,
			'subtotal'    => 10,
			'subtotal_tax'=> 0,
			'discount'    => 0,
			'discount_tax'=> 0,
			'tax_total'   => 0,
			'total'       => 10,
			'taxable'     => true,
			'description' => '',
			'unit'        => '',
			'item_id'     => null,
			'document_id' => null,
		);

		$this->assertWPError(  DocumentItem::insert( $data ), 'Item type is required.' );
		$data['type'] = 'standard';
		$this->assertWPError(  DocumentItem::insert( $data ), 'Item name is required.' );
		$data['name'] = 'Item name';
		$this->assertWPError(  DocumentItem::insert( $data ), 'Item quantity is required.' );
		$data['quantity'] = 1;
		$this->assertWPError(  DocumentItem::insert( $data ), 'Item ID is required.' );
		$data['item_id'] = 1;
		$this->assertWPError(  DocumentItem::insert( $data ), 'Document ID is required.' );
		$data['document_id'] = 1;
		$item = DocumentItem::insert( $data );
		$this->assertNotNull( $item );
		$this->assertEquals( $data['type'], $item->type );
		$this->assertEquals( $data['name'], $item->name );
		$this->assertEquals( $data['price'], $item->price );
		$this->assertEquals( $data['quantity'], $item->quantity );
		$this->assertEquals( $data['subtotal'], $item->subtotal );
		$this->assertEquals( $data['subtotal_tax'], $item->subtotal_tax );
		$this->assertEquals( $data['discount'], $item->discount );
		$this->assertEquals( $data['discount_tax'], $item->discount_tax );
		$this->assertEquals( $data['tax_total'], $item->tax_total );
		$this->assertEquals( $data['total'], $item->total );
		$this->assertEquals( $data['taxable'], $item->taxable );
		$this->assertEquals( $data['description'], $item->description );
		$this->assertEquals( $data['unit'], $item->unit );
		$this->assertEquals( $data['item_id'], $item->item_id );
		$this->assertEquals( $data['document_id'], $item->document_id );
		$this->assertNotEmpty( $item->date_created );

		// test update.
		$data['id']          = $item->id;
		$data['type']        = 'shipping';
		$data['name']        = 'Item name updated';
		$data['price']       = 20;
		$data['quantity']    = 2;
		$data['subtotal']    = 40;
		$data['subtotal_tax']= 5;
		$data['discount']    = 4;
		$data['discount_tax']= 5;
		$data['tax_total']   = 6;
		$data['total']       = 50;
		$data['taxable']     = false;
		$data['description'] = 'Description';
		$data['unit']        = 'Unit';
		$data['item_id']     = 2;
		$data['document_id'] = 2;
		$item                = DocumentItem::insert( $data );
		$this->assertNotNull( $item );
		$this->assertEquals( $data['type'], $item->type );
		$this->assertEquals( $data['name'], $item->name );
		$this->assertEquals( $data['price'], $item->price );
		$this->assertEquals( $data['quantity'], $item->quantity );
		$this->assertEquals( $data['subtotal'], $item->subtotal );
		$this->assertEquals( $data['subtotal_tax'], $item->subtotal_tax );
		$this->assertEquals( $data['discount'], $item->discount );
		$this->assertEquals( $data['discount_tax'], $item->discount_tax );
		$this->assertEquals( $data['tax_total'], $item->tax_total );
		$this->assertEquals( $data['total'], $item->total );
		$this->assertEquals( $data['taxable'], $item->taxable );
		$this->assertEquals( $data['description'], $item->description );
		$this->assertEquals( $data['unit'], $item->unit );
		$this->assertEquals( $data['item_id'], $item->item_id );
		$this->assertEquals( $data['document_id'], $item->document_id );
		$this->assertNotEmpty( $item->date_created );
		$this->assertNotEmpty( $item->date_updated );

		// test delete.
		$this->assertNotFalse( $item->delete() );
		$this->assertNull( DocumentItem::find( $item->id ) );
	}
}
