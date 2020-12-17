<?php
/**
 * Handle the order item object.
 *
 * @package     EverAccounting\Models
 * @class       Currency
 * @version     1.1.0
 */

namespace EverAccounting\Models;

use EverAccounting\Abstracts\ResourceModel;
use EverAccounting\Core\Repositories;

defined( 'ABSPATH' ) || exit;

class LineItem extends ResourceModel {
	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'line_item';

	/**
	 * @since 1.1.0
	 * @var string
	 */
	public $cache_group = 'ea_line_items';

	/**
	 * Item Data array.
	 *
	 * @since 1.1.0
	 * @var array
	 */
	protected $data = array(
		'parent_id'      => null,
		'parent_type'    => '',
		'item_id'        => null,
		'item_name'      => '',
		'item_sku'       => '',
		'item_price'     => 0.00,
		'quantity'       => 1,
		'tax_rate'       => 0.00,
		'vat_rate'       => 0.00,
		'discount_rate'  => 0.00,
		'subtotal'       => 0.00,
		'total_tax'      => 0.00,
		'total_vat'      => 0.00,
		'total_discount' => 0.00,
		'total'          => 0.00,
		'extra'          => '',
		'date_created'   => null,
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
		$this->repository = Repositories::load( 'line-items' );

		if ( $this->get_id() > 0 ) {
			$this->repository->read( $this );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Return the order id.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_parent_id( $context = 'edit' ) {
		return $this->get_prop( 'parent_id', $context );
	}

	/**
	 * Return parent type
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_parent_type( $context = 'edit' ) {
		return $this->get_prop( 'parent_type', $context );
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
	 * Return the sku.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_item_sku( $context = 'edit' ) {
		return $this->get_prop( 'item_sku', $context );
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
	public function get_item_price( $context = 'edit' ) {
		return $this->get_prop( 'item_price', $context );
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
	 * Return the tax.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return float
	 */
	public function get_vat_rate( $context = 'edit' ) {
		return $this->get_prop( 'vat_rate', $context );
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
	public function get_discount_rate( $context = 'edit' ) {
		return $this->get_prop( 'discount_rate', $context );
	}

	/**
	 * Return the sub_total.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return float
	 */
	public function get_subtotal( $context = 'edit' ) {
		return $this->get_prop( 'subtotal', $context );
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
	public function get_total_tax( $context = 'edit' ) {
		return $this->get_prop( 'total_tax', $context );
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
	public function get_total_vat( $context = 'edit' ) {
		return $this->get_prop( 'total_vat', $context );
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
	public function get_total_discount( $context = 'edit' ) {
		return $this->get_prop( 'total_discount', $context );
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
	 * @param string $key
	 * @param bool   $single
	 * @param string $context
	 * @since 1.1.0
	 *
	 * @return array|mixed|string
	 */
	public function get_extra( $serialize = true ) {
		return $this->get_prop( 'extra' );
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
	 * @param int $parent_id .
	 *
	 */
	public function set_parent_id( $parent_id ) {
		$this->set_prop( 'parent_id', absint( $parent_id ) );
	}

	/**
	 * set the type.
	 *
	 * @since  1.1.0
	 *
	 * @param string $type .
	 *
	 */
	public function set_parent_type( $type ) {
		$this->set_prop( 'parent_type', eaccounting_clean( $type ) );
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
	 * set the sku.
	 *
	 * @since  1.1.0
	 *
	 * @param string $sku .
	 *
	 */
	public function set_item_sku( $sku ) {
		$this->set_prop( 'item_sku', eaccounting_clean( $sku ) );
	}

	/**
	 * set the price.
	 *
	 * @since  1.1.0
	 *
	 * @param double $price .
	 *
	 */
	public function set_item_price( $price ) {
		$this->set_prop( 'item_price', eaccounting_sanitize_price( $price ) );
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
		$this->set_prop( 'quantity', floatval( $quantity ) );
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
	 * set the tax.
	 *
	 * @since  1.1.0
	 *
	 * @param double $vat_rate .
	 *
	 */
	public function set_vat_rate( $vat_rate ) {
		$this->set_prop( 'vat_rate', floatval( $vat_rate ) );
	}

	/**
	 * set the tax.
	 *
	 * @since  1.1.0
	 *
	 * @param double $discount_rate .
	 *
	 */
	public function set_discount_rate( $discount_rate ) {
		$this->set_prop( 'discount_rate', floatval( $discount_rate ) );
	}

	/**
	 * set the sub total.
	 *
	 * @since  1.1.0
	 *
	 * @param int $sub_total .
	 *
	 */
	public function set_subtotal( $sub_total ) {
		$this->set_prop( 'subtotal', eaccounting_sanitize_price( $sub_total ) );
	}

	/**
	 * set the tax.
	 *
	 * @since  1.1.0
	 *
	 * @param double $tax .
	 *
	 */
	public function set_total_tax( $tax ) {
		$this->set_prop( 'total_tax', floatval( $tax ) );
	}

	/**
	 * set the tax.
	 *
	 * @since  1.1.0
	 *
	 * @param double $vat .
	 *
	 */
	public function set_total_vat( $vat ) {
		$this->set_prop( 'total_vat', eaccounting_sanitize_number( $vat, true ) );
	}

	/**
	 * set the discount.
	 *
	 * @since  1.1.0
	 *
	 * @param int $discount .
	 *
	 */
	public function set_total_discount( $discount ) {
		$this->set_prop( 'total_discount', floatval( $discount ) );
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



	/*
	|--------------------------------------------------------------------------
	| Additional methods
	|--------------------------------------------------------------------------
	|
	| Does extra thing as helper functions.
	|
	*/


	/**
	 * Save should create or update based on object existence.
	 *
	 * @since  1.1.0
	 * @return \Exception|bool
	 */
	public function save() {
		if ( empty( $this->get_item_id() ) ) {
			throw new \Exception(  __( 'Item ID must be specified.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_item_name() ) ) {
			throw new \Exception(  __( 'Item name can not be blank.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_parent_id() ) ) {
			throw new \Exception(  __( 'Parent ID must be specified.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_parent_type() ) ) {
			throw new \Exception(  __( 'Parent type must be specified.', 'wp-ever-accounting' ) );
		}

		return parent::save();
	}
}
