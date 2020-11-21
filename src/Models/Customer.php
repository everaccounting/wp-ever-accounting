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
	 * Type of the contact.
	 */
	const CONTACT_TYPE = 'customer';

	/**
	 * Customer constructor.
	 *
	 * @param int $data
	 */
	public function __construct( $data = 0 ) {
		parent::__construct( $data );

		// If not vendor then reset to default
		if ( self::CONTACT_TYPE !== $this->get_type() ) {
			$this->set_id( 0 );
			$this->set_defaults();
		}
	}
}
