<?php
/**
 * Transaction repository.
 *
 * Handle transaction insert, update, delete & retrieve from database.
 *
 * @version   1.1.0
 * @package   EverAccounting\Repositories
 */

namespace EverAccounting\Repositories;

use EverAccounting\Abstracts\ResourceRepository;

defined( 'ABSPATH' ) || exit;

/**
 * Class Transactions
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Repositories
 */
class Transactions extends ResourceRepository {

	/**
	 * Name of the table.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	const TABLE = 'ea_transactions';

	/**
	 * Table name.
	 *
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
		'id'             => '%d',
		'type'           => '%s',
		'paid_at'        => '%s',
		'amount'         => '%f',
		'currency_code'  => '%s', // protected
		'currency_rate'  => '%f', // protected
		'account_id'     => '%d',
		'invoice_id'     => '%d',
		'contact_id'     => '%d',
		'category_id'    => '%d',
		'description'    => '%s',
		'payment_method' => '%s',
		'reference'      => '%s',
		'attachment'     => '%d',
		'parent_id'      => '%d',
		'reconciled'     => '%d',
		'creator_id'     => '%d',
		'date_created'   => '%s',
	);


}
