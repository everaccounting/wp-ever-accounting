<?php
/**
 * Customer Trait
<<<<<<< HEAD
 *
 * Handles the customer trait
 *
 * @package Traits
=======
>>>>>>> develop
 */

namespace EverAccounting\Traits;

defined( 'ABSPATH' ) || exit;

trait Customer {

	/**
	 * Get customer object.
	 *
	 * @return \EverAccounting\Models\Customer|\stdClass

	 * @since 1.1.4
	 */
	public function get_customer() {

		if ( ! is_callable( array( $this, 'get_customer_id' ) ) ) {
			return new \stdClass();
		}

		$customer_id = $this->get_customer_id();

		$customer = eaccounting_get_customer( $customer_id );

		return empty( $customer ) ? new \stdClass() : $customer;
	}

	/**
	 * Set customer object.
	 *
	 * @param array|object $customer the customer object.

	 * @since 1.1.4
	 */
	public function set_customer( $customer = null ) {
		if ( ! is_callable( array( $this, 'set_customer_id' ) ) ) {
			return;
		}
		if ( empty( $customer ) || ! is_array( $customer ) || ! is_object( $customer ) ) {
			return;
		}
		$customer = get_object_vars( $customer );
		if ( empty( $customer['id'] ) ) {
			return;
		}

		$this->set_customer_id( absint( $customer['id'] ) );
	}
}

