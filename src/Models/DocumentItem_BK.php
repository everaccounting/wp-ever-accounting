<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class DocumentItem.
 *
 * @since   1.0.0
 * @package EverAccounting\Models
 */
class DocumentItem_BK extends Model {
	/**
	 * Table name.
	 *
	 * This is also used as table alias.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const TABLE_NAME = 'ea_document_items';

	/**
	 * Object type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const OBJECT_TYPE = 'document_item';

	/**
	 * Cache group.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const CACHE_GROUP = 'ea_document_items';

	/**
	 * Core data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $core_data = array(
		'document_id'      => null,
		'item_id'          => null,
		'name'             => '', // Item name.
		'description'      => '', // Item description.
		'price'            => 0.00, // Per-unit price.
		'unit_type'        => '', // Item unit type (e.g., kg, m, etc.).
		'quantity'         => 1, // Item quantity.
		'subtotal'         => 0.00, // Price * quantity.
		'discount'         => 0.00, // Discount amount.
		'shipping_cost'    => 0.00, // Shipping amount.
		'fee_amount'       => 0.00, // Extra fee amount.
		'tax'              => 0.00, // Tax amount.
		'total'            => 0.00, // Subtotal - discount + shipping_cost + fee_amount.
		'is_taxable'       => 'no', // Is the item taxable?
		'shipping_taxable' => 'no', // Is shipping taxable? 'yes' or 'no.
		'fee_taxable'      => 'no', // Is fee taxable? 'yes' or 'no.
		'currency'         => 'USD', // Currency code.
		'updated_at'       => null, // Last updated date.
		'created_at'       => null, // Date created.
	);

	/**
	 * Extra data for this object.
	 *
	 * @since 1.0.0
	 * @var array Extra data.
	 */
	protected $extra_data = array();

	/**
	 * DocumentItem constructor.
	 *
	 * @param int|object|array $data Object ID, post object, or array of data.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $data = 0 ) {
		$tax_enabled                         = eac_tax_enabled();
		$this->core_data['currency']         = eac_get_base_currency();
		$this->core_data['is_taxable']       = $tax_enabled;
		$this->core_data['shipping_taxable'] = $tax_enabled;
		$this->core_data['fee_taxable']      = $tax_enabled;
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
	 * Get the document ID.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	public function get_document_id( $context = 'edit' ) {
		return $this->get_prop( 'document_id', $context );
	}

	/**
	 * Set the document ID.
	 *
	 * @param string $value The document ID.
	 *
	 * @since  1.1.0
	 * @return void
	 */
	public function set_document_id( $value ) {
		$this->set_prop( 'document_id', $value );
	}

	/**
	 * Get the item ID.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	public function get_item_id( $context = 'edit' ) {
		return $this->get_prop( 'item_id', $context );
	}

	/**
	 * Set the item ID.
	 *
	 * @param string $value The item ID.
	 *
	 * @since  1.1.0
	 * @return void
	 */
	public function set_item_id( $value ) {
		$this->set_prop( 'item_id', $value );
	}

	/**
	 * Get the item name.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	public function get_name( $context = 'edit' ) {
		return $this->get_prop( 'name', $context );
	}

	/**
	 * Set the item name.
	 *
	 * @param string $value The item name.
	 *
	 * @since  1.1.0
	 * @return void
	 */
	public function set_name( $value ) {
		$this->set_prop( 'name', $value );
	}


	/**
	 * Get the description.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	public function get_description( $context = 'edit' ) {
		return $this->get_prop( 'description', $context );
	}

	/**
	 * Set the description.
	 *
	 * @param string $value The description.
	 *
	 * @since  1.1.0
	 * @return void
	 */
	public function set_description( $value ) {
		$this->set_prop( 'description', $value );
	}

	/**
	 * Get the unit price.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return float
	 */
	public function get_price( $context = 'edit' ) {
		return $this->get_prop( 'price', $context );
	}

	/**
	 * Set the unit price.
	 *
	 * @param float $value The unit price.
	 *
	 * @since  1.1.0
	 * @return void
	 */
	public function set_price( $value ) {
		$this->set_prop( 'price', $value );
	}


	/**
	 * Get the item unit.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	public function get_unit_type( $context = 'edit' ) {
		return $this->get_prop( 'unit_type', $context );
	}

	/**
	 * Set the item unit.
	 *
	 * @param string $value The item unit.
	 *
	 * @since  1.1.0
	 * @return void
	 */
	public function set_unit_type( $value ) {
		$this->set_prop( 'unit_type', $value );
	}

	/**
	 * Get the quantity.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return float
	 */
	public function get_quantity( $context = 'edit' ) {
		return $this->get_prop( 'quantity', $context );
	}

	/**
	 * Set the quantity.
	 *
	 * @param float $value The quantity.
	 *
	 * @since  1.1.0
	 * @return void
	 */
	public function set_quantity( $value ) {
		$this->set_prop( 'quantity', $value );
	}

	/**
	 * Get subtotal.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return float
	 */
	public function get_subtotal( $context = 'edit' ) {
		return $this->get_prop( 'subtotal', $context );
	}

	/**
	 * Set subtotal.
	 *
	 * @param float $subtotal Subtotal.
	 *
	 * @since  1.1.0
	 */
	public function set_subtotal( $subtotal ) {
		$this->set_prop( 'subtotal', eac_sanitize_number( $subtotal ) );
	}

	/**
	 * Get discount.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return float
	 */
	public function get_discount( $context = 'edit' ) {
		return $this->get_prop( 'discount', $context );
	}

	/**
	 * Set discount.
	 *
	 * @param float $discount Subtotal.
	 *
	 * @since  1.1.0
	 */
	public function set_discount( $discount ) {
		$this->set_prop( 'discount', eac_sanitize_number( $discount, 4 ) );
	}

	/**
	 * Get shipping.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return float
	 */
	public function get_shipping_cost( $context = 'edit' ) {
		return $this->get_prop( 'shipping_cost', $context );
	}

	/**
	 * Set shipping.
	 *
	 * @param float $shipping Subtotal.
	 *
	 * @since  1.1.0
	 */
	public function set_shipping_cost( $shipping ) {
		$this->set_prop( 'shipping_cost', eac_sanitize_number( $shipping, 4 ) );
	}

	/**
	 * Get fee.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return float
	 */
	public function get_fee_amount( $context = 'edit' ) {
		return $this->get_prop( 'fee_amount', $context );
	}

	/**
	 * Set fee.
	 *
	 * @param float $fee Subtotal.
	 *
	 * @since  1.1.0
	 */
	public function set_fee_amount( $fee ) {
		$this->set_prop( 'fee_amount', eac_sanitize_number( $fee, 4 ) );
	}

	/**
	 * Get total.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since 1.1.0
	 * @return float
	 */
	public function get_total( $context = 'edit' ) {
		return $this->get_prop( 'total', $context );
	}

	/**
	 * Set total.
	 *
	 * @param float $total Total.
	 *
	 * @since  1.1.0
	 */
	public function set_total( $total ) {
		$this->set_prop( 'total', eac_clean( $total ) );
	}

	/**
	 * Get total tax
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since 1.1.0
	 * @return float
	 */
	public function get_total_tax( $context = 'edit' ) {
		return $this->get_prop( 'total_tax', $context );
	}

	/**
	 * Set total tax
	 *
	 * @param float $total_tax Total tax.
	 *
	 * @since  1.1.0
	 */
	public function set_total_tax( $total_tax ) {
		$this->set_prop( 'total_tax', eac_sanitize_number( $total_tax ) );
	}

	/**
	 * Get taxable status.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function get_is_taxable( $context = 'edit' ) {
		return $this->get_prop( 'is_taxable', $context );
	}

	/**
	 * Set taxable status.
	 *
	 * @param string $taxable Taxable status.
	 *
	 * @since 1.1.0
	 */
	public function set_is_taxable( $taxable ) {
		if ( ! in_array( $taxable, array( 'yes', 'no' ), true ) ) {
			$taxable = 'no';
		}

		$this->set_prop( 'is_taxable', $taxable );
	}

	/**
	 * Get currency code.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_currency( $context = 'edit' ) {
		return $this->get_prop( 'currency', $context );
	}

	/**
	 * Set currency code.
	 *
	 * @param string $currency Currency code.
	 *
	 * @since  1.1.0
	 */
	public function set_currency( $currency ) {
		$this->set_prop( 'currency', eac_clean( $currency ) );
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
		if ( empty( $this->get_document_id() ) ) {
			return new \WP_Error( 'required_missing', __( 'Document ID is required.', 'easy-appointments' ) );
		}

		if ( empty( $this->get_item_id() ) ) {
			return new \WP_Error( 'required_missing', __( 'Item ID is required.', 'easy-appointments' ) );
		}

		if ( empty( $this->get_name() ) ) {
			return new \WP_Error( 'required_missing', __( 'Item name is required.', 'easy-appointments' ) );
		}

		if ( empty( $this->get_quantity() ) ) {
			return new \WP_Error( 'required_missing', __( 'Item quantity is required.', 'easy-appointments' ) );
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
	 * Is the item taxable?
	 *
	 * @since 1.1.0
	 * @return bool
	 */
	public function is_taxable() {
		return 'yes' === $this->get_is_taxable();
	}

	/**
	 * Get taxes.
	 *
	 * @since 1.1.0
	 * @return Tax[]
	 */
	public function get_tax_objects() {
		return Tax::query(
			array(
				'include' => $this->get_tax_ids(),
			)
		);
	}

	/**
	 * Get discounted price.
	 *
	 * @since 1.1.0
	 * @return float
	 */
	public function get_discounted_price() {
		return (float) $this->get_subtotal() - (float) $this->get_discount();
	}
}
