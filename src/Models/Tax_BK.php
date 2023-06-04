<?php

namespace EverAccounting\Models;

/**
 * Class TaxRate
 *
 * @since  1.0.0
 * @package EverAccounting\Models
 */
class Tax_BK extends Model {
	/**
	 * Table name.
	 *
	 * This is also used as table alias.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const TABLE_NAME = 'ea_taxes';

	/**
	 * Object type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const OBJECT_TYPE = 'tax';

	/**
	 * Cache group.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const CACHE_GROUP = 'ea_taxes';

	/**
	 * Item Data array.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $core_data = array(
		'name'        => '',
		'rate'        => 0,
		'is_compound' => 'no',
		'description' => '',
		'status'      => 'active',
		'created_at'  => null,
		'updated_at'  => null,
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
	 * Get tax name
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_name( $context = 'edit' ) {
		return $this->get_prop( 'name', $context );
	}

	/**
	 * Set tax name
	 *
	 * @param string $name Tax name.
	 *
	 * @since  1.1.0
	 *
	 * @return void
	 */
	public function set_name( $name ) {
		$this->set_prop( 'name', $name );
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
		$this->set_prop( 'rate', floatval( $rate ) );
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
	 * Get tax description
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_description( $context = 'edit' ) {
		return $this->get_prop( 'description', $context );
	}

	/**
	 * Set tax description
	 *
	 * @param string $description Tax description.
	 *
	 * @since  1.1.0
	 *
	 * @return void
	 */
	public function set_description( $description ) {
		$this->set_prop( 'description', $description );
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
		if ( empty( $this->get_name() ) ) {
			return new \WP_Error( 'missing_required', __( 'Tax name is required', 'easy-appointments' ) );
		}

		if ( empty( $this->get_rate() ) ) {
			return new \WP_Error( 'missing_required', __( 'Tax rate is required', 'easy-appointments' ) );
		}

		// If It's update, set the updated date.
		if ( $this->exists() ) {
			$this->set_updated_at( current_time( 'mysql' ) );
		}

		// If date created is not set, set it to now.
		if ( empty( $this->get_created_at() ) ) {
			$this->set_created_at( current_time( 'mysql' ) );
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
