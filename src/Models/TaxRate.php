<?php

namespace EverAccounting\Models;

/**
 * Class TaxRate
 *
 * @since  1.0.0
 * @package EverAccounting\Models
 */
class TaxRate extends Term {
	/**
	 * Object type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const OBJECT_TYPE = 'tax_rate';

	/**
	 * Core meta keys.
	 *
	 * These are must be saved in the meta table even if they are empty.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $core_meta_keys = array( 'rate', 'is_compound' );

	/**
	 * Constructor.
	 *
	 * @param int|object|array $data Object ID, post object, or array of data.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $data = 0 ) {
		$this->core_data['group'] = self::OBJECT_TYPE;
		parent::__construct( $data );
		// after reading check if the contact is a customer.
		if ( $this->exists() && ! $this->is_group( self::OBJECT_TYPE ) ) {
			$this->set_id( 0 );
			$this->set_defaults();
		}
	}

	/**
	 * Returns all data for this object.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public function get_data( $context = 'edit' ) {
		$props                = $this->get_props( $context );
		$props['rate']        = $this->get_rate( $context );
		$props['is_compound'] = $this->get_is_compound( $context );

		return $props;
	}

	/*
	|--------------------------------------------------------------------------
	| Meta Properties
	|--------------------------------------------------------------------------
	| These properties are stored in the meta table.
	*/

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
		return $this->get_meta( 'rate', $context );
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
		$this->set_meta( 'rate', eac_sanitize_number( $rate, 0 ) );
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
		return $this->get_meta( 'is_compound', $context );
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

		$this->set_meta( 'is_compound', $type );
	}

	/*
	|--------------------------------------------------------------------------
	| Extra props getters and setters
	|--------------------------------------------------------------------------
	| Extra props are used to store additional data in the database.
	*/


	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	| Methods which create, read, update and delete discounts from the database.
	*/
	/**
	 * Saves an object in the database.
	 *
	 * @since 1.0.0
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 */
	public function save() {
		if ( empty( eac_is_empty_number( $this->get_rate() ) ) ) {
			return new \WP_Error( 'missing_required', __( 'Tax rate is required', 'wp-ever-accounting' ) );
		}

		// if is compound is not set, set it to no.
		if ( empty( $this->get_is_compound() ) ) {
			$this->set_is_compound( 'no' );
		}

		return parent::save();
	}

	/*
	|--------------------------------------------------------------------------
	| Query methods
	|--------------------------------------------------------------------------
	| Methods that query the database for this object.
	*/

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
	 * @since 1.1.6
	 * @return string
	 */
	public function get_formatted_name() {
		return sprintf( '%1$s (%2$d%%)', $this->get_name(), $this->get_rate() );
	}

}
