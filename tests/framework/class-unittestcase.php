<?php

namespace EverAccounting\Tests\Framework;

class UnitTestCase extends \WP_UnitTestCase{
	/**
	 * Holds the WC_Unit_Test_Factory instance.
	 *
	 * @var Factory
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
		$this->factory = new Factory();
		$this->setOutputCallback( array( $this, 'filter_output' ) );
	}


	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		global $current_user;
		$current_user = new \WP_User(1);
		$current_user->set_role('administrator');
		wp_update_user( array( 'ID' => 1, 'first_name' => 'Admin', 'last_name' => 'User' ) );

		wp_set_current_user( 1 );
	}

	public static function tearDownAfterClass() {
		// Clean existing install first.
		parent::tearDownAfterClass();
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

	/**
	 * @param $message
	 */
	public function log( $message ){
		fwrite(STDERR, print_r($message, TRUE));
	}
}
