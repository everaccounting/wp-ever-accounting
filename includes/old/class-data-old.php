<?php
/**
 * Abstract Data.
 *
 * Handles generic data interaction which is implemented by
 * the different data store classes.
 *
 * @version     1.0.0
 * @since       1.1.3
 * @package     EverAccounting
 */

namespace EverAccounting\Old;

defined( 'ABSPATH' ) || exit();

/**
 * Abstract Data Class.
 */
abstract class Data_Old {
	/**
	 * id for this object.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	protected $id = 0;

	/**
	 * All data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $data = array();

	/**
	 * Core data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $core_data = array();

	/**
	 * Meta data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $meta_data = array();

	/**
	 * Extra data for this object. Name value pairs (name + default value).
	 * Used as a standard way for subclasses (like product types) to add
	 * additional information to an inherited class. Anything that is not
	 * core data may store here,
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $extra_data = array();

	/**
	 * Set to data on construct, so we can track and reset data if needed.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $default_data = array();

	/**
	 * Core data changes for this object.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $changes = array();

	/**
	 * This is false until the object is read from the DB.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	protected $object_read = false;

	/**
	 * Data constructor.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $cache_group = '';

	/**
	 * Meta type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $meta_type = false;

	/**
	 * Metadata which should exist in the DB, even if empty.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $must_exist_meta_keys = array();

	/**
	 * Default constructor.
	 *
	 * @param int|object|array $read ID to load from the DB (optional) or already queried data.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $read = 0 ) {
		$this->data         = array_merge( $this->core_data, $this->meta_data, $this->extra_data );
		$this->default_data = $this->data;
	}

	/**
	 * Only store the object ID to avoid serializing the data object instance.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function __sleep() {
		return array( 'id' );
	}

	/**
	 * Re-run the constructor with the object ID.
	 *
	 * If the object no longer exists, remove the ID.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		try {
			$this->__construct( absint( $this->id ) ); //phpcs:ignore
		} catch ( Exception $e ) {
			$this->set_id( 0 );
			$this->set_object_read( true );
		}
	}

	/**
	 * When the object is cloned, make sure meta is duplicated correctly.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
	}

	/**
	 * Magic method for checking the existence of a certain field.
	 *
	 * @param string $key Field to check if set.
	 *
	 * @since 1.0.0
	 * @return bool Whether the given field is set.
	 */
	public function __isset( $key ) {
		return ! empty( $this->get_prop( $key ) );
	}

	/**
	 * Magic method for unsetting a certain field.
	 *
	 * @param string $key Field to unset.
	 *
	 * @since 1.0.0
	 */
	public function __unset( $key ) {
		$this->set_prop( $key, '' );
	}

	/**
	 * Magic method for setting data fields.
	 *
	 * This method does not update custom fields in the database.
	 *
	 * @since  1.0.0
	 */
	public function __set( $key, $value ) {

		if ( 'id' === strtolower( $key ) ) {
			$this->set_id( $value );
		}

		if ( method_exists( $this, "set_$key" ) ) {
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
	 * @since  1.0.0
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
	 * Change data to JSON format.
	 *
	 * @since  1.0.0
	 * @return string Data in JSON format.
	 */
	public function __toString() {
		return wp_json_encode( $this->get_data() );
	}

	/**
	 * This method will be called when somebody will try to invoke a method in object
	 * context, which does not exist, like:
	 *
	 * $plugin->method($arg, $arg1);
	 *
	 * @param string $method Method name.
	 * @param array $arguments Array of arguments passed to the method.
	 */
	public function __call( $method, $arguments ) {
		$sub_method = substr( $method, 0, 3 );
		// Drop method name.
		$property_name = substr( $method, 4 );
		switch ( $sub_method ) {
			case "get":
				return $this->get_prop( $property_name );
			case "set":
				$this->set_prop( $property_name, $arguments[0] );
				break;
			case "has":
				return $this->get_prop( $property_name ) !== null;
			default:
				throw new \BadMethodCallException( "Undefined method $method" );
		}

		return null;
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	|
	| Methods for getting data from the bill object.
	|
	*/

	/**
	 * Returns the unique ID for this object.
	 *
	 * @since  1.0.0
	 * @return int
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
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since  1.0.0
	 * @return mixed
	 */
	protected function get_prop( $prop ) {
		$value = null;

		if ( array_key_exists( $prop, $this->data ) ) {
			$value = isset( $this->changes[ $prop ] ) ? $this->changes[ $prop ] : $this->data[ $prop ];
		}

		return $value;
	}

	/**
	 * Returns all data for this object.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public function get_data() {
		if ( $this->meta_type ) {
			$this->read_meta_data();
		}

		return array_merge( array( 'id' => $this->get_id() ), array_replace_recursive( $this->data, $this->changes ) );
	}

	/**
	 * Returns array of expected data keys for this object.
	 *
	 * @since   1.0.0
	 * @return array
	 */
	public function get_data_keys() {
		return array_keys( $this->data );
	}

	/**
	 * Get core data.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_core_data() {
		return wp_array_slice_assoc( $this->get_data(), array_keys( $this->core_data ) );
	}

	/**
	 * Returns all "core" data keys for an object.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public function get_core_data_keys() {
		return array_keys( $this->core_data );
	}

	/**
	 * Get Meta data.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_meta_data() {
		return wp_array_slice_assoc( $this->get_data(), array_keys( $this->meta_data ) );
	}

	/**
	 * Returns all "core" data keys for an object.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public function get_meta_data_keys() {
		return array_keys( $this->meta_data );
	}

	/**
	 * Get Extra data.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_extra_data() {
		return wp_array_slice_assoc( $this->get_data(), array_keys( $this->extra_data ) );
	}

	/**
	 * Returns all "extra" data keys for an object (for sub objects like product types).
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public function get_extra_data_keys() {
		return array_keys( $this->extra_data );
	}

	/**
	 * Return data changes only.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_changes() {
		return $this->changes;
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	|
	| Functions for setting boll data. These should not update anything in the
	| database itself and should only change what is stored in the class
	| object.
	|
	*/
	/**
	 * Set ID.
	 *
	 * @param int $id ID.
	 *
	 * @since 1.0.0
	 */
	protected function set_id( $id ) {
		$this->id = absint( $id );
	}

	/**
	 * Set object read property.
	 *
	 * @param boolean $read Should read?.
	 *
	 * @since 1.0.0
	 */
	public function set_object_read( $read = true ) {
		$this->object_read = (bool) $read;
	}

	/**
	 * Set all props to default values.
	 *
	 * @since 1.0.0
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
	 * @since  1.0.0
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
	 * This stores changes in a special array, so we can track what needs saving
	 * the DB later.
	 *
	 * @param string $prop Name of prop to set.
	 * @param mixed $value Value of the prop.
	 *
	 * @since 1.0.0
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
	 * @param string $format Date format.
	 *
	 * @since 1.0.0
	 */
	protected function set_date_prop( $prop, $value, $format = 'Y-m-d H:i:s' ) {
		if ( empty( $value ) || '0000-00-00 00:00:00' === $value || '0000-00-00' === $value ) {
			$this->set_prop( $prop, null );

			return;
		}

		if ( ! $format ) {
			$format = 'Y-m-d H:i:s';
		}

		if ( ! is_numeric( $value ) ) {
			$value = (int) strtotime( $value );
		}

		$value = date_i18n( $format, $value );

		$this->set_prop( $prop, $value );
	}

	/*
	|--------------------------------------------------------------------------
	| Conditionals
	|--------------------------------------------------------------------------
	|
	| Checks if a condition is true or false.
	|
	*/
	/**
	 * Get object read property.
	 *
	 * @since  1.0.0
	 * @return boolean
	 */
	public function is_object_read() {
		return (bool) $this->object_read;
	}

	/**
	 * Checks if the object is saved in the database
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function exists() {
		$id = $this->get_id();

		return ! empty( $id );
	}

	/*
	|--------------------------------------------------------------------------
	| Helper
	|--------------------------------------------------------------------------
	|
	| Helper methods.
	|
	*/

	/**
	 * Merge changes with data and clear.
	 *
	 * @since 1.0.0
	 */
	public function apply_changes() {
		$this->data    = array_replace_recursive( $this->data, $this->changes );
		$this->changes = array();
	}

	/**
	 * Checks if a date is valid or not.
	 *
	 * @param string $date Date to check.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_date_valid( $date ) {
		if ( empty( preg_replace( '/[^0-9]/', '', $date ) ) ) {
			return false;
		}

		return (bool) strtotime( $date );
	}

	/*
	|--------------------------------------------------------------------------
	| Meta Data methods
	|--------------------------------------------------------------------------
	|
	| Meta data related methods.
	|
	*/

	/**
	 * Retrieves the value of a metadata field for the specified object type and ID.
	 *
	 * @param string $meta_key Optional. Metadata key.
	 * @param bool $single Optional. If true, return only the first value of the specified `$meta_key`.
	 *
	 * @since 1.0.0
	 * @return mixed An array of values if `$single` is false.
	 */
	public function get_meta( $meta_key = '', $single = true ) {
		return get_metadata( $this->meta_type, $this->get_id(), $meta_key, $single );
	}

	/**
	 * Deletes meta based on meta ID.
	 *
	 * @param string $meta_key Metadata key.
	 * @param mixed $meta_value Optional. Metadata value. Must be serializable if non-scalar.
	 * @param bool $delete_all Optional. If true, delete matching metadata entries for all objects,
	 *
	 * @since  1.0.0
	 * @return bool True on successful delete, false on failure.
	 */
	public function delete_meta( $meta_key, $meta_value = '', $delete_all = false ) {
		return delete_metadata( $this->meta_type, $this->get_id(), $meta_key, $meta_value, $delete_all );
	}

	/**
	 * Add new piece of meta.
	 *
	 * @param string $meta_key Metadata key.
	 * @param mixed $meta_value Metadata value. Must be serializable if non-scalar.
	 * @param bool $unique Optional. Whether the specified metadata key should be unique for the object.
	 *
	 * @since  1.0.0
	 * @return int|false The meta ID on success, false on failure.
	 */
	public function add_meta( $meta_key, $meta_value, $unique = true ) {
		return add_metadata( $this->meta_type, $this->get_id(), $meta_key, $meta_value, $unique );
	}

	/**
	 * Update meta.
	 *
	 * @param string $meta_key Metadata key.
	 * @param mixed $meta_value Metadata value. Must be serializable if non-scalar.
	 * @param mixed $prev_value Optional. Previous value to check before updating.
	 *
	 * @since  1.0.0
	 */
	public function update_meta( $meta_key, $meta_value, $prev_value = '' ) {
		update_metadata( $this->meta_type, $this->get_id(), $meta_key, $meta_value, $prev_value );
	}

	/**
	 * Read extra data associated with the product.
	 *
	 * @since 1.0.0
	 */
	protected function read_meta_data() {
		if ( ! $this->meta_type || ! $this->exists() || empty( $this->get_meta_data_keys() ) ) {
			return;
		}

		foreach ( $this->get_meta_data_keys() as $key ) {
			$function = 'set_' . $key;
			$value    = $this->get_meta( '_' . $key, true );
			$value    = is_string( $value ) ? wp_unslash( $value ) : $value;
			if ( is_callable( array( $this, $function ) ) ) {
				$this->{$function}( $value );
			} else {
				$this->set_prop( $key, $value );
			}
		}
	}

	/**
	 * Read extra data associated with the product.
	 *
	 * @since 1.0.0
	 * @return array Array of updated metadata keys.
	 */
	protected function update_meta_data() {
		if ( ! $this->exists() || ! $this->meta_data || empty( $this->get_meta_data_keys() ) ) {
			return array();
		}
		$updated       = array();
		$changed_props = $this->get_changes();
		foreach ( $this->get_meta_data_keys() as $meta_key ) {
			if ( array_key_exists( $meta_key, $changed_props ) || ! metadata_exists( $this->meta_type, $this->get_id(), $meta_key ) ) {
				$function = 'get_' . $meta_key;
				if ( is_callable( array( $this, $function ) ) ) {
					$value = $this->{$function}();
				} else {
					$value = $this->$meta_key;
				}
				$value = is_string( $value ) ? wp_slash( $value ) : $value;
				if ( in_array( $value, array( array(), '' ), true ) && ! in_array( $meta_key, $this->must_exist_meta_keys, true ) ) {
					$this->delete_meta( '_' . $meta_key );
				} else {
					$this->update_meta( '_' . $meta_key, $value );
				}

				$updated[ $meta_key ] = $value;
			}
		}

		return $updated;
	}

	/**
	 * Delete all data data.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function delete_meta_data() {
		global $wpdb;
		if ( ! $this->exists() || ! $this->meta_data ) {
			return;
		}
		$column    = sanitize_key( $this->meta_type . '_id' );
		$id_column = ( 'user' === $this->meta_type ) ? 'umeta_id' : 'meta_id';
		$table     = $wpdb->prefix . $this->meta_type . 'meta';
		$sql       = $wpdb->prepare( "SELECT $id_column FROM $table WHERE $column = %d", $this->get_id() );
		$meta_ids  = $wpdb->get_col( $sql );
		foreach ( $meta_ids as $mid ) {
			delete_metadata_by_mid( $this->meta_type, $mid );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Taxonomy methods
	|--------------------------------------------------------------------------
	|
	| Meta data related methods.
	|
	*/

	/**
	 * Get and store terms from a taxonomy.
	 *
	 * @param string $taxonomy Taxonomy name e.g. product_cat
	 * @param string $fields Field name e.g. all
	 *
	 * @since  1.0.0
	 * @return array of terms
	 */
	public function get_terms( $taxonomy, $fields = 'all' ) {
		$terms = wp_get_object_terms( $this->get_id(), $taxonomy, array( 'fields' => $fields ) );
		if ( false === $terms || is_wp_error( $terms ) ) {
			return array();
		}

		return $terms;
	}

	/**
	 * Set terms for the object.
	 *
	 * @param string|int|array $terms A single term slug, single term ID, or array of either term slugs or IDs.
	 * @param string $taxonomy Taxonomy name e.g. product_cat
	 * @param bool $append If false will delete difference of terms. Default false.
	 *
	 * @since  1.0.0
	 * @return array|false Term taxonomy IDs of the affected terms or WP_Error on failure.
	 */
	public function set_terms( $terms, $taxonomy, $append = false ) {
		return wp_set_object_terms( $this->get_id(), $terms, $taxonomy, $append );
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	|
	| Methods which create, read, update and delete documents from the database.
	| Written in abstract fashion so that the way documents are stored can be
	| changed more easily in the future.
	|
	| A save method is included for convenience (chooses update or create based
	| on if the order exists yet).
	|
	*/

	/**
	 *  Create an item in the database.
	 *
	 * This method is not meant to call publicly instead call save
	 * which will conditionally decide which method to call.
	 *
	 * @since 1.0.0
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 */
	abstract protected function create();

	/**
	 * Retrieve the object from database instance.
	 *
	 * @since 1.0.0
	 *
	 * @return object|false Object, false otherwise.
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 */
	abstract protected function read();

	/**
	 *  Update an object in the database.
	 *
	 * This method is not meant to call publicly instead call save
	 * which will conditionally decide which method to call.
	 *
	 * @since 1.0.0
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 */
	abstract protected function update();

	/**
	 * Deletes the object from database.
	 *
	 * @param array $args Array of args to pass to the delete method.
	 *
	 * @since 1.0.0
	 * @return array|false true on success, false on failure.
	 */
	abstract public function delete( $args = array() );

	/**
	 * Saves an object in the database.
	 *
	 * @since 1.0.0
	 * @return \WP_Error|int id on success, WP_Error on failure.
	 */
	abstract public function save();
}
