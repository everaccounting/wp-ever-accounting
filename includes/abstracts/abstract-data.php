<?php
/**
 * Abstract Data.
 *
 * Handles generic data interaction which is implemented by the different object classes.
 *
 * @since 1.1.0
 */

namespace EverAccounting\Abstracts;

defined( 'ABSPATH' ) || exit;

/**
 * Class Data
 * @package EverAccounting\Abstracts
 * @since 1.1.0
 */
abstract class Data {
	/**
	 * id for this object.
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
	 * Used as a standard way for sub classes (like product types) to add
	 * additional information to an inherited class. Anything that is not
	 * core data may store here,
	 *
	 * @since 1.2.1
	 * @var array
	 */
	protected $extra_data = array();

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
	 * Set to data on construct so we can track and reset data if needed.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $default_data = array();

	/**
	 * Data constructor.
	 */
	public function __construct() {
		$this->default_data = $this->data;
	}

	/**
	 * Only store the object ID to avoid serializing the data object instance.
	 *
	 * @return array
	 * @since 1.1.0
	 *
	 */
	public function __sleep() {
		return array( 'id' );
	}

	/**
	 * Re-run the constructor with the object ID.
	 *
	 * If the object no longer exists, remove the ID.
	 *
	 * @since 1.1.0
	 */
	public function __wakeup() {
	}

	/**
	 * When the object is cloned, make sure meta is duplicated correctly.
	 *
	 * @since 1.1.0
	 */
	public function __clone() {
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
		return ! empty( $this->get_prop( $key ) );
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
	 * Magic method for setting data fields.
	 *
	 * This method does not update custom fields in the database.
	 *
	 * @since  1.1.0
	 *
	 */
	public function __set( $key, $value ) {

		if ( 'id' === strtolower( $key ) ) {
			$this->set_id( $value );
		}

		if ( method_exists( $this, "set_$key" ) ) {

			/* translators: %s: $key Key to set */
			//eaccounting_doing_it_wrong( __FUNCTION__, sprintf( __( 'Object data such as "%s" should not be accessed directly. Use getters and setters.', 'wp-ever-accounting' ), $key ), '1.1.0' );

			call_user_func( array( $this, "set_$key" ), $value );
		} else {
			$this->set_prop( $key, $value );
		}

	}

	/**
	 * Magic method for retrieving a property.
	 *
	 * @param $key
	 *
	 * @return mixed|null
	 */
	public function __get( $key ) {
		// Check if we have a helper method for that.
		if ( method_exists( $this, 'get_' . $key ) ) {
			return call_user_func( array( $this, 'get_' . $key ) );
		}

		return $this->get_prop( $key );
	}

	/**
	 * Set ID.
	 *
	 * @param int $id ID.
	 *
	 * @since 1.1.0
	 *
	 */
	protected function set_id( $id ) {
		$this->id = absint( $id );
	}

	/**
	 * Set object read property.
	 *
	 * @param boolean $read Should read?.
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_object_read( $read = true ) {
		$this->object_read = (bool) $read;
	}

	/**
	 * Set all props to default values.
	 *
	 * @since 1.1.0
	 */
	public function set_defaults() {
		$this->data    = $this->default_data;
		$this->changes = array();
		$this->set_object_read( false );
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
		if ( ! is_array( $props ) ) {
			return $this;
		}

		foreach ( $props as $prop => $value ) {
			/**
			 * Checks if the prop being set is allowed, and the value is not null.
			 */
			if ( is_null( $value ) || in_array( $prop, array( 'prop', 'date_prop', 'meta_data' ), true ) ) {
				continue;
			}

			$setter = "set_$prop";

			if ( is_callable( array( $this, $setter ) ) ) {
				$this->{$setter}( $value );
			} else {
				$this->set_prop( $prop, $value );
			}
		}

		return $this;
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
		} elseif ( array_key_exists( $prop, $this->extra_data ) ) {
			$this->extra_data[ $prop ] = $value;
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
	 * Returns array of expected data keys for this object.
	 *
	 * @return array
	 * @since   1.1.0
	 *
	 */
	public function get_data_keys() {
		return array_keys( $this->data );
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
		} elseif ( array_key_exists( $prop, $this->extra_data ) ) {
			$value = $this->extra_data[ $prop ];
		}

		return $value;
	}

	/**
	 * Get object read property.
	 *
	 * @return boolean
	 * @since  1.1.0
	 */
	public function get_object_read() {
		return (bool) $this->object_read;
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
	 * Checks if the object is saved in the database
	 *
	 * @return bool
	 * @since 1.1.0
	 *
	 */
	public function exists() {
		$id = $this->get_id();

		return ! empty( $id );
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
	 * Returns as pure array.
	 *
	 * @return array
	 * @since 1.0.2
	 *
	 */
	public function to_array() {
		return array_merge(
			array( 'id' => $this->get_id() ),
			$this->data,
			$this->changes
		);
	}

	/**
	 * Alias self:to_array();
	 * @return array
	 */
	public function get_data(){
		return $this->to_array();
	}

	/**
	 * Change data to JSON format.
	 *
	 * @return string Data in JSON format.
	 * @since  1.1.0
	 */
	public function __toString() {
		return wp_json_encode( $this->to_array() );
	}


	/**
	 * Retrieve the object from database instance.
	 *
	 * @param int|string $id Object id.
	 * @param string $field Database field.
	 *
	 * @return object|false Object, false otherwise.
	 * @since 1.2.1
	 *
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 *
	 */
	abstract static function get_raw( $id, $field = 'id' );

	/**
	 *  Insert an item in the database.
	 *
	 * This method is not meant to call publicly instead call save
	 * which will conditionally decide which method to call.
	 *
	 * @param array $args An array of arguments for internal use case.
	 *
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 * @since 1.1.0
	 */
	abstract protected function insert( $args = array() );

	/**
	 *  Update an object in the database.
	 *
	 * This method is not meant to call publicly instead call save
	 * which will conditionally decide which method to call.
	 *
	 * @param array $args An array of arguments for internal use case.
	 *
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 * @since 1.1.0
	 */
	abstract protected function update( $args = array() );

	/**
	 * Saves an object in the database.
	 *
	 * @return \WP_Error|int id on success, WP_Error on failure.
	 * @since 1.1.0
	 */
	abstract public function save();

	/**
	 * Deletes the object from database.
	 *
	 * @return array|false true on success, false on failure.
	 * @since 1.1.0
	 */
	abstract public function delete();
}
