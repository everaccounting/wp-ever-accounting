<?php
/**
 * Contact data handler class.
 *
 * @version     1.0.2
 * @package     Ever_Accounting
 * @class       Contact
 */

namespace Ever_Accounting;

use Ever_Accounting\Traits\Attachment;
use Ever_Accounting\Traits\CurrencyTrait;

defined( 'ABSPATH' ) || exit;

/**
 * Contact class.
 */
class Customer extends Contact {
	use Attachment;
	use CurrencyTrait;
	/**
	 * This is the name of this object type.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $object_type = 'customer';

	/**
	 * Contact constructor.
	 *
	 * @param int|customer|object|null $customer customer instance.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $customer = 0 ) {

		$this->core_data['type']       = 'customer';
		$this->set_total_due( 0 );
		$this->set_total_paid( 0 );

		parent::__construct( $customer );

		if ( $this->type !== 'customer' ) {
			$this->set_id( 0 );
			$this->set_defaults();
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get due amount.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return float
	 */
	public function get_total_due( $context = 'view' ) {
		return $this->get_meta( 'total_due', $context );
	}

	/**
	 * Get paid amount.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return float
	 */
	public function get_total_paid( $context = 'view' ) {
		return $this->get_meta( 'total_paid', $context );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Set due.
	 *
	 * @param string $value due amount.
	 */
	public function set_total_due( $value ) {
		$this->update_meta( 'total_due', eaccounting_price( $value, null, true  ) );
	}

	/**
	 * Set paid.
	 *
	 * @param string $value paid amount.
	 */
	public function set_total_paid( $value ) {
		$this->update_meta( 'total_paid', eaccounting_price( $value, null, true ) );
	}

	/*
	|--------------------------------------------------------------------------
	| Non CRUD Methods
	|--------------------------------------------------------------------------
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
			$transactions = $wpdb->get_results( $wpdb->prepare( "SELECT amount, currency_code, currency_rate FROM {$wpdb->prefix}ea_transactions WHERE type='income' AND contact_id=%d", $this->get_id() ) );
			foreach ( $transactions as $transaction ) {
				$total += eaccounting_price_to_default( $transaction->amount, $transaction->currency_code, $transaction->currency_rate );
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
			$total = 0;
			foreach ( $invoices as $invoice ) {
				$total += eaccounting_price_to_default( $invoice->amount, $invoice->currency_code, $invoice->currency_rate );
			}
			if ( ! empty( $total ) ) {
				$invoice_ids = implode( ',', wp_parse_id_list( wp_list_pluck( $invoices, 'id' ) ) );
				$revenues    = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT Sum(amount) amount, currency_code, currency_rate
		  			   FROM   {$wpdb->prefix}ea_transactions
		               WHERE  type = %s AND document_id IN ($invoice_ids)
		  			   GROUP  BY currency_code,currency_rate",
						'income'
					)
				);

				foreach ( $revenues as $revenue ) {
					$total -= eaccounting_price_to_default( $revenue->amount, $revenue->currency_code, $revenue->currency_rate );
				}
			}
			wp_cache_set( 'customer_total_total_due_' . $this->get_id(), $total, 'ea_customers' );
		}

		return $total;
	}
}
