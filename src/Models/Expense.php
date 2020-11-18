<?php
/**
 * Handle the revenue object.
 *
 * @package     EverAccounting\Models
 * @class       Revenue
 * @version     1.0.2
 */

namespace EverAccounting\Models;

use EverAccounting\Abstracts\TransactionModel;
use EverAccounting\Repositories\Expenses;

defined( 'ABSPATH' ) || exit;

/**
 * Class Revenue
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Models
 */
class Expense extends TransactionModel {

	/**
	 * Payment constructor.
	 */
	public function __construct( $data = 0 ) {
		parent::__construct( $data, Expenses::instance() );

		// If not expense then reset to default
		if ( 'expense' !== $this->get_type() ) {
			$this->set_id( 0 );
			$this->set_defaults();
		}
	}

}
