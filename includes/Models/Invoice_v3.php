<?php

namespace EverAccounting\Models;

use ByteKit\Models\Relations\HasMany;

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
 * @property int            $id Invoice ID.
 * @property DocumentItem[] $lines Invoice lines.
 */
class Invoice_v3 extends Document {
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
	 * @param string|array|object $props The model attributes.
	 */
	public function __construct( $props = array() ) {
		$due_after        = get_option( 'eac_invoice_due_date', 7 );
		$_attributes      = array(
			'type'       => $this->get_object_type(),
			'issue_date' => current_time( 'mysql' ),
			'due_date'   => wp_date( 'Y-m-d', strtotime( '+' . $due_after . ' days' ) ),
			'notes'      => get_option( 'eac_invoice_notes', '' ),
			'currency'   => eac_base_currency(),
			'creator_id' => get_current_user_id(),
			'uuid'       => wp_generate_uuid4(),
		);
		$this->attributes = array_merge( $this->attributes, $_attributes );
		parent::__construct( $props );
	}

	/**
	 * Payments relation.
	 *
	 * @since 1.0.0
	 * @return HasMany
	 */
	public function payments() {
		return $this->has_many( Payment::class, 'document_id' )->set_query_var( 'status', 'completed' );
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
	 * Validate data before saving.
	 *
	 * @since 1.0.0
	 * @return void|\WP_Error Return WP_Error if data is not valid or void.
	 */
	protected function validate_save_data() {
		if ( empty( $this->issue_date ) ) {
			$this->issue_date = wp_date( 'Y-m-d' );
		}
		// If due date is not set, ask the user to set the due date.
		if ( empty( $this->due_date ) ) {
			return new \WP_Error( 'missing_required', __( 'Due date is required.', 'ever-accounting' ) );
		}
	}

	/**
	 * Save the object to the database.
	 *
	 * @since 1.0.0
	 * @return \WP_Error|static WP_Error on failure, or the object on success.
	 */
	public function save() {
		$this->recalculate();

		if ( $this->total_paid > 0 && $this->balance > 0 ) {
			$new_status = 'partial';
		} elseif ( $this->total_paid >= $this->total ) {
			$new_status = 'paid';
		} elseif ( $this->is_due() && ! $this->is_status( 'overdue' ) ) {
			$new_status = 'overdue';
		} elseif ( in_array( $this->status, array( 'partial', 'paid' ), true ) ) {
			$new_status = 'sent';
		}

		// if status is sent and sent date is empty, set the sent date.
		if ( 'sent' === $new_status && empty( $this->sent_date ) ) {
			$this->sent_date = wp_date( 'Y-m-d' );
		}
		// if status is paid and paid date is empty, set the paid date.
		if ( 'paid' === $new_status && empty( $this->paid_date ) ) {
			$this->paid_date = wp_date( 'Y-m-d' );
		}

		// If the status is changed, update the status.
		if ( ! empty( $new_status ) && $new_status !== $this->status ) {
			$this->status = $new_status;
		}

		return parent::save();
	}

	/*
	|--------------------------------------------------------------------------
	| Calculations
	|--------------------------------------------------------------------------
	| This section contains methods for calculating totals.
	 */

	/**
	 * Recalculate the invoice.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function recalculate() {
		$this->total_paid = 0;
		$payments         = $this->payments()->get_items();
		foreach ( $payments as $payment ) {
			$amount = eac_convert_currency( $payment->amount, $payment->currency, $this->currency, $payment->exchange_rate, $this->exchange_rate );
			if ( 'revenue' === $payment->type ) {
				$this->total_paid += $amount;
			} elseif ( 'expense' === $payment->type ) {
				$this->total_paid -= $amount;
			}
		}

		$this->calculate_line_subtotals();
		$this->calculate_line_discounts();
		$this->calculate_line_taxes();
		$this->calculate_line_totals();

		$this->items_total    = $this->get_lines_totals( 'standard', 'subtotal', true );
		$this->discount_total = $this->get_lines_totals( 'standard', 'discount', true );
		$this->shipping_total = $this->get_lines_totals( 'shipping', 'subtotal', true );
		$this->fees_total     = $this->get_lines_totals( 'fee', 'subtotal', true );
		$this->tax_total      = $this->get_lines_totals( 'all', 'tax_total', true );
		$this->total          = $this->get_lines_totals( 'all', 'total', true );
		$this->balance        = $this->total - $this->total_paid;

		return array(
			'total'          => $this->total,
			'balance'        => $this->balance,
			'total_paid'     => $this->total_paid,
			'items_total'    => $this->items_total,
			'discount_total' => $this->discount_total,
			'shipping_total' => $this->shipping_total,
			'fees_total'     => $this->fees_total,
			'tax_total'      => $this->tax_total,
		);
	}

	/**
	 * Calculate line subtotals.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function calculate_line_subtotals() {
		foreach ( $this->lines as $line ) {
			$price        = $line->price;
			$qty          = $line->quantity;
			$subtotal     = $price * $qty;
			$subtotal_tax = array_sum( eac_calculate_taxes( $subtotal, $line->taxes, $this->tax_inclusive ) );
			// If the tax is inclusive, we need to subtract the tax amount from the line subtotal.
			if ( $this->tax_inclusive ) {
				$subtotal -= $subtotal_tax;
			}

			$subtotal = max( 0, $subtotal );

			$line->subtotal     = $subtotal;
			$line->subtotal_tax = $subtotal_tax;
		}
	}

	/**
	 * Calculate line discounts.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function calculate_line_discounts() {
		$lines           = $this->get_lines( 'standard' );
		$discount_amount = $this->discount_amount;
		$discount_type   = $this->discount_type;

		// sort the items array by price.

		// Reset item discounts.
		foreach ( $lines as $item ) {
			$item->discount = 0;
		}

		// First apply the discount to the items.
		if ( $discount_amount > 0 && ! empty( $discount_type ) ) {
			$this->apply_discount( $discount_amount, $lines, $discount_type );
		}

		foreach ( $lines as $line ) {
			$discount     = $line->discount;
			$discount_tax = array_sum( eac_calculate_taxes( $discount, $line->taxes, $this->tax_inclusive ) );
			if ( $this->tax_inclusive ) {
				$discount -= $discount_tax;
			}
			$discount = max( 0, $discount );

			$line->discount     = $discount;
			$line->discount_tax = $discount_tax;
		}
	}


	/**
	 * Calculate item taxes.
	 *
	 * @return void
	 */
	protected function calculate_line_taxes() {
		// Calculate line taxes.
		foreach ( $this->lines as $line ) {
			$taxable_amount = $line->subtotal - $line->discount;
			$taxable_amount = max( 0, $taxable_amount );
			$taxes          = eac_calculate_taxes( $taxable_amount, $line->taxes, false );
			$line_tax       = 0;
			foreach ( $line->taxes as $tax ) {
				$amount      = isset( $taxes[ $tax->tax_id ] ) ? $taxes[ $tax->tax_id ] : 0;
				$tax->amount = $amount;
				$line_tax   += $amount;
			}
			$line->tax_total = $line_tax;
		}
	}

	/**
	 * Calculate line totals.
	 *
	 * @return void
	 */
	protected function calculate_line_totals() {
		foreach ( $this->lines as $line ) {
			$total       = $line->subtotal + $line->tax_total - $line->discount;
			$total       = max( 0, $total );
			$line->total = $total;
		}
	}

	/**
	 * Apply discounts.
	 *
	 * @param float  $amount Discount amount.
	 * @param array  $items Items.
	 * @param string $type Discount type.
	 *
	 * @since 1.0.0
	 * @return float Total discount.
	 */
	public function apply_discount( $amount = 0, $items = array(), $type = 'fixed' ) {
		$total_discounted = 0;
		if ( 'fixed' === $type ) {
			$total_discounted = $this->apply_fixed_discount( $amount, $items );
		} elseif ( 'percent' === $type ) {
			$total_discounted = $this->apply_percent_discount( $amount, $items );
		}

		return $total_discounted;
	}

	/**
	 * Apply fixed discount.
	 *
	 * @param float          $amount Discount amount.
	 * @param DocumentItem[] $items Items.
	 *
	 * @since 1.0.0
	 * @return float Total discounted.
	 */
	public function apply_fixed_discount( $amount, $items ) {
		$total_discount = 0;
		$item_count     = 0;

		foreach ( $items as $item ) {
			$item_count += (float) $item->quantity;
		}

		if ( $amount <= 0 || empty( $items ) || $item_count <= 0 ) {
			return $total_discount;
		}

		$per_item_discount = $amount / $item_count;
		if ( $per_item_discount > 0 ) {
			foreach ( $items as $item ) {
				$discounted_price = $item->discounted_subtotal;
				$discount         = $per_item_discount * (float) $item->quantity;
				$discount         = eac_round_number( $discount );
				$discount         = min( $discounted_price, $discount );
				$item->discount   = $item->discount + $discount;

				$total_discount += $discount;
			}

			$total_discount = round( $total_discount, 2 );
			$amount         = round( $amount, 2 );
			// If there is still discount remaining, repeat the process.
			if ( $total_discount > 0 && $total_discount < $amount ) {
				$total_discount += $this->apply_fixed_discount( $amount - $total_discount, $items );
			}
		} elseif ( $amount > 0 ) {
			$total_discount += $this->apply_discount_remainder( $amount, $items );
		}

		return $total_discount;
	}

	/**
	 * Apply percent discount.
	 *
	 * @param float          $amount Discount amount.
	 * @param DocumentItem[] $items Items.
	 */
	public function apply_percent_discount( $amount, $items ) {
		$total_discount = 0;
		$document_total = 0;

		if ( $amount <= 0 || empty( $items ) ) {
			return $total_discount;
		}

		foreach ( $items as $item ) {
			$discounted_price = $item->discounted_subtotal;
			// If the item is not created yet, we need to calculate the discounted price without tax.
			$discount        = $discounted_price * ( $amount / 100 );
			$discount        = min( $discounted_price, $discount );
			$item->discount  = $item->discount + $discount;
			$total_discount += $discount;
			$document_total += $discounted_price;
		}
		// Work out how much discount would have been given to the cart as a whole and compare to what was discounted on all line items.
		$document_discount = round( $document_total * ( $amount / 100 ), 2 );
		$total_discount    = round( $total_discount, 2 );

		if ( $total_discount < $document_discount && $amount > 0 ) {
			$total_discount += $this->apply_discount_remainder( $amount - $total_discount, $items );
		}

		return $total_discount;
	}

	/**
	 * Apply remainder discount.
	 *
	 * @param float          $amount Discount amount.
	 * @param DocumentItem[] $items Items.
	 *
	 * @since 1.0.0
	 * @return float
	 */
	public function apply_discount_remainder( $amount, $items ) {
		$total_discount = 0;
		foreach ( $items as $item ) {
			$quantity = $item->quantity;
			for ( $i = 0; $i < $quantity; $i++ ) {
				$discounted_price = $item->discounted_subtotal;
				$discount         = min( $discounted_price, 1 );
				$item->discount   = $item->discount + $discount;
				$total_discount  += $discount;
				if ( $total_discount >= $amount ) {
					break 2;
				}
			}
			if ( $total_discount >= $amount ) {
				break;
			}
		}

		return $total_discount;
	}

	/**
	 * Get totals.
	 *
	 * @param string $type Type of items.
	 * @param string $column Column name.
	 * @param bool   $round Round the value or not.
	 *
	 * @since 1.0.0
	 */
	public function get_lines_totals( $type, $column = 'total', $round = false ) {
		$lines = $this->get_lines( $type );
		$total = 0;
		foreach ( $lines as $line ) {
			$amount = $line->$column ?? 0;
			$total += $round ? round( $amount, 2 ) : $amount;
		}

		return $round ? round( $total, 2 ) : $total;
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
	 * Get the lines of the given type.
	 *
	 * @param string $type The type of the line.
	 *
	 * @since 1.0.0
	 * @return DocumentItem[]
	 */
	protected function get_lines( $type = 'standard' ) {
		return array_filter(
			$this->lines,
			function ( $line ) use ( $type ) {
				return is_null( $type ) || $line->type === $type;
			}
		);
	}

	/**
	 * Is calculating tax.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_calculating_tax() {
		return 'yes' !== eac_tax_enabled() && ! $this->vat_exempt;
	}

	/**
	 * Get max voucher number.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_max_number() {
		global $wpdb;
		// select the max id
		// return (int) $wpdb->get_var(
		// $wpdb->prepare(
		// "SELECT MAX(REGEXP_REPLACE(number, '[^0-9]', '')) FROM {$wpdb->prefix}{$this->table} WHERE type = %s",
		// esc_sql( $this->type )
		// )
		// );
	}

	/**
	 * Set next transaction number.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_next_number() {
		$max    = $this->get_max_number();
		$prefix = strtoupper( substr( $this->get_object_type(), 0, 3 ) ) . '-';
		$next   = str_pad( $max + 1, 4, '0', STR_PAD_LEFT );

		return $prefix . $next;
	}

	/**
	 * is editable.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_editable() {
		return $this->total_paid <= 0;
	}

	/**
	 * Checks if the invoice has a given status.
	 *
	 * @param string $status Status to check.
	 *
	 * @since 1.1.0
	 * @return bool
	 */
	public function is_status( $status ) {
		return $this->status === $status;
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
	 * Checks if the invoice is draft.
	 *
	 * @since 1.1.0
	 * @return bool
	 */
	public function is_draft() {
		return $this->is_status( 'draft' );
	}

	/**
	 * Checks if the invoice is due.
	 *
	 * @since 1.1.0
	 * @return bool
	 */
	public function is_due() {
		return ! ( empty( $this->due_date ) || $this->is_paid() ) && strtotime( date_i18n( 'Y-m-d 23:59:00' ) ) > strtotime( date_i18n( 'Y-m-d 23:59:00', strtotime( $this->due_date ) ) );
	}

	/**
	 * Checks if an order needs payment, based on status and order total.
	 *
	 * @return bool
	 */
	public function needs_payment() {
		return ! $this->is_status( 'paid' ) && $this->total > 0;
	}
}
