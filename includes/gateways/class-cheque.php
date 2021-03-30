<?php

namespace EverAccounting\Gateways;

use EverAccounting\Abstracts\Gateway;

/**
 * Class Paypal_Payment
 * @package EverAccounting\Gateways
 */
class Cheque extends Gateway {


	/**
	 * Paypal constructor.
	 */
	public function __construct() {
		$this->id    = 'cheque';
		$this->title = __( 'Cheque', 'wp-ever-accounting' );
		parent::__construct();
	}


}
