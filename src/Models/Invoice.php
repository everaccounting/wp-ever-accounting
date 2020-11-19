<?php
/**
 * Handle the invoice object.
 *
 * @package     EverAccounting\Models
 * @class       Currency
 * @version     1.0.2
 */

namespace EverAccounting\Models;

use EverAccounting\Abstracts\ResourceModel;
use EverAccounting\Repositories\Invoices;

defined( 'ABSPATH' ) || exit;

/**
 * Class Invoice
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Models
 */
class Invoice extends ResourceModel {

	/**
	 * Get the Invoice if ID is passed, otherwise the account is new and empty.
	 *
	 * @since 1.0.2
	 *
	 * @param int|object|Invoice $data object to read.
	 */
	public function __construct( $data = 0 ) {
		parent::__construct( $data, Invoices::instance() );
	}
}
