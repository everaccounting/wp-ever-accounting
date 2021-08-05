<?php

namespace EverAccounting\Abstracts;

class MetaData extends Data {
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
	 * Stores meta hash.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	protected $meta_hash = null;

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
		var_dump($key);
		var_dump($value);
		if ( method_exists( $this, 'set_' . $key ) ) {
			$this->{'set_' . $key}( $value );
		} else if ( property_exists( $this, $key ) && is_callable( array( $this, $key ) ) ) {
			$this->$key = $value;
		} else if ( array_key_exists( $key, $this->data ) ) {
			$this->data[ $key ] = $value;
		} else if ( $this->meta_type ) {
			$this->get_meta( $key );
		}

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
		$value = '';
		if ( method_exists( $this, 'get_' . $key ) ) {
			$value = $this->{'get_' . $key};
		} else if ( property_exists( $this, $key ) && is_callable( array( $this, $key ) ) ) {
			$value = $this->$key;
		} else if ( array_key_exists( $key, $this->data ) ) {
			$value = $this->data[ $key ];
		} else if ( $this->meta_type && $this->meta_exists( $key ) ) {
			$value = $this->get_meta( $key );
		}

		return $value;
	}

	/**
	 * Magic method for checking the existence of a certain field.
	 *
	 * @param string $key Account field to check if set.
	 *
	 * @return bool Whether the given Account field is set.
	 * @since 1.2.1
	 */
	public function __isset( $key ) {
		$this->maybe_read_meta_data();

		return parent::__isset( $key );
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
		$array_keys = wp_list_pluck( $this->meta_data, 'key' );

		return in_array( $key, $array_keys, true );
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

		// Only read from cache if the cache key is set.
		$raw_meta_data = false;
		if ( ! $force_read ) {
			$raw_meta_data = wp_cache_get( $this->id, "ea_{$this->meta_type}meta" );
		}

		if ( false === $raw_meta_data ) {
			$table = $wpdb->prefix;
			// If we are dealing with a type of metadata that is not a core type, the table should be prefixed.
			if ( ! in_array( $this->meta_type, array( 'post', 'user', 'comment', 'term' ), true ) ) {
				$table .= 'ea_';
			}
			$table           .= $this->meta_type . 'meta';
			$meta_id_field   = 'meta_id';
			$object_id_field = $this->meta_type . '_id';

			// Figure out our field names.
			if ( 'user' === $this->meta_type ) {
				$meta_id_field = 'umeta_id';
				$table         = $wpdb->usermeta;
			}

			$raw_meta_data = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT $meta_id_field as id, meta_key as `key`, meta_value as `value`
				FROM $table
				WHERE {$object_id_field} = %d
				ORDER BY $meta_id_field",
					(int) $this->id
				)
			);
		}

		wp_cache_add( $this->id, $raw_meta_data, "ea_{$this->meta_type}meta" );
		$this->set_meta_data( $raw_meta_data );
		$this->meta_hash = md5( serialize( $this->meta_data ) );
	}

	/**
	 * Get Meta Data by Key.
	 *
	 * @param string $key Meta Key.
	 * @param bool $single return first found meta with key, or all with $key.
	 *
	 * @return mixed
	 * @since  1.1.0
	 */
	public function get_meta( $key = '', $single = true ) {
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
	 * Set all meta data from array.
	 *
	 * @param array $data Key/Value pairs.
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_meta_data( $data ) {
		if ( is_null( $this->meta_data ) ) {
			$this->meta_data = [];
		}
		if ( ! empty( $data ) && is_array( $data ) ) {
			foreach ( $data as $meta ) {
				if ( isset( $meta->key, $meta->value, $meta->id ) ) {
					$meta->id          = (int) $meta->id;
					$meta->value       = maybe_unserialize( $meta->value );
					$this->meta_data[] = $meta;
				}
			}
		}
	}

	/**
	 * Set meta data.
	 *
	 * @param string $key Meta key.
	 * @param string|array $value Meta value.
	 * @param bool $unique Should this be a unique key?.
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_meta( $key, $value, $unique = true ) {
		$this->maybe_read_meta_data();
		$array_keys = [ count( $this->meta_data ) ];
		if ( $unique ) {
			$array_keys = array_keys( wp_list_pluck( $this->meta_data, 'key' ), $key, true );
			$array_keys = empty( $array_keys ) ? [ count( $this->meta_data ) + 1 ] : $array_keys;
		}

		$function = 'set_' . $key;
		if ( is_callable( array( $this, $function ) ) ) {
			return $this->{$function}( $value );
		}

		foreach ( $array_keys as $array_key ) {
			$existing                      = isset( $this->meta_data[ $array_key ] ) ? $this->meta_data[ $array_key ] : [ 'id' => null ];
			$this->meta_data[ $array_key ] = (object) array_merge(
				(array) $existing,
				[
					'key'   => $key,
					'value' => $value,
				]
			);
		}

		return $this->meta_data;
	}

	/**
	 * Delete meta data.
	 *
	 * @param string $key Meta key.
	 *
	 * @since 1.1.0
	 *
	 */
	public function unset_meta( $key ) {
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
	 * @param int $mid Meta id.
	 *
	 * @since 1.1.0
	 *
	 */
	public function unset_meta_by_mid( $mid ) {
		$this->maybe_read_meta_data();
		$array_keys = array_keys( wp_list_pluck( $this->meta_data, 'id' ), (int) $mid, true );

		if ( $array_keys ) {
			foreach ( $array_keys as $array_key ) {
				$this->meta_data[ $array_key ]->value = null;
			}
		}
	}

	/**
	 * Deletes meta based on meta ID.
	 *
	 * @param \stdClass $meta (containing at least ->id).
	 *
	 * @since  1.1.0
	 */
	public function delete_meta( $meta ) {
		// Maybe abort early.
		if ( ! $this->exists() || empty( $this->meta_type ) ) {
			return;
		}

		$this->maybe_read_meta_data();
		if ( ! isset( $meta->id ) ) {
			return;
		}

		delete_metadata_by_mid( $this->meta_type, $meta->id );
	}

	/**
	 * Add new piece of meta.
	 *
	 * @param \stdClass $meta (containing ->key and ->value).
	 *
	 * @return int meta ID
	 * @since  1.1.0
	 */
	public function add_meta( $meta ) {
		// Maybe abort early.
		if ( ! $this->exists() || empty( $this->meta_type ) ) {
			return 0;
		}

		return add_metadata( $this->meta_type, $this->id, $meta->key, is_string( $meta->value ) ? wp_slash( $meta->value ) : $meta->value, false );
	}

	/**
	 * Update meta.
	 *
	 * @param \stdClass $meta (containing ->id, ->key and ->value).
	 *
	 * @since  1.1.0
	 */
	public function update_meta( $meta ) {
		if ( ! $this->exists() || empty( $this->meta_type ) ) {
			return;
		}
		update_metadata_by_mid( $this->meta_type, $meta->id, $meta->value, $meta->key );
	}

	/**
	 * Get meta data.
	 *
	 * @return array
	 * @since 1.2.1
	 */
	public function get_meta_data() {
		$this->maybe_read_meta_data();

		return wp_list_pluck( $this->meta_data, 'value', 'key' );
	}

	/**
	 * Update Meta Data in the database.
	 *
	 * @since 1.1.0
	 */
	public function save_meta_data() {
		$this->maybe_read_meta_data();
		$meta_hash = md5( serialize( $this->meta_data ) );
		if ( empty( $this->meta_data ) || $meta_hash === $this->meta_hash ) {
			return;
		}

		foreach ( $this->meta_data as $array_key => $meta ) {
			if ( is_null( $meta->value ) ) {
				if ( isset( $meta->id ) && ! empty( $meta->id ) ) {
					$this->delete_meta( $meta );
					unset( $this->meta_data[ $array_key ] );
				}
			} elseif ( empty( $meta->id ) ) {
				$this->meta_data[ $array_key ]->id = $this->add_meta( $meta );
			} else {
				$this->update_meta( $meta );
			}
		}

	}

	/**
	 * Return an array representation.
	 *
	 * @return array Array representation.
	 * @since 1.2.1
	 */
	public function to_array() {
		$this->maybe_read_meta_data();

		return array_merge(
			parent::to_array(),
			array( 'meta_data' => $this->meta_data )
		);
	}
}
