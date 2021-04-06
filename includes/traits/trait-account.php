<?php
/**
 * Account Trait
 *
 * Handles the account trait
 *
 * @package Traits
 */

namespace EverAccounting\Traits;

defined( 'ABSPATH' ) || exit;

trait Account {

	/**
	 * Get account object.
	 *
	 * @return \EverAccounting\Models\Account|\stdClass
	 */
	public function get_account() {
		if ( ! is_callable( array( $this, 'get_account_id' ) ) ) {
			return new \stdClass();
		}

		$account_id = $this->get_account_id();
		$account    = eaccounting_get_account( $account_id );
		return empty( $account ) ? new \stdClass() : $account;
	}

	/**
	 * Set account object.
	 *
	 * @param array|object $account the account object.
	 */
	public function set_account( $account = null ) {
		if ( ! is_callable( array( $this, 'set_account_id' ) ) ) {
			return;
		}
		if ( empty( $account ) || ! is_array( $account ) || ! is_object( $account ) ) {
			return;
		}
		$account = get_object_vars( $account );
		if ( empty( $account['id'] ) ) {
			return;
		}

		$this->set_account_id( absint( $account['id'] ) );
	}
}
