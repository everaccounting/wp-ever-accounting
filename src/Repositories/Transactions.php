<?php
/**
 * Transaction repository.
 *
 * Handle transaction insert, update, delete & retrieve from database.
 *
 * @version   1.1.0
 * @package   EverAccounting\Repositories
 */

namespace EverAccounting\Repositories;

use EverAccounting\Abstracts\ResourceRepository;

defined( 'ABSPATH' ) || exit;

/**
 * Class Transactions
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Repositories
 */
class Transactions extends ResourceRepository {

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
		$this->object_type = 'transaction';
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
		global $wpdb;
		$args = wp_parse_args(
			$args,
			array(
				'category_id'      => array(),
				'category__in'     => array(),
				'category__not_in' => array(),
				'exclude_transfer' => false,
				'type'             => '',
				'currency_code'    => '',
				'paid_at'          => '',
				'where'            => array(),
				'join'             => array(),
			)
		);

		if ( true === $args['exclude_transfer'] ) {
			$category_table  = $wpdb->prefix . Categories::TABLE;
			$alias           = self::TABLE;
			$args['where'][] = array(
				'condition' => "{$alias}.category_id NOT IN (select id from {$category_table} where type='other')",
			);
		}

		if ( ! empty( $args['type'] ) ) {
			$args['where'][] = array(
				'field'    => 'type',
				'operator' => '=',
				'value'    => sanitize_key( $args['type'] ),
			);
			unset( $args['type'] );
		}

		if ( ! empty( $args['currency_code'] ) ) {
			$args['where'][] = array(
				'field'    => 'currency_code',
				'operator' => '=',
				'value'    => sanitize_key( $args['currency_code'] ),
			);
			unset( $args['currency_code'] );
		}

		if ( ! empty( $args['category_id'] ) ) {
			$args['where'][] = array(
				'field'    => 'category_id',
				'operator' => '=',
				'value'    => absint( $args['category_id'] ),
			);
			unset( $args['category_id'] );
		}

		if ( ! empty( $args['account_id'] ) ) {
			$args['where'][] = array(
				'field'    => 'account_id',
				'operator' => 'IN',
				'value'    => wp_parse_id_list( $args['account_id'] ),
			);
			unset( $args['account_id'] );
		}

		if ( ! empty( $args['contact_id'] ) ) {
			$args['where'][] = array(
				'field'    => 'contact_id',
				'operator' => 'IN',
				'value'    => wp_parse_id_list( $args['contact_id'] ),
			);
			unset( $args['contact_id'] );
		}

		if ( ! empty( $args['invoice_id'] ) ) {
			$args['where'][] = array(
				'field'    => 'invoice_id',
				'operator' => 'IN',
				'value'    => wp_parse_id_list( $args['invoice_id'] ),
			);
			unset( $args['invoice_id'] );
		}

		if ( ! empty( $args['category__in'] ) ) {
			$args['where'][] = array(
				'field'    => 'category__in',
				'operator' => 'IN',
				'value'    => wp_parse_id_list( $args['category__in'] ),
			);
			unset( $args['category__in'] );
		}

		if ( ! empty( $args['category__not_in'] ) ) {
			$args['where'][] = array(
				'field'    => 'category__not_in',
				'operator' => 'NOT IN',
				'value'    => wp_parse_id_list( $args['category__not_in'] ),
			);
			unset( $args['category__not_in'] );
		}

		return parent::get_items( $args, $callback );
	}

}
