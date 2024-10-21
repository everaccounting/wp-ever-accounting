<?php

namespace EverAccounting\Models;

use ByteKit\Models\Relations\BelongsTo;
use ByteKit\Models\Relations\BelongsToMany;

/**
 * Invoice model.
 *
 * @since 1.0.0
 * @package EverAccounting
 * @subpackage Models
 * @extends Document
 *
 * @author  Sultan Nasir Uddin <manikdrmc@gmail.com>
 *
 * @property-read  int      $id Invoice ID.
 * @property-read  int      $customer_id Customer ID.
 * @property-read  string   $order_number Order number.
 * @property-read  string   $status_label Status label.
 * @property-read  Customer $customer Customer relation.
 * @property-read Payment[] $payments Invoice payments.
 */
class Invoice extends Document {
	/**
	 * The type of the object. Used for actions and filters. e.g. post, user, etc.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $object_type = 'invoice';

	/**
	 * Default query variables passed to Query class when parsing.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $query_vars = array(
		'type' => 'invoice',
	);

	/**
	 * Create a new model instance.
	 *
	 * @param mixed $attributes The attributes to fill the model with.
	 */
	public function __construct( $attributes = null ) {
		$due_after        = get_option( 'eac_invoice_due_date', 7 );
		$this->attributes = array_merge(
			$this->attributes,
			array(
				'type'       => $this->get_object_type(),
				'issue_date' => current_time( 'mysql' ),
				'due_date'   => wp_date( 'Y-m-d', strtotime( '+' . $due_after . ' days' ) ),
				'note'       => get_option( 'eac_invoice_notes', '' ),
				'currency'   => eac_base_currency(),
				'creator_id' => get_current_user_id(),
				'uuid'       => wp_generate_uuid4(),
			)
		);

		$this->aliases['customer_id']  = 'contact_id';
		$this->aliases['order_number'] = 'reference';
		parent::__construct( $attributes );
	}

	/**
	 * Get formatted status.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_status_label_attr() {
		$statuses = EAC()->invoices->get_statuses();

		return array_key_exists( $this->status, $statuses ) ? $statuses[ $this->status ] : $this->status;
	}

	/**
	 * Customer relation.
	 *
	 * @since 1.0.0
	 * @return BelongsTo
	 */
	public function customer() {
		return $this->belongs_to( Customer::class, 'contact_id' );
	}

	/**
	 * Payments relation.
	 *
	 * @since 1.0.0
	 * @return BelongsToMany
	 */
	public function payments() {
		return $this->belongs_to_many( Payment::class, 'document_id' );
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD Methods
	|--------------------------------------------------------------------------
	| This section contains methods for creating, reading, updating, and deleting
	| objects in the database.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Save the object to the database.
	 *
	 * @since 1.0.0
	 * @return \WP_Error|static WP_Error on failure, or the object on success.
	 */
	public function save() {

		// if number is empty, set next available number.
		if ( empty( $this->number ) ) {
			$this->number = $this->get_next_number();
		}

		return parent::save();
	}
	/*
	|--------------------------------------------------------------------------
	| Invoice Item Handling
	|--------------------------------------------------------------------------
	| Invoice items are used for products, taxes, shipping, and fees within
	| each order.
	*/

	/**
	 * Set items.
	 *
	 * @param array $items Items.
	 *
	 * @since 1.0.0
	 * @return $this
	 */
	public function set_items( $items ) {
		$this->items = array();
	}

	/*
	|--------------------------------------------------------------------------
	| Helper Methods
	|--------------------------------------------------------------------------
	| This section contains utility methods that are not directly related to this
	| object but can be used to support its functionality.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Set next available number.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_next_number() {
		$max    = $this->get_max_number();
		$prefix = get_option( 'eac_invoice_prefix', strtoupper( substr( $this->get_object_type(), 0, 3 ) ) . '-' );
		$number = str_pad( $max + 1, get_option( 'eac_invoice_digits', 4 ), '0', STR_PAD_LEFT );

		return $prefix . $number;
	}

	/**
	 * Calculate the totals amount of the invoice.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function calculate_totals() {
		$this->subtotal = $this->get_items_totals( 'subtotal', true );
		$this->discount = $this->get_items_totals( 'discount', true );
		$this->tax      = $this->get_items_totals( 'tax', true );
		$this->total    = $this->get_items_totals( 'total', true );

		return array(
			'subtotal' => $this->subtotal,
			'discount' => $this->discount,
			'tax'      => $this->tax,
			'total'    => $this->total,
		);
	}

	/**
	 * Get totals.
	 *
	 * @param string $column Column name.
	 * @param bool   $round Round the value or not.
	 *
	 * @since 1.0.0
	 */
	public function get_items_totals( $column = 'total', $round = false ) {
		$total = 0;
		foreach ( $this->items as $item ) {
			$amount = $item->$column ?? 0;
			$total += $round ? round( $amount, 2 ) : $amount;
		}

		return $round ? round( $total, 2 ) : $total;
	}

	/**
	 * Get itemized taxes.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_itemized_taxes() {
		$taxes = array();
		foreach ( $this->items as $item ) {
			if ( ! empty( $item->taxes ) ) {
				foreach ( $item->taxes as $tax ) {
					if ( !isset( $taxes[ $tax->tax_id] ) ) {
						$taxes[ $tax->tax_id ] = $tax;
					} else {
						$taxes[ $tax->tax_id ]->amount += $tax->amount;
					}
				}
			}
		}

		return $taxes;
	}

	/**
	 * Get amount paid.
	 *
	 * @since 1.1.0
	 *
	 * @return float|int|string
	 */
	public function get_amount_paid() {
		$total_paid = 0;
		foreach ( $this->payments as $payment ) {
			$total_paid += (float) eac_convert_currency( $payment->amount, $payment->currency, $this->currency, $payment->exchange_rate, $this->exchange_rate );
		}

		return $total_paid;
	}

	/**
	 * Get amount due.
	 *
	 * @since 1.1.0
	 *
	 * @return float|int
	 */
	public function get_amount_due() {
		$due = $this->total - $this->get_amount_paid();
		if ( eac_convert_currency( $due, $this->currency, $this->exchange_rate ) <= 0 ) {
			$due = 0;
		}

		return $due;
	}

	/**
	 * Checks if the invoice has a given status.
	 *
	 * @param string $status Status to check.
	 *
	 * @since 1.1.0
	 *
	 * @return bool
	 */
	public function is_status( $status ) {
		return $this->status === $status;
	}

	/**
	 * Checks if an order can be edited, specifically for use on the Edit Order screen.
	 *
	 * @return bool
	 */
	public function is_editable() {
		return ! in_array( $this->status, array( 'partial', 'paid' ), true );
	}

	/**
	 * Returns if an order has been paid for based on the order status.
	 *
	 * @since 1.10
	 * @return bool
	 */
	public function is_paid() {
		return $this->is_status( 'paid' );
	}

	/**
	 * Checks if the invoice is due.
	 *
	 * @since 1.1.0
	 * @return bool
	 */
	public function is_due() {
		return ! ( empty( $this->due_date ) || $this->is_paid() ) && strtotime( wp_date( 'Y-m-d 23:59:00' ) ) > strtotime( wp_date( 'Y-m-d 23:59:00', strtotime( $this->due_date ) ) );
	}

	/**
	 * Checks if an order needs payment, based on status and order total.
	 *
	 * @return bool
	 */
	public function needs_payment() {
		return ! $this->is_status( 'paid' ) && $this->total > 0;
	}

	/**
	 * Get edit URL.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_edit_url() {
		return admin_url( 'admin.php?page=eac-sales&tab=invoices&action=edit&id=' . $this->id );
	}

	/**
	 * Get view URL.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_view_url() {
		return admin_url( 'admin.php?page=eac-sales&tab=invoices&action=view&id=' . $this->id );
	}

	/**
	 * Get the public URL.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_public_url() {
		$page_id = get_option( 'eac_invoice_page_id' );
		if ( empty( $page_id ) ) {
			return '';
		}

		$permalink = get_permalink( $page_id );

		return add_query_arg( 'bill', $this->uuid, $permalink );
	}
}
