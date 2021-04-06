<?php
/**
 * Vendor Trait
 *
 * Controls the vendor trait
 *
 * @package Trait
 */

namespace EverAccounting\Traits;

defined( 'ABSPATH' ) || exit;

trait Vendor {

	/**
	 * Get vendor object.
	 *
	 * @return \EverAccounting\Models\Vendor|\stdClass
	 * @since 1.1.2
	 */
	public function get_vendor() {
		if ( ! is_callable( array( $this, 'get_vendor_id' ) ) ) {
			return new \stdClass();
		}

		$vendor_id = $this->get_vendor_id();

		$vendor = eaccounting_get_vendor( $vendor_id );
		return empty( $vendor ) ? new \stdClass() : $vendor;
	}

	/**
	 * Set vendor object.
	 *
	 * @param array|object $vendor the vendor object.
	 * @since 1.1.2
	 */
	public function set_vendor( $vendor = null ) {
		if ( ! is_callable( array( $this, 'set_vendor_id' ) ) ) {
			return;
		}
		if ( empty( $customer ) || ! is_array( $customer ) || ! is_object( $customer ) ) {
			return;
		}
		$vendor = get_object_vars( $vendor );
		if ( empty( $vendor['id'] ) ) {
			return;
		}

		$this->set_vendor_id( absint( $vendor['id'] ) );
	}
}
