<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class Contact
 *
 * @since   1.0.0
 * @package EverAccounting\Models
 */
class Customer extends Contact {
	/**
	 * Object type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const OBJECT_TYPE = 'customer';

	/**
	 * Model constructor.
	 *
	 * @param int|object|array $data Object ID, post object, or array of data.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $data = 0 ) {
		$args            = array(
			'type'          => 'customer',
			'currency_code' => eac_get_base_currency(),
			'country'       => get_option( 'eac_business_country' ),
		);
		$this->core_data = array_merge( $this->core_data, $args );
		parent::__construct( $data );
		// after reading check if the contact is a customer.
		if ( $this->exists() && 'customer' !== $this->get_type() ) {
			$this->set_id( 0 );
			$this->set_defaults();
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Getters and Setters
	|--------------------------------------------------------------------------
	|
	| Methods for getting and setting data.
	|
	*/

	/**
	 * Get due amount.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return float
	 */
	public function get_total_due( $context = 'view' ) {
		return $this->get_meta( 'total_due', $context );
	}

	/**
	 * Set due.
	 *
	 * @param string $value total due amount.
	 */
	public function set_total_due( $value ) {
		$this->set_meta( 'total_due', eac_format_decimal( $value ) );
	}

	/**
	 * Get paid amount.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return float
	 */
	public function get_total_paid( $context = 'view' ) {
		return $this->get_meta( 'total_paid', $context );
	}

	/**
	 * Set paid.
	 *
	 * @param string $value paid amount.
	 */
	public function set_total_paid( $value ) {
		$this->set_meta( 'total_paid', eac_format_decimal( $value ) );
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
	 * Saves an object in the database.
	 *
	 * @since 1.0.0
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 */
	public function save() {
		// Name is required.
		if ( empty( $this->get_name() ) ) {
			return new \WP_Error( 'missing_required_field', __( 'Name is required.', 'wp-ever-accounting' ) );
		}

		// Currency is required.
		if ( empty( $this->get_currency_code() ) ) {
			return new \WP_Error( 'missing_required_field', __( 'Currency is required.', 'wp-ever-accounting' ) );
		}

		// Duplicate email.
		if ( ! empty( $this->get_email() ) ) {
			$existing = self::get( $this->get_email(), 'email' );
			if ( $existing && $existing->get_id() !== $this->get_id() && 'customer' === $existing->get_type() ) {
				return new \WP_Error( 'duplicate_field', __( 'The email address is already in used.', 'wp-ever-accounting' ) );
			}
		}

		// Duplicate phone number.
		if ( ! empty( $this->get_phone() ) ) {
			$existing = self::get( $this->get_phone(), 'phone' );
			if ( $existing && $existing->get_id() !== $this->get_id() && 'customer' === $existing->get_type() ) {
				return new \WP_Error( 'duplicate_field', __( 'The phone number is already in used.', 'wp-ever-accounting' ) );
			}
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
		$clauses = parent::prepare_where_query( $clauses, $args );

		$clauses['where'] .= $wpdb->prepare( ' AND type = %s', $this->get_type() );

		return $clauses;
	}

	/*
	|--------------------------------------------------------------------------
	| Helpers
	|--------------------------------------------------------------------------
	|
	| Helper methods.
	|
	*/

	/**
	 * Get total paid by a customer.
	 *
	 * @since 1.1.0
	 * @return float|int|string
	 */
	public function get_calculated_total_paid() {
		global $wpdb;
		$total = wp_cache_get( 'customer_total_total_paid_' . $this->get_id(), 'ea_customers' );
		if ( false === $total ) {
			$total        = 0;
			$transactions = $wpdb->get_results( $wpdb->prepare( "SELECT amount, currency_code, currency_rate FROM {$wpdb->prefix}ea_transactions WHERE type='payment' AND contact_id=%d", $this->get_id() ) );
			foreach ( $transactions as $transaction ) {
				$total += eac_price_to_default( $transaction->amount, $transaction->currency_code, $transaction->currency_rate );
			}
			wp_cache_set( 'customer_total_total_paid_' . $this->get_id(), $total, 'ea_customers' );
		}

		return $total;
	}

	/**
	 * Get total paid by a customer.
	 *
	 * @since 1.1.0
	 * @return float|int|string
	 */
	public function get_calculated_total_due() {
		global $wpdb;
		$total = wp_cache_get( 'customer_total_total_due_' . $this->get_id(), 'ea_customers' );
		if ( false === $total ) {
			$invoices = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT id, total amount, currency_code, currency_rate  FROM   {$wpdb->prefix}ea_documents
					   WHERE  status NOT IN ( 'draft', 'cancelled', 'paid' )
					   AND type = 'invoice' AND contact_id=%d",
					$this->get_id()
				)
			);
			$total    = 0;
			foreach ( $invoices as $invoice ) {
				$total += eac_price_to_default( $invoice->amount, $invoice->currency_code, $invoice->currency_rate );
			}
			if ( ! empty( $total ) ) {
				$invoice_ids = implode( ',', wp_parse_id_list( wp_list_pluck( $invoices, 'id' ) ) );
				$payments    = $wpdb->get_results(
					$wpdb->prepare( "SELECT Sum(amount) amount, currency_code, currency_rate FROM  {$wpdb->prefix}ea_transactions WHERE  type = %s AND document_id IN ($invoice_ids) GROUP  BY currency_code,currency_rate", 'payment' )
				);

				foreach ( $payments as $payment ) {
					$total -= eac_price_to_default( $payment->amount, $payment->currency_code, $payment->currency_rate );
				}
			}
			wp_cache_set( 'customer_total_total_due_' . $this->get_id(), $total, 'ea_customers' );
		}

		return $total;
	}
}
