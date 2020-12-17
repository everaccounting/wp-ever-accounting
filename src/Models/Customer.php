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

use EverAccounting\Core\Repositories;
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
	 * Get the account if ID is passed, otherwise the account is new and empty.
	 *
	 * @since 1.0.2
	 *
	 * @param int|object $data object to read.
	 */
	public function __construct( $data = 0 ) {
		parent::__construct( $data );

		if ( $data instanceof self ) {
			$this->set_id( $data->get_id() );
		} elseif ( is_numeric( $data ) ) {
			$this->set_id( $data );
		} elseif ( ! empty( $data->id ) ) {
			$this->set_id( $data->id );
		} elseif ( is_array( $data ) ) {
			$this->set_props( $data );
		} else {
			$this->set_object_read( true );
		}

		//Load repository
		$this->repository = Repositories::load( 'contact-customer' );

		if ( $this->get_id() > 0 ) {
			$this->repository->read( $this );
		}

		// If not vendor then reset to default
		if ( self::CONTACT_TYPE !== $this->get_type() ) {
			$this->set_id( 0 );
			$this->set_defaults();
		}
	}

	/**
	 * Save should create or update based on object existence.
	 *
	 * @since  1.1.0
	 * @return \Exception|bool
	 */
	public function save() {
		$this->set_type( self::CONTACT_TYPE );

		return parent::save();
	}
}
