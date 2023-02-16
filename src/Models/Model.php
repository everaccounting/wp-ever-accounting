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
	 * @since 1.0.0
	 * @var string
	 */
	protected $table_name = null;

	/**
	 * Object type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $object_type = '';

	/**
	 * Object type.
	 *
	 * @since 1.0.0
	 * @var string Cache group.
	 */
	protected $cache_group = '';

	/**
	 * The primary key for the model.
	 *
	 * @since 1.0.0
	 * @var string Primary key.
	 */
	protected $id_key = 'id';

	/**
	 * The "type" of the primary key ID.
	 *
	 * @since 1.0.0
	 * @var string Primary key type.
	 */
	protected $id_key_type = 'int';

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
	 * Core data for this object. Name value pairs (name + default value).
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
	 * Meta type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $metatype = false;

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
	 * Constructor.
	 *
	 * @param int|object|array $data Object ID, post object, or array of data.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $data = 0 ) {
		global $wpdb;
		$called_class = get_called_class();
		$this->table  = $wpdb->prefix . $this->table_name;
		$this->data   = array_merge( $this->core_data, $this->extra_data );

		// check if core data is set. If not throw an error.
		if ( empty( $this->core_data ) ) {
			_doing_it_wrong( esc_html( $called_class ), esc_html__( 'You must define core data for the model.', 'wp-ever-accounting' ), '1.0.0' );
		}
		// check if table name is set. If not throw an error.
		if ( empty( $this->table_name ) ) {
			_doing_it_wrong( esc_html( $called_class ), esc_html__( 'You must define a table name for the model.', 'wp-ever-accounting' ), '1.0.0' );
		}
		// check if id key is set. If not throw an error.
		if ( empty( $this->id_key ) ) {
			_doing_it_wrong( esc_html( $called_class ), esc_html__( 'You must define an id key for the model.', 'wp-ever-accounting' ), '1.0.0' );
		}
		// check if id key type is set. If not throw an error.
		if ( empty( $this->id_key_type ) ) {
			_doing_it_wrong( esc_html( $called_class ), esc_html__( 'You must define an id key type for the model.', 'wp-ever-accounting' ), '1.0.0' );
		}
		// Check if object type is set. If not throw an error.
		if ( empty( $this->object_type ) ) {
			_doing_it_wrong( esc_html( $called_class ), esc_html__( 'You must define an object type for the model.', 'wp-ever-accounting' ), '1.0.0' );
		}

		if ( $this->metatype && ! isset( $wpdb->{$this->metatype . 'meta'} ) ) {
			$wpdb->{$this->metatype . 'meta'} = $wpdb->prefix . $this->metatype . 'meta';
		}
		if ( ! $this->cache_group ) {
			$this->cache_group = $this->table_name;
		}
		if ( ! $this->object_type ) {
			$this->object_type = strtolower( str_replace( 'EverAccounting\\', '', $called_class ) );
		}

		if ( is_scalar( $data ) ) {
			$this->set_id( $data );
		} elseif ( $data instanceof $called_class ) {
			$this->set_id( $data->get_id() );
		} elseif ( is_object( $data ) && ! empty( $data->{$this->id_key} ) ) {
			$this->set_id( $data->{$this->id_key} );
		} elseif ( is_array( $data ) && ! empty( $data[ $data->{$this->id_key} ] ) ) {
			$this->set_id( $data[ $data->{$this->id_key} ] );
		} else {
			$this->set_object_read( true );
		}

		$this->read();
	}

	/**
	 * Remove meta-type from database.
	 * This is used to remove the metatype from the database.
	 *
	 * @since 1.0.0
	 */
	public function __destruct() {
		global $wpdb;
		if ( $this->metatype && isset( $wpdb->{$this->metatype . 'meta'} ) ) {
			unset( $wpdb->{$this->metatype . 'meta'} );
		}
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
		if ( ! empty( $this->metadata ) ) {
			foreach ( $this->metadata as $key => $value ) {
				$this->metadata[ $key ] = clone $value;
			}
		}
		$this->set_id( 0 );
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
	 * @param string $key Prop to set.
	 * @param mixed  $value Value to set.
	 *
	 * @since  1.0.0
	 */
	public function __set( $key, $value ) {
		if ( $this->id_key === $key ) {
			$this->set_id( $value );
		}
		$this->set_props( array( $key => $value ) );
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

	/**
	 * Handles statically calling  methods.
	 *
	 * @param string $name Method name.
	 * @param array  $args Method arguments.
	 *
	 * @since 1.0.0
	 * @return mixed
	 */
	public static function __callStatic( $name, $args ) {
		$instance = new static();
		if ( method_exists( $instance, $name ) ) {
			return call_user_func_array( array( $instance, $name ), $args );
		}

		_doing_it_wrong( __CLASS__ . '::' . esc_html( $name ), esc_html__( 'Method does not exist.', 'wp-ever-accounting' ), '1.0.0' );

		return null;
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	|
	| Methods for getting data from the object.
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
	 * Get the id key.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_id_key() {
		return $this->id_key;
	}

	/**
	 * Get the table name.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_table_name() {
		return $this->table_name;
	}

	/**
	 * Get Object Type.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_object_type() {
		return $this->object_type;
	}

	/**
	 * Get the meta type.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_metatype() {
		return $this->metatype;
	}

	/**
	 * Get the table.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_table() {
		return $this->table;
	}

	/**
	 * Get cache group.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_cache_group() {
		return $this->cache_group;
	}

	/**
	 * Get the table alias.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_table_alias() {
		return $this->table_name;
	}

	/**
	 * Get search columns.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_searchable_columns() {
		return $this->get_core_data_keys();
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
	public function get_core_data_keys() {
		return array_merge( array( $this->id_key ), array_keys( $this->core_data ) );
	}

	/**
	 * Get extra data.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_extra_data() {
		return wp_array_slice_assoc( array_replace_recursive( $this->data, $this->changes ), array_keys( $this->extra_data ) );
	}

	/**
	 * Get extra data keys.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_extra_data_keys() {
		return array_keys( $this->extra_data );
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
	 * Returns all data for this object.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public function get_data() {
		$data = array_replace_recursive( [ $this->id_key => $this->get_id() ], $this->data, $this->changes );
		if ( $this->metatype ) {
			$data['metadata'] = $this->get_metadata();
		}

		return $data;
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
		}

		if ( 'view' === $context ) {
			$value = apply_filters( $this->get_hook_prefix() . '_get_' . $prop, $value, $this );
		}

		return $value;
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
	 * Return data changes only.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_changes() {
		return $this->changes;
	}

	/**
	 * Get hook prefix.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_hook_prefix() {
		return 'eaccounting_' . $this->object_type;
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
	 * @return $this
	 */
	protected function set_id( $id ) {
		switch ( $this->id_key_type ) {
			case 'int':
				$this->id = absint( $id );
				break;
			case 'string':
				$this->id = (string) $id;
				break;
			default:
				$this->id = $id;
				break;
		}

		return $this;
	}

	/**
	 * Set object read property.
	 *
	 * @param boolean $read Should read?.
	 *
	 * @since 1.0.0
	 * @return static
	 */
	public function set_object_read( $read = true ) {
		$this->object_read = (bool) $read;

		return $this;
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
		$this->metadata_read = false;
		$this->object_read   = false;

		return $this;
	}

	/**
	 * Set a collection of props in one go, collect any errors, and return the result.
	 * Only sets using public methods.
	 *
	 * @param array|object $props Key value pairs to set. Key is the prop and should map to a setter function name.
	 *
	 * @since  1.0.0
	 * @return static
	 */
	public function set_props( $props ) {
		if ( is_object( $props ) ) {
			$props = get_object_vars( $props );
		}
		if ( ! is_array( $props ) ) {
			return $this;
		}
		foreach ( $props as $prop => $value ) {
			$prop = preg_replace( '/^[^a-zA-Z]+/', '', $prop );
			// if value is array, call the same function for each item.
			if ( 'metadata' === $prop && is_array( $value ) ) {
				$this->set_props( $value );

				return $this;
			} elseif ( $prop === $this->id_key ) {
				$this->set_id( $value );
			} elseif ( is_callable( array( $this, "set_$prop" ) ) ) {
				$this->{"set_$prop"}( $value );
			} else {
				$this->set_prop( $prop, $value );
			}
		}

		return $this;
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
	 * Set Boolean prop.
	 *
	 * @param string $prop Name of prop to set.
	 * @param bool   $value Value of prop.
	 *
	 * @since 1.0.0
	 */
	public function set_boolean_prop( $prop, $value ) {
		$this->set_prop( $prop, (bool) $this->string_to_bool( $value ) );
	}

	/*
	|--------------------------------------------------------------------------
	| Meta Model methods
	|--------------------------------------------------------------------------
	|
	| Meta data related methods.
	|
	*/
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
		$key = preg_replace( '/^[^a-zA-Z]+/', '', $key );
		$key = '_' . $key;
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
		$this->read_metadata();
		$key  = preg_replace( '/^[^a-zA-Z]+/', '', $key );
		$key  = '_' . $key;
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

	/**
	 * Delete meta data.
	 *
	 * @param string $key Meta key.
	 * @param string $value Meta value.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function delete_meta( $key, $value = '' ) {
		$this->read_metadata();
		$key  = preg_replace( '/^[^a-zA-Z]+/', '', $key );
		$key  = '_' . $key;
		$meta = array_filter(
			$this->metadata,
			function ( $meta ) use ( $key ) {
				return $meta->key === $key;
			}
		);

		if ( empty( $meta ) ) {
			return;
		}

		if ( empty( $value ) ) {
			$this->metadata = array_filter(
				$this->metadata,
				function ( $meta ) use ( $key ) {
					return $meta->key !== $key;
				}
			);

			return;
		}

		$this->metadata = array_filter(
			$this->metadata,
			function ( $meta ) use ( $key, $value ) {
				return $meta->key !== $key || $meta->value !== $value;
			}
		);
	}

	/**
	 * Get meta data.
	 *
	 * @since 1.0.0
	 */
	protected function read_metadata() {
		global $wpdb;
		if ( $this->metadata && is_null( $this->metadata ) ) {
			$this->metadata = array();
			// Read metadata based on meta type.
			$table           = $wpdb->prefix . $this->metatype . 'meta';
			$object_id_field = $this->metatype . '_id';
			$meta_id_field   = 'user' === $this->metatype ? 'umeta_id' : 'meta_id';
			$cache_key       = $this->metatype . '_meta';
			$cache_group     = $this->metatype . '_meta';
			$meta            = wp_cache_get( $this->get_id(), $cache_key, $cache_group );

			if ( false === $meta ) {
				$meta = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT $meta_id_field as meta_id, meta_key, meta_value FROM $table WHERE $object_id_field = %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- $object_id_field is a prepared value.
						$this->get_id()
					)
				);
				wp_cache_set( $this->get_id(), $meta, $cache_key, $cache_group );
			}

			if ( $meta ) {
				foreach ( $meta as $meta_item ) {
					$this->metadata[] = (object) array(
						'id'      => $meta_item->meta_id,
						'key'     => $meta_item->meta_key,
						'initial' => maybe_serialize( $meta_item->meta_value ),
						'value'   => maybe_serialize( $meta_item->meta_value ),
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
		if ( $this->metatype ) {
			$table           = $wpdb->prefix . $this->metatype . 'meta';
			$object_id_field = $this->metatype . '_id';
			$cache_key       = $this->metatype . '_meta';
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
	protected function save_metadata() {
		if ( ! $this->metatype ) {
			return;
		}
		$this->read_metadata();
		// Get changed meta data.
		$changed_metadata = array_filter(
			$this->metadata,
			function ( $meta ) {
				// no type checking here, because we want to treat 0 and '0' as equal.
				return $meta->initial != $meta->value; // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
			}
		);

		// If id is not set, we need to add the metadata. if id is set, we need to update the metadata. if value is null, we need to delete the meta data.
		foreach ( $changed_metadata as $key => $meta ) {
			if ( is_null( $meta->id ) && ! empty( $meta->value ) ) {
				$meta_id                         = add_metadata( $this->metatype, $this->get_id(), $meta->key, $meta->value );
				$this->metadata[ $key ]->id      = $meta_id;
				$this->metadata[ $key ]->initial = $meta->value;
			} elseif ( is_null( $meta->value ) && ! is_null( $meta->id ) ) {
				delete_metadata_by_mid( $this->metatype, $meta->id );
				unset( $this->metadata[ $key ] );
			} else {
				update_metadata_by_mid( $this->metatype, $meta->id, $meta->value );
				$this->metadata[ $key ]->initial = $meta->value;
			}
		}

		// Clear cache.
		wp_cache_delete( $this->get_id(), $this->metatype . '_meta' );
	}

	/*
	|--------------------------------------------------------------------------
	| Conditionals.
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
	| Helpers
	|--------------------------------------------------------------------------
	|
	| Helper methods.
	|
	*/
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
			return $value;
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

		if ( $this->metatype && ! empty( $this->metadata ) ) {
			$this->metadata = array_map(
				function ( $meta ) {
					$meta->initial = $meta->value;

					return $meta;
				},
				$this->metadata
			);
		}

		return $this;
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
		return in_array( $column, $this->get_core_data_keys() );
	}

	/**
	 * Sanitizes the data.
	 *
	 * @since 1.0.0
	 * @return \WP_Error|true
	 */
	protected function sanitize_data() {
		/**
		 * Filters whether an item should be checked.
		 *
		 * @param self $item Model object.
		 *
		 * @since 1.0.0
		 */
		$check = apply_filters( $this->get_hook_prefix() . '_sanitize_data', null, $this );
		if ( null !== $check ) {
			return $check;
		}

		return true;
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
		 * @param self $item Model object.
		 *
		 * @since 1.0.0
		 */
		do_action( $this->get_hook_prefix() . '_pre_insert', $this );
		foreach ( $data as $key => $value ) {
			if ( ! is_scalar( $value ) ) {
				$data[ $key ] = maybe_serialize( $value );
			}
		}

		if ( false === $wpdb->insert( $this->table, $data, array() ) ) {
			return new \WP_Error( 'db_insert_error', __( 'Could not insert item into the database.', 'wp-ever-accounting' ), $wpdb->last_error );
		}

		$this->set_id( $wpdb->insert_id );
		$this->apply_changes();
		$this->save_metadata();
		$this->set_object_read( true );

		/**
		 * Fires immediately after an item is inserted in the database.
		 *
		 * @param self $item Model object.
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

		$data = wp_cache_get( $this->get_id(), $this->cache_group );
		if ( false === $data ) {
			$data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $this->table WHERE {$this->id_key} = %d LIMIT 1;", $this->get_id() ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			wp_cache_add( $this->get_id(), $data, $this->cache_group );
		}

		if ( ! $data ) {
			$this->set_id( 0 );

			return false;
		}

		foreach ( $data as $key => $value ) {
			if ( ! is_scalar( $value ) ) {
				$data->$key = maybe_unserialize( $value );
			}
		}

		$this->set_props( $data );
		$this->set_object_read( true );
		do_action( $this->get_hook_prefix() . '_item', $this->get_id(), $this );

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
		 * @param self $item Model object.
		 * @param array $changes The data will be updated.
		 *
		 * @since 1.0.0
		 */
		do_action( $this->get_hook_prefix() . '_pre_update', $this, $changes );

		$data = wp_array_slice_assoc( $changes, $this->get_core_data_keys() );
		if ( ! empty( $data ) ) {
			foreach ( $data as $key => $value ) {
				if ( ! is_scalar( $value ) ) {
					$data[ $key ] = maybe_serialize( $value );
				}
			}
			if ( false === $wpdb->update( $this->table, $data, [ $this->id_key => $this->get_id() ], array(), [ $this->id_key => '%d' ] ) ) {
				return new \WP_Error( 'db_update_error', __( 'Could not update item in the database.', 'wp-ever-accounting' ), $wpdb->last_error );
			}
		}

		/**
		 * Fires immediately after an existing item is updated in the database.
		 *
		 * @param self $item Model object.
		 * @param array $changes The data will be updated.
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
		 * @param self $item Model object.
		 * @param array $data Model data array.
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
		 * @param self $item Model object.
		 * @param array $data Model data array.
		 *
		 * @since 1.0.0
		 */
		do_action( $this->get_hook_prefix() . '_pre_delete', $this, $data );

		global $wpdb;

		$wpdb->delete(
			$this->table,
			array(
				$this->id_key => $this->get_id(),
			),
			array( '%d' )
		);

		$this->delete_metadata();

		/**
		 * Fires after a item is deleted.
		 *
		 * @param self $item Model object.
		 * @param array $data Model data array.
		 *
		 * @since 1.0.0
		 */
		do_action( $this->get_hook_prefix() . '_deleted', $this, $data );

		wp_cache_delete( $this->get_id(), $this->cache_group );
		wp_cache_set( 'last_changed', microtime(), $this->cache_group );
		$this->set_defaults();

		return $data;
	}


	/**
	 * Saves an object in the database.
	 *
	 * @since 1.0.0
	 * @return static|\WP_Error Object instance on success, WP_Error on failure.
	 */
	public function save() {

		// Check if the object is valid.
		$check = $this->sanitize_data();
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
		wp_cache_delete( $this->get_id(), $this->cache_group );
		wp_cache_set( 'last_changed', microtime(), $this->cache_group );

		/**
		 * Fires immediately after a key is inserted or updated in the database.
		 *
		 * @param int $id Key id.
		 * @param static $object The object.
		 *
		 * @since 1.0.0
		 */
		do_action( $this->get_hook_prefix() . '_saved', $this );

		return $this;
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
	 * @param int    $id Object id to retrieve.
	 * @param string $by Optional. The field to retrieve the object by. Default 'id'.
	 * @param array  $args Optional. Additional arguments to pass to the query.
	 *
	 * @since 1.0.0
	 *
	 * @return static|false Object instance on success, false on failure.
	 */
	protected function get( $id, $by = null, $args = array() ) {
		if ( ! $id ) {
			return false;
		}

		if ( is_object( $id ) && method_exists( $id, 'get_' . $this->get_id_key() ) ) {
			$id = $id->{'get_' . $this->get_id_key()}();
		} elseif ( is_array( $id ) && isset( $id[ $this->get_id_key() ] ) ) {
			$id = $id[ $this->get_id_key() ];
		} elseif ( is_numeric( $id ) ) {
			$id = absint( $id );
		} else {
			$id = sanitize_text_field( $id );
		}

		if ( is_null( $by ) || $this->get_id_key() === $by ) {
			$object = new static( $id );
			if ( $object->exists() ) {
				return $object;
			}

			return null;
		}

		if ( in_array( $by, $this->get_core_data_keys(), true ) ) {
			$args  = array_merge(
				$args,
				array(
					'no_count' => true,
					$by        => $id,
				)
			);
			$items = $this->query( $args );

			if ( ! empty( $items ) && is_array( $items ) ) {
				return reset( $items );
			}
		}

		return null;
	}

	/**
	 * Insert or update an object in the database.
	 *
	 * @param array|object $data Model to insert or update.
	 * @param boolean      $wp_error Optional. Whether to return a WP_Error on failure. Default false.
	 *
	 * @return self|false Object, false otherwise.
	 */
	protected function insert( $data, $wp_error = true ) {
		if ( is_object( $data ) ) {
			$data = get_object_vars( $data );
		}

		if ( ! is_array( $data ) || empty( $data ) ) {
			return false;
		}

		$id     = isset( $data[ $this->id_key ] ) ? $data[ $this->id_key ] : null;
		$object = new static( $id );
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
	protected function count( $args = array() ) {
		$args['count'] = true;

		return $this->query( $args );
	}

	/**
	 * Query for objects.
	 *
	 * @param array $args Array of args to pass to the query method.
	 *
	 * @since 1.0.0
	 * @return int|self[]|object[]|int[]|string[] Query results.
	 */
	protected function query( $args = array() ) {
		global $wpdb;
		$args       = $this->prepare_query_args( $args );
		$is_count   = $this->string_to_bool( $args['count'] );
		$no_count   = $this->string_to_bool( $args['no_count'] );
		$return_raw = isset( $args['return'] ) && 'raw' === $args['return'];
		unset( $args['count'], $args['no_count'], $args['return'] );
		$clauses      = $this->get_query_clauses( $args );
		$clauses = $this->prepare_query_clauses( $clauses, $args );
		$last_changed = wp_cache_get_last_changed( $this->get_cache_group() );
		$cache_key    = $this->get_cache_group() . ':' . md5( wp_json_encode( $clauses ) ) . ':' . $last_changed;
		$result       = wp_cache_get( $cache_key, $this->get_cache_group() );

		if ( false !== $result ) {
			return $is_count ? absint( $result->total ) : $result->items;
		}

		// Go through each clause and add it to the query.
		$query = '';
		foreach ( $clauses as $type => $clause ) {
			if ( ! empty( $clause ) ) {
				if ( 'select' === $type ) {
					$clause = implode( ',', (array) $clause );
					$clause = $is_count ? 'SQL_CALC_FOUND_ROWS ' . $clause : $clause;
					$query .= 'SELECT ' . $clause;
				} elseif ( 'from' === $type ) {
					$clause = implode( ',', (array) $clause );
					$query .= ' FROM ' . $clause;
				} elseif ( 'join' === $type ) {
					$clause = implode( ',', (array) $clause );
					$query .= ' ' . $clause;
				} elseif ( 'where' === $type ) {
					$clause = implode( ' ', (array) $clause );
					$query .= ' WHERE 1=1 ' . $clause;
				} elseif ( 'groupby' === $type ) {
					$clause = implode( ',', (array) $clause );
					$query .= ' GROUP BY ' . $clause;
				} elseif ( 'having' === $type ) {
					$clause = implode( ',', (array) $clause );
					$query .= ' Having ' . $clause;
				} elseif ( 'orderby' === $type ) {
					$clause = implode( ',', (array) $clause );
					$query .= ' ORDER BY ' . $clause;
				} elseif ( 'limit' === $type ) {
					$query .= ' LIMIT ' . $clause;
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
			 * @param array $items Query items.
			 * @param array $args Query arguments.
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
		if ( in_array( 'all', $args['fields'], true ) && ! $return_raw ) {
			foreach ( $items as $key => $row ) {
				/**
				 * Filter the query result item.
				 *
				 * @param object $row Query item.
				 * @param array $args Query arguments.
				 *
				 * @since 1.0.0
				 */
				$row = apply_filters( $this->get_hook_prefix() . '_item', $row, $args );

				foreach ( $row as $column => $value ) {
					if ( ! is_scalar( $value ) ) {
						$row->$column = maybe_unserialize( $value );
					}
				}
				$id_key = $this->get_id_key();
				wp_cache_add( $row->$id_key, $row, $this->get_cache_group() );

				$item = new static();
				$item->set_props( $row );
				$item->set_object_read( true );
				$items[ $key ] = $item;
			}
		}

		if ( in_array( 'ids', $args['fields'], true ) ) {
			$items = wp_list_pluck( $items, $this->get_id_key() );
			$items = array_map(
				function ( $id ) {
					return is_numeric( $id ) ? (int) $id : $id;
				},
				$items
			);
		}
		$result = (object) [
			'items' => $items,
			'total' => $total,
		];

		wp_cache_add( $cache_key, $result, $this->get_cache_group() );

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
			'orderby'     => $this->get_id_key(),
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
			'return'      => 'object',
		);

		$args             = wp_parse_args( $args, $default );
		$args['no_count'] = $this->string_to_bool( $args['no_count'] );
		$args['count']    = $this->string_to_bool( $args['count'] );
		$args['fields']   = is_string( $args['fields'] ) ? preg_split( '/[,\s]+/', $args['fields'] ) : $args['fields'];

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
			'select'   => array(),
			'from'     => array(),
			'join'     => array(),
			'where'    => array(),
			'group_by' => array(),
			'orderby'  => array(),
			'limit'    => '',
		);

		/**
		 * Filter the query clauses before setting up the query.
		 *
		 * @param array $clauses Query clauses.
		 * @param array $args Query arguments.
		 * @param self $this Current instance of the class.
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
		 * @param array $clauses Query clauses.
		 * @param array $args Query arguments.
		 * @param self $this Current instance of the class.
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
		 * @param array $clauses Query clauses.
		 * @param array $args Query arguments.
		 * @param self $this Current instance of the class.
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
				$clauses['select'][] = $this->get_table_name() . '.*';
			} elseif ( 'ids' === $field ) {
				$clauses['select'][] = $this->get_table_name() . '.' . $this->get_id_key();
			} elseif ( in_array( $field, $this->get_core_data_keys(), true ) ) {
				$clauses['select'][] = "{$this->get_table_name()}.{$field}";
			}
		}

		/**
		 * Filter the select clause before setting up the query.
		 *
		 * @param array $clauses Query clauses.
		 * @param array $args Query arguments.
		 * @param self $this Current instance of the class.
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

		$clauses['from'][] = "{$this->get_table()} AS {$this->get_table_alias()}";

		/**
		 * Filter the from clause before setting up the query.
		 *
		 * @param array $clauses Query clauses.
		 * @param array $args Query arguments.
		 * @param self $this Current instance of the class.
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
		 * @param array $clauses Query clauses.
		 * @param array $args Query arguments.
		 * @param self $this Current instance of the class.
		 *
		 * @since 1.0.0
		 */
		$clauses = apply_filters( $this->get_hook_prefix() . '_pre_setup_where_query', $clauses, $args, $this );

		$query_where = isset( $args['where_query'] ) ? $args['where_query'] : array();

		// Include clause.
		if ( ! empty( $args['include'] ) ) {
			$query_where[] = array(
				'column'  => "{$this->get_table_name()}.{$this->get_id_key()}",
				'value'   => $args['include'],
				'compare' => 'IN',
			);
		}

		// Exclude clause.
		if ( ! empty( $args['exclude'] ) ) {
			$query_where[] = array(
				'column'  => "{$this->get_table_name()}.{$this->get_id_key()}",
				'value'   => $args['exclude'],
				'compare' => 'NOT IN',
			);
		}

		foreach ( $this->get_core_data_keys() as $column ) {
			// equals clause.
			if ( ! empty( $args[ $column ] ) ) {
				$query_where[] = array(
					'column'  => "{$this->get_table_name()}.{$column}",
					'value'   => $args[ $column ],
					'compare' => '=',
				);
			}

			// __in clause.
			if ( ! empty( $args[ $column . '__in' ] ) ) {
				$query_where[] = array(
					'column'  => "{$this->get_table_name()}.{$column}",
					'value'   => $args[ $column . '__in' ],
					'compare' => 'IN',
				);
			}

			// __not_in clause.
			if ( ! empty( $args[ $column . '__not_in' ] ) ) {
				$query_where[] = array(
					'column'  => "{$this->get_table_name()}.{$column}",
					'value'   => $args[ $column . '__not_in' ],
					'compare' => 'NOT IN',
				);
			}

			// __between clause.
			if ( ! empty( $args[ $column . '__between' ] ) ) {
				$query_where[] = array(
					'column'  => "{$this->get_table_name()}.{$column}",
					'value'   => $args[ $column . '__between' ],
					'compare' => 'BETWEEN',
				);
			}

			// __not_between clause.
			if ( ! empty( $args[ $column . '__not_between' ] ) ) {
				$query_where[] = array(
					'column'  => "{$this->get_table_name()}.{$column}",
					'value'   => $args[ $column . '__not_between' ],
					'compare' => 'NOT BETWEEN',
				);
			}

			// __exists clause.
			if ( ! empty( $args[ $column . '__exists' ] ) ) {
				$query_where[] = array(
					'column'  => "{$this->get_table_name()}.{$column}",
					'compare' => 'EXISTS',
				);
			}

			// __not_exists clause.
			if ( ! empty( $args[ $column . '__not_exists' ] ) ) {
				$query_where[] = array(
					'column'  => "{$this->get_table_name()}.{$column}",
					'compare' => 'NOT EXISTS',
				);
			}

			// __like clause.
			if ( ! empty( $args[ $column . '__like' ] ) ) {
				$query_where[] = array(
					'column'  => "{$this->get_table_name()}.{$column}",
					'value'   => $args[ $column . '__like' ],
					'compare' => 'LIKE',
				);
			}

			// __not_like clause.
			if ( ! empty( $args[ $column . '__not_like' ] ) ) {
				$query_where[] = array(
					'column'  => "{$this->get_table_name()}.{$column}",
					'value'   => $args[ $column . '__not_like' ],
					'compare' => 'NOT LIKE',
				);
			}

			// __starts_with clause.
			if ( ! empty( $args[ $column . '__starts_with' ] ) ) {
				$query_where[] = array(
					'column'  => "{$this->get_table_name()}.{$column}",
					'value'   => $args[ $column . '__starts_with' ],
					'compare' => 'LIKE',
				);
			}

			// __ends_with clause.
			if ( ! empty( $args[ $column . '__ends_with' ] ) ) {
				$query_where[] = array(
					'column'  => "{$this->get_table_name()}.{$column}",
					'value'   => $args[ $column . '__ends_with' ],
					'compare' => 'ENDS WITH',
				);
			}

			// __is_null clause.
			if ( ! empty( $args[ $column . '__is_null' ] ) ) {
				$query_where[] = array(
					'column'  => "{$this->get_table_name()}.{$column}",
					'compare' => 'IS NULL',
				);
			}

			// __is_not_null clause.
			if ( ! empty( $args[ $column . '__is_not_null' ] ) ) {
				$query_where[] = array(
					'column'  => "{$this->get_table_name()}.{$column}",
					'compare' => 'IS NOT NULL',
				);
			}

			// __gt clause.
			if ( ! empty( $args[ $column . '__gt' ] ) ) {
				$query_where[] = array(
					'column'  => "{$this->get_table_name()}.{$column}",
					'value'   => $args[ $column . '__gt' ],
					'compare' => 'GREATER THAN',
				);
			}

			// __lt clause.
			if ( ! empty( $args[ $column . '__lt' ] ) ) {
				$query_where[] = array(
					'column'  => "{$this->get_table_name()}.{$column}",
					'value'   => $args[ $column . '__lt' ],
					'compare' => 'LESS THAN',
				);
			}

			// __regexp clause.
			if ( ! empty( $args[ $column . '__regexp' ] ) ) {
				$query_where[] = array(
					'column'  => "{$this->get_table_name()}.{$column}",
					'value'   => $args[ $column . '__regexp' ],
					'compare' => 'REGEXP',
				);
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
			if ( empty( $where_column ) || ! $this->is_valid_column( str_replace( "{$this->get_table_alias()}.", '', $where_column ) ) ) {
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
					$placeholders       = array_fill( 0, count( $where_value ), '%s' );
					$format             = "AND (  $where_column $where_compare (" . implode( ', ', $placeholders ) . ') )';
					$clauses['where'][] = $wpdb->prepare( $format, $where_value ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
					break;
				case 'BETWEEN':
				case 'NOT BETWEEN':
					if ( empty( $where_value ) || ! is_array( $where_value ) || count( $where_value ) < 2 ) {
						continue 2;
					}
					$placeholder        = wp_is_numeric_array( $where_value ) ? '%d' : '%s';
					$format             = "AND ( $where_column $where_compare $placeholder AND $placeholder )";
					$clauses['where'][] = $wpdb->prepare( $format, $where_value[0], $where_value[1] ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
					break;
				case 'LIKE':
				case 'NOT LIKE':
					$format             = "AND ( $where_column $where_compare %s )";
					$clauses['where'][] = $wpdb->prepare( $format, '%' . $wpdb->esc_like( $where_value ) . '%' ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
					break;
				case 'EXISTS':
				case 'NOT EXISTS':
					$format             = "AND ( $where_compare (SELECT 1 FROM {$this->get_table()} WHERE  $where_column = %s) )";
					$clauses['where'][] = $wpdb->prepare( $format, $where_value ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
					break;
				case 'RLIKE':
					$format             = "AND (  $where_column REGEXP %s )";
					$clauses['where'][] = $wpdb->prepare( $format, $where_value ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
					break;
				case 'ENDS WITH':
					$format             = "AND (  $where_column LIKE %s )";
					$clauses['where'][] = $wpdb->prepare( $format, '%' . $wpdb->esc_like( $where_value ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
					break;
				case 'STARTS WITH':
					$format             = "AND (  $where_column LIKE %s )";
					$clauses['where'][] = $wpdb->prepare( $format, $wpdb->esc_like( $where_value ) . '%' ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
					break;
				case 'IS NULL':
				case 'IS NOT NULL':
					$format             = "AND ( $where_column $where_compare )";
					$clauses['where'][] = $wpdb->prepare( $format, $where_value ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
					break;
				case 'GREATER THAN':
					$placeholder        = is_numeric( $where_value ) ? '%d' : '%s';
					$format             = "AND ( $where_column > $placeholder )";
					$clauses['where'][] = $wpdb->prepare( $format, $where_value ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
					break;
				case 'LESS THAN':
					$placeholder        = is_numeric( $where_value ) ? '%d' : '%s';
					$format             = "AND ( $where_column < $placeholder )";
					$clauses['where'][] = $wpdb->prepare( $format, $where_value ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
					break;
				case 'REGEXP':
				case 'NOT REGEXP':
				default:
					// Placeholder based on type.
					$placeholder        = is_numeric( $where_value ) ? '%d' : '%s';
					$format             = "AND (  $where_column $where_compare $placeholder )";
					$clauses['where'][] = $wpdb->prepare( $format, $where_value ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
					break;
			}
		}

		$clauses = $this->prepare_search_query( $clauses, $args );
		$clauses = $this->prepare_date_query( $clauses, $args );
		$clauses = $this->prepare_meta_query( $clauses, $args );

		/**
		 * Filter the where clause before setting up the query.
		 *
		 * @param array $clauses Query clauses.
		 * @param array $args Query arguments.
		 * @param self $this Current instance of the class.
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
		if ( ! $this->metatype ) {
			return $clauses;
		}

		/**
		 * Filter the meta query before setting up the query.
		 *
		 * @param array $clauses Query clauses.
		 * @param array $args Query arguments.
		 * @param self $this Current instance of the class.
		 *
		 * @since 1.0.0
		 * @return array
		 */
		$clauses = apply_filters( $this->get_hook_prefix() . '_pre_setup_meta_query', $clauses, $args, $this );

		if ( ! empty( $args['meta_query'] ) ) {
			$meta_query = new \WP_Meta_Query( $args['meta_query'] );
			$meta_query->parse_query_vars( $args );
			if ( ! empty( $meta_query->queries ) ) {
				$meta_clauses     = $meta_query->get_sql( $this->metatype, $this->get_table_alias(), $this->get_id_key() );
				$clauses['join']  = array_merge( $clauses['join'], $meta_clauses['join'] );
				$clauses['where'] = array_merge( $clauses['where'], $meta_clauses['where'] );

				if ( $meta_query->has_or_relation() ) {
					$clauses['groupby'][] = $this->get_table_alias() . '.' . $this->get_id_key();
				}
			}
		}

		/**
		 * Filter the meta query after setting up the query.
		 *
		 * @param array $clauses Query clauses.
		 * @param array $args Query arguments.
		 * @param self $this Current instance of the class.
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
		 * @param array $clauses Query clauses.
		 * @param array $args Query arguments.
		 * @param self $this Current instance of the class.
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

					return $this->get_core_data_keys();
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

				if ( empty( $date_query['column'] ) || ! in_array( $date_query['column'], $this->get_core_data_keys(), true ) ) {
					continue;
				}

				$date_query = new \WP_Date_Query( $date_query );
				if ( ! empty( $date_query->queries ) ) {
					$clauses['where'][] = $date_query->get_sql();
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
		 * @param array $clauses Query clauses.
		 * @param array $args Query arguments.
		 * @param self $this Current instance of the class.
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
		 * @param array $clauses Query clauses.
		 * @param array $args Query arguments.
		 * @param self $this Current instance of the class.
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
				/**
				 * Filter the columns to search in when performing a search query.
				 *
				 * @param array $search_columns Array of columns to search in.
				 * @param array $args Query arguments.
				 * @param self $this Current instance of the class.
				 *
				 * @since 1.0.0
				 * @return array
				 */
				$search_columns = apply_filters( $this->get_hook_prefix() . '_search_columns', $this->get_searchable_columns(), $args, $this );
			}
			$search_columns = array_filter( array_unique( $search_columns ) );
			$like           = '%' . $wpdb->esc_like( $search ) . '%';

			$search_clauses = array();
			foreach ( $search_columns as $column ) {
				$search_clauses[] = $wpdb->prepare( $this->get_table_alias() . '.' . $column . ' LIKE %s', $like ); // WPCS: unprepared SQL ok.
			}

			if ( ! empty( $search_clauses ) ) {
				$clauses['where'][] = 'AND (' . implode( ' OR ', $search_clauses ) . ')';
			}
		}

		/**
		 * Filter the search query after setting up the query.
		 *
		 * @param array $clauses Query clauses.
		 * @param array $args Query arguments.
		 * @param self $this Current instance of the class.
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
		 * @param array $clauses Query clauses.
		 * @param array $args Query arguments.
		 * @param self $this Current instance of the class.
		 *
		 * @since 1.0.0
		 * @return array
		 */

		$clauses = apply_filters( $this->get_hook_prefix() . '_pre_setup_join_query', $clauses, $args, $this );

		/**
		 * Filter the join query after setting up the query.
		 *
		 * @param array $clauses Query clauses.
		 * @param array $args Query arguments.
		 * @param self $this Current instance of the class.
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
		 * @param array $clauses Query clauses.
		 * @param array $args Query arguments.
		 * @param self $this Current instance of the class.
		 *
		 * @since 1.0.0
		 * @return array
		 */
		$clauses = apply_filters( $this->get_hook_prefix() . '_pre_setup_group_by_query', $clauses, $args, $this );

		/**
		 * Filter the group by query after setting up the query.
		 *
		 * @param array $clauses Query clauses.
		 * @param array $args Query arguments.
		 * @param self $this Current instance of the class.
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
		 * @param array $clauses Query clauses.
		 * @param array $args Query arguments.
		 * @param self $this Current instance of the class.
		 *
		 * @since 1.0.0
		 * @return array
		 */
		$clauses = apply_filters( $this->get_hook_prefix() . '_pre_setup_having_query', $clauses, $args, $this );

		/**
		 * Filter the having query after setting up the query.
		 *
		 * @param array $clauses Query clauses.
		 * @param array $args Query arguments.
		 * @param self $this Current instance of the class.
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
		 * @param array $clauses Query clauses.
		 * @param array $args Query arguments.
		 * @param self $this Current instance of the class.
		 *
		 * @since 1.0.0
		 * @return array
		 */
		$clauses = apply_filters( $this->get_hook_prefix() . '_pre_setup_order_by_query', $clauses, $args, $this );

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
				if ( in_array( $key, $this->get_core_data_keys(), true ) ) {
					$clauses['orderby'][] = "{$this->get_table_name()}.$key $value";
				}
			}
		} else {
			$clauses['orderby'][] = "{$this->get_table_name()}.{$this->get_id_key()} {$args['order']}";
		}

		/**
		 * Filter the order by query after setting up the query.
		 *
		 * @param array $clauses Query clauses.
		 * @param array $args Query arguments.
		 * @param self $this Current instance of the class.
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
		 * @param array $clauses Query clauses.
		 * @param array $args Query arguments.
		 * @param self $this Current instance of the class.
		 *
		 * @since 1.0.0
		 * @return array
		 */
		$clauses = apply_filters( $this->get_hook_prefix() . '_pre_setup_limit_query', $clauses, $args, $this );

		// Limit clause.
		if ( empty( $args['nopaging'] ) || absint( $args['per_page'] ) > 0 ) {
			$page = absint( $args['paged'] );
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
		 * @param array $clauses Query clauses.
		 * @param array $args Query arguments.
		 * @param self $this Current instance of the class.
		 *
		 * @since 1.0.0
		 * @return array
		 */
		return apply_filters( $this->get_hook_prefix() . '_setup_limit_query', $clauses, $args, $this );
	}

}
