<?php
/**
 * Class for Transaction querying.
 *
 * @since    1.1.0
 * @package  EverAccounting
 */
namespace EverAccounting\Transactions;

defined( 'ABSPATH' ) || exit();

class Query extends \EverAccounting\Query {
	/**
	 * Table name in database (without prefix).
	 *
	 * @var string
	 */
	const TABLE = 'ea_transactions';

	/**
	 * Table name in database (without prefix).
	 *
	 * @var string
	 */
	protected $table = self::TABLE;

	/**
	 * @since 1.0.2
	 * @var string
	 */
	protected $cache_group = 'transactions';

	/**
	 * Get the default allowed query vars.
	 *
	 * @since 1.1.0
	 * @return array
	 */
	protected function get_query_vars() {
		return wp_parse_args(
			array(
				'table'          => self::TABLE,
				'order'          => 'DESC',
				'search_columns' => array( 'paid_at', 'amount', 'reference', 'description' ),
			),
			parent::get_query_vars()
		);
	}

	/**
	 * Parse extra args.
	 *
	 * @param $vars
	 * @since 1.1.0
	 */
	protected function parse_extra( $vars ) {
		if ( ! empty( $vars['type'] ) && array_key_exists( $vars['type'], get_types() ) ) {
			$this->where( 'type', $vars['type'] );
		}
		parent::get_query_vars( $vars );
	}

	/**
	 * Exclude transfer from results.
	 *
	 * @since 1.1.0
	 * @return $this
	 */
	public function without_transfer() {
		global $wpdb;
		$this->where_raw( "{$this->table}.category_id NOT IN(select id from {$wpdb->prefix}ea_categories where type='other')" );

		return $this;
	}

	/**
	 * Include only customers
	 *
	 * @since 1.0.2
	 * @return $this
	 */
	public function only_expense() {
		$this->where( "{$this->table}.type", 'expense' );

		return $this;
	}

	/**
	 * Include only payments
	 *
	 * @since 1.0.2
	 * @return $this
	 */
	public function only_income() {

		$this->where( "{$this->table}.type", 'income' );

		return $this;
	}
}
