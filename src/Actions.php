<?php

namespace EverAccounting;

use EverAccounting\Models\Expense;
use EverAccounting\Models\Payment;

defined( 'ABSPATH' ) || exit();

/**
 * Class Actions
 *
 * @package EverAccounting
 */
class Actions extends Singleton {

	/**
	 * Actions constructor.
	 */
	protected function __construct() {
		add_action( 'init', array( __CLASS__, 'get_actions' ) );
		add_action( 'update_option_eac_base_currency', array( __CLASS__, 'update_base_currency' ), 10, 2 );
		add_action( 'ever_accounting_payment_saved', array( __CLASS__, 'update_invoice_total' ) );
		add_action( 'ever_accounting_payment_deleted', array( __CLASS__, 'update_invoice_total' ) );
		add_action( 'ever_accounting_expense_saved', array( __CLASS__, 'update_bill_total' ) );
		add_action( 'ever_accounting_expense_deleted', array( __CLASS__, 'update_bill_total' ) );
	}

	/**
	 * Hooks EAC actions, when present in the $_GET superglobal. Every eac_action
	 * present in $_GET is called using WordPress's do_action function. These
	 * functions are called on init.
	 *
	 * @since 1.0
	 * @return void
	 */
	public static function get_actions() {
		$key = ! empty( $_GET['eac_action'] ) ? sanitize_key( $_GET['eac_action'] ) : false;

		if ( ! empty( $key ) ) {
			do_action( "eac_action_{$key}", $_GET );
		}
	}

	/**
	 * Update base currency.
	 *
	 * @param string $old_value Old value.
	 * @param string $new_value New value.
	 *
	 * @since 1.0.0
	 */
	public static function update_base_currency( $old_value, $new_value ) {
		// If the transaction table is not empty, we will revert the base currency to the old value.
		// Otherwise, we will update currencies rate based on the new base currency.
		$old_value         = strtoupper( $old_value );
		$new_value         = strtoupper( $new_value );
		$transaction_count = eac_get_transactions( [], true );
		$base_currency     = eac_get_currency( $new_value );
		if ( $transaction_count > 0 ) {
			remove_action( 'update_option_eac_base_currency', array( __CLASS__, 'update_base_currency' ) );
			update_option( 'eac_base_currency', $old_value );

			// die with a message.
			return;
		}
		// If new currency is not found, we will revert the base currency to the old value.
		if ( ! $base_currency ) {
			remove_action( 'update_option_eac_base_currency', array( __CLASS__, 'update_base_currency' ) );
			update_option( 'eac_base_currency', $old_value );

			// die with a message.
			return;
		}
		$currencies = eac_get_currencies( [ 'limit' => - 1 ] );
		foreach ( $currencies as $currency ) {
			$currency->set_exchange_rate( $currency->get_exchange_rate() / $base_currency->get_exchange_rate() );
			$currency->save();
		}

		// Update the base currency rate to 1.
		$base_currency->set_exchange_rate( 1 );
		$base_currency->save();
	}

	/**
	 * Update invoice total.
	 *
	 * @param Payment $payment Payment object.
	 *
	 * @since 1.0.0
	 */
	public static function update_invoice_total( $payment ) {
		$document_id = $payment->get_document_id();
		if ( ! empty( $document_id ) ) {
			$invoice = eac_get_invoice( $document_id );
			if ( $invoice ) {
				$invoice->save();
			}
		}
	}

	/**
	 * Update bill total.
	 *
	 * @param Expense $expense Expense object.
	 *
	 * @since 1.0.0
	 */
	public static function update_bill_total( $expense ) {
		$document_id = $expense->get_document_id();
		if ( ! empty( $document_id ) ) {
			$bill = eac_get_bill( $document_id );
			if ( $bill ) {
				$bill->save();
			}
		}
	}
}
