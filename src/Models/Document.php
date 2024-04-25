<?php

namespace EverAccounting\Models;

/**
 * Document model.
 *
 * @since 1.0.0
 * @author  Sultan Nasir Uddin <manikdrmc@gmail.com>
 * @package EverAccounting
 * @subpackage Models
 *
 * @property int    $id ID of the document.
 * @property string $type Type of the document.
 * @property string $status Status of the document.
 * @property int    $contact_id Contact ID of the document.
 * @property double $items_total Item total of the document.
 * @property double $discount_total Discount total of the document.
 * @property double $shipping_total Shipping total of the document.
 * @property double $fees_total Fees total of the document.
 * @property double $tax_total Tax total of the document.
 * @property double $total Total of the document.
 * @property double $total_paid Total paid of the document.
 * @property double $balance Balance of the document.
 * @property string $discount_type Discount type of the document.
 * @property string $reference Reference of the document.
 * @property string $note Note of the document.
 * @property int    $tax_inclusive Tax inclusive of the document.
 * @property int    $vat_exempt Vat exempt of the document.
 * @property string $issue_date Issue date of the document.
 * @property string $due_date Due date of the document.
 * @property string $sent_date Sent date of the document.
 * @property string $payment_date Payment date of the document.
 * @property string $currency_code Currency code of the document.
 * @property double $exchange_rate Exchange rate of the document.
 * @property int    $parent_id Parent ID of the document.
 * @property string $created_via Created via of the document.
 * @property int    $author_id Author ID of the document.
 * @property string $uuid UUID of the document.
 * @property string $date_updated Date updated of the document.
 * @property string $date_created Date created of the document.
 */
class Document extends Model {

	/**
	 * The table associated with the model.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $table = 'ea_documents';

	/**
	 * Table columns.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $columns = array(
		'id',
		'type',
		'status',
		'contact_id',
		'items_total',
		'discount_total',
		'shipping_total',
		'fees_total',
		'tax_total',
		'total',
		'total_paid',
		'balance',
		'discount_type',
		'reference',
		'note',
		'tax_inclusive',
		'vat_exempt',
		'issue_date',
		'due_date',
		'sent_date',
		'payment_date',
		'currency_code',
		'exchange_rate',
		'parent_id',
		'created_via',
		'author_id',
		'uuid',
	);
}
