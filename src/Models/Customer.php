<?php
/**
 * Handle the customer object.
 *
 * @package     EverAccounting\Models
 * @class       Customer
 * @version     1.0.2
 */

namespace EverAccounting\Models;

use EverAccounting\Abstracts\ContactModel;
use EverAccounting\Repositories\Customers;

defined( 'ABSPATH' ) || exit;

/**
 * Class Customer
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Models
 */
class Customer extends ContactModel {

	/**
	 * Customer constructor.
	 */
	public function __construct( $data = 0 ) {
		$this->repository = Customers::instance();
		parent::__construct( $data );
		// If not Customer then reset to default
		if ( 'customer' !== $this->get_type() ) {
			$this->set_id( 0 );
			$this->set_defaults();
		}
	}

}
