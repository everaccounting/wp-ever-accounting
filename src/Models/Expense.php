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
use EverAccounting\Core\Exception;
use EverAccounting\Core\Repositories;
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
	 * Type of the contact.
	 */
	const TRANS_TYPE = 'expense';

	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = self::TRANS_TYPE;

	/**
	 * Get the expense if ID is passed, otherwise the account is new and empty.
	 *
	 * @since 1.0.2
	 *
	 * @param int|object|Expense $data object to read.
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
		$this->repository = Repositories::load( 'transaction-expense' );

		if ( $this->get_id() > 0 ) {
			$this->repository->read( $this );
		}

		// If not vendor then reset to default
		if ( self::TRANS_TYPE !== $this->get_type() ) {
			$this->set_id( 0 );
			$this->set_defaults();
		}
	}

	/**
	 * Return the contact name.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	public function get_vendor_name() {
		$vendor = eaccounting_get_vendor( $this->get_contact_id() );

		return $this->get_object_prop( $vendor, 'name' );
	}

	/**
	 * Return the customer phone.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	public function get_vendor_phone() {
		$vendor = eaccounting_get_vendor( $this->get_contact_id() );

		return $this->get_object_prop( $vendor, 'phone' );
	}

	/**
	 * Return the customer email.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	public function get_vendor_email() {
		$vendor = eaccounting_get_vendor( $this->get_contact_id() );

		return $this->get_object_prop( $vendor, 'email' );
	}

	/**
	 * Return the customer tax number.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	public function get_vendor_tax_number() {
		$vendor = eaccounting_get_vendor( $this->get_contact_id() );

		return $this->get_object_prop( $vendor, 'tax_number' );
	}

	/**
	 * Return the customer postcode.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	public function get_vendor_postcode() {
		$vendor = eaccounting_get_vendor( $this->get_contact_id() );

		return $this->get_object_prop( $vendor, 'postcode' );
	}

	/**
	 * Return the customer address.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	public function get_vendor_address() {
		$vendor = eaccounting_get_vendor( $this->get_contact_id() );

		return $this->get_object_prop( $vendor, 'postcode' );
	}

	/**
	 * Return the customer country.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	public function get_vendor_country() {
		$vendor = eaccounting_get_vendor( $this->get_contact_id() );

		return $this->get_object_prop( $vendor, 'country' );
	}

	/**
	 * Return the Vendor country.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_vendor_country_nicename() {
		$vendor = eaccounting_get_vendor( $this->get_contact_id() );

		return $this->get_object_prop( $vendor, 'country_nicename' );
	}

	/**
	 * Save should create or update based on object existence.
	 *
	 * @since  1.1.0
	 * @throws Exception
	 * @return \Exception|bool
	 */
	public function save() {
		$this->set_type( self::TRANS_TYPE );

		return parent::save();
	}
}
