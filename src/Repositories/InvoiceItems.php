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
	 * @var string
	 */
	const TABLE = 'ea_invoice_items';

	/**
	 * InvoiceItems constructor.
	 */
	public function __construct() {
		global $wpdb;
		$this->table       = $wpdb->prefix . self::TABLE;
		$this->table_name  = self::TABLE;
		$this->primary_key = 'id';
		$this->object_type = 'invoice_item';
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
			'item_id'      => self::BIGINT,
			'name'         => self::VARCHAR,
			'sku'          => self::VARCHAR,
			'quantity'     => self::DOUBLE,
			'price'        => self::DOUBLE,
			'total'        => self::DOUBLE,
			'tax_id'       => self::BIGINT,
			'tax_name'     => self::VARCHAR,
			'tax'          => self::DOUBLE,
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
			'item_id'      => null,
			'name'         => '',
			'sku'          => '',
			'quantity'     => 1,
			'price'        => 0.00,
			'total'        => 0.00,
			'tax_id'       => null,
			'tax_name'     => '',
			'tax'          => 0.00,
			'date_created' => current_time( 'mysql' ),
		);
	}

}
