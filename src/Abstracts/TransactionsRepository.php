<?php
/**
 * Transaction repository.
 *
 * Handle transaction insert, update, delete & retrieve from database.
 *
 * @version   1.1.0
 * @package   EverAccounting\Repositories
 */

namespace EverAccounting\Abstracts;

defined( 'ABSPATH' ) || exit;

/**
 * Class TransactionsRepository
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Abstracts
 */
class TransactionsRepository extends ResourceRepository {

	/**
	 * Name of the table.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	const TABLE = 'ea_transactions';

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
			'type'           => self::VARCHAR,
			'paid_at'        => self::DATETIME,
			'amount'         => self::DOUBLE,
			'currency_code'  => self::VARCHAR, // protected
			'currency_rate'  => self::DOUBLE, // protected
			'account_id'     => self::BIGINT,
			'invoice_id'     => self::BIGINT,
			'contact_id'     => self::BIGINT,
			'category_id'    => self::BIGINT,
			'description'    => self::LONGTEXT,
			'payment_method' => self::VARCHAR,
			'reference'      => self::VARCHAR,
			'attachment'     => self::INT,
			'parent_id'      => self::BIGINT,
			'reconciled'     => self::TINYINT,
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
			'type'           => '',
			'paid_at'        => null,
			'amount'         => null,
			'currency_code'  => '', // protected
			'currency_rate'  => 1, // protected
			'account_id'     => null,
			'invoice_id'     => null,
			'contact_id'     => null,
			'category_id'    => null,
			'description'    => '',
			'payment_method' => '',
			'reference'      => '',
			'attachment'     => '',
			'parent_id'      => 0,
			'reconciled'     => 0,
			'creator_id'     => eaccounting_get_current_user_id(),
			'date_created'   => current_time( 'mysql' ),
		);
	}


	/**
	 * @since 1.1.0
	 *
	 * @param bool  $callback
	 * @param array $args
	 *
	 * @return array|int
	 */
	public function get_items( $args = array(), $callback = false ) {
		$args = wp_parse_args(
			$args,
			array(
				'category_in'     => array(),
				'category_not_in' => array(),
			)
		);
		if ( isset( $args['category_id'] ) ) {

		}

		return parent::get_items( $args, $callback );
	}

}
