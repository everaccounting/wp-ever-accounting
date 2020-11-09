<?php
/**
 * Class for Account querying.
 *
 * @since    1.1.0
 * @package  EverAccounting
 */

namespace EverAccounting\Accounts;

defined( 'ABSPATH' ) || exit();

/**
 * Class Query
 * @since   1.1.0
 *
 * @package EverAccounting\Account
 */
class Query extends \EverAccounting\Query {
	/**
	 * Table name in database (without prefix).
	 *
	 * @var string
	 */
	const TABLE = 'ea_accounts';

	/**
	 * Table name in database (without prefix).
	 *
	 * @var string
	 */
	protected $table = self::TABLE;



}

