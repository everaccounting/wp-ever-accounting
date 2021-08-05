<?php
/**
 * Handle the Invoice_Item object.
 *
 * @package     EverAccounting
 * @class       Invoice_Item
 * @version     1.2.1
 */

namespace EverAccounting;

use EverAccounting\Abstracts\Data;

defined( 'ABSPATH' ) || exit;

/**
 * Core class used to implement the Invoice_Item object.
 *
 * @package EverAccounting
 *
 * @since 1.2.1
 * @property int $invoice_id
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
class Invoice_Item extends Data {
	/**
	 * Invoice Item id.
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $id = null;

	/**
	 *
	 *
	 * @since 1.2.1
	 * @var null
	 */
	public $document_id = null;

	/**
	 *
	 *
	 * @since 1.2.1
	 * @var null
	 */
	public $item_id = null;

	/**
	 *
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $item_name = '';

	/**
	 *
	 *
	 * @since 1.2.1
	 * @var float
	 */
	public $price = 0.00;

	/**
	 *
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $quantity = 1;

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
	 * @var int
	 */
	public $tax_rate = 0;

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
	 * @var float
	 */
	public $tax = 0.00;

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
	 * @var string
	 */
	public $currency_code = '';

	/**
	 *
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $extra = '';

	/**
	 * Invoice item date.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $date_created = '0000-00-00 00:00:00';

	/**
	 * Retrieve Item instance.
	 *
	 * @param int $item_id Item id.
	 *
	 * @return Invoice_Item|false Item object, false otherwise.
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

		$_item = wp_cache_get( $item_id, 'ea_invoice_items' );

		if ( ! $_item ) {
			$_item = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_invoice_items WHERE id = %d LIMIT 1", $item_id ) );

			if ( ! $_item ) {
				return false;
			}
			$_item = eaccounting_sanitize_invoice_item( $_item, 'raw' );
			wp_cache_add( $_item->id, $_item, 'ea_invoice_items' );
		}

		return new self( $_item );
	}

	/**
	 * Item constructor.
	 *
	 * @param $item
	 *
	 * @since 1.2.1
	 */
	public function __construct( $item = null ) {
		foreach ( get_object_vars( $item ) as $key => $value ) {
			$this->$key = $value;
		}
	}

	/**
	 * Filter item object based on context.
	 *
	 * @param string $context Context.
	 *
	 * @return Invoice_Item|Object
	 * @since 1.2.1
	 */
	public function filter( $context ) {
		if ( $this->context === $context ) {
			return $this;
		}

		if ( 'raw' === $context ) {
			return self::get_instance( $this->id );
		}

		return eaccounting_sanitize_invoice_item( $this, $context );
	}
}
