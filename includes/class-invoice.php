<?php
/**
 * Handle the Invoice object.
 *
 * @package     EverAccounting
 * @class       Invoice
 * @version     1.2.1
 */

namespace EverAccounting;

use EverAccounting\Abstracts\MetaData;

defined( 'ABSPATH' ) || exit;

/**
 * Core class used to implement the Invoice object.
 *
 * @package EverAccounting
 *
 * @since 1.2.1
 *
 */
class Invoice extends MetaData {
	/**
	 * Invoice id.
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $id = null;

	/**
	 *
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $document_number = '';
	/**
	 *
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $type = '';
	/**
	 *
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $order_number = '';
	/**
	 *
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $status = '';
	/**
	 *
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $issue_date = '0000-00-00 00:00:00';
	/**
	 *
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $due_date = '0000-00-00 00:00:00';
	/**
	 *
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $payment_date = '0000-00-00 00:00:00';
	/**
	 *
	 *
	 * @since 1.2.1
	 * @var null
	 */
	public $category_id = null;
	/**
	 *
	 *
	 * @since 1.2.1
	 * @var null
	 */
	public $contact_id = null;
	/**
	 *
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $address = '';
	/**
	 *
	 *
	 * @since 1.2.1
	 * @var float
	 */
	public $discount = 0.00;
	/**
	 *
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $discount_type = 'fixed';
	/**
	 *
	 *
	 * @since 1.2.1
	 * @var float
	 */
	public $subtotal = 0.00;
	/**
	 *
	 *
	 * @since 1.2.1
	 * @var float
	 */
	public $total_tax = 0.00;
	/**
	 *
	 *
	 * @since 1.2.1
	 * @var float
	 */
	public $total_discount = 0.00;
	/**
	 *
	 *
	 * @since 1.2.1
	 * @var float
	 */
	public $total_fees = 0.00;
	/**
	 *
	 *
	 * @since 1.2.1
	 * @var float
	 */
	public $total_shipping = 0.00;
	/**
	 *
	 *
	 * @since 1.2.1
	 * @var float
	 */
	public $total = 0.00;
	/**
	 *
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $tax_inclusive = 0;
	/**
	 *
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $note = '';
	/**
	 *
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $terms = '';

	/**
	 *
	 *
	 * @since 1.2.1
	 * @var null
	 */
	public $attachment_id = null;
	/**
	 *
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $currency_code = '';
	/**
	 *
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $currency_rate = 1;

	/**
	 *
	 *
	 * @since 1.2.1
	 * @var null
	 */
	public $parent_id = null;

	/**
	 * Transaction creator user id.
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $creator_id = 0;

	/**
	 * Invoice created date.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $date_created = '0000-00-00 00:00:00';

	/**
	 * Stores the transaction object's sanitization level.
	 *
	 * Does not correspond to a DB field.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $filter;

	/**
	 * Meta type.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	protected $meta_type = 'invoice';

	/**
	 * Retrieve Invoice instance.
	 *
	 * @param int $invoice_id Invoice id.
	 *
	 * @return Invoice|false Invoice object, false otherwise.
	 * @since 1.2.1
	 *
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 *
	 */
	public static function get_instance( $invoice_id ) {
		global $wpdb;

		$invoice_id = (int) $invoice_id;
		if ( ! $invoice_id ) {
			return false;
		}

		$_item = wp_cache_get( $invoice_id, 'ea_invoices' );

		if ( ! $_item ) {
			$_item = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_invoices WHERE id = %d LIMIT 1", $invoice_id ) );

			if ( ! $_item ) {
				return false;
			}

			$_item = eaccounting_sanitize_invoice( $_item, 'raw' );
			wp_cache_add( $_item->id, $_item, 'ea_invoices' );
		} elseif ( empty( $_item->filter ) ) {
			$_item = eaccounting_sanitize_invoice( $_item, 'raw' );
		}

		return new Invoice( $_item );
	}

	/**
	 * Account constructor.
	 *
	 * @param $invoice
	 *
	 * @since 1.2.1
	 */
	public function __construct( $invoice ) {
		foreach ( get_object_vars( $invoice ) as $key => $value ) {
			$this->$key = $value;
		}
	}

	/**
	 * Magic method for checking the existence of a certain field.
	 *
	 * @param string $key Account field to check if set.
	 *
	 * @return bool Whether the given Account field is set.
	 * @since 1.2.1
	 */
	public function __isset( $key ) {
		if ( isset( $this->$key ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Magic method for setting invoice fields.
	 *
	 * This method does not update custom fields in the database.
	 *
	 * @param string $key Account key.
	 * @param mixed $value Account value.
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
	 * @param string $key Account field to retrieve.
	 *
	 * @return mixed Value of the given Account field (if set).
	 * @since 1.2.1
	 */
	public function __get( $key ) {
		$value = '';
		if ( method_exists( $this, 'get_' . $key ) ) {
			eaccounting_doing_it_wrong( __FUNCTION__, sprintf( __( 'Object data such as "%s" should not be accessed directly. Use getters and setters.', 'wp-ever-accounting' ), $key ), '1.1.0' );
			$value = $this->{'get_' . $key}();
		} else if ( property_exists( $this, $key ) && is_callable( array( $this, $key ) ) ) {
			$value = $this->$key;
		} else if ( $this->meta_exists( $key ) ) {
			$value = $this->get_meta( $key );
			$value = eaccounting_sanitize_contact_field( $key, $value, $this->id, $this->filter );
		}

		return $value;
	}

	/**
	 * Magic method for unsetting a certain field.
	 *
	 * @param string $key Account key to unset.
	 *
	 * @since 1.2.1
	 */
	public function __unset( $key ) {
		if ( isset( $this->$key ) ) {
			unset( $this->$key );
		}
	}

	/**
	 * Filter invoice object based on context.
	 *
	 * @param string $filter Filter.
	 *
	 * @return Account|Object
	 * @since 1.2.1
	 *
	 */
	public function filter( $filter ) {
		if ( $this->filter === $filter ) {
			return $this;
		}

		if ( 'raw' === $filter ) {
			return self::get_instance( $this->id );
		}

		return eaccounting_sanitize_invoice( $this, $filter );
	}

	/**
	 * Determine whether a property or meta key is set
	 *
	 * Consults the invoices.
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
		return get_object_vars( $this );
	}

}
