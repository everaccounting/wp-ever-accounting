<?php

use EverAccounting\Models\DocumentTax;

class DocumentItemTaxTest extends \Codeception\TestCase\WPTestCase {
	/**
	 * @var \FunctionalTester
	 */
	protected $tester;

	public function testCreate() {
		$data = array(
			'name'        => '',
			'rate'        => null,
			'is_compound' => false,
			'amount'      => 10,
			'item_id'     => null,
			'tax_id'      => null,
			'document_id' => null,
		);
		$this->assertWPError(  DocumentTax::insert( $data ), 'Tax name is required.' );
		$data['name'] = 'Tax name';
		$this->assertWPError(  DocumentTax::insert( $data ), 'Tax rate is required.' );
		$data['rate'] = 10;
		$this->assertWPError(  DocumentTax::insert( $data ), 'Item ID is required.' );
		$data['item_id'] = 1;
		$this->assertWPError(  DocumentTax::insert( $data ), 'Tax ID is required.' );
		$data['tax_id'] = 1;
		$this->assertWPError(  DocumentTax::insert( $data ), 'Document ID is required.' );
		$data['document_id'] = 1;
		$item = DocumentTax::insert( $data );
		$this->assertNotNull( $item );
		$this->assertEquals( $data['name'], $item->name );
		$this->assertEquals( $data['rate'], $item->rate );
		$this->assertEquals( $data['is_compound'], $item->is_compound );
		$this->assertEquals( $data['amount'], $item->amount );
		$this->assertEquals( $data['item_id'], $item->item_id );
		$this->assertEquals( $data['tax_id'], $item->tax_id );
		$this->assertEquals( $data['document_id'], $item->document_id );
		$this->assertNotEmpty( $item->date_created );
	}

	public function testUpdate() {
		$data = array(
			'name'        => 'Tax name',
			'rate'        => 10,
			'is_compound' => false,
			'amount'      => 10,
			'item_id'     => 1,
			'tax_id'      => 1,
			'document_id' => 1,
		);

		$item = DocumentTax::insert( $data );

		$data['id']          = $item->id;
		$data['name']        = 'Tax name updated';
		$data['rate']        = 20;
		$data['is_compound'] = true;
		$data['amount']      = 20;
		$data['item_id']     = 2;
		$data['tax_id']      = 2;
		$data['document_id'] = 2;
		$item                = DocumentTax::insert( $data );
		$this->assertNotNull( $item );
		$this->assertEquals( $data['name'], $item->name );
		$this->assertEquals( $data['rate'], $item->rate );
		$this->assertEquals( $data['is_compound'], $item->is_compound );
		$this->assertEquals( $data['amount'], $item->amount );
		$this->assertEquals( $data['item_id'], $item->item_id );
		$this->assertEquals( $data['tax_id'], $item->tax_id );
		$this->assertEquals( $data['document_id'], $item->document_id );
		$this->assertNotEmpty( $item->date_updated );
	}

	public function testDelete() {
		$data = array(
			'name'        => 'Tax name',
			'rate'        => 10,
			'is_compound' => false,
			'amount'      => 10,
			'item_id'     => 1,
			'tax_id'      => 1,
			'document_id' => 1,
		);

		$item = DocumentTax::insert( $data );
		$this->assertNotNull( $item );
		$this->assertNotFalse( $item->delete() );
		$this->assertNull( DocumentTax::find( $item->id ) );
	}

	//test similar compare.
	public function testSimilar() {
		// Test 2 tax items with the same name, rate, and amount. they will be considered similar.
		$data = array(
			'name'        => 'Tax name',
			'rate'        => 10,
			'is_compound' => false,
			'amount'      => 10,
			'item_id'     => 1,
			'tax_id'      => 1,
			'document_id' => 1,
		);

		$item1 = DocumentTax::insert( $data );
		$item2 = DocumentTax::insert( $data );
		$this->assertTrue( $item1->is_similar( $item2 ) );
		//test merge.
		$item1->merge( $item2 );
		$this->assertEquals( 20, $item1->amount );

	}
}
