<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class DocumentTax.
 *
 * @since   1.0.0
 * @package EverAccounting\Models
 */
class DocumentTax extends Model {
	/**
	 * Table name.
	 *
	 * This is also used as table alias.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const TABLE_NAME = 'ea_document_taxes';

	/**
	 * Object type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const OBJECT_TYPE = 'document_tax';

	/**
	 * Cache group.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const CACHE_GROUP = 'ea_document_taxes';

	/**
	 * Core data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $core_data = array(
		'item_id'       => null,
		'tax_id'        => null,
		'document_id'   => null,
		'name'          => '',
		'rate'          => 0.00,
		'is_compound'   => 'no',
		'amount'        => 0.00,
		'currency_code' => '',
		'updated_at'    => null,
		'created_at'    => null,
	);

	/**
	 * DocumentTax constructor.
	 *
	 * @param int|object|array $data Object ID, post object, or array of data.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $data = 0 ) {
		$this->core_data['currency_code'] = eac_get_base_currency();
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
	 * Get the item ID.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return int
	 */
	public function get_item_id( $context = 'edit' ) {
		return $this->get_prop( 'item_id', $context );
	}

	/**
	 * Set the item ID.
	 *
	 * @param int $value The item ID.
	 *
	 * @since  1.1.0
	 * @return void
	 */
	public function set_item_id( $value ) {
		$this->set_prop( 'item_id', absint( $value ) );
	}

	/**
	 * Get the tax ID.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return int
	 */
	public function get_tax_id( $context = 'edit' ) {
		return $this->get_prop( 'tax_id', $context );
	}

	/**
	 * Set the tax ID.
	 *
	 * @param int $value The tax ID.
	 *
	 * @since  1.1.0
	 * @return void
	 */
	public function set_tax_id( $value ) {
		$this->set_prop( 'tax_id', absint( $value ) );
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
		$this->set_prop( 'name', sanitize_text_field( $value ) );
	}

	/**
	 * Get tax rate
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return float
	 */
	public function get_rate( $context = 'edit' ) {
		return $this->get_prop( 'rate', $context );
	}

	/**
	 * Set tax rate
	 *
	 * @param string $rate Tax rate.
	 *
	 * @since  1.1.0
	 *
	 * @return void
	 */
	public function set_rate( $rate ) {
		$this->set_prop( 'rate', eac_sanitize_number( $rate, 4 ) );
	}

	/**
	 * Get tax type
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_is_compound( $context = 'edit' ) {
		return $this->get_prop( 'is_compound', $context );
	}

	/**
	 * Set tax type
	 *
	 * @param string $type Tax type.
	 *
	 * @since  1.1.0
	 *
	 * @return void
	 */
	public function set_is_compound( $type ) {
		$types = array( 'yes', 'no' );
		if ( ! in_array( $type, $types, true ) ) {
			$type = 'no';
		}

		$this->set_prop( 'is_compound', $type );
	}

	/**
	 * Get amount.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return float
	 */
	public function get_amount( $context = 'edit' ) {
		return $this->get_prop( 'amount', $context );
	}

	/**
	 * Set amount tax.
	 *
	 * @param float $value Subtotal.
	 *
	 * @since  1.1.0
	 */
	public function set_amount( $value ) {
		$this->set_prop( 'amount', eac_sanitize_number( $value, 4 ) );
	}

	/**
	 * Get currency.
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
			return new \WP_Error( 'required_missing', __( 'Document ID is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_item_id() ) ) {
			return new \WP_Error( 'required_missing', __( 'Item ID is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_tax_id() ) ) {
			return new \WP_Error( 'required_missing', __( 'Tax ID is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_name() ) ) {
			return new \WP_Error( 'required_missing', __( 'Tax name is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_rate() ) ) {
			return new \WP_Error( 'required_missing', __( 'Tax rate is required.', 'wp-ever-accounting' ) );
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
	| Conditionals methods
	|--------------------------------------------------------------------------
	| Methods that check an object's status, typically based on internal or meta data.
	*/

	/**
	 * Is this tax a compound tax?
	 *
	 * @since 1.1.0
	 * @return bool
	 */
	public function is_compound() {
		return 'yes' === $this->get_is_compound();
	}

	/**
	 * Is the tax similar to another tax?
	 *
	 * @param DocumentTax $tax The tax to compare.
	 *
	 * @since 1.1.0
	 * @return bool
	 */
	public function is_similar( $tax ) {
		if ( $this->get_rate() === $tax->get_rate() && $this->is_compound() === $tax->is_compound() ) {
			return true;
		}

		return false;
	}

	/**
	 * Merge this tax with another tax.
	 *
	 * @param static $line_tax The tax to merge with.
	 *
	 * @since 1.1.0
	 */
	public function merge( $line_tax ) {
		// If the rate is different, we can't merge.
		if ( ! $this->is_similar( $line_tax ) ) {
			return;
		}

		$this->set_amount( $this->get_amount() + $line_tax->get_amount() );
	}

	/**
	 * Get label.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function get_label() {
		return $this->get_name();
	}

	/**
	 * Get formatted total tax.
	 *
	 * @since 1.1.0
	 * @return float
	 */
	public function get_formatted_total() {
		return eac_format_money( $this->get_total(), $this->get_currency_code() );
	}

}
