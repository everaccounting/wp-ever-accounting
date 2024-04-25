<?php

namespace EverAccounting\Models;

/**
 * DocumentItem model.
 *
 * @since 1.0.0
 * @author  Sultan Nasir Uddin <manikdrmc@gmail.com>
 * @package EverAccounting
 * @subpackage Models
 *
 * @property int    $id ID of the document_items.
 * @property string $type Type of the document_items.
 * @property string $name Name of the document_items.
 * @property double $price Price of the document_items.
 * @property double $quantity Quantity of the document_items.
 * @property double $subtotal Subtotal of the document_items.
 * @property double $subtotal_tax Subtotal_tax of the document_items.
 * @property double $discount Discount of the document_items.
 * @property double $discount_tax Discount Tax of the document_items.
 * @property double $tax_total Tax total of the document_items.
 * @property double $total Total of the document_items.
 * @property int    $taxable Taxable of the document_items.
 * @property string $description Description of the document_items.
 * @property string $unit Unit of the document_items.
 * @property int    $item_id Item ID of the document_items.
 * @property int    $document_id Document ID of the document_items.
 * @property string $date_updated Date updated of the document_items.
 * @property string $date_created Date created of the document_items.
 */
class DocumentItem extends Model{
	/**
	 * The table associated with the model.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $table = 'ea_document_items';

	/**
	 * Table columns.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $columns = array(
		'id',
		'type',
		'name',
		'price',
		'quantity',
		'subtotal',
		'subtotal_tax',
		'discount',
		'discount_tax',
		'tax_total',
		'total',
		'taxable',
		'description',
		'unit',
		'item_id',
		'document_id',
	);
}
