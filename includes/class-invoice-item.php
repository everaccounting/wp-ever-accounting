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
	 * Item Data array.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	public $data = array(
		'invoice_id'    => null,
		'item_id'       => null,
		'item_name'     => '',
		'price'         => 0.00,
		'quantity'      => 1,
		'subtotal'      => 0.00,
		'tax_rate'      => 0.00,
		'discount'      => 0.00,
		'tax'           => 0.00,
		'total'         => 0.00,
		'currency_code' => '',
		'extra'         => array(
			'shipping'     => 0.00,
			'shipping_tax' => 0.00,
			'fees'         => 0.00,
			'fees_tax'     => 0.00,
		),
		'date_created'  => null,
	);

	/**
	 * A map of database fields to data types.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $data_type = array(
		'id'            => '%d',
		'document_id'   => '%d',
		'item_id'       => '%d',
		'item_name'     => '%s',
		'price'         => '%.4f',
		'quantity'      => '%.2f',
		'subtotal'      => '%.4f',
		'tax_rate'      => '%.4f',
		'discount'      => '%.4f',
		'tax'           => '%.4f',
		'total'         => '%.4f',
		'currency_code' => '%s',
		'extra'         => '%s',
		'date_created'  => '%s',
	);
}
