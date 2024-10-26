<?php

namespace EverAccounting;

use EverAccounting\Models\Invoice;
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
		add_action( 'eac_payment_inserted', array( __CLASS__, 'invoice_payment_updated' ) );
		add_action( 'eac_payment_deleted', array( __CLASS__, 'invoice_payment_updated' ) );
		add_action( 'eac_payment_updated', array( __CLASS__, 'invoice_payment_updated' ) );
		add_action( 'eac_hourly_event', array( __CLASS__, 'maybe_overdue_invoices' ) );
		add_action( 'eac_invoice_status_transition', array( __CLASS__, 'invoice_status_transition' ), 10, 2 );
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
		if ( array_key_exists( 'document_id', $original ) && $original['document_id'] !== $payment->document_id && $original['document_id'] > 0 ) {
			$old_document = EAC()->invoices->get( $original['document_id'] );
			if ( $old_document ) {
				$old_document->calculate_totals();
				$old_document->save();
			}
		}

		if ( $payment->invoice_id && $payment->invoice ) {
			$invoice = $payment->invoice;
			$invoice->calculate_totals();
			$invoice->save();
		}
	}

	/**
	 * Check for overdue invoices.
	 *
	 * @return void
	 */
	public static function maybe_overdue_invoices() {
		global $wpdb;
		$invoices = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT id FROM {$wpdb->prefix}eac_documents WHERE status NOT IN ('paid', 'cancelled', 'draft', 'overdue') AND due_date < %s",
				current_time( 'mysql' )
			)
		);

		if ( ! empty( $invoices ) ) {
			foreach ( $invoices as $invoice_id ) {
				$invoice = EAC()->invoices->get( $invoice_id );
				if ( $invoice ) {
					$invoice->status = 'overdue';
					$invoice->save();
				}
			}
		}
	}

	/**
	 * Bill status transition.
	 *
	 * @param Invoice $invoice Invoice object.
	 * @param string  $status New status.
	 */
	public static function invoice_status_transition( $invoice, $status ) {
		$invoice->notes()->insert(
			array(
				'parent_type' => 'invoice',
				// translators: %s: status.
				'content'     => sprintf( __( 'Status changed to %s', 'wp-ever-accounting' ), esc_html( $status ) ),
				'created_by'  => get_current_user_id(),
			)
		);
	}
}
