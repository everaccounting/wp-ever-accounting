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
			'invoice_number'     => self::VARCHAR,
			'order_number'       => self::VARCHAR,
			'status'             => self::VARCHAR,
			'invoiced_at'        => self::DATETIME,
			'due_at'             => self::DATETIME,
			'subtotal'           => self::DOUBLE,
			'discount'           => self::DOUBLE,
			'tax'                => self::DOUBLE,
			'shipping'           => self::DOUBLE,
			'total'              => self::DOUBLE,
			'currency_code'      => self::VARCHAR,
			'currency_rate'      => self::VARCHAR,
			'category_id'        => self::BIGINT,
			'contact_id'         => self::BIGINT,
			'contact_name'       => self::BIGINT,
			'contact_email'      => self::BIGINT,
			'contact_tax_number' => self::BIGINT,
			'contact_phone'      => self::BIGINT,
			'contact_address'    => self::VARCHAR,
			'note'               => self::LONGTEXT,
			'footer'             => self::LONGTEXT,
			'attachment'         => self::BIGINT,
			'parent_id'          => self::BIGINT,
			'creator_id'         => self::BIGINT,
			'date_created'       => self::DATETIME,
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
