<?php
/**
 * Abstract Model.
 *
 * Handles generic data interaction which is implemented by the different repository classes.
 *
 * @package EAccounting
 */

namespace EAccounting\Abstracts;

use EAccounting\Repositories\Meta_Data;

defined( 'ABSPATH' ) || exit;

/**
 * Class Resource_Model
 *
 * Implemented by classes using the same CRUD(s) pattern.
 *
 * @since   1.1.0
 *
 * @package EAccounting\Abstracts
 */
abstract class Resource_Model {
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
	 * This is the name of this object type.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	protected $object_type = 'model';

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
	 * Set to _data on construct so we can track and reset data if needed.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $default_data = array();

	/**
	 * Contains a reference to the repository for this class.
	 *
	 * @since 1.1.0
	 *
	 * @var \EAccounting\Abstracts\Resource_Repository
	 */
	protected $repository;

	/**
	 * Stores meta in cache for future reads.
	 * A group must be set to to enable caching.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	protected $cache_group = '';

	/**
	 * Stores additional meta data.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $meta_data = null;

	/**
	 * Array('prop' => readable text )
	 *
	 * @since 1.1.0
	 *
	 * @var array $required_props that will automatically check before saving.
	 */
	protected $required_props;

	/**
	 * Resource_Model constructor.
	 *
	 * @param int|object|array|string $read ID to load from the DB (optional) or already queried data.
	 */
	public function __construct( $read = 0 ) {
		$this->data         = array_merge( $this->data, $this->extra_data );
		$this->default_data = $this->data;
	}


	/**
	 * Only store the object ID to avoid serializing the data object instance.
	 *
	 * @since 1.1.0
	 *
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
	 * @since 1.1.0
	 */
	public function __wakeup() {
		$this->__construct( absint( $this->id ) );
	}

	/**
	 * When the object is cloned, make sure meta is duplicated correctly.
	 *
	 * @since 1.1.0
	 */
	public function __clone() {
		$this->maybe_read_meta_data();
		if ( ! empty( $this->meta_data ) ) {
			foreach ( $this->meta_data as $array_key => $meta ) {
				$this->meta_data[ $array_key ] = clone $meta;
				if ( ! empty( $meta->id ) ) {
					$this->meta_data[ $array_key ]->id = null;
				}
			}
		}
	}

	/**
	 * Get the repository.
	 *
	 * @since  1.1.0
	 *
	 * @return object
	 */
	public function get_repository() {
		return $this->repository;
	}

	/**
	 * Get the object type.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_object_type() {
		return $this->object_type;
	}

	/**
	 * get the cache group.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function get_cache_group() {
		return $this->cache_group;
	}

	/**
	 * Returns the unique ID for this object.
	 *
	 * @since  1.1.0
	 *
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Get form status.
	 *
	 * @param string $context View or edit context.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function get_status( $context = 'view' ) {
		return $this->get_prop( 'status', $context );
	}

	/**
	 * Delete an object, set the ID to 0, and return result.
	 *
	 * @since  1.1.0
	 *
	 * @return bool result
	 */
	public function delete() {
		if ( $this->repository && $this->get_id() ) {
			$this->repository->delete( $this );
			$this->set_id( 0 );

			return true;
		}

		return false;
	}

	/**
	 * Save should create or update based on object existence.
	 *
	 * @since  1.1.0
	 *
	 * @return \Exception|bool
	 */
	public function save() {
		if ( ! $this->repository ) {
			return $this->get_id();
		}

		if ( array_key_exists( 'date_created', $this->data ) && ! $this->get_date_created() ) {
			$this->set_date_created();
		}
		if ( array_key_exists( 'creator_id', $this->data ) && ! $this->get_creator_id() ) {
			$this->set_creator_id();
		}

		/**
		 * Check for any required data if missing throw exception.
		 */
		$this->check_required_items();

		/**
		 * Trigger action before saving to the DB. Allows you to adjust object props before save.
		 *
		 * @param array $data The object data being saved.
		 * @param int $id The ID of the object.
		 * @param Resource_Model $this The object being saved.
		 */
		do_action( 'eaccounting_pre_save_' . $this->object_type, $this->get_data(), $this->get_id(), $this );

		if ( $this->get_id() ) {
			$this->repository->update( $this );
		} else {
			$this->repository->insert( $this );
		}

		/**
		 * Trigger action before saving to the DB. Allows you to adjust object props before save.
		 *
		 * @param array $data The object data being saved.
		 * @param int $id The ID of the object.
		 * @param Resource_Model $this The object being saved.
		 */
		do_action( 'eaccounting_save_' . $this->object_type, $this->get_data(), $this->get_id(), $this );

		return $this->exists();
	}


	/**
	 * Change data to JSON format.
	 *
	 * @since  1.1.0
	 *
	 * @return string Data in JSON format.
	 */
	public function __toString() {
		return wp_json_encode( $this->get_data() );
	}

	/**
	 * Returns all data for this object.
	 *
	 * @since  1.1.0
	 *
	 * @return array
	 */
	public function get_data() {
		return $this->to_array(
			array_merge(
				$this->data,
				$this->changes,
				array( 'id' => $this->get_id() ),
				array( 'meta_data' => $this->get_meta_data() )
			)
		);
	}

	/**
	 * Returns as pure array.
	 * Does depth array casting.
	 *
	 * @param array $data Data to cast.
	 *
	 * @since 1.0.2
	 *
	 * @return array
	 */
	public function to_array( $data = array() ) {
		$output = array();
		$value  = null;
		foreach ( $data as $key => $value ) {
			if ( is_array( $value ) ) {
				$output[ $key ] = $this->to_array( $value );
			} elseif ( is_object( $value ) && method_exists( $value, 'get_data' ) ) {
				$output[ $key ] = $value->get_data();
			} elseif ( is_object( $value ) ) {
				$output[ $key ] = get_object_vars( $value );
			} else {
				$output[ $key ] = $value;
			}
		}

		return $output;
	}

	/**
	 * Returns array of expected data keys for this object.
	 *
	 * @since   1.1.0
	 *
	 * @return array
	 */
	public function get_data_keys() {
		return array_keys( $this->data );
	}

	/**
	 * Returns all "extra" data keys for an object (for sub objects like item types).
	 *
	 * @since  1.1.0
	 *
	 * @return array
	 */
	public function get_extra_data_keys() {
		return array_keys( $this->extra_data );
	}

	/**
	 * Filter null meta values from array.
	 *
	 * @param mixed $meta Meta value to check.
	 *
	 * @since  1.1.0
	 *
	 * @return bool
	 */
	protected function filter_null_meta( $meta ) {
		return ! is_null( $meta->value );
	}

	/**
	 * Get All Meta Data.
	 *
	 * @since 1.1.0
	 *
	 * @return array of objects.
	 */
	public function get_meta_data() {
		$this->maybe_read_meta_data();

		return array_values( array_filter( $this->meta_data, array( $this, 'filter_null_meta' ) ) );
	}

	/**
	 * Check if the key is an internal one.
	 *
	 * @param string $key Key to check.
	 *
	 * @since  1.1.0
	 *
	 * @return bool   true if it's an internal key, false otherwise
	 */
	protected function is_internal_meta_key( $key ) {
		$internal_meta_key = ! empty( $key ) && $this->repository && in_array( $key, $this->repository->get_internal_meta_keys(), true );

		if ( ! $internal_meta_key ) {
			return false;
		}

		$has_setter_or_getter = is_callable( array( $this, 'set_' . $key ) ) || is_callable( array( $this, 'get_' . $key ) );

		if ( ! $has_setter_or_getter ) {
			return false;
		}

		/* translators: %s: $key Key to check */
		eaccounting_doing_it_wrong( __FUNCTION__, sprintf( __( 'Generic add/update/get meta methods should not be used for internal meta data, including "%s". Use getters and setters.', 'wp-ever-accounting' ), $key ), '1.1.0' );

		return true;
	}

	/**
	 * Magic method for setting data fields.
	 *
	 * @param string $key Key to set.
	 * @param mixed  $value Value to set.
	 *
	 *  This method does not update custom fields in the database.
	 *
	 * @since  1.1.0
	 */
	public function __set( $key, $value ) {

		if ( 'id' === strtolower( $key ) ) {
			$this->set_id( $value );
		}

		if ( method_exists( $this, "set_$key" ) ) {

			/* translators: %s: $key Key to set */
			eaccounting_doing_it_wrong( __FUNCTION__, sprintf( __( 'Object data such as "%s" should not be accessed directly. Use getters and setters.', 'wp-ever-accounting' ), $key ), '1.1.0' );

			call_user_func( array( $this, "set_$key" ), $value );
		} else {
			$this->set_prop( $key, $value );
		}

	}

	/**
	 * Magic method for retrieving a property.
	 *
	 * @param string $key Key to get.
	 *
	 * @return mixed|null
	 */
	public function __get( $key ) {

		// Check if we have a helper method for that.
		if ( method_exists( $this, 'get_' . $key ) ) {

			if ( 'post_type' !== $key ) {
				/* translators: %s: $key Key to set */
				eaccounting_doing_it_wrong( __FUNCTION__, sprintf( __( 'Object data such as "%s" should not be accessed directly. Use getters and setters.', 'wp-ever-accounting' ), $key ), '1.1.0' );
			}

			return call_user_func( array( $this, 'get_' . $key ) );
		}

		// Check if the key is in the associated $extra data.
		if ( ! empty( $this->extra_data ) && isset( $this->extra_data[ $key ] ) ) {
			return $this->extra_data[ $key ];
		}

		return $this->get_prop( $key );
	}

	/**
	 * Get Meta Data by Key.
	 *
	 * @param string $key Meta Key.
	 * @param bool   $single return first found meta with key, or all with $key.
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since  1.1.0
	 *
	 * @return mixed
	 */
	public function get_meta( $key = '', $single = true, $context = 'view' ) {

		// Check if this is an internal meta key.
		$_key = str_replace( '_eaccounting', '', $key );
		$_key = str_replace( 'eaccounting', '', $_key );
		if ( $this->is_internal_meta_key( $_key ) ) {
			$function = 'get_' . $_key;

			if ( is_callable( array( $this, $function ) ) ) {
				return $this->{$function}();
			}
		}

		// Read the meta data if not yet read.
		$this->maybe_read_meta_data();
		$meta_data  = $this->get_meta_data();
		$array_keys = array_keys( wp_list_pluck( $meta_data, 'key' ), $key, true );
		$value      = $single ? '' : array();

		if ( ! empty( $array_keys ) ) {
			// We don't use the $this->meta_data property directly here because we don't want meta with a null value (i.e. meta which has been deleted via $this->delete_meta_data()).
			if ( $single ) {
				$value = $meta_data[ current( $array_keys ) ]->value;
			} else {
				$value = array_intersect_key( $meta_data, array_flip( $array_keys ) );
			}
		}

		if ( 'view' === $context ) {
			$value = apply_filters( $this->get_hook_prefix() . $key, $value, $this );
		}

		return $value;
	}

	/**
	 * See if meta data exists, since get_meta always returns a '' or array().
	 *
	 * @param string $key Meta Key.
	 *
	 * @since  1.1.0
	 *
	 * @return boolean
	 */
	public function meta_exists( $key = '' ) {
		$this->maybe_read_meta_data();
		$array_keys = wp_list_pluck( $this->get_meta_data(), 'key' );

		return in_array( $key, $array_keys, true );
	}

	/**
	 * Set all meta data from array.
	 *
	 * @param array $data Key/Value pairs.
	 *
	 * @since 1.1.0
	 */
	public function set_meta_data( $data ) {
		if ( ! empty( $data ) && is_array( $data ) ) {
			$this->maybe_read_meta_data();
			foreach ( $data as $meta ) {
				$meta = (array) $meta;
				if ( isset( $meta['key'], $meta['value'], $meta['id'] ) ) {
					$this->meta_data[] = new Meta_Data(
						array(
							'id'    => $meta['id'],
							'key'   => $meta['key'],
							'value' => $meta['value'],
						)
					);
				}
			}
		}
	}

	/**
	 * Add meta data.
	 *
	 * @param string       $key Meta key.
	 * @param string|array $value Meta value.
	 * @param bool         $unique Should this be a unique key?.
	 *
	 * @since 1.1.0
	 */
	public function add_meta_data( $key, $value, $unique = false ) {
		if ( $this->is_internal_meta_key( $key ) ) {
			$function = 'set_' . $key;

			if ( is_callable( array( $this, $function ) ) ) {
				$this->{$function}( $value );
			}
		}

		$this->maybe_read_meta_data();
		if ( $unique ) {
			$this->delete_meta_data( $key );
		}
		$this->meta_data[] = new Meta_Data(
			array(
				'key'   => $key,
				'value' => $value,
			)
		);
	}

	/**
	 * Update meta data by key or ID, if provided.
	 *
	 * @param string       $key Meta key.
	 * @param string|array $value Meta value.
	 * @param int          $meta_id Meta ID.
	 *
	 * @since  1.1.0
	 */
	public function update_meta_data( $key, $value, $meta_id = 0 ) {
		if ( $this->is_internal_meta_key( $key ) ) {
			$function = 'set_' . $key;

			if ( is_callable( array( $this, $function ) ) ) {
				$this->{$function}( $value );
			}
		}

		$this->maybe_read_meta_data();

		$array_key = false;

		if ( $meta_id ) {
			$array_keys = array_keys( wp_list_pluck( $this->meta_data, 'id' ), $meta_id, true );
			$array_key  = $array_keys ? current( $array_keys ) : false;
		} else {
			// Find matches by key.
			$matches = array();
			foreach ( $this->meta_data as $meta_data_array_key => $meta ) {
				if ( $meta->key === $key ) {
					$matches[] = $meta_data_array_key;
				}
			}

			if ( ! empty( $matches ) ) {
				// Set matches to null so only one key gets the new value.
				foreach ( $matches as $meta_data_array_key ) {
					$this->meta_data[ $meta_data_array_key ]->value = null;
				}
				$array_key = current( $matches );
			}
		}

		if ( false !== $array_key ) {
			$meta        = $this->meta_data[ $array_key ];
			$meta->key   = $key;
			$meta->value = $value;
		} else {
			$this->add_meta_data( $key, $value, true );
		}
	}

	/**
	 * Delete meta data.
	 *
	 * @param string $key Meta key.
	 *
	 * @since 1.1.0
	 */
	public function delete_meta_data( $key ) {
		$this->maybe_read_meta_data();
		$array_keys = array_keys( wp_list_pluck( $this->meta_data, 'key' ), $key, true );

		if ( $array_keys ) {
			foreach ( $array_keys as $array_key ) {
				$this->meta_data[ $array_key ]->value = null;
			}
		}
	}

	/**
	 * Delete meta data.
	 *
	 * @param int $mid Meta ID.
	 *
	 * @since 1.1.0
	 */
	public function delete_meta_data_by_mid( $mid ) {
		$this->maybe_read_meta_data();
		$array_keys = array_keys( wp_list_pluck( $this->meta_data, 'id' ), (int) $mid, true );

		if ( $array_keys ) {
			foreach ( $array_keys as $array_key ) {
				$this->meta_data[ $array_key ]->value = null;
			}
		}
	}

	/**
	 * Read meta data if null.
	 *
	 * @since 1.1.0
	 */
	protected function maybe_read_meta_data() {
		if ( is_null( $this->meta_data ) ) {
			$this->read_meta_data();
		}
	}

	/**
	 * Read Meta Data from the database. Ignore any internal properties.
	 * Uses it's own caches because get_metadata does not provide meta_ids.
	 *
	 * @param bool $force_read True to force a new DB read (and update cache).
	 *
	 * @since 1.1.0
	 */
	public function read_meta_data( $force_read = false ) {

		// Reset meta data.
		$this->meta_data = array();

		// Maybe abort early.
		if ( ! $this->get_id() || ! $this->repository ) {
			return;
		}

		// Only read from cache if the cache key is set.
		$cache_key = null;
		if ( ! $force_read && ! empty( $this->cache_group ) ) {
			$cache_key     = md5( $this->cache_group . '_object_' . $this->get_id() . '_object_meta_' . $this->get_id() );
			$raw_meta_data = wp_cache_get( $cache_key, $this->cache_group );
		}

		// Should we force read?
		if ( empty( $raw_meta_data ) ) {
			$raw_meta_data = $this->repository->read_meta( $this );

			if ( ! empty( $cache_key ) ) {
				wp_cache_set( $cache_key, $raw_meta_data, $this->cache_group );
			}
		}

		// Set meta data.
		if ( is_array( $raw_meta_data ) ) {

			foreach ( $raw_meta_data as $meta ) {
				$this->meta_data[] = new Meta_Data(
					array(
						'id'    => (int) $meta->meta_id,
						'key'   => $meta->meta_key,
						'value' => maybe_unserialize( $meta->meta_value ),
					)
				);
			}
		}
	}

	/**
	 * Update Meta Data in the database.
	 *
	 * @since 1.1.0
	 */
	public function save_meta_data() {
		if ( ! $this->repository || is_null( $this->meta_data ) ) {
			return;
		}
		foreach ( $this->meta_data as $array_key => $meta ) {
			if ( is_null( $meta->value ) ) {
				if ( ! empty( $meta->id ) ) {
					$this->repository->delete_meta( $this, $meta );
					unset( $this->meta_data[ $array_key ] );
				}
			} elseif ( empty( $meta->id ) ) {
				$meta->id = $this->repository->add_meta( $this, $meta );
				$meta->apply_changes();
			} else {
				if ( $meta->get_changes() ) {
					$this->repository->update_meta( $this, $meta );
					$meta->apply_changes();
				}
			}
		}
		if ( ! empty( $this->cache_group ) ) {
			$cache_key = md5( $this->cache_group . '_object_' . $this->get_id() . '_object_meta_' . $this->get_id() );
			wp_cache_delete( $cache_key, $this->cache_group );
		}
	}

	/**
	 * Set ID.
	 *
	 * @param int $id ID.
	 *
	 * @since 1.1.0
	 */
	public function set_id( $id ) {
		$this->id = absint( $id );
	}

	/**
	 * Sets item status.
	 *
	 * @param string $status New status.
	 *
	 * @since 1.1.0
	 *
	 * @return array details of change.
	 */
	public function set_status( $status ) {
		$old_status = $this->get_status();

		$this->set_prop( 'status', $status );

		return array(
			'from' => $old_status,
			'to'   => $status,
		);
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
	 * Set object read property.
	 *
	 * @param boolean $read Should read?.
	 *
	 * @since 1.1.0
	 */
	public function set_object_read( $read = true ) {
		$this->object_read = (bool) $read;
	}

	/**
	 * Get object read property.
	 *
	 * @since  1.1.0
	 * @return boolean
	 */
	public function get_object_read() {
		return (bool) $this->object_read;
	}

	/**
	 * Set a collection of props in one go, collect any errors, and return the result.
	 * Only sets using public methods.
	 *
	 * @param array  $props Key value pairs to set. Key is the prop and should map to a setter function name.
	 * @param string $context In what context to run this.
	 *
	 * @since  1.1.0
	 */
	public function set_props( $props, $context = 'set' ) {
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
	 * @param mixed  $value Value of the prop.
	 *
	 * @since 1.1.0
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
	 * @param string         $format Format to use when converting to a date string.
	 *
	 * @since 1.1.0
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
	 * Set prop from object.
	 *
	 * @param array|object $object The object.
	 * @param string       $property property of the object will be used.
	 * @param string       $prop prop that will be assigned to.
	 * @param mixed        $default default prop that will be assigned to.
	 *
	 * @since 1.1.0
	 */
	protected function set_object_prop( $object, $property, $prop, $default = null ) {
		if ( is_object( $object ) && is_callable( array( $object, 'get_' . $property ) ) ) {
			$method = "get_{$property}";
			$value  = $object->$method();
		} elseif ( is_object( $object ) ) {
			$object = get_object_vars( $object );
			if ( array_key_exists( $property, $object ) ) {
				$value = $object[ $property ];
			}
		} else {
			$value = $default;
		}

		if ( isset( $value ) ) {
			$this->set_prop( $prop, $value );
		}
	}

	/**
	 * Set object status.
	 *
	 * @param int $enabled Enabled.
	 *
	 * @since 1.0.2
	 */
	public function set_enabled( $enabled ) {
		$this->set_prop( 'enabled', eaccounting_bool_to_number( $enabled ) );
	}

	/**
	 * Set object created date.
	 *
	 * @param string $date Date.
	 *
	 * @since 1.0.2
	 */
	public function set_date_created( $date = null ) {
		if ( null === $date ) {
			$date = current_time( 'mysql' );
		}
		$this->set_date_prop( 'date_created', $date );
	}

	/**
	 * Set object creator id.
	 *
	 * @param int $creator_id Creator id.
	 *
	 * @since 1.0.2
	 */
	public function set_creator_id( $creator_id = null ) {
		if ( null === $creator_id ) {
			$creator_id = eaccounting_get_current_user_id();
		}
		$this->set_prop( 'creator_id', absint( $creator_id ) );
	}


	/**
	 * Return data changes only.
	 *
	 * @since 1.1.0
	 *
	 * @return array
	 */
	public function get_changes() {
		return $this->changes;
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
	 * Prefix for action and filter hooks on model.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	protected function get_hook_prefix() {
		return 'eaccounting_get_' . $this->object_type . '_';
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
	 * @since  1.1.0
	 *
	 * @return mixed
	 */
	protected function get_prop( $prop, $context = 'view' ) {
		$value = null;

		if ( array_key_exists( $prop, $this->data ) ) {
			$value = array_key_exists( $prop, $this->changes ) ? $this->changes[ $prop ] : $this->data[ $prop ];

			if ( 'view' === $context ) {
				$value = apply_filters( $this->get_hook_prefix() . $prop, $value, $this );
			}
		}

		return $value;
	}

	/**
	 * Get object created date.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 *
	 * @return string
	 */
	public function get_date_created( $context = 'edit' ) {
		return $this->get_prop( 'date_created', $context );
	}

	/**
	 * Get prop from object.
	 *
	 * @param array|object $object The object.
	 * @param string       $property property of the object will be used.
	 *
	 * @since  1.1.0
	 *
	 * @return mixed|null
	 */
	protected function get_object_prop( $object, $property ) {
		if ( is_object( $object ) && is_callable( array( $object, 'get_' . $property ) ) ) {
			$method = "get_{$property}";

			return $object->$method();
		}

		return null;
	}

	/**
	 * get object status
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 *
	 * @return bool
	 */
	public function get_enabled( $context = 'edit' ) {
		return $this->get_prop( 'enabled', $context );
	}


	/**
	 * Check required fields.
	 *
	 * @since 1.1.0
	 *
	 * @throws \Exception If invalid data is found.
	 */
	public function check_required_items() {
		foreach ( $this->required_props as $key => $title ) {
			$method = "get_{$key}";
			if ( empty( $this->$method( 'edit' ) ) ) {
				/* translators: %s missing item title */
				throw new \Exception( sprintf( __( '%s is required', 'wp-ever-accounting' ), $title ), 400 );
			}
		}
	}

	/**
	 * Alias self::get_enabled()
	 *
	 * @since 1.0.2
	 *
	 * @return bool
	 */
	public function is_enabled() {
		return eaccounting_string_to_bool( $this->get_prop( 'enabled', 'edit' ) );
	}

	/**
	 * Return object created by.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 *
	 * @return mixed|null
	 */
	public function get_creator_id( $context = 'edit' ) {
		return $this->get_prop( 'creator_id', $context );
	}

	/**
	 * Clear object cache
	 *
	 * @since 1.1.0
	 */
	public function clear_cache() {
		eaccounting_cache_set_last_changed( $this->cache_group );
		wp_cache_delete( $this->get_id(), $this->cache_group );
	}

	/**
	 * Checks if the object is saved in the database
	 *
	 * @since 1.1.0
	 *
	 * @return bool
	 */
	public function exists() {
		$id = $this->get_id();

		return ! empty( $id );
	}

	/**
	 * When invalid data is found, throw an exception unless reading from the DB.
	 *
	 * @param string $code Error code.
	 * @param string $message Error message.
	 * @param int    $http_status_code HTTP status code.
	 * @param array  $data Error data.
	 *
	 * @since 1.1.0
	 *
	 * @throws \Exception If invalid data is found.
	 */
	public function error( $code, $message, $http_status_code = 400, $data = array() ) {
		throw new \Exception( $message );
	}

}
