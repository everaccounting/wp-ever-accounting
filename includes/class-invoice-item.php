<?php
/**
 * Handle the Invoice_Item object.
 *
 * @package     EverAccounting
 * @class       Invoice_Item
 * @version     1.2.1
 */

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;

/**
 * Core class used to implement the Invoice_Item object.
 *
 * @package EverAccounting
 *
 * @since 1.2.1
 *
 * @property int $document_id
 * @property int $item_id
 * @property string $item_name
 * @property float $price
 * @property float $quantity
 * @property float $subtotal
 * @property float $tax_rate
 * @property float $discount
 * @property float $tax
 * @property float $total
 * @property string $currency_code
 * @property string $extra
 * @property string $date_created
 */
class Invoice_Item {
	/**
	 * Invoice_Item data container.
	 *
	 * @since 1.2.1
	 * @var \stdClass
	 */
	public $data;

	/**
	 * Invoice_Item id.
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $id = null;

	/**
	 * Invoice_Item constructor.
	 *
	 * @param object $invoice_item Invoice_Item Object
	 *
	 * @return void
	 * @since 1.2.1
	 */
	public function __construct( $invoice_item ) {
		if ( $invoice_item instanceof self ) {
			$this->id = (int) $invoice_item->id;
		} elseif ( is_numeric( $invoice_item ) ) {
			$this->id = $invoice_item;
		} elseif ( ! empty( $invoice_item->id ) ) {
			$this->id = (int) $invoice_item->id;
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
	 * Return only the main invoice_item fields
	 *
	 * @param int $id The id of the invoice_item
	 *
	 * @return object|false Raw invoice_item object
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 * @since 1.2.1
	 */
	public static function load( $id ) {
		global $wpdb;

		if ( ! absint( $id ) ) {
			return false;
		}

		$data = wp_cache_get( $id, 'ea_invoice_items' );
		if ( $data ) {
			return $data;
		}

		$data = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}ea_document_items WHERE id = %d LIMIT 1",
				$id
			)
		);

		if ( ! $data ) {
			return false;
		}

		eaccounting_set_cache( 'ea_invoice_items', $data );

		return $data;
	}

	/**
	 * Magic method for checking the existence of a certain field.
	 *
	 * @param string $key Invoice_Item field to check if set.
	 *
	 * @return bool Whether the given Invoice_Item field is set.
	 * @since 1.2.1
	 */
	public function __isset( $key ) {
		if ( isset( $this->data->$key ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Magic method for setting invoice_item fields.
	 *
	 * This method does not update custom fields in the database.
	 *
	 * @param string $key Invoice_Item key.
	 * @param mixed  $value Invoice_Item value.
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
	 * @param string $key Invoice_Item field to retrieve.
	 *
	 * @return mixed Value of the given Invoice_Item field (if set).
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
	 * @param string $key Invoice_Item key to unset.
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
	 * Consults the invoice_items.
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
	 * Determine whether the invoice_item exists in the database.
	 *
	 * @return bool True if invoice_item exists in the database, false if not.
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
