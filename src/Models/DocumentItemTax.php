<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class DocumentLineTax.
 *
 * @since   1.0.0
 * @package EverAccounting\Models
 */
class DocumentItemTax extends Model {
	/**
	 * Table name.
	 *
	 * This is also used as table alias.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $table_name = 'ea_document_item_taxes';

	/**
	 * Object type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $object_type = 'document_item_tax';

	/**
	 * Core data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $core_data = array(
		'id'           => null,
		'name'         => '',
		'rate'         => 0.00,
		'is_compound'  => 0,
		'amount'       => 0.00,
		'item_id'      => null,
		'tax_id'       => null,
		'document_id'  => null,
		'date_updated' => null,
		'date_created' => null,
	);

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
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 * @since 1.0.0
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
		if ( empty( $this->get_date_created() ) ) {
			$this->set_date_created( current_time( 'mysql' ) );
		}

		// If It's update, set the updated date.
		if ( $this->exists() ) {
			$this->set_date_updated( current_time( 'mysql' ) );
		}

		return parent::save();
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
	 * Get id.
	 *
	 * @return int
	 * @since 1.0.0
	 */
	public function get_id() {
		return (int) $this->get_prop( 'id' );
	}

	/**
	 * Set id.
	 *
	 * @param int $id
	 *
	 * @since 1.0.0
	 */
	public function set_id( $id ) {
		$this->set_prop( 'id', absint( $id ) );
	}

	/**
	 * Get the item ID.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return int
	 * @since  1.1.0
	 */
	public function get_item_id( $context = 'edit' ) {
		return $this->get_prop( 'item_id', $context );
	}

	/**
	 * Set the item ID.
	 *
	 * @param int $value The item ID.
	 *
	 * @return void
	 * @since  1.1.0
	 */
	public function set_item_id( $value ) {
		$this->set_prop( 'item_id', absint( $value ) );
	}

	/**
	 * Get the tax ID.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return int
	 * @since  1.1.0
	 */
	public function get_tax_id( $context = 'edit' ) {
		return $this->get_prop( 'tax_id', $context );
	}

	/**
	 * Set the tax ID.
	 *
	 * @param int $value The tax ID.
	 *
	 * @return void
	 * @since  1.1.0
	 */
	public function set_tax_id( $value ) {
		$this->set_prop( 'tax_id', absint( $value ) );
	}

	/**
	 * Get the document ID.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return int
	 * @since  1.1.0
	 */
	public function get_document_id( $context = 'edit' ) {
		return $this->get_prop( 'document_id', $context );
	}

	/**
	 * Set the document ID.
	 *
	 * @param int $value The document ID.
	 *
	 * @return void
	 * @since  1.1.0
	 */
	public function set_document_id( $value ) {
		$this->set_prop( 'document_id', absint( $value ) );
	}

	/**
	 * Get the item name.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 */
	public function get_name( $context = 'edit' ) {
		return $this->get_prop( 'name', $context );
	}

	/**
	 * Set the item name.
	 *
	 * @param string $value The item name.
	 *
	 * @return void
	 * @since  1.1.0
	 */
	public function set_name( $value ) {
		$this->set_prop( 'name', sanitize_text_field( $value ) );
	}

	/**
	 * Get tax rate
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return float
	 * @since  1.1.0
	 */
	public function get_rate( $context = 'edit' ) {
		return $this->get_prop( 'rate', $context );
	}

	/**
	 * Set tax rate
	 *
	 * @param string $rate Tax rate.
	 *
	 * @return void
	 * @since  1.1.0
	 */
	public function set_rate( $rate ) {
		$this->set_prop( 'rate', eac_sanitize_number( $rate, 4 ) );
	}

	/**
	 * Get tax type
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 */
	public function get_is_compound( $context = 'edit' ) {
		return $this->get_prop( 'is_compound', $context );
	}

	/**
	 * Set tax type
	 *
	 * @param string $type Tax type.
	 *
	 * @return void
	 * @since  1.1.0
	 */
	public function set_is_compound( $type ) {
		$this->set_prop( 'is_compound', $this->string_to_int( $type ) );
	}

	/**
	 * Get amount.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return float
	 * @since  1.1.0
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
	 * @return string
	 * @since  1.1.0
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
	public function get_date_updated( $context = 'edit' ) {
		return $this->get_prop( 'date_updated', $context );
	}

	/**
	 * Set the date updated.
	 *
	 * @param string $date date updated.
	 */
	public function set_date_updated( $date ) {
		$this->set_date_prop( 'date_updated', $date );
	}

	/**
	 * Get the date created.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_date_created( $context = 'edit' ) {
		return $this->get_prop( 'date_created', $context );
	}

	/**
	 * Set the date created.
	 *
	 * @param string $date date created.
	 */
	public function set_date_created( $date ) {
		$this->set_date_prop( 'date_created', $date );
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
	 * @return bool
	 * @since 1.1.0
	 */
	public function is_compound() {
		return (bool) $this->get_is_compound();
	}

	/**
	 * Is the tax similar to another tax?
	 *
	 * @param DocumentItemTax $tax The tax to compare.
	 *
	 * @return bool
	 * @since 1.1.0
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
	 * @return string
	 * @since 1.1.0
	 */
	public function get_label() {
		return $this->get_name();
	}

	/**
	 * Get formatted total tax.
	 *
	 * @return float
	 * @since 1.1.0
	 */
	public function get_formatted_total() {
		return eac_format_money( $this->get_amount(), $this->get_currency_code() );
	}

}
