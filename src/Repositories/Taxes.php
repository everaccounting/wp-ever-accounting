<?php
/**
 * Tax repository.
 *
 * Handle tax insert, update, delete & retrieve from database.
 *
 * @version   1.1.0
 * @package   EverAccounting\Repositories
 */

namespace EverAccounting\Repositories;

use EverAccounting\Abstracts\ResourceRepository;

defined( 'ABSPATH' ) || exit;

/**
 * Class Taxes
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Repositories
 */
class Taxes extends ResourceRepository {
	/**
	 * Table name.
	 *
	 * @var string
	 */
	const TABLE = 'ea_taxes';

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
		'id'           => '%d',
		'name'         => '%s',
		'rate'         => '%s',
		'enabled'      => '%d',
		'date_created' => '%s',
	);
}
