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
