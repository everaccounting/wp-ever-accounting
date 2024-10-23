<?php

namespace EverAccounting;

use EverAccounting\Models\Bill;
use EverAccounting\Models\Payment;

defined( 'ABSPATH' ) || exit;

/**
 * Class Documents.
 *
 * @since   1.0.0
 * @package EverAccounting\Controllers
 */
class Documents {

	/**
	 * Documents constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// add_action( 'eac_payment_inserted', array( __CLASS__, 'invoice_payment_updated' ) );
		// add_action( 'eac_payment_deleted', array( __CLASS__, 'invoice_payment_updated' ) );
		// add_action( 'eac_payment_updated', array( __CLASS__, 'invoice_payment_updated' ) );
		add_action( 'eac_bill_status_transition', array( __CLASS__, 'bill_status_transition' ), 10, 2 );
	}

	/**
	 * Payment updated so we need to update the invoice status.
	 *
	 * @param Payment $payment The payment being edited or deleted.
	 *
	 * @return void
	 */
	public static function invoice_payment_updated( $payment ) {
		$original = $payment->get_original();
		// if ( array_key_exists( 'contact_id', $original ) && $original['contact_id'] !== $payment->contact_id && $original['contact_id'] > 0 ) {
		// $old_customer = EAC()->customers->get( $original['contact_id'] );
		// if ( $old_customer ) {
		// $old_customer->update_amount_paid();
		// }
		// }
	}

	/**
	 * Bill status transition.
	 *
	 * @param Bill   $bill Bill object.
	 * @param string $status New status.
	 */
	public static function bill_status_transition( $bill, $status ) {
		// add a note with the status change.
		$bill->notes()->insert(
			array(
				'content' => sprintf( __( 'Status changed to %s.', 'wp-ever-accounting' ), $status ),
				'type'    => 'bill',
			)
		);
	}
}
