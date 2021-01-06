<?php
/**
 * Formatting functions
 *
 * @package EverAccounting\Tests\Formatting
 */

/**
 * Class Functions.
 *
 * @since 1.0.2
 */
class EverAccounting_Tests_Formatting_Functions extends EverAccounting_Unit_Test_Case {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	/**
	 * Test eaccounting_string_to_bool().
	 *
	 * @since 3.3.0
	 */
	public function test_eaccounting_string_to_bool() {
		$this->assertTrue( eaccounting_string_to_bool( 1 ) );
		$this->assertTrue( eaccounting_string_to_bool( 'yes' ) );
		$this->assertTrue( eaccounting_string_to_bool( 'Yes' ) );
		$this->assertTrue( eaccounting_string_to_bool( 'YES' ) );
		$this->assertTrue( eaccounting_string_to_bool( 'true' ) );
		$this->assertTrue( eaccounting_string_to_bool( 'True' ) );
		$this->assertTrue( eaccounting_string_to_bool( 'TRUE' ) );
		$this->assertFalse( eaccounting_string_to_bool( 0 ) );
		$this->assertFalse( eaccounting_string_to_bool( 'no' ) );
		$this->assertFalse( eaccounting_string_to_bool( 'No' ) );
		$this->assertFalse( eaccounting_string_to_bool( 'NO' ) );
		$this->assertFalse( eaccounting_string_to_bool( 'false' ) );
		$this->assertFalse( eaccounting_string_to_bool( 'False' ) );
		$this->assertFalse( eaccounting_string_to_bool( 'FALSE' ) );
	}

	/**
	 * Test eaccounting_bool_to_string().
	 *
	 * @since 1.0.2
	 */
	public function eaccounting_bool_to_string() {
		$this->assertEquals( array( 'yes', 'no' ), array(
			eaccounting_bool_to_string( true ),
			eaccounting_bool_to_string( false )
		) );
	}

	/**
	 * Test eaccounting_string_to_array().
	 *
	 * @since 1.0.1
	 */
	public function eaccounting_string_to_array() {
		$this->assertEquals(
			array(
				'foo',
				'bar',
			),
			eaccounting_string_to_array( 'foo|bar', '|' )
		);
	}

	/**
	 * Test eaccounting_clean().
	 *
	 * @since 1.0.1
	 */
	public function eaccounting_clean() {
		$this->assertEquals( 'cleaned', eaccounting_clean( '<script>alert();</script>cleaned' ) );
		$this->assertEquals( array( 'cleaned', 'foo' ), eaccounting_clean( array(
			'<script>alert();</script>cleaned',
			'foo'
		) ) );
	}

	/**
	 * Test eaccounting_sanitize_textarea().
	 *
	 * @since 1.0.2
	 */
	public function eaccounting_sanitize_textarea() {
		$this->assertEquals( "foo\ncleaned\nbar", eaccounting_sanitize_textarea( "foo\n<script>alert();</script>cleaned\nbar" ) );
	}

	/**
	 * Test eaccounting_sanitize_tooltip().
	 *
	 * Note this is a basic type test as WP core already has coverage for wp_kses().
	 *
	 * @since 1.0.2
	 */
	public function eaccounting_sanitize_tooltip() {
		$this->assertEquals( 'alert();cleaned&lt;p&gt;foo&lt;/p&gt;&lt;span&gt;bar&lt;/span&gt;', eaccounting_sanitize_tooltip( '<script>alert();</script>cleaned<p>foo</p><span>bar</span>' ) );
	}

	/**
	 * Test eaccounting_date_format().
	 *
	 * @since 1.0.2
	 */
	public function eaccounting_date_format() {
		$this->assertEquals( get_option( 'date_format' ), eaccounting_date_format() );
	}

	/**
	 * Test eaccounting_time_format().
	 *
	 * @since 1.0.2
	 */
	public function eaccounting_time_format() {
		$this->assertEquals( get_option( 'time_format' ), eaccounting_time_format() );
	}

	/**
	 * Test eaccounting_string_to_timestamp().
	 *
	 * @since 1.0.2
	 */
	public function eaccounting_string_to_timestamp() {
		$this->assertEquals( 1507075200, eaccounting_string_to_timestamp( '2017-10-04' ) );
		$this->assertEquals( 1507075200, eaccounting_string_to_timestamp( '2017-10-04', strtotime( '3000-10-04' ) ) );
	}

	/**
	 * Test eaccounting_string_to_datetime().
	 *
	 * @since 1.0.2
	 */
	public function eaccounting_string_to_datetime() {
		$data = eaccounting_string_to_datetime( '2014-10-04' );

		$this->assertInstanceOf( 'EverAccounting_DateTime', $data );
		$this->assertEquals( 1412380800, $data->getTimestamp() );
	}

	/**
	 * Test eaccounting_timezone_string().
	 *
	 * @since 1.0.2
	 */
	public function eaccounting_timezone_string() {
		// Test when timezone string exists.
		update_option( 'timezone_string', 'America/New_York' );
		$this->assertEquals( 'America/New_York', eaccounting_timezone_string() );

		// Restore default.
		update_option( 'timezone_string', '' );

		// Test with missing UTC offset.
		delete_option( 'gmt_offset' );
		$this->assertContains( eaccounting_timezone_string(), array( '+00:00', 'UTC' ) );

		// Test with manually set UTC offset.
		update_option( 'gmt_offset', - 4 );
		$this->assertNotContains( eaccounting_timezone_string(), array( '+00:00', 'UTC' ) );

		// Test with invalid offset.
		update_option( 'gmt_offset', 'invalid' );
		$this->assertContains( eaccounting_timezone_string(), array( '+00:00', 'UTC' ) );

		// Restore default.
		update_option( 'gmt_offset', '0' );
	}

	/**
	 * Test eaccounting_timezone_offset().
	 *
	 * @since 1.0.2
	 */
	public function eaccounting_timezone_offset() {
		$this->assertEquals( 0.0, eaccounting_timezone_offset() );
	}

	/**
	 * Test eaccounting_array_merge_recursive_numeric().
	 *
	 * @since 1.0.2
	 */
	public function eaccounting_array_merge_recursive_numeric() {
		$a = array(
			'A'   => 'bob',
			'sum' => 10,
			'C'   => array(
				'x',
				'y',
				'z' => 50,
			),
		);
		$b = array(
			'A'   => 'max',
			'sum' => 12,
			'C'   => array(
				'x',
				'y',
				'z' => 45,
			),
		);
		$c = array(
			'A'   => 'tom',
			'sum' => 8,
			'C'   => array(
				'x',
				'y',
				'z' => 50,
				'w' => 1,
			),
		);

		$this->assertEquals(
			array(
				'A'   => 'tom',
				'sum' => 30,
				'C'   => array(
					'x',
					'y',
					'z' => 145,
					'w' => 1,
				),
			),
			eaccounting_array_merge_recursive_numeric( $a, $b, $c )
		);
	}


	public function test_sanitize_number() {
		$this->assertEquals( '999', eaccounting_sanitize_number( '999' ) );
		$this->assertEquals( '9.99', eaccounting_sanitize_number( '9...99', true ) );
		$this->assertEquals( '99.9', eaccounting_sanitize_number( '9...9....9', true  ) );
		$this->assertEquals( '-9.99', eaccounting_sanitize_number( '-9.99', true  ) );
	}

}
