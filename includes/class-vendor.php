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
class Vendor extends Contact {
	use Attachment;
	use CurrencyTrait;

	/**
	 * This is the name of this object type.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $object_type = 'vendor';

	/**
	 * Contact constructor.
	 *
	 * @param int|customer|object|null $vendor vendor instance.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $vendor = 0 ) {

		$this->core_data['type']       = 'vendor';
		$this->set_total_paid( 0 );
		$this->set_total_paid( 0 );

		parent::__construct( $vendor );

		if ( $this->type !== 'vendor' ) {
			$this->set_id( 0 );
			$this->set_defaults();
		}
	}
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
		$this->update_meta( 'total_due', eaccounting_price( $value, null, true ) );
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
	 * Get total paid by a vendor.
	 *
	 * @since 1.1.0
	 * @return float|int|string
	 */
	public function get_calculated_total_paid() {
		global $wpdb;
		$total = wp_cache_get( 'vendor_total_total_paid_' . $this->get_id(), 'ea_vendors' );
		if ( false === $total ) {
			$total        = 0;
			$transactions = $wpdb->get_results( $wpdb->prepare( "SELECT amount, currency_code, currency_rate FROM {$wpdb->prefix}ea_transactions WHERE type='expense' AND contact_id=%d", $this->get_id() ) );
			foreach ( $transactions as $transaction ) {
				$total += eaccounting_price_to_default( $transaction->amount, $transaction->currency_code, $transaction->currency_rate );
			}
			wp_cache_set( 'vendor_total_total_paid_' . $this->get_id(), $total, 'ea_vendors' );
		}

		return $total;
	}

	/**
	 * Get total due by a vendor.
	 *
	 * @since 1.1.0
	 * @return float|int|string
	 */
	public function get_calculated_total_due() {
		global $wpdb;
		$total = wp_cache_get( 'vendor_total_total_due_' . $this->get_id(), 'ea_vendors' );
		if ( false === $total ) {
			$bills = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT id, total amount, currency_code, currency_rate  FROM   {$wpdb->prefix}ea_documents
					   WHERE  status NOT IN ( 'draft', 'cancelled', 'paid' )
					   AND type = 'bill' AND contact_id=%d",
					$this->get_id()
				)
			);

			$total = 0;
			foreach ( $bills as $bill ) {
				$total += eaccounting_price_to_default( $bill->amount, $bill->currency_code, $bill->currency_rate );
			}

			if ( ! empty( $total ) ) {
				$bill_ids = implode( ',', wp_parse_id_list( wp_list_pluck( $bills, 'id' ) ) );
				$revenues = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT Sum(amount) amount, currency_code, currency_rate
		  			   FROM   {$wpdb->prefix}ea_transactions
		               WHERE  type = %s AND document_id IN ($bill_ids)
		  			   GROUP  BY currency_code,currency_rate",
						'expense'
					)
				);

				foreach ( $revenues as $revenue ) {
					$total -= eaccounting_price_to_default( $revenue->amount, $revenue->currency_code, $revenue->currency_rate );
				}
			}
			wp_cache_set( 'vendor_total_total_due_' . $this->get_id(), $total, 'ea_vendors' );
		}

		return $total;
	}

}
