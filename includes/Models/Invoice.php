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
 * @property DocumentLine[] $lines Invoice lines.
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
	protected $query_args = array(
		'type' => 'invoice',
	);

	/**
	 * Create a new model instance.
	 *
	 * @param string|array|object $props The model attributes.
	 *
	 * @throws \InvalidArgumentException If table name or object type is not set.
	 */
	public function __construct( $props = array() ) {
		$due_after   = get_option( 'eac_invoice_due_date', 7 );
		$_props      = array(
			'type'          => $this->get_object_type(),
			'number'        => $this->get_next_number(),
			'issue_date'    => current_time( 'mysql' ),
			'due_date'      => wp_date( 'Y-m-d', strtotime( '+' . $due_after . ' days' ) ),
			'notes'         => get_option( 'eac_invoice_notes', '' ),
			'tax_inclusive' => filter_var( eac_price_includes_tax(), FILTER_VALIDATE_BOOLEAN ),
			'currency_code' => eac_get_base_currency(),
			'author_id'     => get_current_user_id(),
			'uuid'          => wp_generate_uuid4(),
		);
		$this->props = array_merge( $this->props, $_props );
		parent::__construct( $props );
	}

	/*
	|--------------------------------------------------------------------------
	| Prop Definition Methods
	|--------------------------------------------------------------------------
	| This section contains methods that define and provide specific prop values
	| related to the model, such as statuses or types. These methods can be accessed
	| without instantiating the model.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get invoice line items columns.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public static function get_line_columns() {
		return apply_filters(
			'ever_accounting_invoice_line_columns',
			array(
				'item'     => __( 'Item', 'wp-ever-accounting' ),
				'price'    => __( 'Price', 'wp-ever-accounting' ),
				'quantity' => __( 'Quantity', 'wp-ever-accounting' ),
				'tax'      => __( 'Tax', 'wp-ever-accounting' ),
				'subtotal' => __( 'Subtotal', 'wp-ever-accounting' ),
			)
		);
	}

	/*
	|--------------------------------------------------------------------------
	| Accessors, Mutators, Relationship and Validation Methods
	|--------------------------------------------------------------------------
	| This section contains methods for getting and setting properties (accessors
	| and mutators) as well as defining relationships between models. It also includes
	| a data validation method that ensures data integrity before saving.
	|--------------------------------------------------------------------------
	*/


	/**
	 * Payments relation.
	 *
	 * @since 1.0.0
	 * @return HasMany
	 */
	public function payments() {
		return $this->has_many( Revenue::class, 'document_id' )->set_query_var( 'status', 'completed' );
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

		// ensure the invoice number is set and unique.
		if ( empty( $this->number ) ) {
			$this->number = $this->get_next_number();
		} else {
			$existing = self::find( array( 'number' => $this->number ) );
			if ( ! empty( $existing ) && $existing->id !== $this->id ) {
				$this->number = $this->get_next_number();
			}
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
			$amount = eac_convert_currency( $payment->amount, $payment->currency_code, $this->currency_code, $payment->exchange_rate, $this->exchange_rate );
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
		} elseif ( 'percentage' === $type ) {
			$total_discounted = $this->apply_percentage_discount( $amount, $items );
		}

		return $total_discounted;
	}

	/**
	 * Apply fixed discount.
	 *
	 * @param float          $amount Discount amount.
	 * @param DocumentLine[] $items Items.
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
	 * Apply percentage discount.
	 *
	 * @param float          $amount Discount amount.
	 * @param DocumentLine[] $items Items.
	 */
	public function apply_percentage_discount( $amount, $items ) {
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
	 * @param DocumentLine[] $items Items.
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
	 * Set line items.
	 *
	 * @param array $lines Items.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function set_lines( $lines ) {
		if ( ! is_array( $lines ) ) {
			return;
		}
		$old_lines = $this->lines()->get_items();
		$this->set_relation( 'lines', array() );
		foreach ( $lines as $line ) {
			$line_default = array(
				'id'       => 0, // line id.
				'item_id'  => 0, // item id **not line id** be careful.
				'type'     => 'standard', // 'standard', 'fee', 'shipping
				'quantity' => 1,
			);

			// The data must be a line item with id or a new array with item_id and additional data.
			if ( empty( $line['id'] ) && empty( $line['item_id'] ) ) {
				return;
			}

			// If id is not set but item_id is set, we need to get the item data.
			if ( empty( $line['id'] ) && ! empty( $line['item_id'] ) ) {
				$item                 = Item::make( $line['item_id'] );
				$item_data            = wp_array_slice_assoc( $item->to_array(), array( 'name', 'type', 'description', 'unit', 'price', 'taxable' ) );
				$item_data['item_id'] = $item->id;
				$line_default         = wp_parse_args( $item_data, $line_default );
			}
			$line = wp_parse_args( $line, $line_default );

			// skip if item_id is not set.
			if ( empty( $line['item_id'] ) ) {
				continue;
			}

			$line_item = $this->lines()->make( $line );
			// If same line already exists, we will merge them.
			foreach ( $this->lines as $old_line_item ) {
				if ( $old_line_item->is_similar( $line_item ) ) {
					$old_line_item->quantity += $line_item->quantity;
					break;
				}
			}

			$this->set_relation(
				'lines',
				function ( $relation ) use ( $line_item ) {
					$relation   = is_array( $relation ) ? $relation : array( $relation );
					$relation[] = $line_item;

					return $relation;
				}
			);
		}
	}

	/**
	 * Set line items.
	 *
	 * @param array $request Items.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function set_lines_v1( $request ) {
		foreach ( $request as $line_data ) {
			$line_default = array(
				'id'          => 0, // line id.
				'item_id'     => 0, // item id **not line id** be careful.
				'type'        => 'standard', // 'standard', 'fee', 'shipping
				'name'        => '',
				'description' => '',
				'unit'        => '',
				'price'       => 0,
				'quantity'    => 1,
			);
			// The data must be a line item with id or a new array with item_id and additional data.
			if ( empty( $line_data['id'] ) && empty( $line_data['item_id'] ) ) {
				return;
			}

			// If id is not set but item_id is set, we need to get the item data.
			if ( empty( $line_data['id'] ) && ! empty( $line_data['item_id'] ) ) {
				$item                 = Item::make( $line_data['item_id'] );
				$item_data            = wp_array_slice_assoc( $item->to_array(), array( 'name', 'type', 'description', 'unit', 'price', 'taxable' ) );
				$item_data['item_id'] = $item->id;
				$line_default         = wp_parse_args( $item_data, $line_default );
			}

			$line_data                = wp_parse_args( $line_data, $line_default );
			$line_data['name']        = wp_strip_all_tags( $line_data['name'] );
			$line_data['description'] = wp_strip_all_tags( $line_data['description'] );
			$line_data['description'] = wp_trim_words( $line_data['description'], 20, '' );
			$line_data['unit']        = wp_strip_all_tags( $line_data['unit'] );

			// skip if item_id is not set.
			if ( empty( $line_data['item_id'] ) ) {
				continue;
			}

			// we have to validate the data before creating the item.
			if ( ! array_key_exists( $line_data['type'], eac_get_item_types() ) ) {
				$line_data['type'] = 'standard';
			}
			if ( ! array_key_exists( $line_data['unit'], eac_get_unit_types() ) ) {
				$line_data['unit'] = '';
			}

			$line = $this->lines()->make( $line_data );
			foreach ( $line->taxes()->get_items() as $old_line_tax ) {
				$old_line_tax->delete();
			}

			// taxes may come in 2 formats, one is just an array of tax ids, and another is an array of tax data.
			if ( isset( $line_data['tax_ids'] ) ) {
				$line_data['taxes'] = array_map(
					function ( $tax_id ) {
						return array(
							'tax_id' => $tax_id,
						);
					},
					(array) $line_data['tax_ids']
				);
			}

			// Line taxes.
			if ( ! empty( $line_data['tax_ids'] ) ) {
				foreach ( $line_data['tax_ids'] as $line_tax_key => $line_tax_data ) {
					$tax_default = array(
						'id'          => 0,
						'tax_id'      => 0,
						'line_id'     => null,
						'document_id' => null,
						'name'        => '',
						'rate'        => 0,
						'is_compound' => 'no',
					);

					if ( empty( $line_tax_data['id'] ) && empty( $line_tax_data['tax_id'] ) ) {
						continue;
					}

					if ( empty( $line_tax_data['id'] ) && ! empty( $line_tax_data['tax_id'] ) ) {
						$tax         = Tax::make( $line_tax_data['tax_id'] );
						$tax_data    = wp_array_slice_assoc( $tax->to_array(), array( 'name', 'rate', 'is_compound' ) );
						$tax_default = wp_parse_args( $tax_data, $tax_default );
					}

					$line_tax_data = wp_parse_args( $line_tax_data, $tax_default );
					// if tax_id is not set, we will skip this tax.
					if ( empty( $line_tax_data['tax_id'] ) ) {
						unset( $line_data['taxes'][ $line_tax_key ] );
						continue;
					}

					$line_tax = $line->taxes()->make( $line_tax_data );

					foreach ( $line->taxes as $line_tax_k => $old_line_tax ) {
						if ( $old_line_tax->tax_id === $line_tax->tax_id ) {
							unset( $line->taxes[ $line_tax_k ] );
							continue;
						}
					}
					$line->set_relation(
						'taxes',
						function ( $relation ) use ( $line_tax ) {
							$relation   = is_array( $relation ) ? $relation : array( $relation );
							$relation[] = $line_tax;

							return $relation;
						}
					);
				}
			}

			// If same line already exists, we will merge them.
			foreach ( $this->lines as $old_line ) {
				if ( $old_line->is_similar( $line ) ) {
					$old_line->quantity += $line->quantity;
					break;
				}
			}

			$this->set_relation(
				'lines',
				function ( $relation ) use ( $line ) {
					$relation   = is_array( $relation ) ? $relation : array( $relation );
					$relation[] = $line;

					return $relation;
				}
			);
		}
	}

	/**
	 * Get the lines of the given type.
	 *
	 * @param string $type The type of the line.
	 *
	 * @since 1.0.0
	 * @return DocumentLine[]
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
		return (int) $this->get_db()->get_var(
			$this->get_db()->prepare(
				"SELECT MAX(REGEXP_REPLACE(number, '[^0-9]', '')) FROM {$this->get_table(true)} WHERE type = %s",
				esc_sql( $this->type )
			)
		);
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
