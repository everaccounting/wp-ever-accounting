<?php
/**
 * Handle the payment object.
 *
 * @package     EverAccounting\Models
 * @class       Payment
 * @version     1.0.2
 */
namespace EverAccounting\Models;

use EverAccounting\Abstracts\TransactionModel;
use EverAccounting\Repositories\Incomes;

defined( 'ABSPATH' ) || exit;

/**
 * Class Payment
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Models
 */
class Income extends TransactionModel {

	/**
	 * Payment constructor.
	 */
	public function __construct( $data = 0 ) {
		parent::__construct( $data, Incomes::instance() );

		// If not Income then reset to default
		if ( 'income' !== $this->get_type() ) {
			$this->set_id( 0 );
			$this->set_defaults();
		}
	}

}
