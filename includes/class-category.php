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
 */
class Category {
	/**
	 * Category id.
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $id = null;

	/**
	 * Category name.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $name = '';

	/**
	 * Category type.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $type = '';

	/**
	 * Category color.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $color = '';

	/**
	 * Item status.
	 *
	 * @since 1.2.1
	 * @var bool
	 */
	public $enabled = true;

	/**
	 * Item creator user id.
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $creator_id = 0;

	/**
	 * Item created date.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $date_created = '0000-00-00 00:00:00';


	/**
	 * Retrieve Category instance.
	 *
	 * @param int $category_id Category id.
	 *
	 * @return Category|false Category object, false otherwise.
	 * @since 1.2.1
	 *
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 *
	 */
	public static function get_instance( $category_id ) {
		global $wpdb;

		$category_id = (int) $category_id;
		if ( ! $category_id ) {
			return false;
		}

		$_item = wp_cache_get( $category_id, 'ea_categories' );

		if ( ! $_item ) {
			$_item = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_categories WHERE id = %d LIMIT 1", $category_id ) );

			if ( ! $_item ) {
				return false;
			}

			$_item = eaccounting_sanitize_category( $_item, 'raw' );
			wp_cache_add( $_item->id, $_item, 'ea_categories' );
		} elseif ( empty( $_item->filter ) ) {
			$_item = eaccounting_sanitize_category( $_item, 'raw' );
		}

		return new Category( $_item );
	}

	/**
	 * Category constructor.
	 *
	 * @param $category
	 *
	 * @since 1.2.1
	 */
	public function __construct( $category ) {
		foreach ( get_object_vars( $category ) as $key => $value ) {
			$this->$key = $value;
		}
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
		if ( isset( $this->$key ) ) {
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
	 * @param mixed $value Category value.
	 *
	 * @since 1.2.1
	 */
	public function __set( $key, $value ) {
		if ( is_callable( array( $this, 'set_' . $key ) ) ) {
			$this->$key( $value );
		} else {
			$this->$key = $value;
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
			$value = $this->$key;
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
		if ( isset( $this->$key ) ) {
			unset( $this->$key );
		}
	}

	/**
	 * Filter category object based on context.
	 *
	 * @param string $filter Filter.
	 *
	 * @return Category|Object
	 * @since 1.2.1
	 */
	public function filter( $filter ) {
		if ( $this->filter === $filter ) {
			return $this;
		}

		if ( 'raw' === $filter ) {
			return self::get_instance( $this->id );
		}

		return eaccounting_sanitize_category( $this, $filter );
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
		return get_object_vars( $this );
	}
}
