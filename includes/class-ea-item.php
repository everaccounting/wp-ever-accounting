<?php
/**
 * Handle the item object.
 *
 * @since       1.0.4
 *
 * @package     EverAccounting
 */

namespace EverAccounting;

use EverAccounting\Abstracts\Base_Object;

defined( 'ABSPATH' ) || exit();

/**
 * Class Item
 *
 * @since 1.0.4
 */
class Item extends Base_Object {
	/**
	 * This is the name of this object type.
	 *
	 * @since 1.0.4
	 * @var string
	 */
	public $object_type = 'item';

	/***
	 * Object table name.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $table = 'ea_items';

	/**
	 * Item Data array.
	 *
	 * @since 1.0.4
	 * @var array
	 */
	protected $data = array(
		'name'           => '',
		'sku'            => '',
		'image_id'       => null,
		'description'    => '',
		'sale_price'     => 0.0000,
		'purchase_price' => 0.0000,
		'quantity'       => 1,
		'category_id'    => null,
		'tax_id'         => null,
		'enabled'        => 1,
		'creator_id'     => null,
		'date_created'   => null,
	);

	/**
	 * Get the item if ID is passed, otherwise the item is new and empty.
	 * This class should NOT be instantiated, but the eaccounting_get_item function
	 * should be used. It is possible, but the aforementioned are preferred and are the only
	 * methods that will be maintained going forward.
	 *
	 * @since 1.0.4
	 *
	 * @param int|object|Category $data object to read.
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
	 * @since 1.0.4
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_name( $context = 'edit' ) {
		return $this->get_prop( 'name', $context );
	}

	/**
	 * @since 1.0.4
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_sku( $context = 'edit' ) {
		return $this->get_prop( 'sku', $context );
	}

	/**
	 * @since 1.0.4
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_image_id( $context = 'edit' ) {
		return $this->get_prop( 'image_id', $context );
	}

	/**
	 * @since 1.0.4
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_description( $context = 'edit' ) {
		return $this->get_prop( 'description', $context );
	}

	/**
	 * @since 1.0.4
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_sale_price( $context = 'edit' ) {
		return $this->get_prop( 'sale_price', $context );
	}

	/**
	 * @since 1.0.4
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_purchase_price( $context = 'edit' ) {
		return $this->get_prop( 'purchase_price', $context );
	}

	/**
	 * @since 1.0.4
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_quantity( $context = 'edit' ) {
		return $this->get_prop( 'quantity', $context );
	}

	/**
	 * @since 1.0.4
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_category_id( $context = 'edit' ) {
		return $this->get_prop( 'category_id', $context );
	}

	/**
	 * @since 1.0.4
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_tax_id( $context = 'edit' ) {
		return $this->get_prop( 'tax_id', $context );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/
	/**
	 * @since 1.0..4
	 *
	 * @param $name
	 */
	public function set_name( $name ) {
		$this->set_prop( 'name', eaccounting_clean( $name ) );
	}

	/**
	 * @since 1.0..4
	 *
	 * @param $sku
	 */
	public function set_sku( $sku ) {
		$this->set_prop( 'sku', eaccounting_clean( $sku ) );
	}

	/**
	 * @since 1.0..4
	 *
	 * @param $image_id
	 */
	public function set_image_id( $image_id ) {
		$this->set_prop( 'image_id', absint( $image_id ) );
	}

	/**
	 * @since 1.0..4
	 *
	 * @param $description
	 */
	public function set_description( $description ) {
		$this->set_prop( 'description', sanitize_textarea_field( $description ) );
	}

	/**
	 * @since 1.0..4
	 *
	 * @param $sale_price
	 */
	public function set_sale_price( $sale_price ) {
		$this->set_prop( 'sale_price', eaccounting_sanitize_price( $sale_price ) );
	}

	/**
	 * @since 1.0..4
	 *
	 * @param $purchase_price
	 */
	public function set_purchase_price( $purchase_price ) {
		$this->set_prop( 'purchase_price', eaccounting_sanitize_price( $purchase_price ) );
	}

	/**
	 * @since 1.0..4
	 *
	 * @param $quantity
	 */
	public function set_quantity( $quantity ) {
		$this->set_prop( 'quantity', absint( $quantity ) );
	}

	/**
	 * @since 1.0..4
	 *
	 * @param $category_id
	 */
	public function set_category_id( $category_id ) {
		$this->set_prop( 'category_id', absint( $category_id ) );
	}

	/**
	 * @since 1.0..4
	 *
	 * @param $tax_id
	 */
	public function set_tax_id( $tax_id ) {
		$this->set_prop( 'tax_id', absint( $tax_id ) );
	}
}
