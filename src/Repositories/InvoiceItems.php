<?php
/**
 * InvoiceItem repository.
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
class InvoiceItems extends ResourceRepository {
	/**
	 * Table name
	 *
	 * @var string
	 */
	const TABLE = 'ea_categories';

	/**
	 * @since 1.1.0
	 * @var string
	 */
	protected $table = self::TABLE;

	/**
	 * A map of database fields to data types.
	 *
	 * @since 1.1.0
	 * @var array
	 */
	protected $data_type = array(
		'id'           => '%d',
		'invoice_id'   => '%d',
		'item_id'      => '%d',
		'name'         => '%s',
		'sku'          => '%s',
		'quantity'     => '%f',
		'price'        => '%f',
		'total'        => '%f',
		'tax_id'       => '%d',
		'tax_name'     => '%s',
		'tax'          => '%f',
		'date_created' => '%s',
	);

}
