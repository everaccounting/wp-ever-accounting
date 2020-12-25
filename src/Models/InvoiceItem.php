<?php

namespace EverAccounting\Models;

use EverAccounting\Abstracts\ResourceModel;
use EverAccounting\Core\Repositories;

class InvoiceItem extends ResourceModel {
	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'invoice_item';

	/**
	 * @since 1.1.0
	 *
	 * @var string
	 */
	public $cache_group = 'ea_invoice_items';

	/**
	 * Item Data array.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $data = array(
		'invoice_id'    => null,
		'item_id'       => null,
		'item_name'     => '',
		'unit_price'    => 0.00,
		'quantity'      => 1,
		'subtotal'      => 0.00,
		'tax_rate'      => 0.00,
		'subtotal_tax'  => 0.00,
		'discount'      => 0.00,
		'discount_tax'  => 0.00,
		'shipping'      => 0.00,
		'shipping_tax'  => 0.00,
		'total_tax'     => 0.00,
		'total'         => 0.00,
		'currency_code' => '',
		'extra'         => '',
		'date_created'  => null,
	);

	/**
	 * Get the line item if ID is passed, otherwise the account is new and empty.
	 *
	 * @since 1.1.0
	 *
	 * @param int|object|LineItem $data object to read.
	 *
	 */
	public function __construct( $data = 0 ) {
		parent::__construct( $data );

		if ( $data instanceof self ) {
			$this->set_id( $data->get_id() );
		} elseif ( is_numeric( $data ) ) {
			$this->set_id( $data );
		} elseif ( ! empty( $data->id ) ) {
			$this->set_id( $data->id );
		} elseif ( is_array( $data ) ) {
			$this->set_props( $data );
		} else {
			$this->set_object_read( true );
		}

		//Load repository
		$this->repository = Repositories::load( 'invoice-items' );

		if ( $this->get_id() > 0 ) {
			$this->repository->read( $this );
		}

		$this->required_props = array(
			'item_id'    => __( 'Item ID', 'wp-ever-accounting' ),
			'item_name'  => __( 'Item name', 'wp-ever-accounting' ),
			'invoice_id' => __( 'Invoice ID', 'wp-ever-accounting' ),
		);
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/
	/**
	 * Return the invoice id.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_invoice_id( $context = 'edit' ) {
		return $this->get_prop( 'invoice_id', $context );
	}

	/**
	 * Return the item id.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_item_id( $context = 'edit' ) {
		return $this->get_prop( 'item_id', $context );
	}

	/**
	 * Return the name.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_item_name( $context = 'edit' ) {
		return $this->get_prop( 'item_name', $context );
	}

	/**
	 * Return the quantity.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_quantity( $context = 'edit' ) {
		return $this->get_prop( 'quantity', $context );
	}


	/**
	 * Return the price.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_unit_price( $context = 'edit' ) {
		return $this->get_prop( 'unit_price', $context );
	}

	/**
	 * Return the tax.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return float
	 */
	public function get_tax_name( $context = 'edit' ) {
		return $this->get_prop( 'tax_name', $context );
	}

	/**
	 * Return the tax.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return float
	 */
	public function get_tax_rate( $context = 'edit' ) {
		return $this->get_prop( 'tax_rate', $context );
	}

	/**
	 * Return the sub_total.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context
	 *
	 * @return float
	 */
	public function get_subtotal( $context = 'edit' ) {
		return $this->get_prop( 'subtotal', $context );
	}

	/**
	 * Return the sub_total.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context
	 *
	 * @return float
	 */
	public function get_subtotal_tax( $context = 'edit' ) {
		return $this->get_prop( 'subtotal_tax', $context );
	}

	/**
	 * Return the discount.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return float
	 */
	public function get_discount( $context = 'edit' ) {
		return $this->get_prop( 'discount', $context );
	}

	/**
	 * Return the discount.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return float
	 */
	public function get_discount_tax( $context = 'edit' ) {
		return $this->get_prop( 'discount_tax', $context );
	}

	/**
	 * Return the discount.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return float
	 */
	public function get_shipping( $context = 'edit' ) {
		return $this->get_prop( 'shipping', $context );
	}

	/**
	 * Return the shipping.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return float
	 */
	public function get_shipping_tax( $context = 'edit' ) {
		return $this->get_prop( 'shipping_tax', $context );
	}

	/**
	 * Return the total.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return float
	 */
	public function get_total_tax( $context = 'edit' ) {
		return $this->get_prop( 'total_tax', $context );
	}

	/**
	 * Return the total.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_total( $context = 'edit' ) {
		return $this->get_prop( 'total', $context );
	}

	/**
	 * @since 1.1.0
	 *
	 * @param bool $serialize
	 *
	 * @return array|mixed|string
	 */
	public function get_extra( $serialize = true ) {
		return $this->get_prop( 'extra' );
	}

	/**
	 * Return the currency code.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_currency_code( $context = 'edit' ) {
		return $this->get_prop( 'currency_code', $context );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * set the order id.
	 *
	 * @since  1.1.0
	 *
	 * @param int $invoice_id .
	 *
	 */
	public function set_invoice_id( $invoice_id ) {
		$this->set_prop( 'invoice_id', absint( $invoice_id ) );
	}

	/**
	 * Return type
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_type( $context = 'edit' ) {
		return $this->get_prop( 'type', $context );
	}

	/**
	 * set the item_id.
	 *
	 * @since  1.1.0
	 *
	 * @param int $item_id .
	 *
	 */
	public function set_item_id( $item_id ) {
		$this->set_prop( 'item_id', absint( $item_id ) );
	}

	/**
	 * set the name.
	 *
	 * @since  1.1.0
	 *
	 * @param string $name .
	 *
	 */
	public function set_item_name( $name ) {
		$this->set_prop( 'item_name', eaccounting_clean( $name ) );
	}

	/**
	 * set the price.
	 *
	 * @since  1.1.0
	 *
	 * @param double $price .
	 *
	 */
	public function set_unit_price( $price ) {
		$this->set_prop( 'unit_price', (float) eaccounting_sanitize_number( $price, true ) );
	}


	/**
	 * set the quantity.
	 *
	 * @since  1.1.0
	 *
	 * @param int $quantity .
	 *
	 */
	public function set_quantity( $quantity = 1 ) {
		$this->set_prop( 'quantity', eaccounting_sanitize_number( $quantity ) );
	}

	/**
	 * Return the sub_total.
	 *
	 * @since  1.1.0
	 *
	 * @param string $subtotal
	 */
	public function set_subtotal( $subtotal ) {
		$this->set_prop( 'subtotal', eaccounting_sanitize_number( $subtotal ) );
	}


	/**
	 * set the tax.
	 *
	 * @since  1.1.0
	 *
	 * @param double $tax .
	 *
	 */
	public function set_tax_rate( $tax_rate ) {
		$this->set_prop( 'tax_rate', floatval( $tax_rate ) );
	}

	/**
	 * Return the sub_total.
	 *
	 * @since  1.1.0
	 *
	 * @param string $subtotal_tax
	 */
	public function set_subtotal_tax( $subtotal_tax ) {
		$this->set_prop( 'subtotal_tax', eaccounting_sanitize_number( $subtotal_tax ) );
	}

	/**
	 * set the tax.
	 *
	 * Flat amount
	 *
	 * @since  1.1.0
	 *
	 * @param double $discount .
	 *
	 */
	public function set_discount( $discount ) {
		$this->set_prop( 'discount', eaccounting_sanitize_number( $discount ) );
	}

	/**
	 * set the tax.
	 *
	 * Flat amount
	 *
	 * @since  1.1.0
	 *
	 * @param double $discount_tax .
	 *
	 */
	public function set_discount_tax( $discount_tax ) {
		$this->set_prop( 'discount_tax', eaccounting_sanitize_number( $discount_tax ) );
	}

	/**
	 * set the tax.
	 *
	 * Flat amount
	 *
	 * @since  1.1.0
	 *
	 * @param double $shipping .
	 *
	 */
	public function set_shipping( $shipping ) {
		$this->set_prop( 'shipping', eaccounting_sanitize_number( $shipping ) );
	}

	/**
	 * set the tax.
	 *
	 * Flat amount
	 *
	 * @since  1.1.0
	 *
	 * @param double $shipping_tax .
	 *
	 */
	public function set_shipping_tax( $shipping_tax ) {
		$this->set_prop( 'shipping_tax', eaccounting_sanitize_number( $shipping_tax ) );
	}

	/**
	 * set the total.
	 *
	 * @since  1.1.0
	 *
	 * @param int $total .
	 *
	 */
	public function set_total( $total ) {
		$this->set_prop( 'total', floatval( $total ) );
	}

	/**
	 * set the total.
	 *
	 * @since  1.1.0
	 *
	 * @param $extra
	 */
	public function set_extra( $extra ) {
		$this->set_prop( 'extra', eaccounting_clean( $extra ) );
	}
}
