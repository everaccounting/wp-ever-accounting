<?php
/**
 * Account repository.
 *
 * Handle account insert, update, delete & retrieve from database.
 *
 * @version   1.1.0
 * @package   EverAccounting\Repositories
 */

namespace EverAccounting\Repositories;

use EverAccounting\Models\Account;
use EverAccounting\Abstracts\ResourceRepository;

defined( 'ABSPATH' ) || exit;

/**
 * Class Accounts
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Repositories
 */
class Accounts extends ResourceRepository {
	/**
	 * @var string
	 */
	const TABLE = 'ea_accounts';

	/**
	 * Accounts constructor.
	 */
	public function __construct() {
		global $wpdb;
		$this->table       = $wpdb->prefix . self::TABLE;
		$this->table_name  = self::TABLE;
		$this->primary_key = 'id';
		$this->object_type = 'account';
	}

	/**
	 * Retrieves the list of columns for the database table.
	 *
	 * Sub-classes should define an array of columns here.
	 *
	 * @since 1.1.0
	 * @return array List of columns.
	 */
	public static function get_columns() {
		return array(
			'name'            => self::VARCHAR,
			'number'          => self::VARCHAR,
			'currency_code'   => self::VARCHAR,
			'opening_balance' => self::DOUBLE,
			'bank_name'       => self::VARCHAR,
			'bank_phone'      => self::VARCHAR,
			'bank_address'    => self::VARCHAR,
			'enabled'         => self::TINYINT,
			'creator_id'      => self::BIGINT,
			'date_created'    => self::DATETIME,
		);
	}

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
			'name'            => '',
			'number'          => '',
			'currency_code'   => '',
			'opening_balance' => 0.0000,
			'bank_name'       => null,
			'bank_phone'      => null,
			'bank_address'    => null,
			'enabled'         => 1,
			'creator_id'      => eaccounting_get_current_user_id(),
			'date_created'    => current_time( 'mysql' ),
		);
	}

}
