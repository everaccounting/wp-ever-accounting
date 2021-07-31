<?php
/**
 * Handle the Item object.
 *
 * @package     EverAccounting
 * @class       Item
 * @version     1.2.1
 */

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;

/**
 * Core class used to implement the Item object.
 *
 * @package EverAccounting
 *
 * @since 1.2.1
 *
 */
class Item {
	/**
	 * Item id.
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $id = null;

	/**
	 * Name of the item.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $name = '';

	/**
	 * Item SKU
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $sku = '';

	/**
	 * Item description
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $description = '';


	/**
	 * Item sale price.
	 *
	 * @since 1.2.1
	 * @var float
	 */
	public $sale_price = 0.00;


	/**
	 * Item purchase price
	 *
	 * @since 1.2.1
	 * @var float
	 */
	public $purchase_price = 0.00;

	/**
	 * Item stock quantity
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $quantity = 0;

	/**
	 * Item category id
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $category_id = 0;

	/**
	 * Item sales tax
	 *
	 * @since 1.2.1
	 * @var float
	 */
	public $sales_tax = 0.00;

	/**
	 * Item purchase tax
	 *
	 * @since 1.2.1
	 * @var float
	 */
	public $purchase_tax = 0.00;

	/**
	 * Item thumbnail id.
	 *
	 * @since 1.2.1
	 * @var null
	 */
	public $thumbnail_id = null;

	/**
	 * Item status
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
	 * Stores the item object's sanitization level.
	 *
	 * Does not correspond to a DB field.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $filter;

	/**
	 * Retrieve Item instance.
	 *
	 * @param int $item_id Item id.
	 *
	 * @return Item|false Item object, false otherwise.
	 * @since 1.2.1
	 *
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 *
	 */
	public static function get_instance( $item_id ) {
		global $wpdb;

		$item_id = (int) $item_id;
		if ( ! $item_id ) {
			return false;
		}

		$_item = wp_cache_get( $item_id, 'ea_items' );

		if ( ! $_item ) {
			$_item = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_items WHERE id = %d LIMIT 1", $item_id ) );

			if ( ! $_item ) {
				return false;
			}

			$_item = eaccounting_sanitize_item( $_item, 'raw' );
			wp_cache_add( $_item->id, $_item, 'ea_items' );
		} elseif ( empty( $_item->filter ) ) {
			$_item = eaccounting_sanitize_item( $_item, 'raw' );
		}

		return new Item( $_item );
	}

	/**
	 * Item constructor.
	 *
	 * @param $item
	 *
	 * @since 1.2.1
	 */
	public function __construct( $item ) {
		foreach ( get_object_vars( $item ) as $key => $value ) {
			$this->$key = $value;
		}
	}

	/**
	 * Magic method for checking the existence of a certain field.
	 *
	 * @param string $key Item field to check if set.
	 *
	 * @return bool Whether the given Item field is set.
	 * @since 1.2.1
	 */
	public function __isset( $key ) {
		if ( isset( $this->$key ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Magic method for setting Item fields.
	 *
	 * This method does not update custom fields in the database.
	 *
	 * @param string $key Item key.
	 * @param mixed $value Item value.
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
	 * @param string $key item field to retrieve.
	 *
	 * @return mixed Value of the given item field (if set).
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
	 * @param string $key item key to unset.
	 *
	 * @since 1.2.1
	 */
	public function __unset( $key ) {
		if ( isset( $this->$key ) ) {
			unset( $this->$key );
		}
	}

	/**
	 * Filter item object based on context.
	 *
	 * @param string $filter Filter.
	 *
	 * @return Item|Object
	 * @since 1.2.1
	 */
	public function filter( $filter ) {
		if ( $this->filter === $filter ) {
			return $this;
		}

		if ( 'raw' === $filter ) {
			return self::get_instance( $this->id );
		}

		return eaccounting_sanitize_item( $this, $filter );
	}

	/**
	 * Determine whether a property or meta key is set
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
	 * Determine whether the item exists in the database.
	 *
	 * @return bool True if item exists in the database, false if not.
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
