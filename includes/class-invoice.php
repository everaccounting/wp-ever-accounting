<?php
/**
 * Handle the invoice object.
 *
 * @package     EverAccounting
 * @class       Invoice
 * @version     1.1.0
 */

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;

/**
 * Class Invoice
*/
class Invoice extends Document {
	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'invoice';

	/**
	 * Invoice constructor.
	 *
	 * @param int|invoice|object|null $invoice invoice instance.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $document = 0 ) {
		$this->core_data['type']       = 'invoice';
		parent::__construct( $document );

		if ( $this->type !== 'invoice' ) {
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
		return Documents::get_invoice_statuses();
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
	 * @since 1.1.0
	 * @return void
	 */
	public function maybe_set_invoice_number() {
		if( empty( $this->get_document_number() ) ) {
			$number = $this->get_id();
			if( empty( $number )) {
				$number = Documents::get_next_number( $this );
			}
			$this->set_document_number( $this->generate_number( $number ) );
		}
	}

	/**
	 * Set the document key.
	 *
	 * @since 1.1.0
	 */
	public function maybe_set_key() {
		$key = $this->get_key();
		if ( empty( $key ) ) {
			$this->set_key( $this->generate_key() );
		}
	}

	/**
	 * Generate key.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function generate_key() {
		$key = 'ea-' . apply_filters( 'eaccounting_generate_invoice_key', 'invoice' . '-' . str_replace( '-', '', wp_generate_uuid4() ) );
		return strtolower( sanitize_key( $key ) );
	}


	/**
	 * Generate number
	 *
	 * @param string $number Number
	 *
	 * @return string
	 * @since 1.1.0
	*/
	public function generate_number( $number ) {
		$prefix           = eaccounting()->settings->get( 'invoice_prefix', 'INV-' );
		$padd             = (int) eaccounting()->settings->get( 'invoice_digit', '5' );
		$formatted_number = zeroise( absint( $number ), $padd );
		$number           = apply_filters( 'eaccounting_generate_invoice_number', $prefix . $formatted_number );

		return $number;
	}

	/**
	 * Set the customer id
	 *
	 * @param int $customer_id Customer ID
	 * @since 1.1.0
	*/
	public function set_customer_id( $customer_id ) {
		if ( $this->get_contact_id() && ( ! $this->exists() || array_key_exists( 'contact_id', $this->changes ) ) ) {
			$contact = new Customer( $this->get_contact_id() );
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
