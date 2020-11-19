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
	 * @var string
	 */
	const TABLE = 'ea_taxes';

	/**
	 * Taxes constructor.
	 */
	public function __construct() {
		global $wpdb;
		$this->table       = $wpdb->prefix . self::TABLE;
		$this->table_name  = self::TABLE;
		$this->primary_key = 'id';
		$this->object_type = 'tax';
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
			'name'         => self::VARCHAR,
			'rate'         => self::DOUBLE,
			'type'         => self::VARCHAR,
			'enabled'      => self::TINYINT,
			'date_created' => self::DATETIME,
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
			'name'         => '',
			'rate'         => 'normal',
			'enabled'      => 1,
			'date_created' => current_time( 'mysql' ),
		);
	}

}
