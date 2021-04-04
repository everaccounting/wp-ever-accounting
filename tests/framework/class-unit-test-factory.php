<?php
/**
 * EverAccounting Unit Test Factory
 *
 * Provides EverAccounting-specific factories.
 *
 */

namespace EverAccounting\Tests\Framework;

use EverAccounting\Tests\Framework\Factories\Customer_Factory;
use EverAccounting\Tests\Framework\Factories\Vendor_Factory;

require_once dirname( __FILE__ ) . '/factories/class-customer-factory.php';

class Factory extends \WP_UnitTest_Factory {

	/**
	 * @var Customer_Factory
	 * @var Vendor_Factory
	 */
	public $customer;
	public $vendor;

	/**
	 * Setup factories.
	 */
	public function __construct() {
		parent::__construct();

		$this->customer = new Customer_Factory( $this );
		$this->vendor = new Vendor_Factory( $this );
	}
}
