<?php
/**
 * ApiKey repository.
 *
 * Handle ApiKey insert, update, delete & retrieve from database.
 *
 * @version   1.1.0
 * @package   EverAccounting\Repositories
 */

namespace EverAccounting\Repositories;

use EverAccounting\Abstracts\ResourceRepository;

defined( 'ABSPATH' ) || exit;

/**
 * Class ApiKeys
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Repositories
 */
class ApiKeys extends ResourceRepository {
	/**
	 * Table name
	 *
	 * @var string
	 */
	const TABLE = 'ea_api_keys';

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
		'id'            => '%d',
		'user_id'       => '%d',
		'description'   => '%s',
		'permission'    => '%s',
		'api_key'       => '%s',
		'api_secret'    => '%s',
		'nonces'        => '%s',
		'truncated_key' => '%s',
		'last_access'   => '%s',
	);

}
