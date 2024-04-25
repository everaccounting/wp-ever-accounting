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
 * @property int    $id ID of the document_item.
 * @property string $type Type of the document_item.
 * @property string $name Name of the document_item.
 * @property double $price Price of the document_item.
 * @property double $quantity Quantity of the document_item.
 * @property double $subtotal Subtotal of the document_item.
 * @property double $subtotal_tax Subtotal_tax of the document_item.
 * @property double $discount Discount of the document_item.
 * @property double $discount_tax Discount Tax of the document_item.
 * @property double $tax_total Tax total of the document_item.
 * @property double $total Total of the document_item.
 * @property int    $taxable Taxable of the document_item.
 * @property string $description Description of the document_item.
 * @property string $unit Unit of the document_item.
 * @property int    $item_id Item ID of the document_item.
 * @property int    $document_id Document ID of the document_item.
 * @property string $date_updated Date updated of the document_item.
 * @property string $date_created Date created of the document_item.
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
