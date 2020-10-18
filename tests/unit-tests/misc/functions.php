<?php
/**
 * Misc functions
 *
 * @package EverAccounting\Tests\Misc
 */

/**
 * Class Functions.
 *
 * @since 1.0.2
 */
class EAccounting_Tests_Misc_Functions extends EAccounting_Unit_Test_Case {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

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
