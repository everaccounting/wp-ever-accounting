<?php
/**
 * Handle the Category object.
 *
 * @package     EverAccounting
 * @class       Category
 * @version     1.2.1
 */

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;

/**
 * Core class used to implement the Category object.
 *
 * @package EverAccounting
 *
 * @since 1.2.1
 *
 * @property int $user_id
 * @property string $name
 * @property string $type
 * @property string $color
 * @property boolean $enabled
 * @property string $date_created
 */
class Category {
	/**
	 * Category data container.
	 *
	 * @since 1.2.1
	 * @var \stdClass
	 */
	public $data;

	/**
	 * Category id.
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $id = null;

	/**
	 * Category constructor.
	 *
	 * @param object $category Category Object
	 *
	 * @return void
	 * @since 1.2.1
	 */
	public function __construct( $category ) {
		if ( $category instanceof self ) {
			$this->id = (int) $category->id;
		} elseif ( is_numeric( $category ) ) {
			$this->id = $category;
		} elseif ( ! empty( $category->id ) ) {
			$this->id = (int) $category->id;
		} else {
			$this->id = 0;
		}

		if ( $this->id > 0 ) {
			$data = self::load( $this->id );
			if ( ! $data ) {
				$this->id = null;

				return;
			}
			$this->data = $data;
			$this->id   = (int) $data->id;
		}
	}

	/**
	 * Return only the main category fields
	 *
	 * @param int $id The id of the category
	 *
	 * @return object|false Raw category object
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 * @since 1.2.1
	 */
	public static function load( $id ) {
		global $wpdb;

		if ( ! absint( $id ) ) {
			return false;
		}

		$data = wp_cache_get( $id, 'ea_categories' );
		if ( $data ) {
			return $data;
		}

		$data = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}ea_categories WHERE id = %d LIMIT 1",
				$id
			)
		);

		if ( ! $data ) {
			return false;
		}

		eaccounting_set_cache( 'ea_categories', $data );

		return $data;
	}
	/**
	 * Magic method for checking the existence of a certain field.
	 *
	 * @param string $key Category field to check if set.
	 *
	 * @return bool Whether the given Category field is set.
	 * @since 1.2.1
	 */
	public function __isset( $key ) {
		if ( isset( $this->data->$key ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Magic method for setting category fields.
	 *
	 * This method does not update custom fields in the database.
	 *
	 * @param string $key Category key.
	 * @param mixed  $value Category value.
	 *
	 * @since 1.2.1
	 */
	public function __set( $key, $value ) {
		if ( is_callable( array( $this, 'set_' . $key ) ) ) {
			$this->$key( $value );
		} else {
			$this->data->$key = $value;
		}
	}

	/**
	 * Magic method for accessing custom fields.
	 *
	 * @param string $key Category field to retrieve.
	 *
	 * @return mixed Value of the given Category field (if set).
	 * @since 1.2.1
	 */
	public function __get( $key ) {

		if ( is_callable( array( $this, 'get_' . $key ) ) ) {
			$value = $this->$key();
		} else {
			$value = $this->data->$key;
		}

		return $value;
	}

	/**
	 * Magic method for unsetting a certain field.
	 *
	 * @param string $key Category key to unset.
	 *
	 * @since 1.2.1
	 */
	public function __unset( $key ) {
		if ( isset( $this->data->$key ) ) {
			unset( $this->data->$key );
		}
	}

	/**
	 * Determine whether a property or meta key is set
	 *
	 * Consults the categories.
	 *
	 * @param string $key Property
	 *
	 * @return bool
	 * @since 1.2.1
	 */
	public function has_prop( string $key ) {
		return $this->__isset( $key );
	}

	/**
	 * Determine whether the category exists in the database.
	 *
	 * @return bool True if category exists in the database, false if not.
	 * @since 1.2.1
	 */
	public function exists() {
		return ! empty( $this->id );
	}

	/**
	 * Return an array representation.
	 *
	 * @return array Array representation.
	 * @since 1.2.1
	 */
	public function to_array() {
		return get_object_vars( $this->data );
	}
}
