<?php

namespace Factory;

class Factory extends \WP_UnitTest_Factory {

	/**
	 * Category factory.
	 *
	 * @var CategoryFactory
	 */
	public $category;

	/**
	 * Tax factory.
	 *
	 * @var TaxFactory
	 */
	public $tax;

	/**
	 * Constructor.
	 */
	public function __construct() {
		// parent::__construct();
		$this->category = new CategoryFactory( $this );
		$this->tax      = new TaxFactory( $this );
	}
}
