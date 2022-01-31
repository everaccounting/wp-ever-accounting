<?php
/**
 * Handle the bill object.
 *
 * @package     EverAccounting
 * @class       Invoice
 * @version     1.1.0
 */

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;

/**
 * Class Bill
 */
class Bill extends Document {
	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'bill';

	/**
	 * Bill constructor.
	 *
	 * @param int|bill|object|null $invoice invoice instance.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $document = 0 ) {
		$this->core_data['type']       = 'bill';
		parent::__construct( $document );

		if ( $this->type !== 'bill' ) {
			$this->set_id( 0 );
			$this->set_defaults();
		}
	}

	/**
	 * Get supported statues
	 *
	 * @return array
	 * @since 1.1.0
	 */
	public function get_statuses() {
		return Documents::get_bill_statuses();
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	|
	*/
	/**
	 * Generate document number.
	 *
	 * @return void
	 * @since 1.1.0
	 */
	public function maybe_set_bill_number() {
		if ( empty( $this->get_bill_number() ) ) {
			$number = $this->get_id();
			if ( empty( $number ) ) {
				$number = Documents::get_next_number( $this );
			}
			$this->set_document_number( $this->generate_number( $number ) );
		}
	}

	/**
	 * Generate number.
	 *
	 * @param $number
	 *
	 * @return string
	 * @since 1.1.0
	 *
	 */
	public function generate_number( $number ) {
		$prefix           = eaccounting()->settings->get( 'bill_prefix', 'BILL-' );
		$padd             = (int) eaccounting()->settings->get( 'bill_digit', '5' );
		$formatted_number = zeroise( absint( $number ), $padd );
		$number           = apply_filters( 'eaccounting_generate_bill_number', $prefix . $formatted_number );

		return $number;
	}

	/**
	 * Set the vendor id
	 *
	 * @param int $vendor_id Vendor ID
	 * @since 1.1.0
	 */
	public function set_vendor( $vendor_id ) {
		if ( $this->get_contact_id() && ( ! $this->exists() || array_key_exists( 'contact_id', $this->changes ) ) ) {
			$contact = new Vendor( $this->get_contact_id() );
			$address = $this->core_data['address'];
			foreach ( $address as $prop => $value ) {
				$getter = "get_{$prop}";
				$setter = "set_{$prop}";
				if ( is_category( array( $contact, $getter ) )
				     && is_callable( array( $this, $setter ) )
				     && is_callable( array( $this, $getter ) ) ) {
					$this->$setter( $contact->$getter() );
				}

			}
		}
	}

}
