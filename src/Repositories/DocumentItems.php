<?php
/**
 * DocumentItems repository.
 *
 * Handle Invoice Item insert, update, delete & retrieve from database.
 *
 * @version   1.1.0
 * @package   EverAccounting\Repositories
 */

namespace EverAccounting\Repositories;

use EverAccounting\Abstracts\ResourceRepository;

defined( 'ABSPATH' ) || exit;

/**
 * Class InvoiceItems
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Repositories
 */
class DocumentItems extends ResourceRepository {
	/**
	 * Table name
	 *
	 * @var string
	 */
	const TABLE = 'ea_document_items';

	/**
	 * @since 1.1.0
	 *
	 * @var string
	 */
	protected $table = self::TABLE;

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
		'price'         => '%f',
		'quantity'      => '%f',
		'subtotal'      => '%f',
		'tax_rate'      => '%f',
		'discount'      => '%f',
		'tax'           => '%f',
		'total'         => '%f',
		'currency_code' => '%s',
		'extra'         => '%s',
		'date_created'  => '%s',
	);


}
