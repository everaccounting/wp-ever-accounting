<?php

namespace EverAccounting\Models;

use EverAccounting\Traits\Attachment;

defined( 'ABSPATH' ) || exit;

/**
 * Class Item.
 *
 * @since   1.0.0
 * @package EverAccounting\Models
 */
class Item extends Model {
	use Attachment;

	/**
	 * Table name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $table_name = 'ea_items';

	/**
	 * Object type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $object_type = 'item';

	/**
	 * Core data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $core_data = array(
		'name'           => '',
		'sku'            => '',
		'thumbnail_id'   => null,
		'description'    => '',
		'sale_price'     => 0.0000,
		'purchase_price' => 0.0000,
		'quantity'       => 1,
		'category_id'    => null,
		'sales_tax'      => null,
		'purchase_tax'   => null,
		'status'         => 'active',
		'creator_id'     => null,
		'date_created'   => null,
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
		$this->set_prop( 'name', eaccounting_clean( $name ) );
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
		$this->set_prop( 'sku', eaccounting_clean( $sku ) );
	}

	/**
	 * Get thumbnail ID.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.1.0
	 * @return mixed|null
	 */
	public function get_thumbnail_id( $context = 'edit' ) {
		return $this->get_prop( 'thumbnail_id', $context );
	}

	/**
	 * Set the item thumbnail.
	 *
	 * @param int $thumbnail_id Attachment ID.
	 *
	 * @since 1.1.0
	 */
	public function set_thumbnail_id( $thumbnail_id ) {
		$this->set_prop( 'thumbnail_id', absint( $thumbnail_id ) );
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
	 * @return mixed|null
	 */
	public function get_sale_price( $context = 'edit' ) {
		return $this->get_prop( 'sale_price', $context );
	}

	/**
	 * Set the sale price.
	 *
	 * @param float $sale_price Item sale price.
	 *
	 * @since 1.1.0
	 */
	public function set_sale_price( $sale_price ) {
		$this->set_prop( 'sale_price', eaccounting_format_decimal( $sale_price, 4 ) );
	}


	/**
	 * Get the sale price.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.1.0
	 * @return mixed|null
	 */
	public function get_purchase_price( $context = 'edit' ) {
		$price = $this->get_prop( 'purchase_price', $context );
		if ( empty( $price ) ) {
			$price = $this->get_sale_price();
		}

		return $price;
	}

	/**
	 * Set purchase price.
	 *
	 * @param float $purchase_price Purchase price.
	 *
	 * @since 1.1.0
	 */
	public function set_purchase_price( $purchase_price ) {
		$this->set_prop( 'purchase_price', eaccounting_format_decimal( $purchase_price, 4 ) );
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
		$this->set_prop( 'sales_tax', eaccounting_format_decimal( $tax, 4 ) );
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
		$this->set_prop( 'purchase_tax', eaccounting_format_decimal( $tax, 4 ) );
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
		$this->set_prop( 'enabled', $this->string_to_int( $value ) );
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
	 * Get the category date created.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 *
	 * @return mixed|null
	 */
	public function get_date_created( $context = 'edit' ) {
		return $this->get_prop( 'date_created', $context );
	}

	/**
	 * Set the category date created.
	 *
	 * @param string $value Category date created.
	 *
	 * @since 1.0.2
	 */
	public function set_date_created( $value ) {
		$this->set_prop( 'date_created', eaccounting_clean( $value ) );
	}

	/*
	|--------------------------------------------------------------------------
	| Helpers
	|--------------------------------------------------------------------------
	|
	| Helper methods.
	|
	*/
	/**
	 * Sanitizes the data.
	 *
	 * @since 1.0.0
	 * @return \WP_Error|true
	 */
	protected function sanitize_data() {
		// If date created is not set, set it to now.
		if ( empty( $this->get_date_created() ) && ! $this->exists() ) {
			$this->set_date_created( current_time( 'mysql' ) );
		}

		// Creator ID.
		if ( empty( $this->get_creator_id() ) && ! $this->exists() && is_user_logged_in() ) {
			$this->set_creator_id( get_current_user_id() );
		}

		// Creator ID.
		if ( empty( $this->get_creator_id() ) ) {
			return new \WP_Error( 'required-missing', __( 'Creator ID is required.', 'wp-ever-accounting' ) );
		}

		// Date created.
		if ( empty( $this->get_date_created() ) ) {
			return new \WP_Error( 'required-missing', __( 'Date created is required.', 'wp-ever-accounting' ) );
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

		return parent::sanitize_data();
	}
}
