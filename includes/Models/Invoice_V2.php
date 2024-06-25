<?php

namespace EverAccounting\Models;

use ByteKit\Models\Relation;

/**
 * Invoice model.
 *
 * @param Customer $customer Customer object.
 *
 * @since 1.0.0
 * @package EverAccounting
 * @subpackage Models
 *
 * @author  Sultan Nasir Uddin <manikdrmc@gmail.com>
 */
class Invoice_V2 extends Document {
	/**
	 * Object type in singular form.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $object_type = 'invoice';

	/**
	 * Create a new model instance.
	 *
	 * @param string|int|array $attributes Attributes.
	 * @return void
	 */
	public function __construct( $attributes = null ) {
		$due_after = get_option( 'eac_invoice_due_date', 7 );
		$notes     = get_option( 'eac_invoice_notes', '' );
		$due_date  = wp_date( 'Y-m-d', strtotime( '+' . $due_after . ' days' ) );

		$this->attributes['type']       = $this->get_object_type();
		$this->attributes['number']     = $this->get_next_number();
		$this->attributes['issue_date'] = wp_date( 'Y-m-d' );
		$this->attributes['due_date']   = $due_date;
		$this->attributes['note']       = $notes;
		$this->query_vars['type']       = $this->get_object_type();
		parent::__construct( $attributes );
	}


	/*
	|--------------------------------------------------------------------------
	| Attributes & Relations
	|--------------------------------------------------------------------------
	| Define the attributes and relations of the model.
	*/

	/**
	 * Customer relation.
	 *
	 * @since 1.0.0
	 * @return Relation
	 */
	protected function customer() {
		return $this->belongs_to( Customer::class, 'contact_id' );
	}


	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	| Methods for saving, updating, and deleting objects.
	*/

	/**
	 * Saves an object in the database.
	 *
	 * @throws \Exception When the invoice is already paid.
	 * @since 1.0.0
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 */
	public function save() {
		$this->calculate_totals();
		$old_status = isset( $this->original['status'] ) ? $this->original['status'] : 'draft';
		$new_status = $this->status;

		if ( empty( $this->issue_date ) ) {
			$this->issue_date = wp_date( 'Y-m-d' );
		}
		// If due date is not set, ask the user to set the due date.
		if ( empty( $this->due_date ) ) {
			return new \WP_Error( 'missing_required', __( 'Due date is required.', 'ever-accounting' ) );
		}

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

		$status_transition = $old_status !== $new_status;

		// If the status is changed, update the status.
		if ( $status_transition ) {
			$this->status = $new_status;
		}

		$saved = parent::save();
		if ( is_wp_error( $saved ) ) {
			return $saved;
		}

		if ( $status_transition ) {

			/**
			 * Fires when the invoice status is changed.
			 *
			 * @param string  $new_status New status.
			 * @param string  $old_status Old status.
			 * @param Invoice $invoice Invoice object.
			 *
			 * @since 1.0.0
			 */
			do_action( 'ever_accounting_invoice_status_transition', $new_status, $old_status, $this );
			/**
			 * Fires when the invoice status is changed.
			 *
			 * @param string  $new_status New status.
			 * @param string  $old_status Old status.
			 * @param Invoice $invoice Invoice object.
			 *
			 * @since 1.0.0
			 */
			do_action( 'ever_accounting_invoice_status_' . $new_status, $new_status, $old_status, $this );
		}

		return $saved;
	}

	/**
	 * Deletes an object from the database.
	 *
	 * @since 1.0.0
	 * @return bool|\WP_Error True on success, false or WP_Error on failure.
	 */
	public function delete() {
		$deleted = parent::delete();
		if ( $deleted ) {
			foreach ( $this->get_payments() as $payment ) {
				$payment->delete();

			}
		}

		return $deleted;
	}

	/*
	|--------------------------------------------------------------------------
	| Line Items related methods
	|--------------------------------------------------------------------------
	| These methods are related to line items.
	*/

	/**
	 * Add item.
	 *
	 * Subtotal, discount is restricted to pass in item data.
	 *
	 * @param array $data Item.
	 *
	 * @return void
	 */
	public function add_item( $data ) {
		$default = array(
			'id'          => 0, // line id.
			'item_id'     => 0, // item id **not line id** be careful.
			'type'        => 'standard', // 'standard', 'fee', 'shipping
			'name'        => '',
			'description' => '',
			'unit'        => '',
			'price'       => 0,
			'quantity'    => 1,
			'taxable'     => $this->is_calculating_tax(),
			'taxes'       => array(),
		);
		if ( is_object( $data ) ) {
			$data = is_callable( array( $data, 'to_array' ) ) ? $data->to_array() : (array) $data;
		} elseif ( is_numeric( $data ) ) {
			$data = array( 'item_id' => $data );
		}

		// The data must be a line item with id or a new array with item_id and additional data.
		if ( ! isset( $data['id'] ) && ! isset( $data['item_id'] ) ) {
			return;
		}
		// If the item id is set, we need to get the item data.
		if ( empty( $data['id'] ) && ! empty( $data['item_id'] ) ) {
			$product       = eac_get_item( $data['item_id'] );
			$product_data  = $product ? $product->to_array() : array();
			$accepted_keys = array(
				'name',
				'type',
				'description',
				'unit',
				'price',
				'taxable',
			);
			// if the currency is not the as the base currency, we need to convert the price.

			if ( eac_get_base_currency() !== $this->currency_code ) {
				$price                 = eac_convert_currency( $product_data['price'], eac_get_base_currency(), $this->currency_code );
				$product_data['price'] = $price;
			}

			$product_data = wp_array_slice_assoc( $product_data, $accepted_keys );
			// prepare tax ids.
			if ( ! empty( $product->tax_ids ) && is_array( $product->tax_ids ) ) {
				$product_data['taxes'] = array_unique( $product->tax_ids );
			} else {
				$product_data['taxes'] = array();
			}
			$default = wp_parse_args( $product_data, $default );
		}

		$data                = wp_parse_args( $data, $default );
		$data['name']        = wp_strip_all_tags( $data['name'] );
		$data['description'] = wp_strip_all_tags( $data['description'] );
		$data['description'] = wp_trim_words( $data['description'], 20, '' );
		$data['unit']        = wp_strip_all_tags( $data['unit'] );
		$data['document_id'] = $this->id;

		// we have to validate the data before creating the item.
		if ( ! array_key_exists( $data['type'], eac_get_item_types() ) ) {
			$data['type'] = 'standard';
		}
		if ( ! array_key_exists( $data['unit'], eac_get_unit_types() ) ) {
			$data['unit'] = '';
		}

		$item = new DocumentItem( $data['id'] );
		$item->fill( $data );
		$item->set_taxes( $data['taxes'] );

		// if product id is not set then it is not product item.
		if ( empty( $item->item_id ) || empty( $item->quantity ) ) {
			return;
		}

		// Check if the item is set to be deleted and all the data matches. If so, remove it from the deletable list and add it to the items list.
		foreach ( $this->deletable as $key => $deletable_item ) {
			if ( $deletable_item->is_similar( $item ) ) {
				unset( $this->deletable[ $key ] );
				$deletable_item->fill( $data );
				$item = $deletable_item;
				break;
			}
		}

		// Check if the item already exists in the items list and all the data matches. If so, update the quantity.
		foreach ( $this->get_items() as $key => $existing_item ) {
			if ( $existing_item->is_similar( $item ) ) {
				$existing_item->quantity += $item->quantity;

				return;
			}
		}

		$this->items[] = $item;
	}

	/*
	|--------------------------------------------------------------------------
	|  Transaction related methods
	|--------------------------------------------------------------------------
	| These methods are related to transactions.
	*/
	/**
	 * Add transaction.
	 *
	 * @param array $data Transaction data.
	 *
	 * @since 1.0.0
	 * @return Transaction| \WP_Error Transaction ID on success, WP_Error otherwise.
	 */
	public function add_payment( $data ) {
		$data = wp_parse_args(
			$data,
			array(
				'date'           => current_time( 'mysql' ),
				'account_id'     => '',
				'document_id'    => $this->id,
				'amount'         => 0,
				'currency_code'  => $this->currency_code,
				'exchange_rate'  => $this->exchange_rate,
				'payment_method' => '',
				'note'           => '',
			)
		);

		// if amount is not set, set it to the total amount of the document.
		if ( empty( $data['amount'] ) ) {
			$data['amount'] = $this->balace;
		}
		if ( empty( $data['account_id'] ) ) {
			return new \WP_Error( 'missing_required', __( 'Payment account is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['payment_method'] ) ) {
			return new \WP_Error( 'missing_required', __( 'Payment method is required.', 'wp-ever-accounting' ) );
		}
		$account = eac_get_account( $data['account_id'] );
		if ( ! $account ) {
			return new \WP_Error( 'invalid_account', __( 'Invalid account.', 'wp-ever-accounting' ) );
		}
		if ( $account->currency_code !== $this->currency_code ) {
			$data['amount']        = eac_convert_currency( $data['amount'], $this->currency_code, $account->currency_code, $this->exchange_rate );
			$data['currency_code'] = $account->currency_code;
			$data['exchange_rate'] = $account->currency ? $account->currency->exchange_rate : 1;
		}

		$revenue = eac_insert_revenue(
			array(
				'date'           => $data['date'], // '2019-12-12 12:12:12
				'document_id'    => $this->id,
				'contact_id'     => $this->contact_id,
				'account_id'     => $data['account_id'],
				'amount'         => $data['amount'],
				'currency_code'  => $data['currency_code'],
				'exchange_rate'  => $data['exchange_rate'],
				'payment_method' => $data['payment_method'],
				'note'           => $data['note'],
				'created_via'    => 'invoice',
				'status'         => 'completed',
			)
		);

		if ( is_wp_error( $revenue ) ) {
			return $revenue;
		}

		return $revenue;
	}

	/**
	 * Get payments
	 *
	 * @param array $args Query arguments.
	 *
	 * @since 1.0.0
	 * @return Note[]
	 */
	public function get_payments( $args = array() ) {
		$args = array_merge(
			array(
				'document_id' => $this->id,
				'limit'       => - 1,
			),
			$args
		);

		return eac_get_revenues( $args );
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
	 * @return array
	 */
	public function calculate_totals() {
		$transactions     = $this->get_payments( array( 'status' => 'completed' ) );
		$this->total_paid = 0;
		if ( ! empty( $transactions ) ) {
			foreach ( $transactions as $transaction ) {
				$amount = eac_convert_currency( $transaction->amount, $transaction->currency_code, $this->currency_code, $transaction->exchange_rate, $this->exchange_rate );
				if ( 'revenue' === $transaction->type ) {
					$this->total_paid += $amount;
				} elseif ( 'expense' === $transaction->type ) {
					$this->total_paid -= $amount;
				}
			}
		}

		$this->calculate_item_subtotals();
		$this->calculate_item_discounts();
		$this->calculate_item_taxes();
		$this->calculate_item_totals();

		$this->items_total    = $this->get_items_totals( 'standard', 'subtotal', true );
		$this->discount_total = $this->get_items_totals( 'standard', 'discount', true );
		$this->shipping_total = $this->get_items_totals( 'shipping', 'subtotal', true );
		$this->fees_total     = $this->get_items_totals( 'fee', 'subtotal', true );
		$this->tax_total      = $this->get_items_totals( 'all', 'tax_total', true );
		$this->total          = $this->get_items_totals( 'all', 'total', true );
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
	 * Calculate item subtotals.
	 *
	 * @return void
	 */
	protected function calculate_item_subtotals() {
		$items = $this->get_items();

		foreach ( $items as $item ) {
			$price        = $item->price;
			$qty          = $item->quantity;
			$subtotal     = $price * $qty;
			$subtotal_tax = array_sum( eac_calculate_taxes( $subtotal, $item->get_taxes(), $this->tax_inclusive ) );
			// If the tax is inclusive, we need to subtract the tax amount from the line subtotal.
			if ( $this->tax_inclusive ) {
				$subtotal -= $subtotal_tax;
			}

			$subtotal = max( 0, $subtotal );

			$item->subtotal     = $subtotal;
			$item->subtotal_tax = $subtotal_tax;
		}
	}


	/**
	 * Calculate item discounts.
	 *
	 * @return void
	 */
	protected function calculate_item_discounts() {
		$items           = $this->get_items( 'standard' );
		$discount_amount = $this->discount_amount;
		$discount_type   = $this->discount_type;

		// sort the items array by price.

		// Reset item discounts.
		foreach ( $items as $item ) {
			$item->discount = 0;
		}

		// First apply the discount to the items.
		if ( $discount_amount > 0 && ! empty( $discount_type ) ) {
			$this->apply_discount( $discount_amount, $items, $discount_type );
		}

		foreach ( $items as $item ) {
			$discount     = $item->discount;
			$discount_tax = array_sum( eac_calculate_taxes( $discount, $item->get_taxes(), $this->tax_inclusive ) );
			if ( $this->tax_inclusive ) {
				$discount -= $discount_tax;
			}
			$discount = max( 0, $discount );

			$item->discount     = $discount;
			$item->discount_tax = $discount_tax;
		}
	}


	/**
	 * Calculate item taxes.
	 *
	 * @return void
	 */
	protected function calculate_item_taxes() {
		$items = $this->get_items();
		// Calculate item taxes.
		foreach ( $items as $item ) {
			$taxable_amount = $item->subtotal - $item->discount;
			$taxable_amount = max( 0, $taxable_amount );
			$taxes          = eac_calculate_taxes( $taxable_amount, $item->get_taxes(), false );
			$line_tax       = 0;
			foreach ( $item->get_taxes() as $tax ) {
				$amount      = isset( $taxes[ $tax->tax_id ] ) ? $taxes[ $tax->tax_id ] : 0;
				$tax->amount = $amount;
				$line_tax    += $amount;
			}
			$item->tax_total = $line_tax;
		}
	}

	/**
	 * Calculate item totals.
	 *
	 * @return void
	 */
	protected function calculate_item_totals() {
		foreach ( $this->get_items() as $item ) {
			$total       = $item->subtotal + $item->tax_total - $item->discount;
			$total       = max( 0, $total );
			$item->total = $total;
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
		} elseif ( 'percentage' === $type ) {
			$total_discounted = $this->apply_percentage_discount( $amount, $items );
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
				$discounted_price = $item->get_discounted_price();
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
	 * Apply percentage discount.
	 *
	 * @param float          $amount Discount amount.
	 * @param DocumentItem[] $items Items.
	 */
	public function apply_percentage_discount( $amount, $items ) {
		$total_discount = 0;
		$document_total = 0;

		if ( $amount <= 0 || empty( $items ) ) {
			return $total_discount;
		}

		foreach ( $items as $item ) {
			$discounted_price = $item->get_discounted_price();
			// If the item is not created yet, we need to calculate the discounted price without tax.
			$discount       = $discounted_price * ( $amount / 100 );
			$discount       = min( $discounted_price, $discount );
			$item->discount = $item->discount + $discount;
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
			for ( $i = 0; $i < $quantity; $i ++ ) {
				$discounted_price = $item->get_discounted_price();
				$discount         = min( $discounted_price, 1 );
				$item->discount   = $item->discount + $discount;
				$total_discount   += $discount;
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
	public function get_items_totals( $type, $column = 'total', $round = false ) {
		$items = $this->get_items( $type );
		$total = 0;
		foreach ( $items as $item ) {
			$amount = $item->$column ?? 0;
			$total  += $round ? round( $amount, 2 ) : $amount;
		}

		return $round ? round( $total, 2 ) : $total;
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
		return get_option( 'eac_invoice_prefix', 'INV-' );
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
		$min_digits = get_option( 'eac_invoice_digits', 4 );
		$number     = str_pad( $number, $min_digits, '0', STR_PAD_LEFT );

		return implode( '', array( $prefix, $number ) );
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
}
