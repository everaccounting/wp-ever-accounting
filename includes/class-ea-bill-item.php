<?php
/**
 * Handle the bill item object.
 *
 * @since       1.1.0
 *
 * @package     EverAccounting
 */

namespace EverAccounting;

use EverAccounting\Abstracts\Base_Object;

defined( 'ABSPATH' ) || exit();

/**
 * Class Bill item
 *
 * @since 1.1.0
 */
class Bill_Item extends Base_Object {
	/**
	 * This is the name of this object type.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	public $object_type = 'bill_item';

	/***
	 * Object table name.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	public $table = 'ea_bill_items';

	/**
	 * Item Data array.
	 *
	 * @since 1.1.0
	 * @var array
	 */
	protected $data = array(
		'bill_id'   => '',
		'item_id'      => '',
		'name'         => '',
		'sku'          => '',
		'quantity'     => 0.00,
		'price'        => 0.0000,
		'total'        => 0.0000,
		'tax_id'       => '',
		'tax_name'     => '',
		'tax'          => 0.0000,
		'date_created' => null
	);

	/**
	 * Get the bill item if ID is passed, otherwise the bill item is new and empty.
	 * This class should NOT be instantiated, but the eaccounting_get_bill_item function
	 * should be used. It is possible, but the aforementioned are preferred and are the only
	 * methods that will be maintained going forward.
	 *
	 * @param int|object|Category $data object to read.
	 *
	 * @since 1.1.0
	 *
	 */
	public function __construct( $data = 0 ) {
		parent::__construct( $data );

		if ( is_numeric( $data ) && $data > 0 ) {
			$this->set_id( $data );
		} elseif ( $data instanceof self ) {
			$this->set_id( $data->get_id() );
		} elseif ( ! empty( $data->id ) ) {
			$this->set_id( $data->id );
		} else {
			$this->set_id( 0 );
		}

		if ( $this->get_id() > 0 && ! $this->object_read ) {
			$this->read();
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get bill id
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_bill_id( $context = 'edit' ) {
		return $this->get_prop( 'bill_id', $context );
	}

	/**
	 * Get item_id
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_item_id( $context = 'edit' ) {
		return $this->get_prop( 'item_id', $context );
	}

	/**
	 * Get name
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_name( $context = 'edit' ) {
		return $this->get_prop( 'name', $context );
	}

	/**
	 * Get sku
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_sku( $context = 'edit' ) {
		return $this->get_prop( 'sku', $context );
	}

	/**
	 * Get quantity
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_quantity( $context = 'edit' ) {
		return $this->get_prop( 'quantity', $context );
	}

	/**
	 * Get price
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_price( $context = 'edit' ) {
		return $this->get_prop( 'price', $context );
	}

	/**
	 * Get total
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_total( $context = 'edit' ) {
		return $this->get_prop( 'total', $context );
	}

	/**
	 * Get tax_id
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_tax_id( $context = 'edit' ) {
		return $this->get_prop( 'tax_id', $context );
	}

	/**
	 * Get tax_name
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_tax_name( $context = 'edit' ) {
		return $this->get_prop( 'tax_name', $context );
	}

	/**
	 * Get tax
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_tax( $context = 'edit' ) {
		return $this->get_prop( 'tax', $context );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 *  Set bill_id
	 *
	 * @param $bill_id
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_bill_id( $bill_id ) {
		$this->set_prop( 'bill_id', absint( $bill_id ) );
	}

	/**
	 *  Set item_id
	 *
	 * @param $item_id
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_item_id( $item_id ) {
		$this->set_prop( 'item_id', absint( $item_id ) );
	}

	/**
	 *  Set name
	 *
	 * @param $name
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_name( $name ) {
		$this->set_prop( 'name', eaccounting_clean( $name ) );
	}

	/**
	 *  Set sku
	 *
	 * @param $sku
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_sku( $sku ) {
		$this->set_prop( 'sku', eaccounting_clean( $sku ) );
	}

	/**
	 *  Set quantity
	 *
	 * @param $quantity
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_quantity( $quantity ) {
		$this->set_prop( 'quantity', (float) $quantity );
	}

	/**
	 *  Set price
	 *
	 * @param $price
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_price( $price ) {
		$this->set_prop( 'price', eaccounting_sanitize_price( $price ) );
	}

	/**
	 *  Set total
	 *
	 * @param $total
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_total( $total ) {
		$this->set_prop( 'total', eaccounting_sanitize_price( $total ) );
	}

	/**
	 *  Set tax_id
	 *
	 * @param $tax_id
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_tax_id( $tax_id ) {
		$this->set_prop( 'tax_id', absint( $tax_id ) );
	}

	/**
	 *  Set tax_name
	 *
	 * @param $tax_name
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_tax_name( $tax_name ) {
		$this->set_prop( 'tax_name', eaccounting_clean( $tax_name ) );
	}

	/**
	 *  Set tax
	 *
	 * @param $tax
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_tax( $tax ) {
		$this->set_prop( 'tax', (double) $tax );
	}
}
