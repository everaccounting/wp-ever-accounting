<?php
/**
 * Abstract Model.
 *
 * Handles generic data interaction which is implemented by the different repository classes.
 * @since 1.1.0
 */

namespace EverAccounting\Abstracts;

defined( 'ABSPATH' ) || exit;

/**
 * Class Resource_Model
 *
 * Implemented by classes using the same CRUD(s) pattern.
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Abstracts
 */
class Data_Another {
	/**
	 * ID for this object.
	 *
	 * @since 1.1.0
	 *
	 * @var int
	 */
	protected $id = 0;

	/**
	 * Core data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $data = array();

	/**
	 * Extra data for this object. Name value pairs (name + default value).
	 * Used as a standard way for sub classes (like item types) to add
	 * additional information to an inherited class.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $extra_data = array();

	/**
	 * Set to data on construct so we can track and reset data if needed.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $default_data = array();

	/**
	 * Core data changes for this object.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $changes = array();

	/**
	 * This is false until the object is read from the DB.
	 *
	 * @since 1.1.0
	 *
	 * @var bool
	 */
	protected $object_read = false;

	/**
	 * Stores the object's sanitization level.
	 *
	 * Does not correspond to a DB field.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	protected $context = 'raw';

	/**
	 * Data constructor.
	 */
	public function __construct() {
		$this->data         = array_merge( $this->data, $this->extra_data );
		$this->default_data = $this->data;
	}

	/**
	 * Magic method for setting account fields.
	 *
	 * This method does not update custom fields in the database.
	 *
	 * @param string $key Account key.
	 * @param mixed $value Account value.
	 *
	 * @since 1.2.1
	 */
	public function __set( $key, $value ) {
		$this->set_props( [
			$key => $value
		] );
	}

	/**
	 * Magic method for accessing custom fields.
	 *
	 * @param string $key Account field to retrieve.
	 *
	 * @return mixed Value of the given Account field (if set).
	 * @since 1.2.1
	 */
	public function __get( $key ) {
		return $this->get_prop( $key );
	}

	/**
	 * Magic method for checking the existence of a certain field.
	 *
	 * @param string $key Field to check if set.
	 *
	 * @return bool Whether the given field is set.
	 * @since 1.2.1
	 */
	public function __isset( $key ) {
		return empty( $this->get_prop( $key ) );
	}

	/**
	 * Magic method for unsetting a certain field.
	 *
	 * @param string $key Field to unset.
	 *
	 * @since 1.2.1
	 */
	public function __unset( $key ) {
		$this->set_prop( $key, '' );
	}

	/**
	 * Set id.
	 *
	 * @param int $id id.
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_id( $id ) {
		$this->id = absint( $id );
	}

	/**
	 * Set a collection of props in one go, collect any errors, and return the result.
	 * Only sets using public methods.
	 *
	 * @param array|object $props Key value pairs to set. Key is the prop and should map to a setter function name.
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_props( $props ) {
		if ( is_object( $props ) ) {
			$props = get_object_vars( $props );
		}

		foreach ( $props as $prop => $value ) {
			/**
			 * Checks if the prop being set is allowed, and the value is not null.
			 */
			if ( is_null( $value ) ) {
				continue;
			}

			$setter = "set_$prop";

			if ( is_callable( array( $this, $setter ) ) ) {
				$this->{$setter}( $value );
			} else if ( property_exists( $this, $prop ) ) {
				$this->$prop = $value;
			} else if ( array_key_exists( $prop, $this->data ) ) {
				$this->set_prop( $prop, $value );
			} else {
				$this->extra_data[ $prop ] = $value;
			}
		}
	}

	/**
	 * Sets a prop for a setter method.
	 *
	 * This stores changes in a special array so we can track what needs saving
	 * the the DB later.
	 *
	 * @param string $prop Name of prop to set.
	 * @param mixed $value Value of the prop.
	 *
	 * @since 1.1.0
	 *
	 */
	protected function set_prop( $prop, $value ) {
		if ( array_key_exists( $prop, $this->data ) ) {
			if ( true === $this->object_read ) {
				if ( $value !== $this->data[ $prop ] || array_key_exists( $prop, $this->changes ) ) {
					$this->changes[ $prop ] = $value;
				}
			} else {
				$this->data[ $prop ] = $value;
			}
		}
	}

	/**
	 * Sets a date prop whilst handling formatting and datetime objects.
	 *
	 * @param string $prop Name of prop to set.
	 * @param string|integer $value Value of the prop.
	 * @param string $format
	 *
	 * @since 1.1.0
	 *
	 */
	protected function set_date_prop( $prop, $value, $format = 'Y-m-d H:i:s' ) {
		$value = eaccounting_date( $value, $format );
		if ( empty( $value ) ) {
			$this->set_prop( $prop, null );

			return;
		}
		$this->set_prop( $prop, $value );
	}

	/**
	 * Set all props to default values.
	 *
	 * @since 1.1.0
	 */
	public function set_defaults() {
		$this->data        = $this->default_data;
		$this->changes     = array();
		$this->object_read = false;
	}

	/**
	 * Returns the unique ID for this object.
	 *
	 * @return int
	 * @since  1.1.0
	 *
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Gets a prop for a getter method.
	 *
	 * Gets the value from either current pending changes, or the data itself.
	 * Context controls what happens to the value before it's returned.
	 *
	 * @param string $prop Name of prop to get.
	 *
	 * @return mixed
	 * @since  1.1.0
	 *
	 */
	protected function get_prop( $prop ) {
		$value = null;

		if ( array_key_exists( $prop, $this->data ) ) {
			$value = array_key_exists( $prop, $this->changes ) ? $this->changes[ $prop ] : $this->data[ $prop ];
		}

		return $value;
	}

	/**
	 * Return data changes only.
	 *
	 * @return array
	 * @since 1.1.0
	 *
	 */
	public function get_changes() {
		return $this->changes;
	}

	/**
	 * Determine whether the item exists in the database.
	 *
	 * @return bool True if item exists in the database, false if not.
	 * @since 1.2.1
	 */
	public function exists() {
		return ! empty( $this->id );
	}

	/**
	 * Merge changes with data and clear.
	 *
	 * @since 1.1.0
	 */
	public function apply_changes() {
		$this->data    = array_replace_recursive( $this->data, $this->changes );
		$this->changes = array();
	}

	/**
	 * Returns all data for this object.
	 *
	 * @return array
	 * @since  1.1.0
	 *
	 */
	public function get_data() {
		return array_merge(
			array( 'id' => $this->get_id() ),
			$this->data,
			$this->changes,
			$this->extra_data,
		);
	}

	/**
	 * Returns as pure array.
	 * Does depth array casting.
	 *
	 * @return array
	 * @since 1.0.2
	 *
	 */
	public function to_array( $data = array() ) {
		return array_merge(
			$this->get_data(),
			$data
		);
	}
}
