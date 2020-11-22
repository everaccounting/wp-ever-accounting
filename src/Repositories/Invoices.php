<?php
/**
 * Invoice repository.
 *
 * Handle invoice insert, update, delete & retrieve from database.
 *
 * @version   1.1.0
 * @package   EverAccounting\Repositories
 */

namespace EverAccounting\Repositories;

use EverAccounting\Abstracts\ResourceRepository;

defined( 'ABSPATH' ) || exit;

/**
 * Class Accounts
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Repositories
 */
class Invoices extends ResourceRepository {
	/**
	 * @var string
	 */
	const TABLE = 'ea_invoices';

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
		'id'                 => '%d',
		'invoice_number'     => '%s',
		'order_number'       => '%s',
		'status'             => '%s',
		'invoiced_at'        => '%s',
		'due_at'             => '%s',
		'subtotal'           => '%f',
		'discount'           => '%f',
		'tax'                => '%f',
		'shipping'           => '%f',
		'total'              => '%f',
		'currency_code'      => '%s',
		'currency_rate'      => '%s',
		'category_id'        => '%d',
		'contact_id'         => '%d',
		'contact_name'       => '%d',
		'contact_email'      => '%d',
		'contact_tax_number' => '%d',
		'contact_phone'      => '%d',
		'contact_address'    => '%s',
		'note'               => '%s',
		'footer'             => '%s',
		'attachment'         => '%d',
		'parent_id'          => '%d',
		'creator_id'         => '%d',
		'date_created'       => '%s',
	);

	/**
	 * Retrieves column defaults.
	 *
	 * Sub-classes can define default for any/all of columns defined in the get_columns() method.
	 *
	 * @since 1.1.0
	 * @return array All defined column defaults.
	 */
	public static function get_defaults() {
		return array(
			'invoice_number'     => '',
			'order_number'       => '',
			'status'             => 'pending',
			'invoiced_at'        => null,
			'due_at'             => null,
			'subtotal'           => 0.00,
			'discount'           => 0.00,
			'tax'                => 0.00,
			'shipping'           => 0.00,
			'total'              => 0.00,
			'currency_code'      => null,
			'currency_rate'      => null,
			'category_id'        => null,
			'contact_id'         => null,
			'contact_name'       => null,
			'contact_email'      => null,
			'contact_tax_number' => null,
			'contact_phone'      => null,
			'contact_address'    => '',
			'note'               => '',
			'footer'             => '',
			'attachment'         => null,
			'parent_id'          => null,
			'creator_id'         => eaccounting_get_current_user_id(),
			'date_created'       => current_time( 'mysql' ),
		);
	}

}
