<?php
/**
 * EverAccounting Unit Test Factory
 *
 * Provides EverAccounting-specific factories.
 *
 */

namespace EverAccounting\Tests\Framework;

use EverAccounting\Tests\Framework\Factories\Account_Factory;
use EverAccounting\Tests\Framework\Factories\Category_Factory;
use EverAccounting\Tests\Framework\Factories\Customer_Factory;
use EverAccounting\Tests\Framework\Factories\Item_Factory;
use EverAccounting\Tests\Framework\Factories\Revenue_Factory;
use EverAccounting\Tests\Framework\Factories\Vendor_Factory;

//require_once dirname( __FILE__ ) . '/factories/class-customer-factory.php';
//require_once dirname( __FILE__ ) . '/factories/class-vendor-factory.php';
//require_once dirname( __FILE__ ) . '/factories/class-account-factory.php';
//require_once dirname( __FILE__ ) . '/factories/class-category-factory.php';
//require_once dirname( __FILE__ ) . '/factories/class-item-factory.php';
//require_once dirname( __FILE__ ) . '/factories/class-revenue-factory.php';


class Factory extends \WP_UnitTest_Factory {

	/**
	 * @var Customer_Factory
	 */
	public $customer;

	/**
	 * @var Vendor_Factory
	 */
	public $vendor;

	/**
	 * @var Account_Factory
	 */
	public $account;

	/**
	 * @var Category_Factory
	 */
	public $category;

	/**
	 * @var Item_Factory
	 */
	public $item;

	/**
	 * @var Revenue_Factory
	 */
	public $revenue;

	/**
	 * Setup factories.
	 */
	public function __construct() {
		parent::__construct();

//		$this->customer = new Customer_Factory( $this );
//		$this->vendor = new Vendor_Factory( $this );
//		$this->account = new Account_Factory( $this );
//		$this->category = new Category_Factory( $this );
//		$this->item = new Item_Factory( $this );
//		$this->revenue = new Revenue_Factory( $this );
	}
}
