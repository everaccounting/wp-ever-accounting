<?php

class MiscFuncTest extends \Codeception\TestCase\WPTestCase {
	/**
	 * @var \WpunitTester
	 */
	protected $tester;

	/**
	 * Test eaccounting_get_random_color().
	 *
	 * @since 1.0.1
	 */
	public function test_eaccounting_get_random_color() {
		$this->assertNotNull( eaccounting_get_random_color() );
		$this->assertNotNull( eaccounting_get_random_color() );
		$this->assertNotNull( eaccounting_get_random_color() );
		$this->assertNotNull( eaccounting_get_random_color() );
		$this->assertNotNull( eaccounting_get_random_color() );
	}
}
