<?php
/**
 * Item repository.
 *
 * Handle item insert, update, delete & retrieve from database.
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
class Items extends ResourceRepository {
	/**
	 * @var string
	 */
	const TABLE = 'ea_items';

	/**
	 * Accounts constructor.
	 */
	public function __construct() {
		global $wpdb;
		$this->table       = $wpdb->prefix . self::TABLE;
		$this->table_name  = self::TABLE;
		$this->primary_key = 'id';
		$this->object_type = 'item';
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
			'name'           => self::VARCHAR,
			'sku'            => self::VARCHAR,
			'image_id'       => self::BIGINT,
			'description'    => self::LONGTEXT,
			'sale_price'     => self::DOUBLE,
			'purchase_price' => self::DOUBLE,
			'quantity'       => self::BIGINT,
			'category_id'    => self::BIGINT,
			'tax_id'         => self::BIGINT,
			'enabled'        => self::TINYINT,
			'creator_id'     => self::BIGINT,
			'date_created'   => self::DATETIME,
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
			'name'           => '',
			'sku'            => '',
			'image_id'       => null,
			'description'    => '',
			'sale_price'     => 0.0000,
			'purchase_price' => 0.0000,
			'quantity'       => 1,
			'category_id'    => null,
			'tax_id'         => null,
			'enabled'        => 1,
			'creator_id'     => eaccounting_get_current_user_id(),
			'date_created'   => current_time( 'mysql' ),
		);
	}

}
