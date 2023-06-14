<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class Model.
 *
 * @since   1.0.0
 * @package EverAccounting
 */
abstract class Model {
	/**
	 * Table name.
	 *
	 * This is also used as table alias.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const TABLE_NAME = 'table_name';

	/**
	 * Type of the object.
	 *
	 * This is used for hooks.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const OBJECT_TYPE = 'object_type';

	/**
	 * Cache group.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const CACHE_GROUP = 'cache_group';

	/**
	 * Meta type declaration for the object.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const META_TYPE = false;

	/**
	 * id for this object.
	 *
	 * @since 1.0.0
	 * @var int ID.
	 */
	protected $id = 0;

	/**
	 * Table with prefix.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $table = null;

	/**
	 * Table alias.
	 *
	 * Never set this directly. It is automatically generated from the table name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $table_alias = null;

	/**
	 * Core data for this object. Name value pairs (name + default value).
	 *
	 * Everything in this array will be saved to the core table.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $core_data = array();

	/**
	 * Extra data for this object. Name value pairs (name + default value).
	 * add additional information to an inherited class. Anything that is not
	 * in the columns array will be stored here.
	 *
	 * @since 1.0.0
	 * @var array Extra data.
	 */
	protected $extra_data = array();

	/**
	 * Meta data for this object. Name value pairs (name + default value).
	 *
	 * For core meta data, use the $metadata array.
	 *
	 * @since 1.0.0
	 * @var array Meta data.
	 */
	protected $metadata = array();

	/**
	 * All data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.0.0
	 * @var array All data.
	 */
	protected $data = array();

	/**
	 * Model changes.
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
	 * This is false until the metadata is read from the DB.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	protected $metadata_read = false;

	/**
	 * Search columns.
	 *
	 * null means all columns.
	 *
	 * @since 1.0.0
	 * @var array|null
	 */
	protected $search_columns = null;

	/**
	 * Core meta keys.
	 *
	 * These are must be saved in the meta table even if they are empty.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $core_meta_keys = array();

	/**
	 * Model constructor.
	 *
	 * @param int|object|array $data Object ID, post object, or array of data.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $data = 0 ) {
		global $wpdb;
		$called_class      = get_called_class();
		$this->table_alias = static::TABLE_NAME;
		$this->table       = $wpdb->prefix . static::TABLE_NAME;
		$this->data        = array_merge( $this->core_data, $this->extra_data );

		if ( static::META_TYPE ) {
			$metatype          = static::META_TYPE . 'meta';
			$wpdb->{$metatype} = $wpdb->prefix . $metatype;
		}

		if ( is_scalar( $data ) ) {
			$this->set_id( $data );
		} elseif ( $data instanceof $called_class ) {
			$this->set_id( $data->get_id() );
		} elseif ( is_object( $data ) && ! empty( $data->id ) ) {
			$this->set_id( $data->id );
		} elseif ( is_array( $data ) && ! empty( $data[ $data->id ] ) ) {
			$this->set_id( $data[ $data->id ] );
		} else {
			$this->object_read = true;
		}

		$this->read();
	}

	/**
	 * Remove meta-type from database.
	 *
	 * @since 1.0.0
	 */
	public function __destruct() {
	}

	/**
	 * Only store the object primary key to avoid serializing the data object instance.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function __sleep() {
		return array( 'id' );
	}

	/**
	 * Re-run the constructor with the object primary key.
	 *
	 * If the object no longer exists, remove the ID.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		try {
			$this->__construct( $this->get_id() );
		} catch ( \Exception $e ) {
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
		$this->set_id( 0 );
		if ( static::META_TYPE ) {
			$this->read_metadata();
			foreach ( $this->metadata as $key => $value ) {
				if ( isset( $value->id ) ) {
					$value->id = null;
				}
				$this->metadata[ $key ] = clone $value;
			}
		}
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
		// If there is a getter function for this field, use it.
		$getter = 'get_' . $key;
		if ( method_exists( $this, $getter ) ) {
			return null !== $this->$getter();
		}

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
		// If there is a setter function for this field, use it.
		$setter = 'set_' . $key;
		if ( method_exists( $this, $setter ) ) {
			$this->$setter( null );

			return;
		}
		$this->set_prop( $key, '' );
	}

	/**
	 * Magic method for setting data fields.
	 *
	 * This method does not update custom fields in the database.
	 *
	 * @param string $key Prop to set.
	 * @param mixed  $value Value to set.
	 *
	 * @since  1.0.0
	 */
	public function __set( $key, $value ) {
		// If there is a setter function for this field, use it.
		$setter = 'set_' . $key;
		if ( method_exists( $this, $setter ) ) {
			$this->$setter( $value );

			return;
		}
		$this->set_prop( $key, $value );
	}

	/**
	 * Magic method for retrieving a property.
	 *
	 * @param string $key Key to get.
	 *
	 * @since  1.0.0
	 * @return mixed|null
	 */
	public function __get( $key ) {
		// Check if we have a helper method for that.
		if ( method_exists( $this, 'get_' . $key ) ) {
			return $this->{'get_' . $key}( 'edit' );
		}

		return $this->get_prop( $key );
	}

	/**
	 * Change data to JSON format.
	 *
	 * @since  1.0.0
	 * @return string Model in JSON format.
	 */
	public function __toString() {
		$json = wp_json_encode( $this->get_data() );

		return ! $json ? '{}' : $json;
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
	 * Returns the unique ID for this object.
	 *
	 * @since  1.0.0
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Set id of the object.
	 *
	 * @param int $id ID.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function set_id( $id ) {
		$this->id = absint( $id );
	}

	/**
	 * Gets a prop for a getter method.
	 *
	 * Gets the value from either current pending changes, or the data itself.
	 * Context controls what happens to the value before it's returned.
	 *
	 * @param string $prop Name of prop to get.
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.0.0
	 * @return mixed
	 */
	protected function get_prop( $prop, $context = 'edit' ) {
		$value = null;
		if ( array_key_exists( $prop, $this->data ) ) {
			$value = isset( $this->changes[ $prop ] ) ? $this->changes[ $prop ] : $this->data[ $prop ];
		} elseif ( 'id' === $prop ) {
			$value = $this->get_id();
		}

		if ( 'view' === $context ) {
			$value = apply_filters( $this->get_hook_prefix() . '_get_' . $prop, $value, $this );
		}

		return $value;
	}

	/**
	 * Set a prop for a write/batch operation. This should not update anything in the database itself and should only change what is stored in the class object.
	 *
	 * @param string $prop Name of prop to set.
	 * @param mixed  $value Value of prop.
	 *
	 * @since  1.0.0
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
	 * Get date prop
	 *
	 * @param string $prop Name of prop to get.
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @param string $format Date format.
	 *
	 * @since 1.0.0
	 * @return string|null
	 */
	public function get_date_prop( $prop, $context = 'edit', $format = 'Y-m-d H:i:s' ) {
		$datetime = $this->sanitize_date( $this->get_prop( $prop ) );

		$value = $datetime ? date( $format, strtotime( $datetime ) ) : null; // @codingStandardsIgnoreLine - date() is ok here.

		if ( 'view' === $context ) {
			$value = apply_filters( $this->get_hook_prefix() . '_get_' . $prop, $value, $this );
		}

		return $value;
	}

	/**
	 * Sets a date prop whilst handling formatting and datetime objects.
	 *
	 * @param string         $prop Name of prop to set.
	 * @param string|integer $value Value of the prop.
	 * @param string         $format Date format.
	 *
	 * @since 1.0.0
	 */
	public function set_date_prop( $prop, $value, $format = 'Y-m-d H:i:s' ) {
		$date = $this->sanitize_date( $value );
		if ( ! empty( $date ) ) {
			$date = date( $format, strtotime( $date ) ); // @codingStandardsIgnoreLine - date() is ok here.
		}
		$this->set_prop( $prop, $date );
	}

	/**
	 * Get Boolean prop.
	 *
	 * @param string $prop Name of prop to get.
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function get_boolean_prop( $prop, $context = 'edit' ) {
		$value = (bool) $this->string_to_bool( $this->get_prop( $prop ) );

		if ( 'view' === $context ) {
			$value = apply_filters( $this->get_hook_prefix() . '_get_' . $prop, $value, $this );
		}

		return $value;
	}

	/**
	 * Set Boolean prop.
	 *
	 * @param string $prop Name of prop to set.
	 * @param bool   $value Value of the prop.
	 *
	 * @since 1.0.0
	 */
	public function set_boolean_prop( $prop, $value ) {
		$this->set_prop( $prop, $this->bool_to_string( $value ) );
	}


	/**
	 * Get all props for an object.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public function get_props( $context = 'edit' ) {
		$props = array(
			'id' => $this->get_id(),
		);
		foreach ( $this->core_data as $key => $value ) {
			$props[ $key ] = $this->get_prop( $key, $context );
		}

		return $props;
	}

	/**
	 * Set a collection of props in one go, collect any errors, and return the result.
	 * Only sets using public methods.
	 *
	 * @param array|object $props Key value pairs to set. Key is the prop and should map to a setter function name.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function set_props( $props ) {
		if ( is_object( $props ) ) {
			$props = get_object_vars( $props );
		}
		if ( ! is_array( $props ) ) {
			return;
		}

		foreach ( $props as $prop => $value ) {
			$prop = preg_replace( '/^[^a-zA-Z]+/', '', $prop );
			// If the property name is id, then we will skip it.
			if ( 'id' === $prop ) {
				continue;
			}
			// if value is array, call the same function for each item.
			if ( 'metadata' === $prop && is_array( $value ) ) {
				$this->set_props( $value );

				return;
			} elseif ( is_callable( array( $this, "set_$prop" ) ) ) {
				$this->{"set_$prop"}( $value );
			} else {
				$this->set_prop( $prop, $value );
			}
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
		$props = $this->get_props( $context );
		if ( static::META_TYPE ) {
			$props['metadata'] = $this->get_metadata();
		}

		return $props;
	}

	/**
	 * Get Meta data.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_metadata() {
		$this->read_metadata();
		$metadata = array();
		// Loop over the metadata and get the values. Values could be single or multiple.
		foreach ( $this->metadata as $meta ) {
			$metadata[ $meta->key ][] = $meta->value;
		}

		return $metadata;
	}

	/**
	 * Set object read property.
	 *
	 * @param boolean $read Should read?.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function set_object_read( $read = true ) {
		$this->object_read = (bool) $read;
	}

	/**
	 * Get object read property.
	 *
	 * @since 1.0.0
	 * @return boolean
	 */
	public function get_object_read() {
		return $this->object_read;
	}

	/**
	 * Get meta data.
	 *
	 * @param string $key Meta key.
	 * @param bool   $single Single.
	 *
	 * @since 1.0.0
	 * @return array|mixed|null
	 */
	public function get_meta( $key, $single = true ) {
		if ( static::META_TYPE ) {
			$this->read_metadata();
			$meta = array_filter(
				$this->metadata,
				function ( $meta ) use ( $key ) {
					return $meta->key === $key;
				}
			);

			if ( $single ) {
				$meta = current( $meta );

				return $meta ? $meta->value : null;
			}

			// get all values without keys.
			return array_values(
				array_map(
					function ( $meta ) {
						return $meta->value;
					},
					$meta
				)
			);
		}

		return null;
	}

	/**
	 * Set meta data.
	 *
	 * @param string $key Meta key.
	 * @param string $value Meta value.
	 * @param bool   $single Single.
	 *
	 * @since 1.0.0
	 */
	public function set_meta( $key, $value, $single = true ) {
		if ( static::META_TYPE ) {
			$this->read_metadata();
			$meta = array_filter(
				$this->metadata,
				function ( $meta ) use ( $key ) {
					return $meta->key === $key;
				}
			);

			if ( $single ) {
				$meta = array_map(
					function ( $meta ) {
						$meta->value = null;

						return $meta;
					},
					$meta
				);
			}

			if ( ! empty( $meta ) && $single ) {
				$index                 = array_search( reset( $meta ), $this->metadata, true );
				$meta[ $index ]->value = $value;
			} else {
				$this->metadata[] = (object) array(
					'id'      => null,
					'initial' => null,
					'key'     => $key,
					'value'   => $value,
				);
			}
		}
	}

	/**
	 * Delete meta data.
	 *
	 * @param string $key Meta key.
	 * @param string $value Meta value.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function delete_meta( $key, $value = null ) {
		if ( static::META_TYPE ) {
			$this->read_metadata();

			// If value is not set, delete all meta with the key. Otherwise, delete only the meta with the key and value.
			if ( is_null( $value ) ) {
				$this->metadata = array_filter(
					$this->metadata,
					function ( $meta ) use ( $key ) {
						if ( $meta->key === $key ) {
							$meta->value = null;
						}

						return $meta;
					}
				);
			} else {
				$this->metadata = array_filter(
					$this->metadata,
					function ( $meta ) use ( $key, $value ) {
						if ( $meta->key === $key && $this->is_equal( $meta->value, $value ) ) {
							$meta->value = null;
						}

						return $meta;
					}
				);
			}
		}
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

	/**
	 * Set change.
	 *
	 * @param string $key Key.
	 * @param mixed  $value Value.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function set_change( $key, $value ) {
		$this->changes[ $key ] = $value;
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
	protected function create() {
		global $wpdb;
		$data = wp_unslash( $this->get_core_data() );

		/**
		 * Fires immediately before an item is inserted in the database.
		 *
		 * @param static $object Model object.
		 *
		 * @since 1.0.0
		 */
		do_action( $this->get_hook_prefix() . '_pre_insert', $this );

		foreach ( $data as $key => $value ) {
			if ( ! is_scalar( $value ) ) {
				$data[ $key ] = maybe_serialize( $value );
			}
		}

		/**
		 * Filters the data to be inserted into the database.
		 *
		 * @param array  $data Data to be inserted.
		 * @param static $object Model object.
		 *
		 * @since 1.0.0
		 */
		$data = apply_filters( $this->get_hook_prefix() . '_insert_data', $data, $this );

		if ( false === $wpdb->insert( $this->table, $data, array() ) ) {
			// translators: %s: database error message.
			return new \WP_Error( 'db_insert_error', sprintf( __( 'Could not insert item into the database error %s', 'wp-ever-accounting' ), $wpdb->last_error ) );
		}

		$this->set_id( $wpdb->insert_id );
		$this->set_object_read( true );

		/**
		 * Fires immediately after an item is inserted in the database.
		 *
		 * @param static $item Model object.
		 *
		 * @since 1.0.0
		 */
		do_action( $this->get_hook_prefix() . '_inserted', $this );

		return $this->exists();
	}

	/**
	 * Retrieve the object from database instance.
	 *
	 * @since 1.0.0
	 *
	 * @return object|false Object, false otherwise.
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 */
	protected function read() {
		global $wpdb;
		$this->set_defaults();
		// Bail early if no id is set.
		if ( ! $this->get_id() ) {
			return false;
		}
		$data = wp_cache_get( $this->get_id(), static::CACHE_GROUP );
		if ( false === $data ) {
			$data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $this->table WHERE id = %d LIMIT 1;", $this->get_id() ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		}
		if ( ! $data ) {
			$this->set_id( 0 );

			return false;
		}
		$id = isset( $data->ID ) ? $data->ID : $data->id; // Support both ID and id.
		wp_cache_add( $id, $data, static::CACHE_GROUP );

		foreach ( $data as $key => $value ) {
			if ( is_serialized( $value ) ) {
				$data->$key = maybe_unserialize( $value );
			}
		}

		/**
		 * Filters the data retrieved from the database.
		 *
		 * @param array  $data Data retrieved from the database.
		 * @param static $object Model object.
		 *
		 * @since 1.0.0
		 */
		$data = apply_filters( $this->get_hook_prefix() . '_db_data', (array) $data, $this );
		$this->set_props( $data );
		$this->set_object_read( true );

		return $data;
	}

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
	protected function update() {
		global $wpdb;
		$changes = $this->get_changes();
		// Bail if nothing to save.
		if ( empty( $changes ) ) {
			return true;
		}
		/**
		 * Fires immediately before an existing item is updated in the database.
		 *
		 * @param static $item Model object.
		 * @param array  $changes The data will be updated.
		 *
		 * @since 1.0.0
		 */
		do_action( $this->get_hook_prefix() . '_pre_update', $this, $changes );

		$data = wp_array_slice_assoc( $changes, $this->get_columns() );

		/**
		 * Filters the data to be updated in the database.
		 *
		 * @param array  $data Data to be updated.
		 * @param static $object Model object.
		 *
		 * @since 1.0.0
		 */
		$data = apply_filters( $this->get_hook_prefix() . '_update_data', $data, $this );

		if ( ! empty( $data ) ) {
			foreach ( $data as $key => $value ) {
				if ( ! is_scalar( $value ) ) {
					$data[ $key ] = maybe_serialize( $value );
				}
			}
			if ( false === $wpdb->update( $this->table, $data, [ 'id' => $this->get_id() ], array(), [ 'id' => '%d' ] ) ) {
				return new \WP_Error( 'db_update_error', __( 'Could not update item in the database.', 'wp-ever-accounting' ), $wpdb->last_error );
			}
		}

		/**
		 * Fires immediately after an existing item is updated in the database.
		 *
		 * @param static $item Model object.
		 * @param array  $changes The data will be updated.
		 *
		 * @since 1.0.0
		 */
		do_action( $this->get_hook_prefix() . '_updated', $this, $changes );

		return true;
	}

	/**
	 * Deletes the object from database.
	 *
	 * @since 1.0.0
	 * @return array|false true on success, false on failure.
	 */
	public function delete() {
		if ( ! $this->exists() ) {
			return false;
		}

		$data = $this->get_data();

		/**
		 * Filters whether an item delete should take place.
		 *
		 * @param static $item Model object.
		 * @param array  $data Model data array.
		 *
		 * @since 1.0.0
		 */
		$check = apply_filters( $this->get_hook_prefix() . '_check_delete', null, $this, $data );
		if ( null !== $check ) {
			return $check;
		}

		/**
		 * Fires before an item is deleted.
		 *
		 * @param static $item Model object.
		 * @param array  $data Model data array.
		 *
		 * @since 1.0.0
		 */
		do_action( $this->get_hook_prefix() . '_pre_delete', $this, $data );

		global $wpdb;

		$wpdb->delete(
			$this->table,
			array(
				'id' => $this->get_id(),
			),
			array( '%d' )
		);

		$this->delete_metadata();

		/**
		 * Fires after a item is deleted.
		 *
		 * @param static $item Model object.
		 * @param array  $data Model data array.
		 *
		 * @since 1.0.0
		 */
		do_action( $this->get_hook_prefix() . '_deleted', $this, $data );

		wp_cache_delete( $this->get_id(), static::CACHE_GROUP );
		wp_cache_set( 'last_changed', microtime(), static::CACHE_GROUP );
		$this->set_defaults();

		return $data;
	}

	/**
	 * Saves an object in the database.
	 *
	 * @since 1.0.0
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 */
	public function save() {

		/**
		 * Filters whether an item should be checked.
		 *
		 * @param static $object Model object.
		 *
		 * @since 1.0.0
		 */
		$check = apply_filters( $this->get_hook_prefix() . '_sanitize_data', null, $this );
		if ( is_wp_error( $check ) ) {
			return $check;
		}

		/**
		 * Fires immediately before the object is inserted or updated in the database.
		 *
		 * @param static $object The object.
		 *
		 * @since 1.0.0
		 */
		do_action( $this->get_hook_prefix() . '_pre_save', $this );

		if ( ! $this->exists() ) {
			$is_error = $this->create();
		} else {
			$is_error = $this->update();
		}

		if ( is_wp_error( $is_error ) ) {
			return $is_error;
		}

		$this->save_metadata();
		$this->apply_changes();

		// Clear cache.
		wp_cache_delete( $this->get_id(), static::CACHE_GROUP );
		wp_cache_set( 'last_changed', microtime(), static::CACHE_GROUP );

		/**
		 * Fires immediately after a key is inserted or updated in the database.
		 *
		 * @param int    $id Key id.
		 * @param static $object The object.
		 *
		 * @since 1.0.0
		 */
		do_action( $this->get_hook_prefix() . '_saved', $this );

		return true;
	}


	/**
	 * Get meta data.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function read_metadata() {
		global $wpdb;
		if ( static::META_TYPE && $this->exists() && ! $this->metadata_read ) {
			// Read metadata based on meta type.
			$table           = $wpdb->prefix . static::META_TYPE . 'meta';
			$object_id_field = static::META_TYPE . '_id';
			$meta_id_field   = 'user' === static::META_TYPE ? 'umeta_id' : 'meta_id';
			$cache_key       = static::META_TYPE . '_meta';
			$cache_group     = static::META_TYPE . '_meta';
			$metas           = wp_cache_get( $this->get_id(), $cache_key, $cache_group );
			if ( false === $metas ) {
				$metas = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT $meta_id_field as meta_id, meta_key, meta_value FROM $table WHERE $object_id_field = %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- $object_id_field is a prepared value.
						$this->get_id()
					)
				);
				wp_cache_set( $this->get_id(), $metas, $cache_key, $cache_group );
			}

			if ( $metas ) {
				foreach ( $metas as $meta_item ) {
					$this->metadata[] = (object) array(
						'id'      => $meta_item->meta_id,
						'key'     => $meta_item->meta_key,
						'initial' => maybe_unserialize( $meta_item->meta_value ),
						'value'   => maybe_unserialize( $meta_item->meta_value ),
					);
				}
			}

			$this->metadata_read = true;
		}

		// If you must exist meta keys are not set, then initialize with empty array.
		if ( static::META_TYPE && ! empty( $this->core_meta_keys ) ) {
			foreach ( $this->core_meta_keys as $key ) {
				$keys = wp_list_pluck( $this->metadata, 'key' );
				if ( ! in_array( $key, $keys, true ) ) {
					$this->metadata[] = (object) array(
						'id'      => null,
						'initial' => null,
						'key'     => $key,
						'value'   => null,
					);
				}
			}
		}
	}


	/**
	 * Delete all meta data.
	 *
	 * @since 1.0.0
	 */
	protected function delete_metadata() {
		global $wpdb;
		if ( static::META_TYPE && $this->exists() ) {
			$table           = $wpdb->prefix . static::META_TYPE . 'meta';
			$object_id_field = static::META_TYPE . '_id';
			$cache_key       = static::META_TYPE . '_meta';
			$wpdb->delete(
				$table,
				array(
					$object_id_field => $this->get_id(),
				)
			);
			$this->metadata = array();
			wp_cache_delete( $this->get_id(), $cache_key );
		}
	}

	/**
	 * Update Meta Model in the database.
	 *
	 * @since 1.0.0
	 */
	public function save_metadata() {
		if ( static::META_TYPE && $this->exists() ) {
			$this->read_metadata();
			// Get changed meta data.
			$changed_metadata = array_filter(
				$this->metadata,
				function ( $meta ) {
					return ! $this->is_equal( $meta->initial, $meta->value );
				}
			);

			// If id is not set, we need to add the metadata. if id is set, we need to update the metadata. if value is null, we need to delete the meta data.
			foreach ( $changed_metadata as $key => $meta ) {
				if ( is_null( $meta->id ) && ! empty( $meta->value ) ) {
					$meta_id                         = add_metadata( static::META_TYPE, $this->get_id(), $meta->key, $meta->value );
					$this->metadata[ $key ]->id      = $meta_id;
					$this->metadata[ $key ]->initial = $meta->value;
				} elseif ( empty( $meta->value ) && ! in_array( $meta->key, $this->core_meta_keys, true ) && ! is_null( $meta->id ) ) {
					delete_metadata_by_mid( static::META_TYPE, $meta->id );
					unset( $this->metadata[ $key ] );
				} elseif ( ! is_null( $meta->id ) && ! $this->is_equal( $meta->initial, $meta->value ) ) {
					update_metadata_by_mid( static::META_TYPE, $meta->id, $meta->value );
					$this->metadata[ $key ]->initial = $meta->value;
				}
			}

			// Clear cache.
			wp_cache_delete( $this->get_id(), static::META_TYPE . '_meta' );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Query Methods
	|--------------------------------------------------------------------------
	|
	| Methods for reading and manipulating the object properties.
	|
	*/

	/**
	 * Retrieve the object instance.
	 *
	 * @param int|array|static $id Object ID or array of arguments.
	 *
	 * @since 1.0.0
	 *
	 * @return static|false Object instance on success, false on failure.
	 */
	public static function get( $id ) {
		if ( empty( $id ) ) {
			return false;
		}

		// If It's array, then assume its args.
		if ( is_array( $id ) ) {
			$args['no_count'] = true;
			$args             = $id;
			$items            = static::query( $args );
			if ( ! empty( $items ) && is_array( $items ) ) {
				return reset( $items );
			}

			return false;
		}

		if ( is_a( $id, __CLASS__ ) ) {
			$id = $id->get_id();
		} elseif ( is_object( $id ) ) {
			$data = get_object_vars( $id );
			if ( ! empty( $data['id'] ) ) {
				$id = $data['id'];
			}
		}

		$record = new static( $id );
		if ( $record->exists() ) {
			return $record;
		}

		return false;
	}

	/**
	 * Insert or update an object in the database.
	 *
	 * @param array|object $data Model to insert or update.
	 * @param boolean      $wp_error Optional. Whether to return a WP_Error on failure. Default false.
	 *
	 * @return static|false Object, false otherwise.
	 */
	public static function insert( $data, $wp_error = true ) {
		if ( is_object( $data ) ) {
			$data = get_object_vars( $data );
		}

		if ( ! is_array( $data ) || empty( $data ) ) {
			return false;
		}

		if ( ! isset( $data['id'] ) ) {
			$data['id'] = null;
		}
		$class  = new \ReflectionClass( get_called_class() );
		$object = $class->newInstance( $data['id'] );
		$object->set_props( $data );
		$save = $object->save();

		if ( is_wp_error( $save ) ) {
			if ( $wp_error ) {
				return $save;
			} else {
				return false;
			}
		}

		return $object->exists() ? $object : false;
	}

	/**
	 * Get count of objects.
	 *
	 * @param array $args Array of args to pass to the query method.
	 *
	 * @since 1.0.0
	 * @return int Count of objects.
	 */
	public static function count( $args = array() ) {
		$args['count'] = true;

		return ( new static() )->all( $args );
	}

	/**
	 * Query for objects.
	 *
	 * @param array $args Array of args to pass to the query method.
	 *
	 * @since 1.0.0
	 * @return int|static[]|object[]|int[]|string[] Query results.
	 */
	public static function query( $args = array() ) {
		return ( new static() )->all( $args );
	}

	/**
	 * Query for objects.
	 *
	 * @param array $args Array of args to pass to the query method.
	 *
	 * @since 1.0.0
	 * @return int|static[]|object[]|int[]|string[] Query results.
	 */
	public function all( $args = array() ) {
		global $wpdb;
		$args     = $this->prepare_query_args( $args );
		$is_count = $this->string_to_bool( $args['count'] );
		$no_count = $this->string_to_bool( $args['no_count'] );
		unset( $args['count'], $args['no_count'] );
		$clauses      = $this->get_query_clauses( $args );
		$clauses      = $this->prepare_query_clauses( $clauses, $args );
		$last_changed = wp_cache_get_last_changed( static::CACHE_GROUP );
		$cache_key    = static::CACHE_GROUP . ':' . md5( wp_json_encode( $clauses ) ) . ':' . $last_changed;
		$result       = wp_cache_get( $cache_key, static::CACHE_GROUP );

		if ( false === $result ) {
			// Go through each clause and add it to the query.
			$query = '';
			foreach ( $clauses as $type => $clause ) {
				if ( ! empty( $clause ) ) {
					if ( 'select' === $type ) {
						$clause = ! $no_count ? 'SQL_CALC_FOUND_ROWS ' . $clause : $clause;
						$query .= 'SELECT ' . trim( $clause );
					} elseif ( 'from' === $type ) {
						$query .= ' FROM ' . trim( $clause );
					} elseif ( 'join' === $type ) {
						$query .= ' ' . trim( $clause );
					} elseif ( 'where' === $type ) {
						$query .= ' WHERE 1=1 ' . trim( $clause );
					} elseif ( 'groupby' === $type && ! empty( $clause ) ) {
						$query .= ' GROUP BY ' . trim( $clause );
					} elseif ( 'having' === $type && ! empty( $clause ) ) {
						$query .= ' Having ' . trim( $clause );
					} elseif ( 'orderby' === $type && ! empty( $clause ) ) {
						$query .= ' ORDER BY ' . trim( $clause );
					} elseif ( 'limit' === $type && ! empty( $clause ) ) {
						$query .= ' LIMIT ' . trim( $clause );
					}
				}
			}
			// var dump the query, no carecter limit.
			$total = 0;
			if ( is_array( $args['fields'] ) || 'all' === $args['fields'] ) {
				$items = $wpdb->get_results( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			} else {
				$items = $wpdb->get_col( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			}

			if ( ! $no_count ) {
				/**
				 * Filter the query result items.
				 *
				 * @param string $sql SQL for finding the item count.
				 * @param array  $items Query items.
				 * @param array  $args Query arguments.
				 *
				 * @since 1.0.0
				 */
				$found_rows = apply_filters( $this->get_hook_prefix() . '_found_rows', 'SELECT FOUND_ROWS()', $items, $args );
				$total      = (int) $wpdb->get_var( $found_rows ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			}

			/**
			 * Filter the query result items.
			 *
			 * @param array $items Query items.
			 * @param array $args Query arguments.
			 *
			 * @since 1.0.0
			 */
			$items = apply_filters( $this->get_hook_prefix() . '_query_items', $items, $args );

			$result = (object) [
				'items' => $items,
				'total' => $total,
			];

			wp_cache_add( $cache_key, $result, static::CACHE_GROUP );
		}

		$items = isset( $result->items ) ? $result->items : array();
		$total = isset( $result->total ) ? absint( $result->total ) : 0;

		if ( in_array( 'all', $args['fields'], true ) ) {
			foreach ( $items as $key => $row ) {
				/**
				 * Filter the query result item.
				 *
				 * @param object $row Query item.
				 * @param array  $args Query arguments.
				 *
				 * @since 1.0.0
				 */
				$row = (object) apply_filters( $this->get_hook_prefix() . '_db_data', (array) $row, $this );
				foreach ( $row as $column => $value ) {
					if ( is_serialized( $value ) ) {
						$row->$column = maybe_unserialize( $value );
					}
				}

				$id = isset( $row->ID ) ? $row->ID : $row->id;
				wp_cache_add( $id, $row, static::CACHE_GROUP );

				$item = new static( $id );
				$item->set_object_read( true );
				$item->set_props( $row );
				$items[ $key ] = $item;
			}

			// Based on output prepare the result.
			if ( ARRAY_A === $args['output'] ) {
				$items = wp_list_pluck( $items, 'data' );
			} elseif ( ARRAY_N === $args['output'] ) {
				$items = wp_list_pluck( $items, 'data' );
				foreach ( $items as $key => $data ) {
					$items[ $key ] = array_values( $data );
				}
			} elseif ( OBJECT === $args['output'] ) {
				$items = wp_list_pluck( $items, 'data' );
				foreach ( $items as $key => $data ) {
					$items[ $key ] = (object) $data;
				}
			}
		}

		if ( in_array( 'ids', $args['fields'], true ) ) {
			$items = wp_list_pluck( $items, 'id' );
			$items = array_map(
				function ( $id ) {
					return is_numeric( $id ) ? (int) $id : $id;
				},
				$items
			);
		}

		return $is_count ? $total : $items;
	}

	/**
	 * Parse query args.
	 *
	 * @param array $args Array of args to pass to the query method.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	protected function prepare_query_args( $args = array() ) {
		$default = array(
			'orderby'     => 'id',
			'order'       => 'ASC',
			'search'      => '',
			'include'     => '',
			'exclude'     => '',
			'offset'      => '',
			'per_page'    => 20,
			'paged'       => 1,
			'no_count'    => false,
			'count'       => false,
			'where_query' => array(),
			'meta_query'  => array(), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			'date_query'  => array(),
			'fields'      => 'all',
			'output'      => get_called_class(),
		);

		$args             = wp_parse_args( $args, $default );
		$args['no_count'] = $this->string_to_bool( $args['no_count'] );
		$args['count']    = $this->string_to_bool( $args['count'] );
		$args['fields']   = is_string( $args['fields'] ) ? preg_split( '/[,\s]+/', $args['fields'] ) : $args['fields'];

		if ( ! empty( $args['limit'] ) ) {
			$args['per_page'] = $args['limit'];
			unset( $args['limit'] );
		}

		if ( ! empty( $args['nopaging'] ) ) {
			$args['per_page'] = - 1;
			unset( $args['nopaging'] );
		}

		return $args;
	}

	/**
	 * Get query clauses.
	 *
	 * @param array $args Array of args to pass to the query method.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	protected function get_query_clauses( $args = array() ) {
		// Query clauses.
		$clauses = array(
			'select'  => '',
			'from'    => '',
			'join'    => '',
			'where'   => '',
			'groupby' => '',
			'orderby' => '',
			'limit'   => '',
		);

		/**
		 * Filter the query clauses before setting up the query.
		 *
		 * @param array  $clauses Query clauses.
		 * @param array  $args Query arguments.
		 * @param static $this Current instance of the class.
		 *
		 * @since 1.0.0
		 */
		return apply_filters( $this->get_hook_prefix() . '_setup_query_clauses', $clauses, $args, $this );
	}

	/**
	 * Prepare query clauses.
	 *
	 * @param array $clauses Query clauses.
	 * @param array $args Query arguments.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	protected function prepare_query_clauses( $clauses, $args = array() ) {
		/**
		 * Filter the query clauses before setting up the query.
		 *
		 * @param array  $clauses Query clauses.
		 * @param array  $args Query arguments.
		 * @param static $this Current instance of the class.
		 *
		 * @since 1.0.0
		 * @return array
		 */
		$clauses = apply_filters( $this->get_hook_prefix() . '_pre_setup_query_clauses', $clauses, $args, $this );

		$clauses = $this->prepare_select_query( $clauses, $args );
		$clauses = $this->prepare_from_query( $clauses, $args );
		$clauses = $this->prepare_join_query( $clauses, $args );
		$clauses = $this->prepare_where_query( $clauses, $args );
		$clauses = $this->prepare_group_by_query( $clauses, $args );
		$clauses = $this->prepare_having_query( $clauses, $args );
		$clauses = $this->prepare_order_by_query( $clauses, $args );
		$clauses = $this->prepare_limit_query( $clauses, $args );

		/**
		 * Filter the query clauses after setting up the query.
		 *
		 * @param array  $clauses Query clauses.
		 * @param array  $args Query arguments.
		 * @param static $this Current instance of the class.
		 *
		 * @since 1.0.0
		 * @return array
		 */
		return apply_filters( $this->get_hook_prefix() . '_setup_query_clauses', $clauses, $args, $this );
	}

	/**
	 * Prepare fields query.
	 *
	 * @param array $clauses Query clauses.
	 * @param array $args Array of args to pass to the query method.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	protected function prepare_select_query( $clauses, $args = array() ) {
		foreach ( $args['fields'] as $field ) {
			if ( 'all' === $field ) {
				$clauses['select'] .= $this->table_alias . '.*';
			} elseif ( 'ids' === $field ) {
				$clauses['select'] .= $this->table_alias . '.id';
			} elseif ( in_array( $field, $this->get_columns(), true ) ) {
				$clauses['select'] .= "{$this->table_alias}.{$field}";
			}
		}

		/**
		 * Filter the select clause before setting up the query.
		 *
		 * @param array  $clauses Query clauses.
		 * @param array  $args Query arguments.
		 * @param static $this Current instance of the class.
		 *
		 * @since 1.0.0
		 */
		return apply_filters( $this->get_hook_prefix() . '_prepare_select_query', $clauses, $args, $this );
	}

	/**
	 * Prepare from query.
	 *
	 * @param array $clauses Query clauses.
	 * @param array $args Array of args to pass to the query method.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	protected function prepare_from_query( $clauses, $args = array() ) {

		$clauses['from'] .= "{$this->table} as {$this->table_alias}";

		/**
		 * Filter the from clause before setting up the query.
		 *
		 * @param array  $clauses Query clauses.
		 * @param array  $args Query arguments.
		 * @param static $this Current instance of the class.
		 *
		 * @since 1.0.0
		 */
		return apply_filters( $this->get_hook_prefix() . '_prepare_from_query', $clauses, $args, $this );
	}

	/**
	 * Prepare where query.
	 *
	 * @param array $clauses Query clauses.
	 * @param array $args Array of args to pass to the query method.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	protected function prepare_where_query( $clauses, $args = array() ) {
		global $wpdb;
		/**
		 * Filter the where clause before setting up the query.
		 *
		 * @param array  $clauses Query clauses.
		 * @param array  $args Query arguments.
		 * @param static $this Current instance of the class.
		 *
		 * @since 1.0.0
		 */
		$clauses = apply_filters( $this->get_hook_prefix() . '_pre_setup_where_query', $clauses, $args, $this );

		$query_where = isset( $args['where_query'] ) ? $args['where_query'] : array();

		// Include clause.
		if ( ! empty( $args['include'] ) ) {
			$query_where[] = array(
				'column'  => "{$this->table_alias}.id",
				'value'   => $args['include'],
				'compare' => 'IN',
			);
		}

		// Exclude clause.
		if ( ! empty( $args['exclude'] ) ) {
			$query_where[] = array(
				'column'  => "{$this->table_alias}.id",
				'value'   => $args['exclude'],
				'compare' => 'NOT IN',
			);
		}

		foreach ( $this->get_columns() as $column ) {
			// equals clause.
			if ( ! empty( $args[ $column ] ) ) {
				$query_where[] = [
					'column'  => "{$this->table_alias}.{$column}",
					'value'   => $args[ $column ],
					'compare' => false !== strpos( $column, '_ids' ) ? 'FIND_IN_SET' : '=', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				];
			} elseif ( ! empty( $args[ $column . '__in' ] ) ) {
				$query_where[] = [
					'column'  => "{$this->table_alias}.{$column}",
					'value'   => $args[ $column . '__in' ],
					'compare' => false !== strpos( $column, '_ids' ) ? 'FIND_IN_SET' : 'IN', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				];
			} elseif ( ! empty( $args[ $column . '__not_in' ] ) ) {
				// __not_in clause.
				$query_where[] = [
					'column'  => "{$this->table_alias}.{$column}",
					'value'   => $args[ $column . '__not_in' ],
					'compare' => false !== strpos( $column, '_ids' ) ? 'NOT_IN_SET' : 'NOT IN', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				];
			} elseif ( ! empty( $args[ $column . '__between' ] ) ) {
				// __between clause.
				$query_where[] = [
					'column'  => "{$this->table_alias}.{$column}",
					'value'   => $args[ $column . '__between' ],
					'compare' => 'BETWEEN',
				];
			} elseif ( ! empty( $args[ $column . '__not_between' ] ) ) {
				// __not_between clause.
				$query_where[] = [
					'column'  => "{$this->table_alias}.{$column}",
					'value'   => $args[ $column . '__not_between' ],
					'compare' => 'NOT BETWEEN',
				];
			} elseif ( ! empty( $args[ $column . '__exists' ] ) ) {
				// __exists clause.
				$query_where[] = [
					'column'  => "{$this->table_alias}.{$column}",
					'compare' => 'EXISTS',
				];
			} elseif ( ! empty( $args[ $column . '__not_exists' ] ) ) {
				// __not_exists clause.
				$query_where[] = [
					'column'  => "{$this->table_alias}.{$column}",
					'compare' => 'NOT EXISTS',
				];
			} elseif ( ! empty( $args[ $column . '__like' ] ) ) {
				// __like clause.
				$query_where[] = [
					'column'  => "{$this->table_alias}.{$column}",
					'value'   => $args[ $column . '__like' ],
					'compare' => 'LIKE',
				];
			} elseif ( ! empty( $args[ $column . '__not_like' ] ) ) {
				// __not_like clause.
				$query_where[] = [
					'column'  => "{$this->table_alias}.{$column}",
					'value'   => $args[ $column . '__not_like' ],
					'compare' => 'NOT LIKE',
				];
			} elseif ( ! empty( $args[ $column . '__starts_with' ] ) ) {
				// __starts_with clause.
				$query_where[] = [
					'column'  => "{$this->table_alias}.{$column}",
					'value'   => $args[ $column . '__starts_with' ],
					'compare' => 'LIKE',
				];
			} elseif ( ! empty( $args[ $column . '__ends_with' ] ) ) {
				// __ends_with clause.
				$query_where[] = [
					'column'  => "{$this->table_alias}.{$column}",
					'value'   => $args[ $column . '__ends_with' ],
					'compare' => 'ENDS WITH',
				];
			} elseif ( ! empty( $args[ $column . '__is_null' ] ) ) {
				// __is_null clause.
				$query_where[] = [
					'column'  => "{$this->table_alias}.{$column}",
					'compare' => 'IS NULL',
				];
			} elseif ( ! empty( $args[ $column . '__is_not_null' ] ) ) {
				// __is_not_null clause.
				$query_where[] = [
					'column'  => "{$this->table_alias}.{$column}",
					'compare' => 'IS NOT NULL',
				];
			} elseif ( ! empty( $args[ $column . '__gt' ] ) ) {
				// __gt clause.
				$query_where[] = [
					'column'  => "{$this->table_alias}.{$column}",
					'value'   => $args[ $column . '__gt' ],
					'compare' => 'GREATER THAN',
				];
			} elseif ( ! empty( $args[ $column . '__lt' ] ) ) {
				// __lt clause.
				$query_where[] = [
					'column'  => "{$this->table_alias}.{$column}",
					'value'   => $args[ $column . '__lt' ],
					'compare' => 'LESS THAN',
				];
			} elseif ( ! empty( $args[ $column . '__find_in_set' ] ) ) {
				// find_in_set clause.
				$query_where[] = [
					'column'  => "{$this->table_alias}.{$column}",
					'compare' => 'FIND_IN_SET',
					'value'   => $args[ $column . '__find_in_set' ],
				];
			} elseif ( ! empty( $args[ $column . '__find_not_in_set' ] ) ) {
				// find_in_set clause.
				$query_where[] = [
					'column'  => "{$this->table_alias}.{$column}",
					'compare' => 'NOT_IN_SET',
					'value'   => $args[ $column . '__find_not_in_set' ],
				];
			} elseif ( ! empty( $args[ $column . '__regexp' ] ) ) {
				// __regexp clause.
				$query_where[] = [
					'column'  => "{$this->table_alias}.{$column}",
					'value'   => $args[ $column . '__regexp' ],
					'compare' => 'REGEXP',
				];
			}
		}

		// Parse each query where clause.
		foreach ( $query_where as $where_key => $where_clause ) {
			if ( ! is_numeric( $where_key ) && is_string( $where_clause ) ) {
				$where_clause = array(
					'column'  => $where_key,
					'value'   => $where_clause,
					'compare' => '=',
				);
			}
			$where_clause = wp_parse_args(
				$where_clause,
				array(
					'column'  => '',
					'value'   => '',
					'compare' => '=',
				)
			);

			$where_column  = $where_clause['column'];
			$where_value   = $where_clause['value'];
			$where_compare = strtoupper( $where_clause['compare'] );
			// Column is not valid or empty. Skip.
			if ( empty( $where_column ) || ! $this->is_valid_column( str_replace( "{$this->table_alias}.", '', $where_column ) ) ) {
				continue;
			}
			// Validate value.
			if ( ! is_array( $where_value ) && ! is_numeric( $where_value ) && ! is_string( $where_value ) ) {
				continue;
			}
			if ( in_array( $where_compare, array( 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN' ), true ) && ! is_array( $where_value ) ) {
				$where_value = preg_split( '/[,\s]+/', $where_value );
			} elseif ( is_string( $where_value ) ) {
				$where_value = trim( $where_value );
			}

			// Make sql query based on compare.
			switch ( $where_compare ) {
				case 'IN':
				case 'NOT IN':
					if ( empty( $where_value ) ) {
						continue 2;
					}
					$placeholders      = array_fill( 0, count( $where_value ), '%s' );
					$format            = "AND (  $where_column $where_compare (" . implode( ', ', $placeholders ) . ') )';
					$clauses['where'] .= $wpdb->prepare( $format, $where_value ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
					break;
				case 'BETWEEN':
				case 'NOT BETWEEN':
					if ( empty( $where_value ) || ! is_array( $where_value ) || count( $where_value ) < 2 ) {
						continue 2;
					}
					$placeholder       = '%s';
					$format            = "AND ( $where_column $where_compare $placeholder AND $placeholder )";
					$clauses['where'] .= $wpdb->prepare( $format, $where_value[0], $where_value[1] ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
					break;
				case 'LIKE':
				case 'NOT LIKE':
					$format            = "AND ( $where_column $where_compare %s )";
					$clauses['where'] .= $wpdb->prepare( $format, '%' . $wpdb->esc_like( $where_value ) . '%' ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
					break;
				case 'EXISTS':
				case 'NOT EXISTS':
					$format            = "AND ( $where_compare (SELECT 1 FROM {$this->table} WHERE  $where_column = %s) )";
					$clauses['where'] .= $wpdb->prepare( $format, $where_value ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
					break;
				case 'RLIKE':
					$format            = "AND (  $where_column REGEXP %s )";
					$clauses['where'] .= $wpdb->prepare( $format, $where_value ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
					break;
				case 'ENDS WITH':
					$format            = "AND (  $where_column LIKE %s )";
					$clauses['where'] .= $wpdb->prepare( $format, '%' . $wpdb->esc_like( $where_value ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
					break;
				case 'STARTS WITH':
					$format            = "AND (  $where_column LIKE %s )";
					$clauses['where'] .= $wpdb->prepare( $format, $wpdb->esc_like( $where_value ) . '%' ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
					break;
				case 'IS NULL':
				case 'IS NOT NULL':
					$format            = "AND ( $where_column $where_compare )";
					$clauses['where'] .= $wpdb->prepare( $format, $where_value ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
					break;
				case 'GREATER THAN':
					$placeholder       = is_numeric( $where_value ) ? '%d' : '%s';
					$format            = "AND ( $where_column > $placeholder )";
					$clauses['where'] .= $wpdb->prepare( $format, $where_value ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
					break;
				case 'LESS THAN':
					$placeholder       = is_numeric( $where_value ) ? '%d' : '%s';
					$format            = "AND ( $where_column < $placeholder )";
					$clauses['where'] .= $wpdb->prepare( $format, $where_value ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
					break;
				case 'FIND_IN_SET':
					$clause            = is_array( $where_value ) ? 'REGEXP' : 'FIND_IN_SET';
					$where_value       = is_array( $where_value ) ? implode( '|', $where_value ) : $where_value;
					$format            = 'REGEXP' === $clause ? "AND ( $where_column REGEXP %s )" : "AND ( FIND_IN_SET( %s, $where_column ) > 0 )";
					$clauses['where'] .= $wpdb->prepare( $format, $where_value ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
					break;
				case 'NOT_IN_SET':
					$clause            = is_array( $where_value ) ? 'NOT REGEXP' : 'FIND_IN_SET';
					$where_value       = is_array( $where_value ) ? implode( '|', $where_value ) : $where_value;
					$format            = 'NOT REGEXP' === $clause ? "AND ( $where_column NOT REGEXP %s )" : "AND ( FIND_IN_SET( %s, $where_column ) = 0 )";
					$clauses['where'] .= $wpdb->prepare( $format, $where_value ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
					break;
				case 'REGEXP':
				case 'NOT REGEXP':
				default:
					// Placeholder based on type.
					$placeholder       = is_numeric( $where_value ) ? '%d' : '%s';
					$format            = "AND (  $where_column $where_compare $placeholder )";
					$clauses['where'] .= $wpdb->prepare( $format, $where_value ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
					break;
			}
		}

		$clauses = $this->prepare_search_query( $clauses, $args );
		$clauses = $this->prepare_date_query( $clauses, $args );
		$clauses = $this->prepare_meta_query( $clauses, $args );

		/**
		 * Filter the where clause before setting up the query.
		 *
		 * @param array  $clauses Query clauses.
		 * @param array  $args Query arguments.
		 * @param static $this Current instance of the class.
		 *
		 * @since 1.0.0
		 */
		return apply_filters( $this->get_hook_prefix() . '_setup_where_query', $clauses, $args, $this );
	}

	/**
	 * Prepare meta query.
	 *
	 * @param array $clauses Query clauses.
	 * @param array $args Array of args to pass to the query method.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	protected function prepare_meta_query( $clauses, $args = array() ) {
		if ( ! static::META_TYPE ) {
			return $clauses;
		}

		/**
		 * Filter the meta query before setting up the query.
		 *
		 * @param array  $clauses Query clauses.
		 * @param array  $args Query arguments.
		 * @param static $this Current instance of the class.
		 *
		 * @since 1.0.0
		 * @return array
		 */
		$clauses = apply_filters( $this->get_hook_prefix() . '_pre_setup_meta_query', $clauses, $args, $this );

		if ( ! empty( $args['meta_query'] ) ) {
			$meta_query = new \WP_Meta_Query( $args['meta_query'] );
			$meta_query->parse_query_vars( $args );
			if ( ! empty( $meta_query->queries ) ) {
				$meta_clauses      = $meta_query->get_sql( static::META_TYPE, $this->table_alias, 'id' );
				$clauses['join']  .= $meta_clauses['join'];
				$clauses['where'] .= $meta_clauses['where'];
				if ( $meta_query->has_or_relation() ) {
					$clauses['groupby'] .= empty( $clauses['groupby'] ) ? $this->table_alias . '.id' : ', ' . $this->table_alias . '.id';
				}
			}
		}

		/**
		 * Filter the meta query after setting up the query.
		 *
		 * @param array  $clauses Query clauses.
		 * @param array  $args Query arguments.
		 * @param static $this Current instance of the class.
		 *
		 * @since 1.0.0
		 * @return array
		 */
		return apply_filters( $this->get_hook_prefix() . '_setup_meta_query', $clauses, $args, $this );
	}

	/**
	 * Prepare date query.
	 *
	 * @param array $clauses Query clauses.
	 * @param array $args Array of args to pass to the query method.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	protected function prepare_date_query( $clauses, $args = array() ) {
		/**
		 * Filter the date query before setting up the query.
		 *
		 * @param array  $clauses Query clauses.
		 * @param array  $args Query arguments.
		 * @param static $this Current instance of the class.
		 *
		 * @since 1.0.0
		 * @return array
		 */
		$clauses = apply_filters( $this->get_hook_prefix() . '_pre_setup_date_query', $clauses, $args, $this );

		if ( ! empty( $args['date_query'] ) ) {
			$wp_columns = array();
			// Whitelist our column.
			add_filter(
				'date_query_valid_columns',
				function ( $cols ) use ( $wp_columns ) {
					$wp_columns = $cols;

					return $this->get_columns();
				}
			);

			foreach ( $args['date_query'] as $date_query ) {
				$date_query = wp_parse_args(
					$date_query,
					array(
						'column'    => '',
						'after'     => '',
						'before'    => '',
						'inclusive' => true,
					)
				);

				if ( empty( $date_query['column'] ) || ! in_array( $date_query['column'], $this->get_columns(), true ) ) {
					continue;
				}

				$date_query = new \WP_Date_Query( $date_query );
				if ( ! empty( $date_query->queries ) ) {
					$clauses['where'] .= $date_query->get_sql();
				}
			}

			// Restore the original columns.
			add_filter(
				'date_query_valid_columns',
				function ( $cols ) use ( $wp_columns ) {
					return $wp_columns;
				}
			);
		}

		/**
		 * Filter the date query after setting up the query.
		 *
		 * @param array  $clauses Query clauses.
		 * @param array  $args Query arguments.
		 * @param static $this Current instance of the class.
		 *
		 * @since 1.0.0
		 * @return array
		 */
		return apply_filters( $this->get_hook_prefix() . '_setup_date_query', $clauses, $args, $this );
	}

	/**
	 * Prepare search query.
	 *
	 * @param array $clauses Query clauses.
	 * @param array $args Array of args to pass to the query method.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	protected function prepare_search_query( $clauses, $args = array() ) {
		global $wpdb;
		/**
		 * Filter the search query before setting up the query.
		 *
		 * @param array  $clauses Query clauses.
		 * @param array  $args Query arguments.
		 * @param static $this Current instance of the class.
		 *
		 * @since 1.0.0
		 * @return array
		 */
		$clauses = apply_filters( $this->get_hook_prefix() . '_pre_setup_search_query', $clauses, $args, $this );

		if ( ! empty( $args['search'] ) ) {
			$search = $args['search'];
			if ( ! empty( $args['search_columns'] ) ) {
				$search_columns = wp_parse_list( $args['search_columns'] );
			} else {
				$search_columns = is_null( $this->search_columns ) ? $this->get_columns() : $this->search_columns;
				/**
				 * Filter the columns to search in when performing a search query.
				 *
				 * @param array  $search_columns Array of columns to search in.
				 * @param array  $args Query arguments.
				 * @param static $instance Current instance of the class.
				 *
				 * @since 1.0.0
				 * @return array
				 */
				$search_columns = apply_filters( $this->get_hook_prefix() . '_search_columns', $search_columns, $args, $this );
			}
			$search_columns = array_filter( array_unique( $search_columns ) );
			$like           = '%' . $wpdb->esc_like( $search ) . '%';

			$search_clauses = array();
			foreach ( $search_columns as $column ) {
				$search_clauses[] = $wpdb->prepare( $this->table_alias . '.' . $column . ' LIKE %s', $like ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			}

			if ( ! empty( $search_clauses ) ) {
				$clauses['where'] .= 'AND (' . implode( ' OR ', $search_clauses ) . ')';
			}
		}

		/**
		 * Filter the search query after setting up the query.
		 *
		 * @param array  $clauses Query clauses.
		 * @param array  $args Query arguments.
		 * @param static $this Current instance of the class.
		 *
		 * @since 1.0.0
		 * @return array
		 */
		return apply_filters( $this->get_hook_prefix() . '_setup_search_query', $clauses, $args, $this );
	}

	/**
	 * Prepare join query.
	 *
	 * @param array $clauses Query clauses.
	 * @param array $args Array of args to pass to the query method.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	protected function prepare_join_query( $clauses, $args = array() ) {
		/**
		 * Filter the join query before setting up the query.
		 *
		 * @param array  $clauses Query clauses.
		 * @param array  $args Query arguments.
		 * @param static $this Current instance of the class.
		 *
		 * @since 1.0.0
		 * @return array
		 */

		$clauses = apply_filters( $this->get_hook_prefix() . '_pre_setup_join_query', $clauses, $args, $this );

		/**
		 * Filter the join query after setting up the query.
		 *
		 * @param array  $clauses Query clauses.
		 * @param array  $args Query arguments.
		 * @param static $this Current instance of the class.
		 *
		 * @since 1.0.0
		 * @return array
		 */
		return apply_filters( $this->get_hook_prefix() . '_setup_join_query', $clauses, $args, $this );
	}

	/**
	 * Prepare group by query.
	 *
	 * @param array $clauses Query clauses.
	 * @param array $args Array of args to pass to the query method.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	protected function prepare_group_by_query( $clauses, $args = array() ) {
		/**
		 * Filter the group by query before setting up the query.
		 *
		 * @param array  $clauses Query clauses.
		 * @param array  $args Query arguments.
		 * @param static $this Current instance of the class.
		 *
		 * @since 1.0.0
		 * @return array
		 */
		$clauses = apply_filters( $this->get_hook_prefix() . '_pre_setup_group_by_query', $clauses, $args, $this );

		/**
		 * Filter the group by query after setting up the query.
		 *
		 * @param array  $clauses Query clauses.
		 * @param array  $args Query arguments.
		 * @param static $this Current instance of the class.
		 *
		 * @since 1.0.0
		 * @return array
		 */
		return apply_filters( $this->get_hook_prefix() . '_setup_group_by_query', $clauses, $args, $this );
	}

	/**
	 * Prepare having query.
	 *
	 * @param array $clauses Query clauses.
	 * @param array $args Array of args to pass to the query method.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	protected function prepare_having_query( $clauses, $args = array() ) {
		/**
		 * Filter the having query before setting up the query.
		 *
		 * @param array  $clauses Query clauses.
		 * @param array  $args Query arguments.
		 * @param static $this Current instance of the class.
		 *
		 * @since 1.0.0
		 * @return array
		 */
		$clauses = apply_filters( $this->get_hook_prefix() . '_pre_setup_having_query', $clauses, $args, $this );

		/**
		 * Filter the having query after setting up the query.
		 *
		 * @param array  $clauses Query clauses.
		 * @param array  $args Query arguments.
		 * @param static $this Current instance of the class.
		 *
		 * @since 1.0.0
		 * @return array
		 */
		return apply_filters( $this->get_hook_prefix() . '_setup_having_query', $clauses, $args, $this );

	}

	/**
	 * Prepare order by query.
	 *
	 * @param array $clauses Query clauses.
	 * @param array $args Array of args to pass to the query method.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	protected function prepare_order_by_query( $clauses, $args = array() ) {
		/**
		 * Filter the order by query before setting up the query.
		 *
		 * @param array  $clauses Query clauses.
		 * @param array  $args Query arguments.
		 * @param static $this Current instance of the class.
		 *
		 * @since 1.0.0
		 * @return array
		 */
		$clauses = apply_filters( $this->get_hook_prefix() . '_pre_setup_order_by_query', $clauses, $args, $this );

		// Check if order is already a sql clause.
		if ( empty( $clauses['orderby'] ) ) {
			if ( 'rand' === $args['orderby'] ) {
				$clauses['orderby'] = 'RAND()';
			} elseif ( ! empty( $args['orderby'] ) ) {
				$orderby = $args['orderby'];
				$order   = strtoupper( $args['order'] );
				if ( ! is_array( $orderby ) ) {
					// convert comma separated string to associative array.
					$orderby = explode( ',', $orderby );
					$orderby = array_map( 'trim', $orderby );
					foreach ( $orderby as $key => $value ) {
						$value                = explode( ' ', $value );
						$orderby[ $value[0] ] = isset( $value[1] ) ? $value[1] : $order;
						unset( $orderby[ $key ] );
					}
				}
				foreach ( $orderby as $key => $value ) {
					if ( ! in_array( strtoupper( $value ), array( 'ASC', 'DESC' ), true ) ) {
						$orderby[ $key ] = 'ASC';
					}
				}
				foreach ( $orderby as $key => $value ) {
					if ( in_array( $key, $this->get_columns(), true ) ) {
						$clauses['orderby'] .= "{$this->table_alias}.$key $value";
					}
				}
			} else {
				$clauses['orderby'] .= "{$this->table_alias}.id {$args['order']}";
			}
		}

		/**
		 * Filter the order by query after setting up the query.
		 *
		 * @param array  $clauses Query clauses.
		 * @param array  $args Query arguments.
		 * @param static $this Current instance of the class.
		 *
		 * @since 1.0.0
		 * @return array
		 */
		return apply_filters( $this->get_hook_prefix() . '_setup_order_by_query', $clauses, $args, $this );
	}

	/**
	 * Prepare limit query.
	 *
	 * @param array $clauses Query clauses.
	 * @param array $args Array of args to pass to the query method.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	protected function prepare_limit_query( $clauses, $args = array() ) {
		/**
		 * Filter the limit query before setting up the query.
		 *
		 * @param array  $clauses Query clauses.
		 * @param array  $args Query arguments.
		 * @param static $instance Current instance of the class.
		 *
		 * @since 1.0.0
		 * @return array
		 */
		$clauses = apply_filters( $this->get_hook_prefix() . '_pre_setup_limit_query', $clauses, $args, $this );

		// Limit clause.
		if ( intval( $args['per_page'] ) > 0 ) {
			$page = intval( $args['paged'] );
			if ( ! $page ) {
				$page = 1;
			}
			// If 'offset' is provided, it takes precedence over 'paged'.
			if ( isset( $args['offset'] ) && is_numeric( $args['offset'] ) ) {
				$args['offset'] = absint( $args['offset'] );
				$pgstrt         = $args['offset'] . ', ';
			} else {
				$pgstrt = absint( ( $page - 1 ) * $args['per_page'] ) . ', ';
			}

			$clauses['limit'] = $pgstrt . absint( $args['per_page'] );
		}

		/**
		 * Filter the limit query after setting up the query.
		 *
		 * @param array  $clauses Query clauses.
		 * @param array  $args Query arguments.
		 * @param static $instance Current instance of the class.
		 *
		 * @since 1.0.0
		 * @return array
		 */
		return apply_filters( $this->get_hook_prefix() . '_setup_limit_query', $clauses, $args, $this );
	}

	/*
	|--------------------------------------------------------------------------
	| Helpers
	|--------------------------------------------------------------------------
	|
	| Methods which do not modify class properties but are used by the class.
	|
	*/

	/**
	 * Get the hook prefix.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_hook_prefix() {
		return 'ever_accounting_' . static::OBJECT_TYPE;
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

	/**
	 * Compare if the value of 2 given data is equal. If the data is an array, it will be serialized before comparing.
	 * This is useful for comparing metadata.
	 *
	 * @param mixed $data1 First data to compare.
	 * @param mixed $data2 Second data to compare.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_equal( $data1, $data2 ) {
		$temp = array( $data1, $data2 );
		foreach ( $temp as $key => $value ) {
			if ( is_scalar( $value ) ) {
				$temp[ $key ] = (string) $value;
			} else {
				$temp[ $key ] = maybe_serialize( $value );
			}
		}

		return $temp[0] === $temp[1];
	}

	/**
	 * Convert string to boolean.
	 *
	 * @param string $value Value to convert.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function string_to_bool( $value ) {
		if ( is_bool( $value ) ) {
			return $value;
		}

		if ( is_numeric( $value ) ) {
			return (bool) $value;
		}

		if ( is_string( $value ) ) {
			$value = strtolower( $value );
			if ( 'true' === $value || 'yes' === $value || '1' === $value ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Convert boolean to string.
	 *
	 * @param bool $value Value to convert.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function bool_to_string( $value ) {
		if ( is_string( $value ) ) {
			if ( 'true' === $value || 'yes' === $value || '1' === $value ) {
				return 'yes';
			}

			return 'no';
		}

		if ( is_numeric( $value ) ) {
			return (bool) $value ? 'yes' : 'no';
		}

		if ( is_bool( $value ) ) {
			return $value ? 'yes' : 'no';
		}

		return 'no';
	}

	/**
	 * Convert string to integer.
	 *
	 * @param string $value Value to convert.
	 *
	 * @since 1.0.0
	 * @return int
	 */
	public function string_to_int( $value ) {
		if ( is_int( $value ) ) {
			return $value;
		}

		if ( is_numeric( $value ) ) {
			return (int) $value;
		}

		if ( is_bool( $value ) ) {
			return $value ? 1 : 0;
		}

		if ( is_string( $value ) ) {
			return (int) preg_replace( '/[^0-9]/', '', $value );
		}

		return 0;
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

	/**
	 * Sanitize date property.
	 * If the date is a valid date, it will be returned to the given format.
	 *
	 * @param string $date Date.
	 *
	 * @since 1.0.0
	 * @return string|null
	 */
	public function sanitize_date( $date ) {
		if ( empty( $date ) || '0000-00-00 00:00:00' === $date || '0000-00-00' === $date ) {
			return null;
		}

		if ( ! $this->is_date_valid( $date ) ) {
			return null;
		}

		// get the date format from the given date.
		$length = strlen( $date );
		switch ( $length ) {
			case 8:
				$format = 'H:i:s';
				break;
			case 10:
				$format = 'Y-m-d';
				break;
			case 19:
			default:
				$format = 'Y-m-d H:i:s';
				break;
		}

		$d = \DateTime::createFromFormat( $format, $date );

		return $d && $d->format( $format ) === $date ? $d->format( $format ) : null;
	}

	/**
	 * Merge changes with data and clear.
	 *
	 * @since 1.0.0
	 */
	protected function apply_changes() {
		$this->data    = array_replace_recursive( $this->data, $this->changes );
		$this->changes = array();

		if ( static::META_TYPE && ! empty( $this->metadata ) ) {
			$this->metadata = array_map(
				function ( $meta ) {
					$meta->initial = $meta->value;

					return $meta;
				},
				$this->metadata
			);
		}
	}

	/**
	 * Is valid column.
	 *
	 * @param string $column Column name.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	protected function is_valid_column( $column ) {
		return in_array( $column, $this->get_columns(), true );
	}

	/**
	 * Get core data.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_core_data() {
		return wp_array_slice_assoc( array_replace_recursive( $this->data, $this->changes ), array_keys( $this->core_data ) );
	}

	/**
	 * Get core data keys.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_columns() {
		return array_merge( array( 'id' ), array_keys( $this->core_data ) );
	}

	/**
	 * Set all props to default values.
	 *
	 * @since 1.0.0
	 * @return static
	 */
	public function set_defaults() {
		$this->data          = array_merge_recursive( $this->core_data, $this->extra_data );
		$this->changes       = array();
		$this->object_read   = false;
		$this->metadata_read = false;
		$this->metadata      = array();

		return $this;
	}

	/**
	 * Reset cached data.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function remove_cache() {
		wp_cache_delete( static::CACHE_GROUP );
	}
}
