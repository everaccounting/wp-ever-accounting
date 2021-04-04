<?php
/**
 * EverAccounting Unit Test Factory
 *
 * Provides EverAccounting-specific factories.
 *
 */

namespace EverAccounting\Tests\Framework;

use EverAccounting\Tests\Framework\Factories\Customer_Factory;

require_once dirname( __FILE__ ) . '/factories/class-customer-factory.php';

class Factory extends \WP_UnitTest_Factory {

	/**
	 * @var Customer_Factory
	 */
	public $customer;

	/**
	 * Setup factories.
	 */
	public function __construct() {
		parent::__construct();

		$this->customer = new Customer_Factory( $this );
	}
}
