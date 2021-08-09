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
 *
 * @property Invoice_Item[] $items
 */
class Invoice extends Data {

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
	 * A map of database fields to data types.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $data_type = array(
		'id'              => '%d',
		'document_number' => '%s',
		'type'            => '%s',
		'order_number'    => '%s',
		'status'          => '%s',
		'issue_date'      => '%s',
		'due_date'        => '%s',
		'payment_date'    => '%s',
		'category_id'     => '%d',
		'contact_id'      => '%d',
		'address'         => '%s',
		'currency_code'   => '%s',
		'currency_rate'   => '%.8f',
		'discount'        => '%.4f',
		'discount_type'   => '%s',
		'subtotal'        => '%.4f',
		'total_tax'       => '%.4f',
		'total_discount'  => '%.4f',
		'total_fees'      => '%.4f',
		'total_shipping'  => '%.4f',
		'total'           => '%.4f',
		'tax_inclusive'   => '%d',
		'note'            => '%s',
		'terms'           => '%s',
		'attachment_id'   => '%d',
		'key'             => '%s',
		'parent_id'       => '%d',
		'creator_id'      => '%d',
		'date_created'    => '%s',
	);

}
