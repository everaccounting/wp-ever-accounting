<?php

use EverAccounting\Models\Tax;

class TaxCrudTest extends TestCase\WPTestCase {

	public function testCreate() {
		$tax = $this->factory()->tax->create_and_get();
		// Check if the tax was created.
		$this->assertInstanceOf( Tax::class, $tax );

		// Check if the tax has an ID.
		$this->assertNotEmpty( $tax->id );
		$this->assertIsInt( $tax->id );
	}
}
