<?php
/**
 * Handle the item object.
 *
 * @package     EverAccounting\Models
 * @class       Item
 * @version     1.1.0
 */

namespace EverAccounting\Models;

use EverAccounting\Abstracts\ResourceModel;
use EverAccounting\Core\Repositories;
use EverAccounting\Traits\AttachmentTrait;
use EverAccounting\Traits\CurrencyTrait;

defined( 'ABSPATH' ) || exit;

/**
 * Class Item
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Models
 */
class Item extends ResourceModel {
	use AttachmentTrait;

	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'item';

	/**
	 * @since 1.1.0
	 *
	 * @var string
	 */
	public $cache_group = 'ea_items';

	/**
	 * Item Data array.
	 *
	 * @since 1.0.4
	 *
	 * @var array
	 */
	protected $data = array(
		'name'              => '',
		'sku'               => '',
		'thumbnail_id'      => null,
		'description'       => '',
		'sale_price'        => 0.0000,
		'purchase_price'    => 0.0000,
		'quantity'          => 1,
		'category_id'       => null,
		'sales_tax'    => null,
		'purchase_tax' => null,
		'enabled'           => 1,
		'creator_id'        => null,
		'date_created'      => null,
	);

	/**
	 * Get the item if ID is passed, otherwise the item is new and empty.
	 *
	 * @param int|string|object|Item $item Item object to read.
	 */
	public function __construct( $item = 0 ) {
		parent::__construct( $item );

		if ( $item instanceof self ) {
			$this->set_id( $item->get_id() );
		} elseif ( is_numeric( $item ) ) {
			$this->set_id( $item );
		} elseif ( ! empty( $item->id ) ) {
			$this->set_id( $item->id );
		} elseif ( is_array( $item ) ) {
			$this->set_props( $item );
		} else {
			$this->set_object_read( true );
		}

		//Load repository
		$this->repository = Repositories::load( 'items' );

		if ( $this->get_id() > 0 ) {
			$this->repository->read( $this );
		}

		$this->required_props = array(
			'name'       => __( 'Item name', 'wp-ever-accounting' ),
			'quantity'   => __( 'Item Quantity', 'wp-ever-accounting' ),
			'sale_price' => __( 'Item Purchase Price', 'wp-ever-accounting' ),
		);
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	|
	| Methods which create, read, update and delete items from the database.
	|
	*/

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	|
	| Functions for getting item data. Getter methods wont change anything unless
	| just returning from the props.
	|
	*/

	/**
	 * @since 1.1.0
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_name( $context = 'edit' ) {
		return $this->get_prop( 'name', $context );
	}

	/**
	 * @since 1.1.0
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_sku( $context = 'edit' ) {
		return $this->get_prop( 'sku', $context );
	}

	/**
	 * @since 1.1.0
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_thumbnail_id( $context = 'edit' ) {
		return $this->get_prop( 'thumbnail_id', $context );
	}

	/**
	 * @since 1.1.0
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_description( $context = 'edit' ) {
		return $this->get_prop( 'description', $context );
	}

	/**
	 * @since 1.1.0
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_sale_price( $context = 'edit' ) {
		return $this->get_prop( 'sale_price', $context );
	}

	/**
	 * @since 1.1.0
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_purchase_price( $context = 'edit' ) {
		return $this->get_prop( 'purchase_price', $context );
	}

	/**
	 * @since 1.1.0
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_quantity( $context = 'edit' ) {
		return $this->get_prop( 'quantity', $context );
	}

	/**
	 * @since 1.1.0
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_category_id( $context = 'edit' ) {
		return $this->get_prop( 'category_id', $context );
	}

	/**
	 * @since 1.1.0
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_sales_tax( $context = 'edit' ) {
		return $this->get_prop( 'sales_tax', $context );
	}

	/**
	 * @since 1.1.0
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_purchase_tax( $context = 'edit' ) {
		return $this->get_prop( 'purchase_tax', $context );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	|
	| Functions for setting item data. These should not update anything in the
	| database itself and should only change what is stored in the class
	| object.
	*/

	/**
	 * @since 1.1.0
	 *
	 * @param $name
	 *
	 */
	public function set_name( $name ) {
		$this->set_prop( 'name', eaccounting_clean( $name ) );
	}

	/**
	 * @since 1.1.0
	 *
	 * @param $sku
	 *
	 */
	public function set_sku( $sku ) {
		$this->set_prop( 'sku', eaccounting_clean( $sku ) );
	}

	/**
	 * @since 1.1.0
	 *
	 * @param $thumbnail_id
	 *
	 */
	public function set_thumbnail_id( $thumbnail_id ) {
		$this->set_prop( 'thumbnail_id', absint( $thumbnail_id ) );
	}

	/**
	 * @since 1.1.0
	 *
	 * @param $description
	 *
	 */
	public function set_description( $description ) {
		$this->set_prop( 'description', sanitize_textarea_field( $description ) );
	}

	/**
	 * @since 1.1.0
	 *
	 * @param $sale_price
	 *
	 */
	public function set_sale_price( $sale_price ) {
		$this->set_prop( 'sale_price', (float) eaccounting_sanitize_number( $sale_price, true ) );
	}

	/**
	 * @since 1.1.0
	 *
	 * @param $purchase_price
	 *
	 */
	public function set_purchase_price( $purchase_price ) {
		$this->set_prop( 'purchase_price', (float) eaccounting_sanitize_number( $purchase_price, true ) );
	}

	/**
	 * @since 1.1.0
	 *
	 * @param $quantity
	 *
	 */
	public function set_quantity( $quantity ) {
		$this->set_prop( 'quantity', absint( $quantity ) );
	}

	/**
	 * @since 1.1.0
	 *
	 * @param $category_id
	 *
	 */
	public function set_category_id( $category_id ) {
		$this->set_prop( 'category_id', absint( $category_id ) );
	}

	/**
	 * @since 1.1.0
	 *
	 * @param $tax
	 *
	 */
	public function set_sales_tax( $tax ) {
		$this->set_prop( 'sales_tax', floatval( $tax ) );
	}

	/**
	 * @since 1.1.0
	 *
	 * @param $tax_ids
	 *
	 */
	public function set_purchase_tax( $tax ) {
		$this->set_prop( 'purchase_tax', floatval( $tax ) );
	}

	/**
	 * Clears the subscription's cache.
	 */
	public function clear_cache() {
		wp_cache_delete( $this->get_id(), $this->cache_group );
	}

	/*
	|--------------------------------------------------------------------------
	| Additional methods
	|--------------------------------------------------------------------------
	|
	| Does extra thing as helper functions.
	|
	*/

}
