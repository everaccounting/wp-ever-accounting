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

		if ( $this->get_id() > 0 && ! $this->get_object_read() ) {
			$customer = Customers::instance()->get( $this->get_id() );
			if ( $customer && 'customer' === $customer->get_type() ) {
				$this->set_props( $customer->get_data() );
				$this->set_object_read( $customer->exists() );
			} else {
				$this->set_id( 0 );
			}
		}
	}

}
