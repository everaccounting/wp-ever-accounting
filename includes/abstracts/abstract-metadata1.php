<?php

namespace EverAccounting\Abstracts;

class MetaDataBK {
	/**
	 * Meta type. This should match up with
	 * the types available at https://developer.wordpress.org/reference/functions/add_metadata/.
	 * WP defines 'post', 'user', 'comment', and 'term'.
	 *
	 * @var string
	 */
	protected $meta_type = false;

	/**
	 * This only needs set if you are using a custom metadata type.
	 *
	 * @var string
	 */
	protected $object_id_field_for_meta = '';

	/**
	 * Data stored in meta keys, but not considered "meta" for an object.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $internal_meta_keys = array();

	/**
	 * Meta data which should exist in the DB, even if empty.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $must_exist_meta_keys = array();

	/**
	 * Stores additional meta data.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $meta_data = null;

	/**
	 * Filter null meta values from array.
	 *
	 * @since  1.1.0
	 *
	 * @param mixed $meta Meta value to check.
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
	 * @since  1.1.0
	 *
	 * @param string $key Key to check.
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
	 * Get Meta Data by Key.
	 *
	 * @since  1.1.0
	 *
	 * @param string $key     Meta Key.
	 * @param bool   $single  return first found meta with key, or all with $key.
	 * @param string $context What the value is for. Valid values are view and edit.
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
	 * @since  1.1.0
	 *
	 * @param string $key Meta Key.
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
	 * @since 1.1.0
	 *
	 * @param array $data Key/Value pairs.
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
	 * @since 1.1.0
	 *
	 * @param string       $key    Meta key.
	 * @param string|array $value  Meta value.
	 * @param bool         $unique Should this be a unique key?.
	 *
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
	 * @since  1.1.0
	 *
	 * @param string       $key     Meta key.
	 * @param string|array $value   Meta value.
	 * @param int          $meta_id Meta ID.
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
	 * @since 1.1.0
	 *
	 * @param string $key Meta key.
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
	 * @since 1.1.0
	 *
	 * @param int $mid Meta ID.
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
	 * @since 1.1.0
	 *
	 * @param bool $force_read True to force a new DB read (and update cache).
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
			$cache_key     = md5( $this->cache_group . '_' . 'object_' . $this->get_id() . '_' . 'object_meta_' . $this->get_id() );
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
			$cache_key = md5( $this->cache_group . '_' . 'object_' . $this->get_id() . '_' . 'object_meta_' . $this->get_id() );
			wp_cache_delete( $cache_key, $this->cache_group );
		}
	}
}
