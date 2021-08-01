<?php
/**
 * Handle the Invoice object.
 *
 * @package     EverAccounting
 * @class       Invoice
 * @version     1.2.1
 */

namespace EverAccounting;

use EverAccounting\Abstracts\Data;
use EverAccounting\Abstracts\MetaData;

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
class Invoice extends Data {
	/**
	 * Invoice id.
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $id = null;

	/**
	 * Invoice data container.
	 *
	 * @since 1.2.1
	 * @var array
	 */
	public $data = array(
		'document_number' => '',
		'type'            => '',
		'order_number'    => '',
		'status'          => 'draft',
		'issue_date'      => null,
		'due_date'        => null,
		'payment_date'    => null,
		'category_id'     => null,
		'contact_id'      => null,
		'address'         => array(
			'name'       => '',
			'company'    => '',
			'street'     => '',
			'city'       => '',
			'state'      => '',
			'postcode'   => '',
			'country'    => '',
			'email'      => '',
			'phone'      => '',
			'vat_number' => '',
		),
		'discount'        => 0.00,
		'discount_type'   => 'percentage',
		'subtotal'        => 0.00,
		'total_tax'       => 0.00,
		'total_discount'  => 0.00,
		'total_fees'      => 0.00,
		'total_shipping'  => 0.00,
		'total'           => 0.00,
		'tax_inclusive'   => 1,
		'note'            => '',
		'terms'           => '',
		'attachment_id'   => null,
		'currency_code'   => null,
		'currency_rate'   => 1,
		'key'             => null,
		'parent_id'       => null,
		'creator_id'      => null,
		'date_created'    => null,
	);

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
	 * Filter invoice object based on context.
	 *
	 * @param string $filter Filter.
	 *
	 * @return Invoice|Object
	 * @since 1.2.1
	 */
	public function filter( $filter ) {
		if ( $this->filter === $filter ) {
			return $this;
		}

		if ( 'raw' === $filter ) {
			return self::get_instance( $this->id );
		}

		return new self( eaccounting_sanitize_invoice( (object) $this->to_array(), $filter ) );
	}
}
