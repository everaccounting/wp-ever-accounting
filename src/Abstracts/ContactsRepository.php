<?php
/**
 * Contact repository.
 *
 * Handle contact insert, update, delete & retrieve from database.
 *
 * @version   1.1.0
 * @package   EverAccounting\Repositories
 */
namespace EverAccounting\Abstracts;

defined( 'ABSPATH' ) || exit;

/**
 * Class ContactsRepository
 * @since   1.1.0
 *
 * @package EverAccounting\Abstracts
 */
class ContactsRepository extends ResourceRepository {

	/**
	 * Name of the table.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	const TABLE = 'ea_contacts';

	/**
	 * Accounts constructor.
	 */
	public function __construct() {
		global $wpdb;
		$this->table       = $wpdb->prefix . self::TABLE;
		$this->table_name  = self::TABLE;
		$this->primary_key = 'id';
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
			'user_id'       => self::BIGINT,
			'name'          => self::VARCHAR,
			'email'         => self::VARCHAR,
			'phone'         => self::VARCHAR,
			'fax'           => self::VARCHAR,
			'birth_date'    => self::DATETIME,
			'address'       => self::VARCHAR,
			'country'       => self::VARCHAR,
			'website'       => self::VARCHAR,
			'tax_number'    => self::VARCHAR,
			'currency_code' => self::VARCHAR,
			'type'          => self::VARCHAR,
			'note'          => self::LONGTEXT,
			'enabled'       => self::TINYINT,
			'creator_id'    => self::BIGINT,
			'date_created'  => self::DATETIME,
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
			'user_id'       => null,
			'name'          => '',
			'email'         => '',
			'phone'         => '',
			'fax'           => '',
			'birth_date'    => '',
			'address'       => '',
			'country'       => '',
			'website'       => '',
			'tax_number'    => '',
			'currency_code' => '',
			'type'          => '',
			'note'          => '',
			'enabled'       => 1,
			'creator_id'    => eaccounting_get_current_user_id(),
			'date_created'  => current_time( 'mysql' ),
		);
	}

}
