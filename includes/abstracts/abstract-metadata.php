<?php
/**
 * Abstract MetaData.
 *
 * Handles generic data interaction which is implemented by the different object classes.
 *
 * @since 1.1.0
 */

namespace EverAccounting\Abstracts;

use EverAccounting\Meta;

defined( 'ABSPATH' ) || exit;

/**
 * Class MetaData
 * @package EverAccounting\Abstracts
 * @since 1.1.0
 */
abstract class MetaData extends Data {
	/**
	 * Meta type.
	 *
	 * @since 1.1.0
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
	 * Meta data which should exist in the DB, even if empty.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $must_exist_meta_keys = array();

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
	 * @since 1.2.1
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
			$meta_data       = array_filter( $meta_data, array( $this, 'exclude_internal_meta_keys' ) );
			$meta_data       = apply_filters( "eaccounting_{$this->meta_type}_read_meta", $meta_data, $this );
			wp_cache_add( $this->id, $meta_data, "ea_{$this->meta_type}meta" );
		}

		if ( $meta_data ) {
			foreach ( $meta_data as $meta ) {
				$this->meta_data[] = new Meta(
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
	 * Callback to remove unwanted meta data.
	 *
	 * @param object $meta Meta object to check if it should be excluded or not.
	 *
	 * @return bool
	 */
	protected function exclude_internal_meta_keys( $meta ) {
		return ! in_array( $meta->meta_key, $this->get_data_keys(), true );
	}

	/**
	 * See if meta data exists, since get_meta always returns a '' or array().
	 *
	 * @param string $key Meta Key.
	 *
	 * @return boolean
	 * @since 1.1.0
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
	 * @since  2.6.0
	 */
	public function get_meta( $key = '', $single = true ) {
//		if ( $this->is_internal_meta_key( $key ) ) {
//			$function = 'get_' . $key;
//
//			if ( is_callable( array( $this, $function ) ) ) {
//				return $this->{$function}();
//			}
//		}

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
	 */
	public function set_meta_data( $data ) {
		if ( ! empty( $data ) && is_array( $data ) ) {
			$this->maybe_read_meta_data();
			foreach ( $data as $meta ) {
				$meta = (array) $meta;
				if ( isset( $meta['key'], $meta['value'], $meta['id'] ) ) {
					$this->meta_data[] = new Meta(
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
	 * @param string $key Meta key.
	 * @param string|array $value Meta value.
	 * @param bool $unique Should this be a unique key?.
	 *
	 * @since 1.1.0
	 *
	 */
	public function add_meta_data( $key, $value, $unique = true ) {
//		if ( $this->is_internal_meta_key( $key ) ) {
//			$function = 'set_' . $key;
//
//			if ( is_callable( array( $this, $function ) ) ) {
//				return $this->{$function}( $value );
//			}
//		}

		$this->maybe_read_meta_data();
		if ( $unique ) {
			$this->delete_meta_data( $key );
		}
		$this->meta_data[] = new Meta(
			array(
				'key'   => $key,
				'value' => $value,
			)
		);
	}

	/**
	 * Update meta data by key or ID, if provided.
	 *
	 * @param string $key Meta key.
	 * @param string|array $value Meta value.
	 * @param int $meta_id Meta ID.
	 *
	 * @since  2.6.0
	 *
	 */
	public function update_meta_data( $key, $value, $meta_id = 0 ) {
//		if ( $this->is_internal_meta_key( $key ) ) {
//			$function = 'set_' . $key;
//
//			if ( is_callable( array( $this, $function ) ) ) {
//				return $this->{$function}( $value );
//			}
//		}

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
	 * Check if the key is an internal one.
	 *
	 * Restrict set or get object data as array. eg if the object have data key with
	 * account_id restrict setting that as meta
	 *
	 * @param string $key Key to check.
	 *
	 * @return bool   true if it's an internal key, false otherwise
	 * @since  1.1.0
	 */
	protected function is_internal_meta_key( $key ) {
		$has_setter_or_getter = is_callable( array( $this, 'set_' . $key ) ) || is_callable( array( $this, 'get_' . $key ) );

		if ( ! $has_setter_or_getter ) {
			return false;
		}
		/* translators: %s: $key Key to check */
		eaccounting_doing_it_wrong( __FUNCTION__, sprintf( __( 'Generic add/update/get meta methods should not be used for internal meta data, including "%s". Use getters and setters.', 'wp-ever-accounting' ), $key ), '1.1.0' );

		return true;
	}

	/**
	 * Filter null meta values from array.
	 *
	 * @param mixed $meta Meta value to check.
	 *
	 * @return bool
	 * @since  1.1.0
	 */
	protected function filter_null_meta( $meta ) {
		return ! is_null( $meta->value );
	}

	/**
	 * Get All Meta Data.
	 *
	 * @return array of objects.
	 * @since 1.1.0
	 */
	public function get_meta_data() {
		$this->maybe_read_meta_data();

		return array_values( array_filter( $this->meta_data, array( $this, 'filter_null_meta' ) ) );
	}

	/**
	 * Update Meta Data in the database.
	 *
	 * @since 1.1.0
	 */
	public function save_meta_data() {
		if ( ! $this->meta_type || is_null( $this->meta_data ) ) {
			return;
		}

		foreach ( $this->meta_data as $array_key => $meta ) {
			if ( is_null( $meta->value ) && ! empty( $meta->id ) ) {
				$this->delete_meta( $meta );
				unset( $this->meta_data[ $array_key ] );
			} elseif ( empty( $meta->id ) ) {
				$meta->id = $this->add_meta( $meta );
				$meta->apply_changes();
			} else if ( $meta->get_changes() ) {
				$this->update_meta( $meta );
				$meta->apply_changes();
			}
		}

		wp_cache_delete( $this->id, "ea_{$this->meta_type}meta" );
	}

	/**
	 * Deletes meta based on meta ID.
	 *
	 * @param \stdClass $meta (containing at least ->id).
	 *
	 * @since  1.1.0
	 */
	public function delete_meta( $meta ) {
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
		return add_metadata( $this->meta_type, $this->get_id(), $meta->key, is_string( $meta->value ) ? wp_slash( $meta->value ) : $meta->value, false );
	}

	/**
	 * Update meta.
	 *
	 * @param \stdClass $meta (containing ->id, ->key and ->value).
	 *
	 * @since  1.1.0
	 */
	public function update_meta( $meta ) {
		update_metadata_by_mid( $this->meta_type, $meta->id, $meta->value, $meta->key );
	}

	/**
	 * Gets a list of props and meta keys that need updated based on change state
	 * or if they are present in the database or not.
	 *
	 * @param array $meta_key_to_props A mapping of meta keys => prop names.
	 *
	 * @return array                        A mapping of meta keys => prop names, filtered by ones that should be updated.
	 */
	protected function get_props_to_update( $meta_key_to_props ) {
		$props_to_update = array();
		$changed_props   = $this->get_changes();

		// Props should be updated if they are a part of the $changed array or don't exist yet.
		foreach ( $meta_key_to_props as $meta_key => $prop ) {
			if ( array_key_exists( $prop, $changed_props ) || ! metadata_exists( $this->meta_type, $this->get_id(), $meta_key ) ) {
				$props_to_update[ $meta_key ] = $prop;
			}
		}

		return $props_to_update;
	}

	/**
	 * Returns as pure array.
	 *
	 * @return array
	 * @since 1.0.2
	 *
	 */
	public function to_array() {
		return array_merge( parent::to_array(), array( 'meta_data' => $this->get_meta_data() ) );
	}

}
