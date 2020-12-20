<?php
/**
 * Invoice Controller
 *
 * Handles invoice's insert, update and delete events.
 *
 * @package     EverAccounting\Controllers
 * @class       InvoiceController
 * @version     1.1.0
 */

namespace EverAccounting\Controllers;

use EverAccounting\Abstracts\Singleton;
use EverAccounting\Core\Emails;


defined( 'ABSPATH' ) || exit;

/**
 * Class InvoiceController
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Controllers
 */
class InvoiceController extends Singleton {

	/**
	 * RevenueController constructor.
	 */
	public function __construct() {
		add_action('eaccounting_invoice_action_send_customer_invoice', array( __CLASS__, 'send_customer_invoice'));
	}

	public static function send_customer_invoice($invoice){
		Emails::send_customer_invoice($invoice);
	}

}
