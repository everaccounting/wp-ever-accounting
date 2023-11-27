<?php

namespace EverAccounting\Models;

/**
 * Class TaxRate
 *
 * @since  1.0.0
 * @package EverAccounting\Models
 */
class Tax extends Model {
	/**
	 * Table name.
	 *
	 * This is also used as table alias.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $table_name = 'ea_taxes';

	/**
	 * Object type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $object_type = 'tax';

	/**
	 * Item Data array.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $core_data = array(
		'id'           => null,
		'name'         => '',
		'rate'         => 0,
		'is_compound'  => 0,
		'description'  => '',
		'status'       => 'active',
		'date_created' => null,
		'date_updated' => null,
	);


	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	| Methods which create, read, update and delete discounts from the database.
	*/
	/**
	 * Saves an object in the database.
	 *
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 * @since 1.0.0
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
			$this->set_date_updated( current_time( 'mysql' ) );
		}

		// If date created is not set, set it to now.
		if ( empty( $this->get_date_created() ) ) {
			$this->set_date_created( current_time( 'mysql' ) );
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
	 * Get tax name
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_name( $context = 'edit' ) {
		return $this->get_prop( 'name', $context );
	}

	/**
	 * Set tax name
	 *
	 * @param string $name Tax name.
	 *
	 * @return void
	 * @since  1.1.0
	 *
	 */
	public function set_name( $name ) {
		$this->set_prop( 'name', $name );
	}

	/**
	 * Get tax rate
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return float
	 * @since  1.1.0
	 *
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
	 *
	 */
	public function set_rate( $rate ) {
		$this->set_prop( 'rate', floatval( $rate ) );
	}

	/**
	 * Get tax type
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 *
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
	 *
	 */
	public function set_is_compound( $type ) {
		$this->set_prop( 'is_compound', $this->string_to_int( $type ) );
	}

	/**
	 * Get tax description
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_description( $context = 'edit' ) {
		return $this->get_prop( 'description', $context );
	}

	/**
	 * Set tax description
	 *
	 * @param string $description Tax description.
	 *
	 * @return void
	 * @since  1.1.0
	 *
	 */
	public function set_description( $description ) {
		$this->set_prop( 'description', $description );
	}

	/**
	 * Get the category status.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 * @since 1.0.2
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
	 * @param string $date_created date created.
	 */
	public function set_date_created( $date_created ) {
		$this->set_date_prop( 'date_created', $date_created );
	}

	/*
	|--------------------------------------------------------------------------
	| Extra props getters and setters
	|--------------------------------------------------------------------------
	| Extra props are used to store additional data in the database.
	*/

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
	 * @return bool
	 * @since 1.1.0
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
	 * @return string
	 * @since 1.1.6
	 */
	public function get_formatted_name() {
		return sprintf( '%1$s (%2$d%%)', $this->get_name(), $this->get_rate() );
	}

}
