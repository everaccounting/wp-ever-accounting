<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class DocumentItem.
 *
 * @since   1.0.0
 * @package EverAccounting\Models
 */
class DocumentItem extends Model {
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
		'product_id'       => null,
		'name'             => '', // Item name.
		'description'      => '', // Item description.
		'unit'             => '', // Item unit type (e.g., kg, m, etc.).
		'price'            => 0.00, // Per-unit price.
		'quantity'         => 1, // Item quantity.
		'subtotal'         => 0.00, // Price * quantity.
		'subtotal_tax'     => 0.00, // Tax amount for the subtotal.
		'discount'         => 0.00, // Discount amount.
		'discount_tax'     => 0.00, // Discount tax amount.
		'shipping'         => 0.00, // Shipping amount.
		'shipping_tax'     => 0.00, // Shipping tax amount.
		'fee'              => 0.00, // Extra fee amount.
		'fee_tax'          => 0.00, // Extra fee tax amount.
		'tax'              => 0.00, // Tax amount.
		'total'            => 0.00, // Subtotal - discount + shipping_cost + fee_amount.
		'taxable'          => 'no', // Is the item taxable?
		'taxable_shipping' => 'no', // Is shipping taxable? 'yes' or 'no.
		'taxable_fee'      => 'no', // Is fee taxable? 'yes' or 'no.
		'currency_code'    => 'USD', // Currency code.
		'updated_at'       => null, // Last updated date.
		'created_at'       => null, // Date created.
	);

	/**
	 * document taxes will be stored here, sometimes before they persist in the DB.
	 *
	 * @since 1.1.0
	 *
	 * @var DocumentTax[]
	 */
	protected $taxes = null;

	/**
	 * Taxes to delete.
	 *
	 * @since 1.0.0
	 *
	 * @var DocumentTax[]
	 */
	protected $deletable = array();

	/**
	 * DocumentItem constructor.
	 *
	 * @param int|object|array $data Object ID, post object, or array of data.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $data = 0 ) {
		$tax_enabled                         = eac_tax_enabled();
		$tax_enabled                         = $tax_enabled ? 'yes' : 'no';
		$this->core_data['currency_code']    = eac_get_base_currency();
		$this->core_data['taxable']          = $tax_enabled;
		$this->core_data['taxable_shipping'] = $tax_enabled;
		$this->core_data['taxable_fee']      = $tax_enabled;
		parent::__construct( $data );
	}

	/*
	|--------------------------------------------------------------------------
	| Overridden Methods
	|--------------------------------------------------------------------------
	| Methods that are overridden from the parent class.
	|
	*/

	/**
	 * Returns all data for this object.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public function get_data( $context = 'edit' ) {
		$data = parent::get_data( $context );

		$data['taxes'] = array();
		foreach ( $this->get_taxes() as $tax ) {
			$data['taxes'][] = $tax->get_data( $context );
		}

		return $data;
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
	 * Get the item ID.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return int
	 */
	public function get_product_id( $context = 'edit' ) {
		return $this->get_prop( 'product_id', $context );
	}

	/**
	 * Set the item ID.
	 *
	 * @param int $value The item ID.
	 *
	 * @since  1.1.0
	 * @return void
	 */
	public function set_product_id( $value ) {
		$this->set_prop( 'product_id', absint( $value ) );
	}

	/**
	 * Get the document ID.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return int
	 */
	public function get_document_id( $context = 'edit' ) {
		return $this->get_prop( 'document_id', $context );
	}

	/**
	 * Set the document ID.
	 *
	 * @param int $value The document ID.
	 *
	 * @since  1.1.0
	 * @return void
	 */
	public function set_document_id( $value ) {
		$this->set_prop( 'document_id', absint( $value ) );
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
	 * Get the item unit.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	public function get_unit( $context = 'edit' ) {
		return $this->get_prop( 'unit', $context );
	}

	/**
	 * Set the item unit.
	 *
	 * @param string $value The item unit.
	 *
	 * @since  1.1.0
	 * @return void
	 */
	public function set_unit( $value ) {
		if ( ! array_key_exists( $value, eac_get_unit_types() ) ) {
			$value = 'unit';
		}
		$this->set_prop( 'unit', $value );
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
		$this->set_prop( 'subtotal', $subtotal );
	}

	/**
	 * Get subtotal tax.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return float
	 */
	public function get_subtotal_tax( $context = 'edit' ) {
		return $this->get_prop( 'subtotal_tax', $context );
	}

	/**
	 * Set subtotal tax.
	 *
	 * @param float $value Subtotal.
	 *
	 * @since  1.1.0
	 */
	public function set_subtotal_tax( $value ) {
		$this->set_prop( 'subtotal_tax', $value );
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
		$this->set_prop( 'discount', $discount );
	}

	/**
	 * Get discount_tax.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return float
	 */
	public function get_discount_tax( $context = 'edit' ) {
		return $this->get_prop( 'discount_tax', $context );
	}

	/**
	 * Set discount_tax.
	 *
	 * @param float $value Subtotal.
	 *
	 * @since  1.1.0
	 */
	public function set_discount_tax( $value ) {
		$this->set_prop( 'discount_tax', $value );
	}

	/**
	 * Get shipping.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return float
	 */
	public function get_shipping( $context = 'edit' ) {
		return $this->get_prop( 'shipping', $context );
	}

	/**
	 * Set shipping.
	 *
	 * @param float $shipping Subtotal.
	 *
	 * @since  1.1.0
	 */
	public function set_shipping( $shipping ) {
		$this->set_prop( 'shipping', $shipping );
	}

	/**
	 * Get shipping.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return float
	 */
	public function get_shipping_tax( $context = 'edit' ) {
		return $this->get_prop( 'shipping_tax', $context );
	}

	/**
	 * Set shipping_tax.
	 *
	 * @param float $shipping_tax Subtotal.
	 *
	 * @since  1.1.0
	 */
	public function set_shipping_tax( $shipping_tax ) {
		$this->set_prop( 'shipping_tax', $shipping_tax );
	}

	/**
	 * Get fee.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return float
	 */
	public function get_fee( $context = 'edit' ) {
		return $this->get_prop( 'fee', $context );
	}

	/**
	 * Set fee.
	 *
	 * @param float $fee Subtotal.
	 *
	 * @since  1.1.0
	 */
	public function set_fee( $fee ) {
		$this->set_prop( 'fee', $fee );
	}

	/**
	 * Get fee.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return float
	 */
	public function get_fee_tax( $context = 'edit' ) {
		return $this->get_prop( 'fee_tax', $context );
	}

	/**
	 * Set fee_tax.
	 *
	 * @param float $fee_tax Subtotal.
	 *
	 * @since  1.1.0
	 */
	public function set_fee_tax( $fee_tax ) {
		$this->set_prop( 'fee_tax', $fee_tax );
	}

	/**
	 * Get total tax
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since 1.1.0
	 * @return float
	 */
	public function get_tax( $context = 'edit' ) {
		return $this->get_prop( 'tax', $context );
	}

	/**
	 * Set total tax
	 *
	 * @param float $value Total tax.
	 *
	 * @since  1.1.0
	 */
	public function set_tax( $value ) {
		$this->set_prop( 'tax', $value );
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
		$this->set_prop( 'total', $total );
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
	 * @param string $taxable Taxable status.
	 *
	 * @since 1.1.0
	 */
	public function set_taxable( $taxable ) {
		if ( ! in_array( $taxable, array( 'yes', 'no' ), true ) ) {
			$taxable = 'no';
		}

		$this->set_prop( 'taxable', $taxable );
	}

	/**
	 * Get taxable shipping.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function get_taxable_shipping( $context = 'edit' ) {
		return $this->get_prop( 'taxable_shipping', $context );
	}

	/**
	 * Set taxable shipping.
	 *
	 * @param string $taxable Taxable status.
	 *
	 * @since 1.1.0
	 */
	public function set_taxable_shipping( $taxable ) {
		if ( ! in_array( $taxable, array( 'yes', 'no' ), true ) ) {
			$taxable = 'no';
		}

		$this->set_prop( 'taxable_shipping', $taxable );
	}

	/**
	 * Get taxable shipping.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function get_taxable_fee( $context = 'edit' ) {
		return $this->get_prop( 'taxable_fee', $context );
	}

	/**
	 * Set taxable shipping.
	 *
	 * @param string $taxable Taxable status.
	 *
	 * @since 1.1.0
	 */
	public function set_taxable_fee( $taxable ) {
		if ( ! in_array( $taxable, array( 'yes', 'no' ), true ) ) {
			$taxable = 'no';
		}

		$this->set_prop( 'taxable_fee', $taxable );
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
	public function get_currency_code( $context = 'edit' ) {
		return $this->get_prop( 'currency_code', $context );
	}

	/**
	 * Set currency code.
	 *
	 * @param string $currency Currency code.
	 *
	 * @since  1.1.0
	 */
	public function set_currency_code( $currency ) {
		$this->set_prop( 'currency_code', eac_clean( $currency ) );
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
	| Document Taxes related methods
	|--------------------------------------------------------------------------
	| These methods are related to line items taxes.
	*/

	/**
	 * Get taxes.
	 *
	 * @since 1.0.0
	 * @return DocumentTax[]
	 */
	public function get_taxes() {
		if ( is_null( $this->taxes ) ) {
			$this->taxes = array();
			if ( $this->exists() ) {
				$this->taxes = DocumentTax::query(
					array(
						'item_id'     => $this->get_id(),
						'document_id' => $this->get_document_id(),
						'orderby'     => 'id',
						'order'       => 'ASC',
						'limit'       => - 1,
					)
				);
			}
		}

		return $this->taxes;
	}

	/**
	 * Set taxes.
	 *
	 * @param array $taxes Taxes.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public function set_taxes( $taxes ) {
		$old_taxes       = array_merge( $this->get_taxes(), $this->deletable );
		$this->taxes     = array();
		$this->deletable = array_filter(
			$old_taxes,
			function ( $old_tax ) {
				return $old_tax->exists();
			}
		);

		if ( ! is_array( $taxes ) ) {
			$taxes = wp_parse_id_list( $taxes );
		}

		foreach ( $taxes as $tax ) {
			$this->add_tax( $tax );
		}

		// Go through deletable items and if they are in the new items list, remove them from the deletable list.
		foreach ( $this->deletable as $key => $deletable ) {
			foreach ( $this->taxes as $new_tax ) {
				if ( $deletable->get_id() === $new_tax->get_id() ) {
					unset( $this->deletable[ $key ] );
				}
			}
		}
	}

	/**
	 * Add tax.
	 *
	 * @param int|array|DocumentTax $data Tax data.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function add_tax( $data ) {
		// Load items before call this method. Otherwise, it will cause mis calculation.
		$default = array(
			'id'          => 0,
			'tax_id'      => 0,
			'item_id'     => $this->get_id(),
			'document_id' => $this->get_document_id(),
			'name'        => '',
			'rate'        => 0,
			'is_compound' => 'no',
		);

		if ( is_object( $data ) ) {
			$data = $data instanceof \stdClass ? get_object_vars( $data ) : $data->get_data();
		} elseif ( is_numeric( $data ) ) {
			$data = array( 'tax_id' => $data );
		}

		if ( empty( $data['id'] ) && empty( $data['tax_id'] ) ) {
			return;
		}

		if ( ! empty( $data['tax_id'] ) ) {
			$tax           = eac_get_tax( $data['tax_id'] );
			$tax_data      = $tax ? $tax->get_data() : array();
			$accepted_keys = array( 'name', 'rate', 'is_compound' );
			$tax_data      = wp_array_slice_assoc( $tax_data, $accepted_keys );
			$data          = wp_parse_args( $data, $tax_data );
		}

		$data     = wp_parse_args( $data, $default );
		$item_tax = new DocumentTax( $data['id'] );
		$item_tax->set_props( $data );
		// If tax id is not set, we will ignore the tax.
		if ( empty( $item_tax->get_tax_id() ) ) {
			return;
		}

		// Check if the item is set to be deleted and all the data matches. If so, remove it from the deletable list and add it to the items list.
		foreach ( $this->deletable as $key => $deletable_item ) {
			if ( $deletable_item->is_similar( $item_tax ) ) {
				unset( $this->deletable[ $key ] );
				$deletable_item->set_props( $data );
				$this->taxes[] = $deletable_item;

				return;
			}
		}

		// if the tax_id with same tax_id already exists, we will ignore the tax.
		foreach ( $this->taxes as $tax ) {
			if ( $tax->get_tax_id() === $item_tax->get_tax_id() ) {
				return;
			}
		}

		$this->taxes[] = $item_tax;
	}

	/**
	 * Get tax ids.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_tax_ids() {
		$tax_ids = array();
		foreach ( $this->get_taxes() as $tax ) {
			$tax_ids[] = $tax->get_tax_id();
		}

		return implode( ',', $tax_ids );
	}

	/**
	 * Set tax ids.
	 *
	 * @param string $tax_ids Tax ids.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function set_tax_ids( $tax_ids ) {
		$tax_ids = wp_parse_id_list( $tax_ids );
		$taxes   = array();
		foreach ( $tax_ids as $tax_id ) {
			$taxes[] = array( 'tax_id' => $tax_id );
		}
		$this->set_taxes( $taxes );
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
	 * @throws \Exception When the document item is invalid.
	 * @since 1.0.0
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 */
	public function save() {
		global $wpdb;

		// Required fields check.
		if ( empty( $this->get_document_id() ) ) {
			return new \WP_Error( 'required_missing', __( 'Document ID is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_product_id() ) ) {
			return new \WP_Error( 'required_missing', __( 'Product ID is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_name() ) ) {
			return new \WP_Error( 'required_missing', __( 'Product name is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_quantity() ) ) {
			return new \WP_Error( 'required_missing', __( 'Product quantity is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_currency_code() ) ) {
			return new \WP_Error( 'required_missing', __( 'Currency code is required.', 'wp-ever-accounting' ) );
		}

		// If the item is not taxable, then shipping and fee should not be taxable.
		if ( 'no' === $this->get_taxable() ) {
			$this->set_taxable_shipping( 'no' );
			$this->set_taxable_fee( 'no' );
		}

		// If date created is not set, set it to now.
		if ( empty( $this->get_created_at() ) ) {
			$this->set_created_at( current_time( 'mysql' ) );
		}

		// If It's update, set the updated date.
		if ( $this->exists() && empty( $this->get_updated_at() ) ) {
			$this->set_updated_at( current_time( 'mysql' ) );
		}

		try {
			$wpdb->query( 'START TRANSACTION' );
			$saved = parent::save();
			if ( is_wp_error( $saved ) ) {
				throw new \Exception( $saved->get_error_message() );
			}

			foreach ( $this->get_taxes() as $doc_tax ) {
				$doc_tax->set_document_id( $this->get_document_id() );
				$doc_tax->set_item_id( $this->get_id() );

				// check if tax is 0, set it to delete.
				// if ( empty( $doc_tax->get_subtotal() ) && empty( $doc_tax->get_discount() ) && empty( $doc_tax->get_shipping() ) && empty( $doc_tax->get_fee() ) && empty( $doc_tax->get_total() ) ) {
				// $this->deletable[] = $doc_tax;
				// continue;
				// }

				$saved = $doc_tax->save();
				if ( is_wp_error( $saved ) ) {
					throw new \Exception( $saved->get_error_message() );
				}
			}

			// Delete taxes which are not in the current list.
			foreach ( $this->deletable as $tax ) {
				if ( $tax->exists() && ! $tax->delete() ) {
					// translators: %s: error message.
					throw new \Exception( sprintf( __( 'Error while deleting unused tax. error: %s', 'wp-ever-accounting' ), $wpdb->last_error ) );
				}
			}

			$this->deletable = [];

			$wpdb->query( 'COMMIT' );

			return true;
		} catch ( \Exception $e ) {
			$wpdb->query( 'ROLLBACK' );

			return new \WP_Error( 'db_error', $e->getMessage() );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Calculations
	|--------------------------------------------------------------------------
	| This section contains methods for calculating totals.
	*/

	/**
	 * Calculate subtotal.
	 *
	 * @param bool $tax_inclusive Tax inclusive.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function calculate_subtotal( $tax_inclusive = false ) {
		$subtotal = $this->get_quantity() * $this->get_price();
		if ( $tax_inclusive ) {
			$tax_rates    = eac_calculate_taxes( $subtotal, $this->get_taxes(), $tax_inclusive );
			$subtotal_tax = array_sum( wp_list_pluck( $tax_rates, 'amount' ) );
			$subtotal    -= $subtotal_tax;
		}

		$this->set_subtotal( $subtotal );
	}

	/*
	|--------------------------------------------------------------------------
	| Helper methods.
	|--------------------------------------------------------------------------
	| Utility methods which don't directly relate to this object but may be
	| used by this object.
	*/

	/**
	 * Is similar.
	 *
	 * @param DocumentItem $item Item to compare.
	 *
	 * @since 1.1.0
	 * @return bool
	 */
	public function is_similar( $item ) {
		return $this->get_product_id() === $item->get_product_id() &&
			   $this->get_name() === $item->get_name() &&
			   $this->get_unit() === $item->get_unit() &&
			   $this->get_price() === $item->get_price() &&
			   $this->is_taxable() === $item->is_taxable() &&
			   $this->get_tax_ids() === $item->get_tax_ids() &&
			   $this->is_taxable_shipping() === $item->is_taxable_shipping() &&
			   $this->is_taxable_fee() === $item->is_taxable_fee();
	}

	/**
	 * Merge two items.
	 *
	 * @param DocumentItem $item Item to merge.
	 *
	 * @since 1.1.0
	 * @return static|false Merged item or false on failure.
	 */
	public function merge( $item ) {
		// If the item's properties are same, then merge and increase the quantity.
		if ( $this->is_similar( $item ) ) {
			$this->set_quantity( $this->get_quantity() + $item->get_quantity() );

			return $this;
		}

		return false;
	}

	/**
	 * Is the item taxable?
	 *
	 * @since 1.1.0
	 * @return bool
	 */
	public function is_taxable() {
		return 'yes' === $this->get_taxable();
	}

	/**
	 * Is the shipping taxable?
	 *
	 * @since 1.1.0
	 * @return bool
	 */
	public function is_taxable_shipping() {
		return 'yes' === $this->get_taxable_shipping();
	}

	/**
	 * Is the fee taxable?
	 *
	 * @since 1.1.0
	 * @return bool
	 */
	public function is_taxable_fee() {
		return 'yes' === $this->get_taxable_fee();
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

	/**
	 * Get formatted price.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function get_formatted_price() {
		return eac_format_money( $this->get_price(), $this->get_currency_code() );
	}

	/**
	 * Get formatted tax.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function get_formatted_tax() {
		return eac_format_money( $this->get_tax(), $this->get_currency_code() );
	}

	/**
	 * Get formatted subtotal.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function get_formatted_subtotal() {
		return eac_format_money( $this->get_subtotal(), $this->get_currency_code() );
	}
}
