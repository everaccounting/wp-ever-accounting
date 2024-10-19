<?php

namespace EverAccounting\Models;

use ByteKit\Models\Relations\BelongsTo;

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
 * @property int $id Invoice ID.
 * @property int $vendor_id Vendor ID.
 * @property string $order_number Order number.
 *
 * @property Vendor $vendor Vendor relation.
 */
class Bill extends Document {
	/**
	 * The type of the object. Used for actions and filters. e.g. post, user, etc.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $object_type = 'bill';

	/**
	 * Default query variables passed to Query class when parsing.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $query_vars = array(
		'type' => 'bill',
	);

	/**
	 * Create a new model instance.
	 *
	 * @param mixed $attributes The attributes to fill the model with.
	 */
	public function __construct( $attributes = null ) {
		$due_after        = get_option( 'eac_bill_due_date', 7 );
		$this->attributes = array_merge(
			$this->attributes,
			array(
				'type'       => $this->get_object_type(),
				'issue_date' => current_time( 'mysql' ),
				'due_date'   => wp_date( 'Y-m-d', strtotime( '+' . $due_after . ' days' ) ),
				'notes'      => get_option( 'eac_bill_notes', '' ),
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
	 * Vendor relation.
	 *
	 * @since 1.0.0
	 * @return BelongsTo
	 */
	public function vendor() {
		return $this->belongs_to( Customer::class, 'contact_id' );
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
		$prefix = get_option( 'eac_bill_prefix', strtoupper( substr( $this->get_object_type(), 0, 3 ) ) . '-' );
		$number = str_pad( $max + 1, get_option( 'eac_bill_digits', 4 ), '0', STR_PAD_LEFT );

		return $prefix . $number;
	}

	/**
	 * Calculate the totals amount of the bill.
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
	 * Is taxed.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_taxed() {
		return 'yes' === get_option( 'eac_tax_enabled', 'no' ) || ( $this->exists() && $this->tax > 0 );
	}

	/**
	 * Get edit URL.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_edit_url() {
		return admin_url( 'admin.php?page=eac-purchases&tab=bills&action=edit&id=' . $this->id );
	}

	/**
	 * Get view URL.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_view_url() {
		return admin_url( 'admin.php?page=eac-purchases&tab=bills&action=view&id=' . $this->id );
	}

	/**
	 * Get the public URL.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_public_url() {
		$page_id = get_option( 'eac_bill_page_id' );
		if ( empty( $page_id ) ) {
			return '';
		}

		$permalink = get_permalink( $page_id );
		return add_query_arg( 'bill', $this->uuid, $permalink );
	}
}
