<?php

namespace EverAccounting;

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
		add_action( 'ever_accounting_revenue_saved', array( $this, 'recalculate_invoice' ) );
		add_action( 'ever_accounting_expense_saved', array( $this, 'recalculate_invoice' ) );
		add_action( 'ever_accounting_revenue_deleted', array( $this, 'recalculate_invoice' ) );
		add_action( 'ever_accounting_expense_deleted', array( $this, 'recalculate_invoice' ) );
	}

	/**
	 * Recalculate invoice.
	 *
	 * @param object $transaction Transaction object.
	 *
	 * @since 1.0.0
	 */
	public function recalculate_invoice( $transaction ) {
//		if ( empty( $transaction->document_id ) ) {
//			return;
//		}
//		$invoice = eac_get_invoice( $transaction->document_id );
//		if ( ! $invoice ) {
//			return;
//		}
//
//		$invoice->calculate_totals();
//		$invoice->save();
	}
}
