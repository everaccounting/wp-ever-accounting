<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class DocumentItem.
 *
 * @since   1.0.0
 * @package EverAccounting\Models
 */
class DocumentItem_Lgacy extends Model {
	/**
	 * Table name.
	 *
	 * This is also used as table alias.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const TABLE_NAME = 'ea_document_items';

	/**
	 * Object type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const OBJECT_TYPE = 'document_item';

	/**
	 * Cache group.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const CACHE_GROUP = 'ea_document_items';

	/**
	 * Core data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $core_data = array(
		'document_id'     => null,
		'item_id'         => null,
		'item_name'       => '',
		'item_unit'       => '',
		'unit_price'      => 0.00,
		'description'     => '',
		'quantity'        => 1,
		'discount_type'   => 0.00,
		'discount_rate'   => 0.00,
		'subtotal'        => 0.00,
		'subtotal_tax'    => 0.00,
		'discount_amount' => 0.00,
		'discount_tax'    => 0.00,
		'shipping_amount' => 0.00,
		'shipping_tax'    => 0.00,
		'fee_amount'      => 0.00,
		'fee_tax'         => 0.00,
		'total'           => 0.00,
		'total_tax'       => 0.00,
		'currency_code'   => '',
		'updated_at'      => null,
		'created_at'      => null,
	);

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Return the order id.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	public function get_document_id( $context = 'edit' ) {
		return $this->get_prop( 'document_id', $context );
	}

	/**
	 * Return the item id.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	public function get_item_id( $context = 'edit' ) {
		return $this->get_prop( 'item_id', $context );
	}

	/**
	 * Return the name.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	public function get_item_name( $context = 'edit' ) {
		return $this->get_prop( 'item_name', $context );
	}

	/**
	 * Get the item unit.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function get_item_unit( $context = 'edit' ) {
		return $this->get_prop( 'item_unit', $context );
	}



	/**
	 * Return the price.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	public function get_price( $context = 'edit' ) {
		return $this->get_prop( 'price', $context );
	}

	/**
	 * Return the quantity.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return int
	 */
	public function get_quantity( $context = 'edit' ) {
		return $this->get_prop( 'quantity', $context );
	}

	/**
	 * Return the sub_total.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return float
	 */
	public function get_subtotal( $context = 'edit' ) {
		return $this->get_prop( 'subtotal', $context );
	}

	/**
	 * Return the tax.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return float
	 */
	public function get_tax_rate( $context = 'edit' ) {
		return $this->get_prop( 'tax_rate', $context );
	}

	/**
	 * Return the discount.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return float
	 */
	public function get_discount( $context = 'edit' ) {
		return $this->get_prop( 'discount', $context );
	}

	/**
	 * Get total tax.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since 1.1.0
	 * @return float
	 */
	public function get_tax( $context = 'edit' ) {
		return $this->get_prop( 'tax', $context );
	}

	/**
	 * Return the total.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	public function get_total( $context = 'edit' ) {
		return $this->get_prop( 'total', $context );
	}

	/**
	 * Return the total.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	public function get_currency_code( $context = 'edit' ) {
		return $this->get_prop( 'currency_code', $context );
	}

	/**
	 * Get extra data.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since 1.1.0
	 * @return array|mixed|string
	 */
	public function get_extra( $context = 'edit' ) {
		return $this->get_prop( 'extra' );
	}

	/**
	 * Gets a prop for a getter method.
	 *
	 * @param string $prop Name of prop to get.
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since  1.1.0
	 * @return mixed
	 */
	protected function get_extra_prop( $prop, $context = 'view' ) {
		$value = null;

		if ( array_key_exists( $prop, $this->data['extra'] ) ) {
			$value = isset( $this->changes['extra'][ $prop ] ) ? $this->changes['extra'][ $prop ] : $this->data['extra'][ $prop ];

			if ( 'view' === $context ) {
				$value = apply_filters( $this->get_hook_prefix() . 'extra_' . $prop, $value, $this );
			}
		}

		return $value;
	}

	/**
	 * Get shipping cost
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since 1.1.0
	 * @return float
	 */
	public function get_shipping( $context = 'edit' ) {
		return $this->get_extra_prop( 'shipping', $context );
	}

	/**
	 * get shipping tax
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since 1.1.0
	 * @return float
	 */
	public function get_shipping_tax( $context = 'edit' ) {
		return $this->get_extra_prop( 'shipping_tax', $context );
	}

	/**
	 * Get fees.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since 1.1.0
	 * @return float
	 */
	public function get_fees( $context = 'edit' ) {
		return $this->get_extra_prop( 'fees', $context );
	}

	/**
	 * Get fees tax.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since 1.1.0
	 * @return float
	 */
	public function get_fees_tax( $context = 'edit' ) {
		return $this->get_extra_prop( 'fees_tax', $context );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * set the order id.
	 *
	 * @param int $document_id .
	 *
	 * @since  1.1.0
	 */
	public function set_document_id( $document_id ) {
		$this->set_prop( 'document_id', absint( $document_id ) );
	}

	/**
	 * set the item_id.
	 *
	 * @param int $item_id .
	 *
	 * @since  1.1.0
	 */
	public function set_item_id( $item_id ) {
		$this->set_prop( 'item_id', absint( $item_id ) );
	}

	/**
	 * set the name.
	 *
	 * @param string $name .
	 *
	 * @since  1.1.0
	 */
	public function set_item_name( $name ) {
		$this->set_prop( 'item_name', sanitize_text_field( $name ) );
	}

	/**
	 * set the price.
	 *
	 * @param double $price .
	 *
	 * @since  1.1.0
	 */
	public function set_price( $price ) {
		$this->set_prop( 'price', eac_format_decimal( $price, 4 ) );
	}


	/**
	 * set the quantity.
	 *
	 * @param int $quantity .
	 *
	 * @since  1.1.0
	 */
	public function set_quantity( $quantity = 1 ) {
		$this->set_prop( 'quantity', eac_format_decimal( $quantity, 2 ) );
	}

	/**
	 * set the tax.
	 *
	 * Flat amount
	 *
	 * @param double $subtotal .
	 *
	 * @since  1.1.0
	 */
	public function set_subtotal( $subtotal ) {
		$this->set_prop( 'subtotal', eac_format_decimal( $subtotal, 4 ) );
	}

	/**
	 * set the tax.
	 *
	 * @param float $tax_rate .
	 *
	 * @since  1.1.0
	 */
	public function set_tax_rate( $tax_rate ) {
		$this->set_prop( 'tax_rate', eac_format_decimal( $tax_rate, 4 ) );
	}

	/**
	 * set the tax.
	 *
	 * @param float $tax Tax amount.
	 *
	 * @since  1.1.0
	 */
	public function set_tax( $tax ) {
		$this->set_prop( 'tax', eac_format_decimal( $tax, 4 ) );
	}

	/**
	 * set the tax.
	 *
	 * Flat amount
	 *
	 * @param double $discount .
	 *
	 * @since  1.1.0
	 */
	public function set_discount( $discount ) {
		$this->set_prop( 'discount', eac_format_decimal( $discount, 4 ) );
	}

	/**
	 * set the total.
	 *
	 * @param int $total .
	 *
	 * @since  1.1.0
	 */
	public function set_total( $total ) {
		$this->set_prop( 'total', eac_format_decimal( $total, 4 ) );
	}

	/**
	 * set the total.
	 *
	 * @param string $currency_code .
	 *
	 * @since  1.1.0
	 */
	public function set_currency_code( $currency_code ) {
		$this->set_prop( 'currency_code', eac_clean( $currency_code ) );
	}

	/**
	 * set the total.
	 *
	 * @param array $extra Extra data.
	 * @param bool  $append Append extra data.
	 *
	 * @since  1.1.0
	 */
	public function set_extra( $extra, $append = true ) {
		$extra = eac_clean( $extra );
		if ( is_array( $extra ) ) {
			$extra = $append ? array_merge( $this->data['extra'], $extra ) : $extra;
			$this->set_prop( 'extra', eac_clean( $extra ) );
		}
	}

	/**
	 * Sets a prop for a setter method.
	 *
	 * @param string $prop Name of prop to set.
	 * @param mixed  $value Value of the prop.
	 *
	 * @since 1.1.0
	 */
	protected function set_extra_prop( $prop, $value ) {
		if ( array_key_exists( $prop, $this->data['extra'] ) ) {
			if ( true === $this->object_read ) {
				if ( $value !== $this->data['extra'][ $prop ] || ( isset( $this->changes['extra'] ) && array_key_exists( $prop, $this->changes['extra'] ) ) ) {
					$this->changes['extra'][ $prop ] = $value;
				}
			} else {
				$this->data['extra'][ $prop ] = $value;
			}
		}
	}

	/**
	 * Set shipping.
	 *
	 * @param string $shipping shipping.
	 *
	 * @since 1.1.0
	 */
	public function set_shipping( $shipping ) {
		$this->set_extra_prop( 'shipping', eac_format_decimal( $shipping, 4 ) );
	}

	/**
	 * Set shipping_tax.
	 *
	 * @param string $shipping_tax shipping_tax.
	 *
	 * @since 1.1.0
	 */
	public function set_shipping_tax( $shipping_tax ) {
		$this->set_extra_prop( 'shipping_tax', eac_format_decimal( $shipping_tax, 4 ) );
	}

	/**
	 * Set fees.
	 *
	 * @param string $fees fees.
	 *
	 * @since 1.1.0
	 */
	public function set_fees( $fees ) {
		$this->set_extra_prop( 'fees', eac_format_decimal( $fees, 4 ) );
	}

	/**
	 * Set fees_tax.
	 *
	 * @param string $fees_tax fees_tax.
	 *
	 * @since 1.1.0
	 */
	public function set_fees_tax( $fees_tax ) {
		$this->set_extra_prop( 'fees_tax', eac_format_decimal( $fees_tax, 4 ) );
	}

	/**
	 * Increment quantity.
	 *
	 * @param int $increment .
	 *
	 * @since 1.1.0
	 */
	public function increment_quantity( $increment ) {
		$this->set_quantity( $this->get_quantity() + $increment );
	}

	/**
	 * Calculate total.
	 *
	 * @since 1.1.0
	 */
	public function calculate_total() {
		$subtotal         = $this->get_price() * $this->get_quantity();
		$discount         = $this->get_discount();
		$subtotal_for_tax = $subtotal - $discount;
		$tax_rate         = ( $this->get_tax_rate() / 100 );
		$total_tax        = eaccounting_calculate_tax( $subtotal_for_tax, $tax_rate );

		if ( 'tax_subtotal_rounding' !== eaccounting()->settings->get( 'tax_subtotal_rounding', 'tax_subtotal_rounding' ) ) {
			$total_tax = eac_format_decimal( $total_tax, 2 );
		}
		if ( eaccounting_prices_include_tax() ) {
			$subtotal -= $total_tax;
		}
		$total = $subtotal - $discount + $total_tax;
		if ( $total < 0 ) {
			$total = 0;
		}

		$this->set_subtotal( $subtotal );
		$this->set_tax( $total_tax );
		$this->set_total( $total );

	}

	/**
	 * Save should create or update based on object existence.
	 *
	 * @since  1.1.0
	 * @return \Exception|bool
	 */
	public function save() {
		if ( ! empty( $this->changes ) || ! $this->exists() ) {
			$this->calculate_total();
		}

		return parent::save();
	}

}
