<?php
/**
 * InvoiceHistories repository.
 *
 * Handle InvoiceHistories insert, update, delete & retrieve from database.
 *
 * @version   1.1.0
 * @package   EverAccounting\Repositories
 */

namespace EverAccounting\Repositories;

use EverAccounting\Abstracts\ResourceRepository;

defined( 'ABSPATH' ) || exit;

/**
 * Class InvoiceHistories
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Repositories
 */
class InvoiceHistories extends ResourceRepository {
	/**
	 * @var string
	 */
	const TABLE = 'ea_invoices';

	/**
	 * Accounts constructor.
	 */
	public function __construct() {
		global $wpdb;
		$this->table       = $wpdb->prefix . self::TABLE;
		$this->table_name  = self::TABLE;
		$this->primary_key = 'id';
		$this->object_type = 'invoice';
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
			'invoice_id'   => self::BIGINT,
			'status'       => self::VARCHAR,
			'notify'       => self::TINYINT,
			'description'  => self::LONGTEXT,
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
			'invoice_id'   => null,
			'status'       => '',
			'notify'       => 0,
			'description'  => '',
			'date_created' => current_time( 'mysql' ),
		);
	}

}
