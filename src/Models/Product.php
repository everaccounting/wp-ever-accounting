<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class Product.
 *
 * @since   1.0.0
 * @package EverAccounting\Models
 */
class Product extends Model {

	/**
	 * Table name.
	 *
	 * This is also used as table alias.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const TABLE_NAME = 'ea_products';

	/**
	 * Object type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const OBJECT_TYPE = 'product';

	/**
	 * Cache group.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const CACHE_GROUP = 'ea_products';

	/**
	 * Core data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $core_data = array(
		'name'        => '',
		'price'       => 0.0000,
		'unit'        => 'unit',
		'description' => '',
		'category_id' => null,
		'taxable'     => 'no',
		'tax_ids'     => '',
		'status'      => 'active',
		'updated_at'  => null,
		'created_at'  => null,
	);

	/**
	 * Product constructor.
	 *
	 * @param int|object|array $data Object ID, post object, or array of data.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $data = 0 ) {
		$this->core_data['taxable'] = eac_tax_enabled();
		parent::__construct( $data );
	}

	/*
	|--------------------------------------------------------------------------
	| Getters and Setters
	|--------------------------------------------------------------------------
	|
	| Methods for getting and setting data.
	|
	*/
	/**
	 * Get product name.
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
	 * Set product name.
	 *
	 * @param string $name Item name.
	 *
	 * @since 1.1.0
	 */
	public function set_name( $name ) {
		$this->set_prop( 'name', eac_clean( $name ) );
	}

	/**
	 * Get product price.
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
	 * Set product price.
	 *
	 * @param float $price Item price.
	 *
	 * @since 1.1.0
	 */
	public function set_price( $price ) {
		$this->set_prop( 'price', eac_format_decimal( $price, 4 ) );
	}

	/**
	 * Get product unit.
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
	 * Set product unit.
	 *
	 * @param string $unit Item unit.
	 *
	 * @since 1.1.0
	 */
	public function set_unit( $unit ) {
		$this->set_prop( 'unit', eac_clean( $unit ) );
	}

	/**
	 * Get product description.
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
	 * Set product description.
	 *
	 * @param string $description Item description.
	 *
	 * @since 1.1.0
	 */
	public function set_description( $description ) {
		$this->set_prop( 'description', sanitize_textarea_field( $description ) );
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
	 * Get taxable status.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function get_taxable( $context = 'edit' ) {
		return $this->get_prop( 'taxable', $context );
	}

	/**
	 * Set taxable status.
	 *
	 * @param string|bool $taxable Taxable status.
	 *
	 * @since 1.1.0
	 */
	public function set_taxable( $taxable ) {
		$taxable = $this->bool_to_string( $taxable );
		$this->set_prop( 'taxable', $this->bool_to_string( $taxable ) );
	}

	/**
	 * Get the item's tax IDs.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.1.0
	 * @return mixed|null
	 */
	public function get_tax_ids( $context = 'edit' ) {
		$tax_ids = $this->get_prop( 'tax_ids', $context );
		if ( ! empty( $tax_ids ) ) {
			$tax_ids = wp_parse_id_list( $tax_ids );
			$tax_ids = array_map( 'absint', $tax_ids );
			$tax_ids = array_unique( array_filter( $tax_ids ) );
			$tax_ids = implode( ',', $tax_ids );
		}

		return $tax_ids;
	}

	/**
	 * Set tax ids.
	 *
	 * @param array|string $tax_ids Tax IDs.
	 *
	 * @since 1.1.0
	 */
	public function set_tax_ids( $tax_ids ) {
		if ( is_array( $tax_ids ) ) {
			$tax_ids = implode( ',', $tax_ids );
		}
		$tax_ids = wp_parse_id_list( $tax_ids );
		$tax_ids = array_map( 'absint', $tax_ids );
		$tax_ids = array_unique( array_filter( $tax_ids ) );
		$tax_ids = implode( ',', $tax_ids );

		$this->set_prop( 'tax_ids', $tax_ids );
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
		// Required fields check.
		if ( empty( $this->get_name() ) ) {
			return new \WP_Error( 'required_missing', __( 'Product name is required.', 'wp-ever-accounting' ) );
		}

		// If date created is not set, set it to now.
		if ( empty( $this->get_created_at() ) ) {
			$this->set_created_at( current_time( 'mysql' ) );
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
	 * Is taxable?
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_taxable() {
		return 'yes' === $this->get_taxable();
	}

	/**
	 * Is the enabled?
	 *
	 * @since 1.0.2
	 *
	 * @return bool
	 */
	public function is_enabled() {
		return 'active' === $this->get_status();
	}

	/**
	 * Get formatted name.
	 *
	 * @since 1.1.6
	 * @return string
	 */
	public function get_formatted_name() {
		return sprintf( '%s (#%s)', $this->get_name(), $this->get_id() );
	}
}
