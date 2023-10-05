<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class Bill.
 *
 * @since   1.0.0
 * @package EverAccounting\Models
 */
class Bill extends Document {

	/**
	 * Object type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $object_type = 'bill';

	/**
	 * Constructor.
	 *
	 * @param int|object|array $data Object ID, post object, or array of data.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $data = 0 ) {
		$core_data       = array(
			'type'       => $this->object_type,
			'status'     => 'draft',
			'issue_date' => wp_date( 'Y-m-d' ),
			'due_date'   => wp_date( 'Y-m-d', strtotime( '+' . get_option( 'eac_bill_due_date', 7 ) . ' days' ) ),
			'note'       => get_option( 'eac_bill_notes', '' ),
		);
		$this->core_data = array_merge( $this->core_data, $core_data );
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
	 * @since 1.0.0
	 * @return bool|\WP_Error True on success, false or WP_Error on failure.
	 */
	public function delete( $force_delete = false ) {
		$this->delete_payments();

		return parent::delete( $force_delete );
	}

	/**
	 * Saves an object in the database.
	 *
	 * @throws \Exception When the bill is already paid.
	 * @since 1.0.0
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 */
	public function save() {
		// draft, sent, partial, paid, cancelled, overdue.
		$this->calculate_totals();
		$old_status = isset( $this->data['status'] ) ? $this->data['status'] : 'draft';
		$new_status = isset( $this->changes['status'] ) ? $this->changes['status'] : $old_status;

		$due_date = empty( $this->get_due_date() ) ? 0 : strtotime( $this->get_due_date() );
		// If bill is not paid with due date, set the status to draft.
		if ( $this->get_balance() > 0 && $this->has_due_date() > 0 && $due_date < time() ) {
			$new_status = 'overdue';
		} elseif ( $this->get_total_paid() > 0 && $this->get_balance() > 0 ) {
			$new_status = 'partial';
		} elseif ( $this->get_total_paid() >= $this->get_total() ) {
			$new_status = 'paid';
		}

		// if status is sent and sent date is empty, set the sent date.
		if ( $new_status === 'sent' && empty( $this->get_sent_date() ) ) {
			$this->set_sent_date( current_time( 'mysql' ) );
		}
		// if status is paid and paid date is empty, set the paid date.
		if ( $new_status === 'paid' && empty( $this->get_payment_date() ) ) {
			$this->set_payment_date( current_time( 'mysql' ) );
		}


		// If the status is changed, update the status.
		if ( $old_status !== $new_status ) {
			$this->set_status( $new_status );

			/**
			 * Fires when the bill status is changed.
			 *
			 * @param string  $new_status New status.
			 * @param string  $old_status Old status.
			 * @param Invoice $bill Invoice object.
			 *
			 * @since 1.0.0
			 */
			do_action( 'ever_accounting_bill_status_transition', $new_status, $old_status, $this );
			/**
			 * Fires when the bill status is changed.
			 *
			 * @param string  $new_status New status.
			 * @param string  $old_status Old status.
			 * @param Invoice $bill Invoice object.
			 *
			 * @since 1.0.0
			 */
			do_action( 'ever_accounting_bill_status_transition_' . $new_status, $new_status, $old_status, $this );
		}

		return parent::save();
	}

	/**
	 * Prepare where query.
	 *
	 * @param array $clauses Query clauses.
	 * @param array $args Array of args to pass to the query method.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	protected function prepare_where_query( $clauses, $args = array() ) {
		global $wpdb;
		$clauses['where'] .= $wpdb->prepare( " AND {$this->table_name}.type = %s", 'bill' ); // phpcs:ignore

		return parent::prepare_where_query( $clauses, $args );
	}

	/*
	|--------------------------------------------------------------------------
	| Calculations
	|--------------------------------------------------------------------------
	| This section contains methods for calculating totals.
	*/

	/**
	 * Prepare object for database.
	 * This method is called before saving the object to the database.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function calculate_totals() {
		$payments   = $this->get_payments();
		$total_paid = 0;
		foreach ( $payments as $payment ) {
			$total_paid += eac_convert_money( $payment->get_amount(), $payment->get_currency_code(), $this->get_currency_code(), $payment->get_exchange_rate(), $this->get_exchange_rate() );
		}
		$this->set_total_paid( $total_paid );
		parent::calculate_totals();
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
	 * @since 1.0.0
	 * @return Transaction[]
	 */
	public function get_payments( $args = array() ) {
		$payments = array();
		if ( $this->get_id() ) {
			$payments = eac_get_payments(
				array_merge(
					array(
						'document_id' => $this->get_id(),
						'limit'       => - 1,
					),
					$args
				)
			);
		}

		return $payments;
	}

	/**
	 * Remove payments.
	 *
	 * @since 1.0.0
	 * @return void
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
	 * @since 1.0.0
	 * @return int| \WP_Error Payment ID on success, WP_Error otherwise.
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

	/*
	|--------------------------------------------------------------------------
	| Helper methods.
	|--------------------------------------------------------------------------
	| Utility methods which don't directly relate to this object but may be
	| used by this object.
	*/
	/**
	 * Get document number prefix.
	 *
	 * @since 1.1.6
	 * @return string
	 */
	public function get_number_prefix() {
		return get_option( 'eac_bill_prefix', 'BILL-' );
	}

	/**
	 * Get formatted document number.
	 *
	 * @since 1.1.6
	 * @return string
	 */
	public function get_next_number() {
		$number     = $this->get_max_number();
		$prefix     = $this->get_number_prefix();
		$number     = absint( $number ) + 1;
		$min_digits = get_option( 'eac_bill_digits', 4 );
		$number     = str_pad( $number, $min_digits, '0', STR_PAD_LEFT );

		return implode( '', [ $prefix, $number ] );
	}

	/**
	 * Get status label.
	 *
	 * @since 1.1.6
	 * @return string
	 */
	public function get_status_label() {
		$statuses = eac_get_bill_statuses();
		$status   = $this->get_status();

		return isset( $statuses[ $status ] ) ? $statuses[ $status ] : '';
	}
}
