<?php

class DocumentTaxTest extends \Codeception\TestCase\WPTestCase {
	/**
	 * @var \WpunitTester
	 */
	protected $tester;

//	public function testCreateDocumentTax() {
//		$data    = $this->tester->create_document_tax( [], true );
//		$doc_tax = new \EverAccounting\Models\DocumentTax();
//		$doc_tax->set_props( $data );
//		$this->assertWPError( $doc_tax->save() );
//
//		$doc_tax->set_document_id( 1 );
//		$this->assertWPError( $doc_tax->save() );
//		$doc_tax->set_item_id( 1 );
//		$this->assertWPError( $doc_tax->save() );
//		$doc_tax->set_tax_id( 1 );
//		$this->assertWPError( $doc_tax->save() );
//		$doc_tax->set_name( '32' );
//		$this->assertWPError( $doc_tax->save() );
//		$doc_tax->set_rate( 1.00 );
//		$this->assertTrue( $doc_tax->save() );
//	}
//
//	public function testGetDocumentTax() {
//		$doc_tax = $this->tester->create_document_tax(['document_id' => 1, 'item_id' => 1, 'tax_id' => 1, 'name' => '32', 'rate' => 1.00, 'total' => 2.00]);
//		$this->assertEquals( $doc_tax->get_document_id(), 1 );
//		$this->assertEquals( $doc_tax->get_item_id(), 1 );
//		$this->assertEquals( $doc_tax->get_tax_id(), 1 );
//		$this->assertEquals( $doc_tax->get_name(), '32' );
//		$this->assertEquals( $doc_tax->get_rate(), 1.00 );
//		$this->assertEquals( $doc_tax->get_total(), 2.00 );
//	}
//
//	public function testUpdateDocumentTax() {
//		$doc_tax = $this->tester->create_document_tax(['document_id' => 1, 'item_id' => 1, 'tax_id' => 1, 'name' => '32', 'rate' => 1.00, 'total' => 2.00]);
//		$doc_tax->set_document_id( 2 );
//		$this->assertTrue( $doc_tax->save() );
//		$doc_tax->set_item_id( 2 );
//		$this->assertTrue( $doc_tax->save() );
//		$doc_tax->set_tax_id( 2 );
//		$this->assertTrue( $doc_tax->save() );
//		$doc_tax->set_name( '33' );
//		$this->assertTrue( $doc_tax->save() );
//		$doc_tax->set_rate( 2.00 );
//		$this->assertTrue( $doc_tax->save() );
//		$doc_tax->set_total( 3.00 );
//		$this->assertTrue( $doc_tax->save() );
//	}
//
//	public function testDeleteDocumentTax() {
//		$doc_tax = $this->tester->create_document_tax(['document_id' => 1, 'item_id' => 1, 'tax_id' => 1, 'name' => '32', 'rate' => 1.00, 'total' => 2.00]);
//		$this->assertNotFalse( $doc_tax->delete() );
//	}
}
