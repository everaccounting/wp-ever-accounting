<?php
/**
 * Handle the item object.
 *
 * @since       1.1.0
 *
 * @package     EverAccounting
 */

namespace EverAccounting;

use EverAccounting\Abstracts\Base_Object;

defined( 'ABSPATH' ) || exit();

/**
 * Class Item
 *
 * @since 1.1.0
 */
class Item extends Base_Object {
	/**
	 * This is the name of this object type.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	public $object_type = 'item';

	/***
	 * Object table name.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	public $table = 'ea_items';

	/**
	 * Item Data array.
	 *
	 * @since 1.1.0
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
	 * Get item name
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
	 * Get item sku
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
	 * Get item image_id
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_image_id( $context = 'edit' ) {
		return $this->get_prop( 'image_id', $context );
	}

	/**
	 * Get item description
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_description( $context = 'edit' ) {
		return $this->get_prop( 'description', $context );
	}

	/**
	 * Get item sale_price
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_sale_price( $context = 'edit' ) {
		return $this->get_prop( 'sale_price', $context );
	}

	/**
	 * Get item purchase_price
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_purchase_price( $context = 'edit' ) {
		return $this->get_prop( 'purchase_price', $context );
	}

	/**
	 * Get item_quantity
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
	 * Get item category_id
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_category_id( $context = 'edit' ) {
		return $this->get_prop( 'category_id', $context );
	}

	/**
	 * Get item tax_id
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

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/
	/**
	 *  Set item name
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
	 * Set item sku
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
	 * Set item image_id
	 *
	 * @param $image_id
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_image_id( $image_id ) {
		$this->set_prop( 'image_id', absint( $image_id ) );
	}

	/**
	 * Set item description
	 *
	 * @param $description
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_description( $description ) {
		$this->set_prop( 'description', sanitize_textarea_field( $description ) );
	}

	/**
	 * Set item sale_price
	 *
	 * @param $sale_price
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_sale_price( $sale_price ) {
		$this->set_prop( 'sale_price', eaccounting_sanitize_price( $sale_price ) );
	}

	/**
	 * Set item purchase_price
	 *
	 * @param $purchase_price
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_purchase_price( $purchase_price ) {
		$this->set_prop( 'purchase_price', eaccounting_sanitize_price( $purchase_price ) );
	}

	/**
	 * Set item quantity
	 *
	 * @param $quantity
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_quantity( $quantity ) {
		$this->set_prop( 'quantity', absint( $quantity ) );
	}

	/**
	 * Set item category_id
	 *
	 * @param $category_id
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_category_id( $category_id ) {
		$this->set_prop( 'category_id', absint( $category_id ) );
	}

	/**
	 * Set item tax_id
	 *
	 * @param $tax_id
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_tax_id( $tax_id ) {
		$this->set_prop( 'tax_id', absint( $tax_id ) );
	}
}
