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
abstract class MetaData_BK extends Data {
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
	 * A map of meta keys to data props.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $meta_key_to_props = array();

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
	 * Callback to remove unwanted meta data.
	 *
	 * @param object $meta Meta object to check if it should be excluded or not.
	 *
	 * @return bool
	 */
	protected function exclude_internal_meta_keys( $meta ) {
		return ! in_array( $meta->meta_key, $this->internal_meta_keys, true ) && 0 !== stripos( $meta->meta_key, 'wp_' );
	}

	/**
	 * Gets a list of props and meta keys that need updated based on change state
	 * or if they are present in the database or not.
	 *
	 * @param array $meta_key_to_props A mapping of meta keys => prop names.
	 * @param string $meta_type The internal WP meta type (post, user, etc).
	 *
	 * @return array A mapping of meta keys => prop names, filtered by ones that should be updated.
	 */
	protected function get_props_to_update( $meta_key_to_props, $meta_type ) {
		$props_to_update = array();
		$changed_props   = $this->get_changes();

		// Props should be updated if they are a part of the $changed array or don't exist yet.
		foreach ( $meta_key_to_props as $meta_key => $prop ) {
			if ( array_key_exists( $prop, $changed_props ) || ! metadata_exists( $meta_type, $this->get_id(), $meta_key ) ) {
				$props_to_update[ $meta_key ] = $prop;
			}
		}

		return $props_to_update;
	}

	/**
	 * Update meta data in, or delete it from, the database.
	 *
	 * Avoids storing meta when it's either an empty string or empty array.
	 * Other empty values such as numeric 0 and null should still be stored.
	 * Data-stores can force meta to exist using `must_exist_meta_keys`.
	 *
	 * Note: WordPress `get_metadata` function returns an empty string when meta data does not exist.
	 *
	 * @param string $meta_key Meta key to update.
	 * @param mixed $meta_value Value to save.
	 *
	 * @return bool True if updated/deleted.
	 */
	protected function update_or_delete_meta( $meta_key, $meta_value ) {
		if ( in_array( $meta_value, array(
				array(),
				''
			), true ) && ! in_array( $meta_key, $this->must_exist_meta_keys, true ) ) {
			$updated = delete_metadata( $this->meta_type, $this->get_id(), $meta_key );
		} else {
			$updated = update_metadata( $this->meta_type, $this->get_id(), $meta_key, $meta_value );
		}

		return (bool) $updated;
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
	 * Read meta data if null.
	 *
	 * @since 1.1.0
	 */
	protected function maybe_read_meta_data() {
		if ( is_null( $this->meta_data ) ) {
			$this->read_meta_data();
		}
	}


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
			$meta_data = wp_cache_get( $this->get_id(), "{$this->meta_type}meta" );
		}

		$table = _get_meta_table( $this->meta_type );
		if ( ! $table ) {
			return;
		}

		if ( $meta_data === false ) {
			$object_id_field = sanitize_key( ltrim( $this->meta_type, 'ea_' ) . '_id' );
			$raw_meta_data   = $wpdb->get_results( $wpdb->prepare(
				"SELECT meta_id, meta_key, meta_value
				FROM {$table}
				WHERE {$object_id_field} = %d
				ORDER BY meta_id",
				$this->get_id()
			) );
			$meta_data       = array_map( array( $this, 'unserialize_metadata' ), $raw_meta_data );
			wp_cache_add( $this->id, $meta_data, "{$this->meta_type}meta" );
		}

		if ( ! empty( $meta_data ) ) {
			$this->meta_data = $meta_data;
		}
	}
}
