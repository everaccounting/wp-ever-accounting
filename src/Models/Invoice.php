<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class Invoice.
 *
 * @since   1.0.0
 * @package EverAccounting\Models
 */
class Invoice extends Document {

	/**
	 * Object type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $object_type = 'invoice';

	/**
	 * Invoice constructor.
	 *
	 * @param int|object|array $data Object ID, post object, or array of data.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $data = 0 ) {
		$due_after                     = get_option( 'eac_invoice_due_date', 7 );
		$notes                         = get_option( 'eac_invoice_notes', '' );
		$due_date                      = wp_date( 'Y-m-d', strtotime( '+' . $due_after . ' days' ) );
		$this->core_data['type']       = $this->object_type;
		$this->core_data['issue_date'] = wp_date( 'Y-m-d' );
		$this->core_data['due_date']   = $due_date;
		$this->core_data['note']       = $notes;
		parent::__construct( $data );

		// after reading check if the contact is a customer.
		if ( $this->exists() && $this->object_type !== $this->get_type() ) {
			$this->set_id( 0 );
			$this->set_defaults();
		}
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	|
	| Methods which create, read, update and delete discounts from the database.
	|
	*/
	/**
	 * Deletes an object from the database.
	 *
	 * @param bool $force_delete Whether to bypass trash and force deletion. Default false.
	 *
	 * @return bool|\WP_Error True on success, false or WP_Error on failure.
	 * @since 1.0.0
	 */
	public function delete( $force_delete = false ) {
		$this->delete_payments();

		return parent::delete( $force_delete );
	}

	/**
	 * Saves an object in the database.
	 *
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 * @throws \Exception When the invoice is already paid.
	 * @since 1.0.0
	 */
	public function save() {
		// draft, sent, partial, paid, cancelled, overdue.
		$old_status    = $this->get_status();
		$new_status    = $this->data['status'];
		$total_paid    = (int) $this->get_total_paid();
		$invoice_total = (int) $this->get_total();
		$due_date      = empty( $this->get_due_date() ) ? 0 : strtotime( $this->get_due_date() );

		// If invoice is not paid with due date, set the status to draft.
		if ( $invoice_total > $total_paid && $due_date > 0 && $due_date < time() ) {
			$new_status = 'overdue';
		} elseif ( $total_paid > 0 && $invoice_total > $total_paid ) {
			$new_status = 'partial';
		} elseif ( $total_paid >= $invoice_total ) {
			$new_status = 'paid';
		}

		// If the status is changed, update the status.
		if ( $old_status !== $new_status ) {
			$this->set_status( $new_status );

			/**
			 * Fires when the invoice status is changed.
			 *
			 * @param string $new_status New status.
			 * @param string $old_status Old status.
			 * @param Invoice $invoice Invoice object.
			 *
			 * @since 1.0.0
			 */
			do_action( 'ever_accounting_invoice_status_transition', $new_status, $old_status, $this );
			/**
			 * Fires when the invoice status is changed.
			 *
			 * @param string $new_status New status.
			 * @param string $old_status Old status.
			 * @param Invoice $invoice Invoice object.
			 *
			 * @since 1.0.0
			 */
			do_action( 'ever_accounting_invoice_status_transition_' . $new_status, $new_status, $old_status, $this );
		}
//		$total_paid = 0;
//		foreach ( $this->get_payments() as $payment ) {
//			$total_paid += eac_convert_money_from_base( $payment->get_amount(), $payment->get_currency_code(), $this->get_exchange_rate() );
//		}
//		$this->set_total_paid( $total_paid );
		return parent::save();
	}

	/**
	 * Prepare where query.
	 *
	 * @param array $clauses Query clauses.
	 * @param array $args Array of args to pass to the query method.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	protected function prepare_where_query( $clauses, $args = array() ) {
		global $wpdb;
		$clauses['where'] .= $wpdb->prepare( " AND {$this->table_name}.type = %s", 'invoice' ); // phpcs:ignore

		return parent::prepare_where_query( $clauses, $args );
	}

	/*
	|--------------------------------------------------------------------------
	|  Payments related methods
	|--------------------------------------------------------------------------
	| These methods are related to payments.
	*/
	/**
	 * Get payments.
	 *
	 * @param array $args Query arguments.
	 *
	 * @return Transaction[]
	 * @since 1.0.0
	 */
	public function get_payments( $args = array() ) {
		return eac_get_payments(
			array_merge(
				array(
					'document_id' => $this->get_id(),
					'limit'       => - 1,
				),
				$args
			)
		);
	}

	/**
	 * Remove payments.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function delete_payments() {
		foreach ( $this->get_payments() as $payment ) {
			$payment->delete();
		}
	}


	/**
	 * Add payment.
	 *
	 * @param array $data Payment data.
	 *
	 * @return int| \WP_Error Payment ID on success, WP_Error otherwise.
	 * @since 1.0.0
	 */
	public function add_payment( $data ) {
//		$data = wp_parse_args(
//			$data,
//			array(
//				'account_id'     => '',
//				'amount'         => 0,
//				'currency_code'  => $this->get_currency_code(),
//				'payment_method' => '',
//				'note'           => '',
//				'date'           => current_time( 'mysql' ),
//			)
//		);
		//
		// if amount is not set, set it to the total amount of the document.
		// if ( empty( $data['amount'] ) ) {
		// $data['amount'] = $this->get_total();
		// $data['currency_code'] = $this->get_currency_code();
		// }
		//
		// if ( empty( $args['account_id'] ) ) {
		// return new \WP_Error( 'missing_required', __( 'Payment account is required.', 'wp-ever-accounting' ) );
		// }
		// if ( empty( $args['payment_method'] ) ) {
		// return new \WP_Error( 'missing_required', __( 'Payment method is required.', 'wp-ever-accounting' ) );
		// }
		//
		// $account = eac_get_account( $data['account_id'] );
		// if ( ! $account ) {
		// return new \WP_Error( 'invalid_account', __( 'Invalid account.', 'wp-ever-accounting' ) );
		// }
		// if( $account->get_currency_code() != $data['currency_code'] ) {
		// $data['amount'] = eac_convert_money( $data['amount'], $data['currency_code'], $account->get_currency_code() );
		// $data['currency_code'] = $account->get_currency_code();
		// $data['exchange_rate'] = $this->get_exchange_rate();
		// }
		//
		// $payment = new Transaction();
		// $payment->set_data( array(
		// 'document_id'    => $this->get_id(),
		// 'account_id'     => $data['account_id'],
		// 'amount'         => $amount,
		// 'currency_code'  => $data['currency_code'],
		// 'exchange_rate'  => $this->get_exchange_rate(),
		// 'payment_method' => $data['payment_method'],
		// 'note'           => $data['note'],
		// 'date'           => $data['date'],
		// ) );
		// $saved = $payment->save();
		// if ( is_wp_error( $saved ) ) {
		// return $saved;
		// }
		//
		// return $payment->get_id();
	}
}
