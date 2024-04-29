<?php

namespace EverAccounting\Models;

/**
 * Invoice model.
 *
 * @since 1.0.0
 * @author  Sultan Nasir Uddin <manikdrmc@gmail.com>
 * @package EverAccounting
 * @subpackage Models
 */
class Invoice extends Document {
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
	 *
	 * @throws \InvalidArgumentException If table name or object type is not set.
	 * @return void
	 */
	public function __construct( $attributes = null ) {
		$this->attributes['type']   = $this->get_object_type();
		$this->attributes['number'] = $this->get_next_number();
		$this->query_args['type']   = $this->get_object_type();
		parent::__construct( $attributes );
	}


	/*
	|--------------------------------------------------------------------------
	| Attributes & Relations
	|--------------------------------------------------------------------------
	| Define the attributes and relations of the model.
	*/


	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	| Methods for saving, updating, and deleting objects.
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
			'tax_ids'     => '',
		);

		if ( is_object( $data ) ) {
			$data = $data instanceof \stdClass ? get_object_vars( $data ) : $data->to_array();
		} elseif ( is_numeric( $data ) ) {
			$data = array( 'item_id' => $data );
		}

		// The data must be a line item with id or a new array with item_id and additional data.
		if ( ! isset( $data['id'] ) && ! isset( $data['item_id'] ) ) {
			return;
		}

		if ( ! empty( $data['item_id'] ) ) {
			$product       = eac_get_item( $data['item_id'] );
			$product_data  = $product ? $product->to_array() : array();
			$accepted_keys = array(
				'name',
				'type',
				'description',
				'unit',
				'price',
				'taxable',
			// 'tax_ids',
			);
			// if the currency is not the as the base currency, we need to convert the price.

			if ( eac_get_base_currency() !== $this->currency_code ) {
				$price                 = eac_convert_money( $product_data['price'], eac_get_base_currency(), $this->currency_code );
				$product_data['price'] = $price;
			}

			$product_data = wp_array_slice_assoc( $product_data, $accepted_keys );
			// prepare tax ids.
			if ( $product->taxable && ! empty( $product->tax_ids ) ) {
				foreach ( $product->tax_ids as $tax_id ) {
					$product_data['taxes'][] = array(
						'tax_id' => $tax_id,
						'amount' => 0,
					);
				}
			}

			$data = wp_parse_args( $data, $product_data );
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
		if ( ! empty( $data['taxes'] ) ) {
			$item->set_taxes( $data['taxes'] );
		}

		// if product id is not set then it is not product item.
		if ( empty( $item->item_id ) || empty( $item->quantity ) ) {
			return;
		}

		// Check if the item is set to be deleted and all the data matches. If so, remove it from the deletable list and add it to the items list.
		foreach ( $this->deletable as $key => $deletable_item ) {
			if ( $deletable_item->is_similar( $item ) ) {
				unset( $this->deletable[ $key ] );
				$deletable_item->fill( $data );
				$this->items[] = $deletable_item;

				return;
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
		$transactions     = $this->transactions()->query(
			array(
				'status'   => 'completed',
				'orderby'  => 'id',
				'order'    => 'ASC',
				'limit'    => - 1,
				'no_count' => true,
			)
		);
		$this->total_paid = 0;
		if ( ! empty( $transactions ) ) {
			foreach ( $transactions as $transaction ) {
				if ( 'income' === $transaction->type ) {
					$this->total_paid += $transaction->amount;
				} else {
					$this->total_paid -= $transaction->amount;
				}
			}
		}

		$this->calculate_item_prices();
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
				$line_tax   += $amount;
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
			$total        = $item->subtotal + $item->tax_total - $item->discount;
			$total        = max( 0, $total );
			$item->total += $total;
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
				$discounted_price = $item->get_discounted_price();
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
	public function get_items_totals( $type, $column = 'total', $round = false ) {
		$items = $this->get_items( $type );
		$total = 0;
		foreach ( $items as $item ) {
			$amount = $item->$column ?? 0;
			$total += $round ? round( $amount, 2 ) : $amount;
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
}
