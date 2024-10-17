<?php

namespace EverAccounting;

use EverAccounting\Models\Payment;

/**
 * Class Contacts
 *
 * @package EverAccounting
 */
class Contacts {

	/**
	 * Contacts constructor.
	 */
	public function __construct() {
		// customers.
		add_action( 'eac_payment_inserted', array( __CLASS__, 'update_customer_paid' ) );
		add_action( 'eac_payment_deleted', array( __CLASS__, 'update_customer_paid' ) );
		add_action( 'eac_payment_updated', array( __CLASS__, 'update_customer_paid' ) );
//		add_action( 'eac_invoice_insertedd', array( __CLASS__, 'update_customer_paid' ), 10, 2 );
//		add_action( 'eac_invoice_updated', array( __CLASS__, 'update_customer_paid' ), 10, 2 );
//		add_action( 'eac_invoice_deleted', array( __CLASS__, 'update_customer_paid' ), 10, 2 );
//		add_action( 'eac_delete_customer', array( __CLASS__, 'delete_customer_reference' ) );
	}

	/**
	 * Update customer paid.
	 *
	 * @param Payment $payment Payment.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function update_customer_paid( $payment ) {
		$original = $payment->get_original();
		if ( array_key_exists( 'contact_id', $original ) && $original['contact_id'] !== $payment->contact_id && $original['contact_id'] > 0 ) {
			$old_customer = EAC()->customers->get( $original['contact_id'] );
			if ( $old_customer ) {
				$old_customer->update_amount_paid();
			}
		}

		if ( $payment->customer ) {
			$payment->customer->update_amount_paid();
		}
	}

}
