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
 * @property string $name
 * @property string $sku
 * @property string $description
 * @property float $sale_price
 * @property float $purchase_price
 * @property float $quantity
 * @property int $category_id
 * @property float $sales_tax
 * @property int $purchase_tax
 * @property float $thumbnail_id
 * @property boolean $enabled
 * @property int $creator_id
 * @property string $date_created
 */
class Item {
	/**
	 * Item data container.
	 *
	 * @since 1.2.1
	 * @var \stdClass
	 */
	public $data;

	/**
	 * Item id.
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $id = null;

	/**
	 * Item constructor.
	 *
	 * @param object $item Item Object
	 *
	 * @return void
	 * @since 1.2.1
	 */
	public function __construct( $item ) {
		if ( $item instanceof self ) {
			$this->id = absint( $item->id );
		} elseif ( is_object( $item ) && ! empty( $item->id ) ) {
			$this->id = absint( $item->id );
		} elseif ( is_array( $item ) && ! empty( $item['id'] ) ) {
			$this->id = absint( $item['id'] );
		} else {
			$this->id = absint( $item );
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
	 * Return only the main item fields
	 *
	 * @param int $id The id of the item
	 *
	 * @return object|false Raw item object
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 * @since 1.2.1
	 */
	public static function load( $id ) {
		global $wpdb;

		if ( ! absint( $id ) ) {
			return false;
		}

		$data = wp_cache_get( $id, 'ea_items' );
		if ( $data ) {
			return $data;
		}

		$_data = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}ea_items WHERE id = %d LIMIT 1",
				$id
			)
		);

		if ( ! $_data ) {
			return false;
		}
		$data = eaccounting_sanitize_item( $_data, 'raw' );

		eaccounting_set_cache( 'ea_items', $data );

		return $data;
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
		if ( isset( $this->data->$key ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Magic method for setting item fields.
	 *
	 * This method does not update custom fields in the database.
	 *
	 * @param string $key Item key.
	 * @param mixed  $value Item value.
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
	 * @param string $key Item field to retrieve.
	 *
	 * @return mixed Value of the given Item field (if set).
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
	 * @param string $key Item key to unset.
	 *
	 * @since 1.2.1
	 */
	public function __unset( $key ) {
		if ( isset( $this->data->$key ) ) {
			unset( $this->data->$key );
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
			return self::load( $this->id );
		}

		$this->data = eaccounting_sanitize_item( $this->data, $filter );

		return $this;
	}

	/**
	 * Determine whether a property or meta key is set
	 *
	 * Consults the items.
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
		return get_object_vars( $this->data );
	}
}
