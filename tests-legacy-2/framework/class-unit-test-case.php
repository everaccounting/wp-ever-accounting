<?php
/**
 * Class EverAccounting_Unit_Test_Case
 * @since 1.0.2
 */

namespace EverAccounting\Tests\Framework;

class Unit_Test_Case extends HTTP_TestCase {

	/**
	 * Holds the WC_Unit_Test_Factory instance.
	 *
	 * @var Unit_Test_Factory
	 */
	protected $factory;

	/**
	 * Setup test case.
	 *
	 * @since 1.0.1
	 */
	public function setUp() {
		parent::setUp();
		// Add custom factories.
		$this->factory = new Unit_Test_Factory();

		$this->setOutputCallback( array( $this, 'filter_output' ) );
	}

	/**
	 * Set up class unit test.
	 *
	 * @since 1.0.1
	 */
	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();
	}

	/**
	 * Strip newlines and tabs when using expectedOutputString() as otherwise.
	 * the most template-related tests will fail due to indentation/alignment in.
	 * the template not matching the sample strings set in the tests.
	 *
	 * @since 1.0.1
	 *
	 * @param string $output The captured output.
	 * @return string The $output string, sans newlines and tabs.
	 */
	public function filter_output( $output ) {

		$output = preg_replace( '/[\n]+/S', '', $output );
		$output = preg_replace( '/[\t]+/S', '', $output );

		return $output;
	}

	/**
	 * Throws an exception with an optional message and code.
	 *
	 * Note: can't use `throwException` as that's reserved.
	 *
	 * @since 1.0.1
	 * @param string $message Optional. The exception message. Default is empty.
	 * @param int    $code    Optional. The exception code. Default is empty.
	 * @throws \Exception Containing the given message and code.
	 */
	public function throwAnException( $message = null, $code = null ) {
		$message = $message ? $message : "We're all doomed!";
		throw new \Exception( $message, $code );
	}

}
