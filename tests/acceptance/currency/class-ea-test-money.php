<?php

class EverAccounting_Tests_Money extends EverAccounting_Unit_Test_Case {
	public function test_format_simple() {
		$m1 = new \EverAccounting\Core\Money( 1, 'USD' );
		$m2 = new \EverAccounting\Core\Money( 10, 'USD' );
		$m3 = new \EverAccounting\Core\Money( 100, 'USD' );
		$m4 = new \EverAccounting\Core\Money( 1000, 'USD' );
		$m5 = new \EverAccounting\Core\Money( 10000, 'USD' );
		$m6 = new \EverAccounting\Core\Money( 100000, 'USD' );

		$this->assertEquals( '0.01', $m1->format_simple() );
		$this->assertEquals( '0.10', $m2->format_simple() );
		$this->assertEquals( '1.00', $m3->format_simple() );
		$this->assertEquals( '10.00', $m4->format_simple() );
		$this->assertEquals( '100.00', $m5->format_simple() );
		$this->assertEquals( '1,000.00', $m6->format_simple() );
	}

	public function test_comparators() {
		$m1 = new \EverAccounting\Core\Money( 0, 'USD' );
		$m2 = new \EverAccounting\Core\Money( - 1, 'USD' );
		$m3 = new \EverAccounting\Core\Money( 1, 'USD' );
		$m4 = new \EverAccounting\Core\Money( 1, 'USD' );
		$m5 = new \EverAccounting\Core\Money( 1, 'USD' );
		$m6 = new \EverAccounting\Core\Money( - 1, 'USD' );

		$this->assertTrue( $m1->isZero() );
		$this->assertTrue( $m2->isNegative() );
		$this->assertTrue( $m3->isPositive() );
		$this->assertFalse( $m4->isZero() );
		$this->assertFalse( $m5->isNegative() );
		$this->assertFalse( $m6->isPositive() );
	}
}
