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
use EverAccounting\Tests\Framework\Factories\Payment_Factory;
use EverAccounting\Tests\Framework\Factories\Currency_Factory;

require_once dirname( __FILE__ ) . '/factories/class-customer-factory.php';
require_once dirname( __FILE__ ) . '/factories/class-vendor-factory.php';
require_once dirname( __FILE__ ) . '/factories/class-account-factory.php';
require_once dirname( __FILE__ ) . '/factories/class-category-factory.php';
require_once dirname( __FILE__ ) . '/factories/class-item-factory.php';
require_once dirname( __FILE__ ) . '/factories/class-revenue-factory.php';
require_once dirname( __FILE__ ) . '/factories/class-payment-factory.php';
require_once dirname( __FILE__ ) . '/factories/class-currency-factory.php';


class Factory extends \WP_UnitTest_Factory {

	/**
	 * @var Customer_Factory
	 * @var Vendor_Factory
	 * @var Account_Factory
	 * @var Category_Factory
	 * @var Item_Factory
	 * @var Revenue_Factory
	 * @var Payment_Factory
	 * @var Currency_Factory
	 */
	public $customer;
	public $vendor;
	public $account;
	public $category;
	public $item;
	public $revenue;
	public $payment;
	public $currency;

	/**
	 * Setup factories.
	 */
	public function __construct() {
		parent::__construct();

		$this->customer = new Customer_Factory( $this );
		$this->vendor   = new Vendor_Factory( $this );
		$this->account  = new Account_Factory( $this );
		$this->category = new Category_Factory( $this );
		$this->item     = new Item_Factory( $this );
		$this->revenue  = new Revenue_Factory( $this );
		$this->payment  = new Payment_Factory( $this );
		$this->currency  = new Currency_Factory( $this );
	}
}
