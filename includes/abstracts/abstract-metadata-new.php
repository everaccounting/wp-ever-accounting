<?php
/**
 * Abstract MetaData.
 *
 * Handles generic data interaction which is implemented by the different object classes.
 *
 * @since 1.1.0
 */

namespace EverAccounting\Abstracts;

defined( 'ABSPATH' ) || exit;

/**
 * Class MetaData
 * @package EverAccounting\Abstracts
 * @since 1.1.0
 */
abstract class MetaData_NEW extends Data {
	/**
	 * Meta type.
	 *
	 * @var string
	 */
	protected $meta_type = false;

	/**
	 * Stores additional meta data.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $meta_data = null;

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
		parent::__clone();
	}


	/**
	 * See if meta data exists, since get_meta always returns a '' or array().
	 *
	 * @param string $key Meta Key.
	 *
	 * @return boolean
	 * @since  1.1.0
	 *
	 */
	public function meta_exists( $key = '' ) {
		$this->maybe_read_meta_data();
		$array_keys = wp_list_pluck( $this->get_meta_data(), 'key' );

		return in_array( $key, $array_keys, true );
	}

	/**
	 * Read meta data if not read before.
	 *
	 * @since 1.1.0
	 */
	protected function maybe_read_meta_data() {
		if ( is_null( $this->meta_data ) ) {
			$this->read_meta_data();
		}
	}

	/**
	 * Get All Meta Data with key value pair.
	 *
	 * @return array of objects.
	 * @since 1.1.0
	 */
	public function get_meta_data() {
		$this->maybe_read_meta_data();

		return array_values( array_filter( $this->meta_data, array( $this, 'filter_null_meta' ) ) );
	}

	/**
	 * Read Meta Data from the database. Ignore any internal properties.
	 * Uses it's own caches because get_metadata does not provide meta_ids.
	 *
	 * @param bool $force_read True to force a new DB read (and update cache).
	 *
	 * @since 1.1.0
	 *
	 */
	public function read_meta_data( $force_read = false ) {
		global $wpdb;
		// Reset meta data.
		$this->meta_data = array();

		// Maybe abort early.
		if ( ! $this->exists() || empty( $this->meta_type ) ) {
			return;
		}

		$meta_data = false;
		if ( ! $force_read ) {
			$meta_data = wp_cache_get( $this->get_id(), "ea_{$this->meta_type}meta" );
		}

		$table = _get_meta_table( $this->meta_type );
		if ( ! $table ) {
			return;
		}

		if ( $meta_data === false ) {
			$object_id_field = sanitize_key( ltrim( $this->meta_type, 'ea_' ) . '_id' );
			$meta_data       = $wpdb->get_results( $wpdb->prepare(
				"SELECT meta_id, meta_key, meta_value
				FROM {$table}
				WHERE {$object_id_field} = %d
				ORDER BY meta_id",
				$this->get_id()
			) );
			wp_cache_add( $this->id, $meta_data, "ea_{$this->meta_type}meta" );
		}

		// Set meta data.
		if ( is_array( $meta_data ) ) {
			foreach ( $meta_data as $meta ) {
				if ( isset( $meta->meta_key, $meta->meta_value, $meta->meta_id ) ) {
					$meta->id      = (int) $meta->meta_id;
					$meta->key     = $meta->meta_key;
					$meta->value   = maybe_unserialize( $meta->meta_value );
					$meta->changed = false;
					unset( $meta->meta_key, $meta->meta_id, $meta->meta_value );
					$this->meta_data[] = $meta;
				}
			}
		}
	}

	/**
	 * Get Meta Data by Key.
	 *
	 * @param string $key Meta Key.
	 * @param bool $single return first found meta with key, or all with $key.
	 *
	 * @return mixed
	 * @since  1.1.0
	 *
	 */
	public function get_meta_prop( $key = '', $single = true ) {
		// Check if this is an internal meta key.
		$function = 'get_meta_' . $key;
		if ( is_callable( array( $this, $function ) ) ) {
			return $this->{$function}();
		}

		// Read the meta data if not yet read.
		$this->maybe_read_meta_data();
		$meta_data  = $this->get_meta_data();
		$array_keys = array_keys( wp_list_pluck( $meta_data, 'key' ), $key, true );
		$value      = $single ? '' : array();

		if ( ! empty( $array_keys ) ) {
			if ( $single ) {
				$value = $meta_data[ current( $array_keys ) ]->value;
			} else {
				$value = array_intersect_key( $meta_data, array_flip( $array_keys ) );
			}
		}

		return $value;
	}

	/**
	 * Add meta data.
	 *
	 * @param string $key Meta key.
	 * @param string|array $value Meta value.
	 * @param bool $unique Should this be a unique key?.
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_meta_prop( $key, $value, $unique = true ) {
		// Check if this is an internal meta key.
		$function = 'set_meta_' . $key;
		if ( is_callable( array( $this, $function ) ) ) {
			$this->{$function}( $value );

			return;
		}
		$this->maybe_read_meta_data();
		$array_keys = [ count( $this->meta_data ) ];
		if ( $unique ) {
			$array_keys = array_keys( wp_list_pluck( $this->meta_data, 'key' ), $key, true );
			$array_keys = empty( $array_keys ) ? [ count( $this->meta_data ) + 1 ] : $array_keys;
		}

		foreach ( $array_keys as $array_key ) {
			if ( array_key_exists( $array_key, $this->meta_data ) ) {
				$this->meta_data[ $array_key ] = (object) array_merge(
					(array) $this->meta_data[ $array_key ],
					array(
						'value'   => $value,
						'changed' => $this->meta_data[ $array_key ]->value != $value
					)
				);
				continue;
			}
			$this->meta_data[ $array_key ] = (object) array(
				'id'      => null,
				'key'     => $key,
				'value'   => $value,
				'changed' => false
			);
		}
	}

	/**
	 * Delete meta data.
	 *
	 * @param string $key Meta key.
	 *
	 * @since 1.1.0
	 *
	 */
	public function delete_meta( $key ) {
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
	 *
	 */
	public function delete_meta_by_mid( $mid ) {
		$this->maybe_read_meta_data();
		$array_keys = array_keys( wp_list_pluck( $this->meta_data, 'id' ), (int) $mid, true );

		if ( $array_keys ) {
			foreach ( $array_keys as $array_key ) {
				$this->meta_data[ $array_key ]->value = null;
			}
		}
	}


	/**
	 * Update Meta Data in the database.
	 *
	 * @since 1.1.0
	 */
	public function save_meta_data() {
		if ( is_null( $this->meta_data ) ) {
			return;
		}

		foreach ( $this->meta_data as $array_key => $meta ) {
			if ( ! empty( $meta->id ) && is_null( $meta->value ) ) {
				delete_metadata_by_mid( $this->meta_type, $meta->id );
				unset( $this->meta_data[ $array_key ] );
			} elseif ( empty( $meta->id ) ) {
				$this->meta_data[ $array_key ]->id      = add_metadata( $this->meta_type, $this->get_id(), $meta->key, is_string( $meta->value ) ? wp_slash( $meta->value ) : $meta->value, false );
				$this->meta_data[ $array_key ]->changed = false;
			} elseif ( $meta->changed ) {
				update_metadata_by_mid( $this->meta_type, $meta->id, $meta->value, $meta->key );
				$this->meta_data[ $array_key ]->changed = false;
			}
		}
		wp_cache_delete( $this->id, "ea_{$this->meta_type}meta" );
	}

	/**
	 * Filter null meta values from array.
	 *
	 * @param mixed $meta Meta value to check.
	 *
	 * @return bool
	 * @since  1.1.0
	 *
	 */
	protected function filter_null_meta( $meta ) {
		return ! is_null( $meta->value );
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
				if ( $value != $this->data[ $prop ] || array_key_exists( $prop, $this->changes ) ) { //phpcs: ignore
					$this->changes[ $prop ] = $value;
				}
			} else {
				$this->data[ $prop ] = $value;
			}
		} elseif ( $this->meta_exists( $prop ) ) {
			$this->set_meta_prop( $prop, $value, true );
		} else {
			if ( true === $this->object_read ) {
				if ( $value != $this->extra_data[ $prop ] || array_key_exists( $prop, $this->changes ) ) { //phpcs: ignore
					$this->changes[ $prop ] = $value;
				}
			} else {
				$this->extra_data[ $prop ] = $value;
			}
		}
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
			$value = array_key_exists( $prop, $this->changes ) ? $this->changes[ $prop ] : $this->extra_data[ $prop ];
		} elseif ( $this->meta_exists( $prop ) ) {
			$value = $this->get_meta_prop( $prop, true );
		}

		return $value;
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
			parent::to_array(),
			[ 'meta_data' => $this->get_meta_data() ]
		);
	}


}
