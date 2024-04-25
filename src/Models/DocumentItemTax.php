<?php

namespace EverAccounting\Models;

/**
 * DocumentItemTax model.
 *
 * @since 1.0.0
 * @author  Sultan Nasir Uddin <manikdrmc@gmail.com>
 * @package EverAccounting
 * @subpackage Models
 *
 * @property int    $id ID of the document_item_tax.
 * @property string $name Name of the document_item_tax.
 * @property double $rate Rate of the document_item_tax.
 * @property bool   $is_compound Compound of the document_item_tax.
 * @property double $amount Amount of the document_item_tax.
 * @property int    $item_id Item ID of the document_item_tax.
 * @property int    $tax_id Tax ID of the document_item_tax.
 * @property int    $document_id Document ID of the document_item_tax.
 * @property string $date_updated Date updated of the document_item_tax.
 * @property string $date_created Date created of the document_item_tax.
 *
 */
class DocumentItemTax extends Model {

	/**
	 * The table associated with the model.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $table = 'ea_document_item_tax';

	/**
	 * Table columns.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $columns = array(
		'id',
		'name',
		'rate',
		'is_compound',
		'amount',
		'item_id',
		'tax_id',
		'document_id',
	);

}
