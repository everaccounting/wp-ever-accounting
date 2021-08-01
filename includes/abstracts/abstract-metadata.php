<?php

namespace EverAccounting\Abstracts;

class MetaData {
	/**
	 * Meta table name.
	 *
	 * @var string
	 */
	protected $meta_table = false;

	/**
	 * Stores additional meta data.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $meta_data = null;

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
		$array_keys = wp_list_pluck( $this->meta_data, 'meta_key' );

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
		if ( ! $this->exists() || empty( $this->meta_table ) ) {
			return;
		}

		// Only read from cache if the cache key is set.
		$raw_meta_data = false;
		if ( ! $force_read ) {
			$raw_meta_data = wp_cache_get( $this->id, $this->meta_table );
		}

		if ( ! isset( $this->meta_object_id_field ) ) {
			$this->meta_object_id_field = 'object_id';
		}

		if ( false === $raw_meta_data ) {
			$raw_meta_data = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT *
				FROM {$wpdb->prefix}$this->meta_table
				WHERE {$this->meta_object_id_field} = %d
				ORDER BY meta_id",
					(int) $this->id
				)
			);
		}

		$this->set_meta_data( $raw_meta_data );
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
				$meta = (array) $meta;
				if ( isset( $meta['meta_key'], $meta['meta_value'], $meta['meta_id'] ) ) {
					$this->meta_data[] = array(
						'meta_id'    => (int) $meta['meta_id'],
						'meta_key'   => $meta['meta_key'],
						'meta_value' => maybe_unserialize( $meta['meta_value'] ),
					);
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
	public function set_meta( $key, $value, $unique = false ) {
		$this->maybe_read_meta_data();
		if ( $unique ) {
			$this->unset_meta( $key );
		}

		$function = 'set_' . $key;
		if ( is_callable( array( $this, $function ) ) ) {
			$this->{$function}( $value );
		}

		$this->meta_data[] = array(
			'meta_id'    => null,
			'meta_key'   => $key,
			'meta_value' => $value,
		);
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
		$array_keys = array_keys( wp_list_pluck( $this->meta_data, 'meta_key' ), $key, true );
		if ( $array_keys ) {
			foreach ( $array_keys as $array_key ) {
				$this->meta_data[ $array_key ]['meta_value'] = null;
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
		$array_keys = array_keys( wp_list_pluck( $this->meta_data, 'meta_id' ), (int) $mid, true );

		if ( $array_keys ) {
			foreach ( $array_keys as $array_key ) {
				$this->meta_data[ $array_key ]['meta_value'] = null;
			}
		}
	}
}
