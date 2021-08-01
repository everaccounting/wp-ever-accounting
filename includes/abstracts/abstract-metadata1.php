<?php

namespace EverAccounting\Abstracts;

class MetaDataDK {
	/**
	 * Meta type. This should match up with
	 * the types available at https://developer.wordpress.org/reference/functions/add_metadata/.
	 * WP defines 'post', 'user', 'comment', and 'term'.
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
	 * Get All Meta Data.
	 *
	 * @return array of objects.
	 * @since 1.1.0
	 *
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

		// Reset meta data.
		$this->meta_data = array();

		// Maybe abort early.
		if ( ! $this->exists() ) {
			return;
		}

		// Only read from cache if the cache key is set.
		$cache_key = null;
		if ( ! $force_read && ! empty( $this->meta_type ) ) {
			$cache_key     = md5( $this->meta_type . '_' . 'object_' . $this->id . '_' . 'object_meta_' . $this->id );
			$raw_meta_data = wp_cache_get( $cache_key, $this->meta_type );
		}

		// Should we force read?
		if ( empty( $raw_meta_data ) ) {
			$raw_meta_data = $this->read_meta( $this );

			if ( ! empty( $cache_key ) ) {
				wp_cache_set( $cache_key, $raw_meta_data, $this->meta_type );
			}
		}

		// Set meta data.
		if ( is_array( $raw_meta_data ) ) {
			foreach ( $raw_meta_data as $meta ) {
				$data              = new \StdClass;
				$data->id          = (int) $meta->meta_id;
				$data->key         = $meta->meta_key;
				$data->value       = maybe_unserialize( $meta->meta_value );
				$this->meta_data[] = $data;
			}
		}
	}


	/**
	 * Returns an array of meta for an object.
	 *
	 * @param object $entity Object.
	 *
	 * @return array
	 * @since  1.10
	 */
	public function read_meta( $entity ) {
		if ( $this->meta_type && $entity->id ) {
			global $wpdb;
			$db_info       = $this->get_db_info();
			$raw_meta_data = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT {$db_info['meta_id_field']} as meta_id, meta_key, meta_value
				FROM {$db_info['table']}
				WHERE {$db_info['object_id_field']} = %d
				ORDER BY {$db_info['meta_id_field']}",
					(int) $entity->id
				)
			);

			return $raw_meta_data;
		}

		return array();
	}

	/**
	 * Table structure is slightly different between meta types, this function will return what we need to know.
	 *
	 * @return array Array elements: table, object_id_field, meta_id_field
	 * @since  1.1.0
	 */
	protected function get_db_info() {
		global $wpdb;

		$meta_id_field = 'meta_id'; // users table calls this umeta_id so we need to track this as well.
		$table         = $wpdb->prefix;

		// If we are dealing with a type of metadata that is not a core type, the table should be prefixed.
		if ( ! in_array( $this->meta_type, array( 'post', 'user', 'comment', 'term' ), true ) ) {
			$table .= 'ea_';
		}

		$table           .= $this->meta_type . 'meta';
		$object_id_field = $this->meta_type . '_id';

		// Figure out our field names.
		if ( 'user' === $this->meta_type ) {
			$meta_id_field = 'umeta_id';
			$table         = $wpdb->usermeta;
		}

		if ( ! empty( $this->object_id_field_for_meta ) ) {
			$object_id_field = $this->object_id_field_for_meta;
		}

		return array(
			'table'           => $table,
			'object_id_field' => $object_id_field,
			'meta_id_field'   => $meta_id_field,
		);
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
	 * Get Meta Data by Key.
	 *
	 * @param string $key Meta Key.
	 * @param bool $single return first found meta with key, or all with $key.
	 *
	 * @return mixed
	 * @since  1.1.0
	 *
	 */
	public function get_meta( $key = '', $single = true ) {
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
		if ( ! empty( $data ) && is_array( $data ) ) {
			$this->maybe_read_meta_data();
			foreach ( $data as $meta ) {
				$meta = (array) $meta;
				if ( isset( $meta['key'], $meta['value'], $meta['id'] ) ) {
					$this->meta_data[] = array(
						'id'    => $meta['id'],
						'key'   => $meta['key'],
						'value' => $meta['value'],
					);
				}
			}
		}
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
	public function add_meta_data( $key, $value, $unique = false ) {
		$this->maybe_read_meta_data();
		if ( $unique ) {
			$this->delete_meta_data( $key );
		}

		$function = 'set_' . $key;
		if ( is_callable( array( $this, $function ) ) ) {
			$this->{$function}( $value );
		}

		$this->meta_data[] = (object) array(
			'key'   => $key,
			'value' => $value,
		);
	}

	/**
	 * Update meta data by key or ID, if provided.
	 *
	 * @param string $key Meta key.
	 * @param string|array $value Meta value.
	 * @param int $meta_id Meta ID.
	 *
	 * @since  1.1.0
	 *
	 */
	public function update_meta_data( $key, $value, $meta_id = 0 ) {
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
	 *
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
	 *
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
}
