<?php
/**
 * Currency repository.
 *
 * Handle currency insert, update, delete & retrieve from database.
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
class Currencies extends ResourceRepository {
	/**
	 * Table name
	 *
	 * @var string
	 */
	const TABLE = 'ea_currencies';

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
		'name'               => '%s',
		'code'               => '%s',
		'rate'               => '%f',
		'precision'          => '%d',
		'symbol'             => '%s',
		'position'           => '%s',
		'decimal_separator'  => '%s',
		'thousand_separator' => '%s',
		'enabled'            => '%d',
		'date_created'       => '%s',
	);

}
