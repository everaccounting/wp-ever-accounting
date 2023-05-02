<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class Item.
 *
 * @since   1.0.0
 * @package EverAccounting\Models
 */
class Item extends Model {

	/**
	 * Table name.
	 *
	 * This is also used as table alias.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const TABLE_NAME = 'ea_items';

	/**
	 * Object type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const OBJECT_TYPE = 'item';

	/**
	 * Cache group.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const CACHE_GROUP = 'ea_items';

	/**
	 * Core data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $core_data = array(
		'name'         => '',
		'type'         => 'product',
		'price'        => 0.0000,
		'unit'         => 'pc',
		'sku'          => '',
		'description'  => '',
		'quantity'     => 1,
		'category_id'  => null,
		'sales_tax'    => null,
		'purchase_tax' => null,
		'status'       => 'active',
		'creator_id'   => null,
		'updated_at'   => null,
		'created_at'   => null,
	);

	/*
	|--------------------------------------------------------------------------
	| Getters and Setters
	|--------------------------------------------------------------------------
	|
	| Methods for getting and setting data.
	|
	*/
	/**
	 * Get item name.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.1.0
	 * @return mixed|null
	 */
	public function get_name( $context = 'edit' ) {
		return $this->get_prop( 'name', $context );
	}

	/**
	 * Set item name.
	 *
	 * @param string $name Item name.
	 *
	 * @since 1.1.0
	 */
	public function set_name( $name ) {
		$this->set_prop( 'name', eac_clean( $name ) );
	}


	/**
	 * Get item type.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.1.0
	 * @return mixed|null
	 */
	public function get_type( $context = 'edit' ) {
		return $this->get_prop( 'type', $context );
	}

	/**
	 * Set item type.
	 *
	 * @param string $type Item type.
	 *
	 * @since 1.1.0
	 */
	public function set_type( $type ) {
		$this->set_prop( 'type', eac_clean( $type ) );
	}

	/**
	 * Get item price.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.1.0
	 * @return mixed|null
	 */
	public function get_price( $context = 'edit' ) {
		return $this->get_prop( 'price', $context );
	}

	/**
	 * Set the price.
	 *
	 * @param float $price Item price.
	 *
	 * @since 1.1.0
	 */
	public function set_price( $price ) {
		$this->set_prop( 'price', eac_format_decimal( $price, 4 ) );
	}

	/**
	 * Get item unit.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.1.0
	 * @return mixed|null
	 */
	public function get_unit( $context = 'edit' ) {
		return $this->get_prop( 'unit', $context );
	}

	/**
	 * Set item unit.
	 *
	 * @param string $unit Item unit.
	 *
	 * @since 1.1.0
	 */
	public function set_unit( $unit ) {
		$this->set_prop( 'unit', eac_clean( $unit ) );
	}

	/**
	 * Get item SKU.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.1.0
	 * @return mixed|null
	 */
	public function get_sku( $context = 'edit' ) {
		return $this->get_prop( 'sku', $context );
	}

	/**
	 * Set the SKU.
	 *
	 * @param string $sku Item SKU.
	 *
	 * @since 1.1.0
	 */
	public function set_sku( $sku ) {
		$this->set_prop( 'sku', eac_clean( $sku ) );
	}

	/**
	 * Get item description.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.1.0
	 * @return mixed|null
	 */
	public function get_description( $context = 'edit' ) {
		return $this->get_prop( 'description', $context );
	}

	/**
	 * Set the description.
	 *
	 * @param string $description Item description.
	 *
	 * @since 1.1.0
	 */
	public function set_description( $description ) {
		$this->set_prop( 'description', sanitize_textarea_field( $description ) );
	}

	/**
	 * Get item sale price.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.1.0
	 * @deprecated 1.1.6 Use get_price() instead.
	 * @return mixed|null
	 */
	public function get_sale_price( $context = 'edit' ) {
		return $this->get_price( $context );
	}

	/**
	 * Set the sale price.
	 *
	 * @param float $sale_price Item sale price.
	 *
	 * @since 1.1.0
	 * @deprecated 1.1.6 Use set_price() instead.
	 */
	public function set_sale_price( $sale_price ) {
		$this->set_price( $sale_price );
	}


	/**
	 * Get the sale price.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.1.0
	 * @deprecated 1.1.6 Use get_price() instead.
	 * @return mixed|null
	 */
	public function get_purchase_price( $context = 'edit' ) {
		return $this->get_price( $context );
	}

	/**
	 * Set purchase price.
	 *
	 * @param float $purchase_price Purchase price.
	 *
	 * @since 1.1.0
	 * @deprecated 1.1.6 Use set_price() instead.
	 */
	public function set_purchase_price( $purchase_price ) {
		$this->set_price( $purchase_price );
	}

	/**
	 * Get the item's quantity.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.1.0
	 * @return mixed|null
	 */
	public function get_quantity( $context = 'edit' ) {
		return $this->get_prop( 'quantity', $context );
	}

	/**
	 * Set quantity.
	 *
	 * @param int $quantity Quantity to add to the current quantity.
	 *
	 * @since 1.1.0
	 */
	public function set_quantity( $quantity ) {
		$this->set_prop( 'quantity', absint( $quantity ) );
	}

	/**
	 * Get the item's category ID.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.1.0
	 * @return mixed|null
	 */
	public function get_category_id( $context = 'edit' ) {
		return $this->get_prop( 'category_id', $context );
	}

	/**
	 * Set category id.
	 *
	 * @param int $category_id Category ID.
	 *
	 * @since 1.1.0
	 */
	public function set_category_id( $category_id ) {
		$this->set_prop( 'category_id', absint( $category_id ) );
	}

	/**
	 * Get the sales tax.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.1.0
	 * @return mixed|null
	 */
	public function get_sales_tax( $context = 'edit' ) {
		return $this->get_prop( 'sales_tax', $context );
	}

	/**
	 * Set sales tax.
	 *
	 * @param float $tax Sales tax.
	 *
	 * @since 1.1.0
	 */
	public function set_sales_tax( $tax ) {
		$this->set_prop( 'sales_tax', eac_format_decimal( $tax, 4 ) );
	}

	/**
	 * Get the purchase tax.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.1.0
	 * @return mixed|null
	 */
	public function get_purchase_tax( $context = 'edit' ) {
		return $this->get_prop( 'purchase_tax', $context );
	}

	/**
	 * Set the purchase tax.
	 *
	 * @param string $tax Tax.
	 *
	 * @since 1.1.0
	 */
	public function set_purchase_tax( $tax ) {
		$this->set_prop( 'purchase_tax', eac_format_decimal( $tax, 4 ) );
	}

	/**
	 * Get the category status.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 * @return string
	 */
	public function get_status( $context = 'edit' ) {
		return $this->get_prop( 'status', $context );
	}

	/**
	 * Set the category status.
	 *
	 * @param string $value Category status.
	 *
	 * @since 1.0.2
	 */
	public function set_status( $value ) {
		if ( in_array( $value, array( 'active', 'inactive' ), true ) ) {
			$this->set_prop( 'status', $value );
		}
	}

	/**
	 * Set the category enabled status.
	 *
	 * @param string $value Category enabled status.
	 *
	 * @since 1.0.2
	 */
	public function set_enabled( $value ) {
		$status = $this->string_to_int( $value ) ? 'active' : 'inactive';
		$this->set_status( $status );
	}

	/**
	 * Is the category enabled?
	 *
	 * @since 1.0.2
	 *
	 * @return bool
	 */
	public function is_enabled() {
		return 'active' === $this->get_status();
	}

	/**
	 * Get creator ID.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 *
	 * @return mixed|null
	 */
	public function get_creator_id( $context = 'edit' ) {
		return $this->get_prop( 'creator_id', $context );
	}

	/**
	 * Set creator ID.
	 *
	 * @param int $value Creator ID.
	 *
	 * @since 1.0.2
	 */
	public function set_creator_id( $value ) {
		$this->set_prop( 'creator_id', absint( $value ) );
	}
	/**
	 * Get the date updated.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_updated_at( $context = 'edit' ) {
		return $this->get_prop( 'updated_at', $context );
	}

	/**
	 * Set the date updated.
	 *
	 * @param string $updated_at date updated.
	 */
	public function set_updated_at( $updated_at ) {
		$this->set_date_prop( 'updated_at', $updated_at );
	}

	/**
	 * Get the date created.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_created_at( $context = 'edit' ) {
		return $this->get_prop( 'created_at', $context );
	}

	/**
	 * Set the date created.
	 *
	 * @param string $created_at date created.
	 */
	public function set_created_at( $created_at ) {
		$this->set_date_prop( 'created_at', $created_at );
	}


	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	|
	| Methods which create, read, update and delete discounts from the database.
	|
	*/
	/**
	 * Saves an object in the database.
	 *
	 * @since 1.0.0
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 */
	public function save() {

		// Creator ID.
		if ( empty( $this->get_creator_id() ) && ! $this->exists() && is_user_logged_in() ) {
			$this->set_creator_id( get_current_user_id() );
		}

		// Creator ID.
		if ( empty( $this->get_creator_id() ) ) {
			return new \WP_Error( 'required-missing', __( 'Creator ID is required.', 'wp-ever-accounting' ) );
		}

		// Required fields check.
		if ( empty( $this->get_name() ) ) {
			return new \WP_Error( 'required-missing', __( 'Item name is required.', 'wp-ever-accounting' ) );
		}

		// Sales price.
		if ( empty( $this->get_sale_price() ) ) {
			return new \WP_Error( 'required-missing', __( 'Sales price is required.', 'wp-ever-accounting' ) );
		}

		// Purchase price.
		if ( empty( $this->get_purchase_price() ) ) {
			return new \WP_Error( 'required-missing', __( 'Purchase price is required.', 'wp-ever-accounting' ) );
		}

		// If date created is not set, set it to now.
		if ( empty( $this->get_created_at() ) ) {
			$this->set_created_at( current_time( 'mysql' ) );
		}

		// Creator ID.
		if ( empty( $this->get_creator_id() ) && ! $this->exists() && is_user_logged_in() ) {
			$this->set_creator_id( get_current_user_id() );
		}

		// If It's update, set the updated date.
		if ( $this->exists() ) {
			$this->set_updated_at( current_time( 'mysql' ) );
		}

		return parent::save();
	}

	/*
	|--------------------------------------------------------------------------
	| Helper methods.
	|--------------------------------------------------------------------------
	| Utility methods which don't directly relate to this object but may be
	| used by this object.
	*/

	/**
	 * Get formatted name.
	 *
	 * @return string
	 * @since 1.1.6
	 */
	public function get_formatted_name() {
		return sprintf( '%s (#%s)', $this->get_name(), $this->get_id() );
	}
}
