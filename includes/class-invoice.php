<?php
/**
 * Handle the Invoice object.
 *
 * @package     EverAccounting
 * @class       Invoice
 * @version     1.2.1
 */

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;

/**
 * Core class used to implement the Invoice object.
 *
 * @package EverAccounting
 *
 * @since 1.2.1
 *
 * @property string $document_number
 * @property string $type
 * @property string $order_number
 * @property string $status
 * @property string $issue_date
 * @property string $due_date
 * @property string $payment_date
 * @property int $category_id
 * @property int $contact_id
 * @property string $address
 * @property string $currency_code
 * @property float $currency_rate
 * @property float $discount
 * @property string $discount_type
 * @property float $subtotal
 * @property float $total_tax
 * @property float $total_discount
 * @property float $total_fees
 * @property float $total_shipping
 * @property float $total
 * @property boolean $tax_inclusive
 * @property string $note
 * @property string $terms
 * @property int $attachment_id
 * @property string $key
 * @property int $parent_id
 * @property int $creator_id
 * @property string $date_created
 */
class Invoice {
	/**
	 * Invoice data container.
	 *
	 * @since 1.2.1
	 * @var \stdClass
	 */
	public $data;

	/**
	 * Invoice id.
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $id = null;

	/**
	 * Invoice constructor.
	 *
	 * @param object $invoice Invoice Object
	 *
	 * @return void
	 * @since 1.2.1
	 */
	public function __construct( $invoice ) {
		if ( $invoice instanceof self ) {
			$this->id = (int) $invoice->id;
		} elseif ( is_numeric( $invoice ) ) {
			$this->id = $invoice;
		} elseif ( ! empty( $invoice->id ) ) {
			$this->id = (int) $invoice->id;
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
	 * Return only the main invoice fields
	 *
	 * @param int $id The id of the invoice
	 *
	 * @return object|false Raw invoice object
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 * @since 1.2.1
	 */
	public static function load( $id ) {
		global $wpdb;

		if ( ! absint( $id ) ) {
			return false;
		}

		$data = wp_cache_get( $id, 'ea_invoices' );
		if ( $data ) {
			return $data;
		}

		$data = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}ea_documents WHERE id = %d LIMIT 1",
				$id
			)
		);

		if ( ! $data ) {
			return false;
		}

		eaccounting_set_cache( 'ea_invoices', $data );

		return $data;
	}

	/**
	 * Magic method for checking the existence of a certain field.
	 *
	 * @param string $key Invoice field to check if set.
	 *
	 * @return bool Whether the given Contact field is set.
	 * @since 1.2.1
	 */
	public function __isset( $key ) {
		if ( isset( $this->data->$key ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Magic method for setting invoice fields.
	 *
	 * This method does not update custom fields in the database..
	 *
	 * @param string $key Invoice key.
	 * @param mixed  $value Invoice value.
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
	 * @param string $key Invoice field to retrieve.
	 *
	 * @return mixed Value of the given Invoice field (if set).
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
	 * @param string $key Invoice key to unset.
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
	 * Consults the documents tables.
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
	 * Determine whether the invoice exists in the database.
	 *
	 * @return bool True if invoice exists in the database, false if not.
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
