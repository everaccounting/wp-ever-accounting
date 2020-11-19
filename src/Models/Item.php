<?php
/**
 * Handle the item object.
 *
 * @package     EverAccounting\Models
 * @class       Currency
 * @version     1.1.0
 */

namespace EverAccounting\Models;

use EverAccounting\Abstracts\ResourceModel;
use EverAccounting\Repositories\Items;

defined( 'ABSPATH' ) || exit;

/**
 * Class Item
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Models
 */
class Item extends ResourceModel {
	/**
	 * Get the Item if ID is passed, otherwise the invoice is new and empty.
	 *
	 * @param int|object|Item $data object to read.
	 *
	 * @since 1.1.0
	 *
	 */
	public function __construct( $data = 0 ) {
		parent::__construct( $data, Items::instance() );
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
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
	 * @param $name
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_name( $name ) {
		$this->set_prop( 'name', eaccounting_clean( $name ) );
	}

	/**
	 * @param $sku
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_sku( $sku ) {
		$this->set_prop( 'sku', eaccounting_clean( $sku ) );
	}

	/**
	 * @param $image_id
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_image_id( $image_id ) {
		$this->set_prop( 'image_id', absint( $image_id ) );
	}

	/**
	 * @param $description
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_description( $description ) {
		$this->set_prop( 'description', sanitize_textarea_field( $description ) );
	}

	/**
	 * @param $sale_price
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_sale_price( $sale_price ) {
		$this->set_prop( 'sale_price', eaccounting_sanitize_price( $sale_price ) );
	}

	/**
	 * @param $purchase_price
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_purchase_price( $purchase_price ) {
		$this->set_prop( 'purchase_price', eaccounting_sanitize_price( $purchase_price ) );
	}

	/**
	 * @param $quantity
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_quantity( $quantity ) {
		$this->set_prop( 'quantity', absint( $quantity ) );
	}

	/**
	 * @param $category_id
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_category_id( $category_id ) {
		$this->set_prop( 'category_id', absint( $category_id ) );
	}

	/**
	 * @param $tax_id
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_tax_id( $tax_id ) {
		$this->set_prop( 'tax_id', absint( $tax_id ) );
	}
}
