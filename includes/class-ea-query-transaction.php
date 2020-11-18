<?php
/**
 * Class for Transaction querying.
 *
 * @since    1.0.2
 * @package  EverAccounting
 */

namespace EverAccounting;

use EverAccounting\Traits\Query_Where;

defined( 'ABSPATH' ) || exit();

class Query_Transaction extends Query {
	use Query_Where;

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
	 * @since 1.0.2
	 * @var array
	 */
	protected $search_columns = [ 'paid_at', 'amount', 'reference', 'description' ];

	/**
	 * Static constructor.
	 *
	 * @since 1.0.2
	 *
	 * @return Query_Transaction
	 */
	public static function init() {
		$builder = new self();
		$builder->from( self::TABLE . ' as `' . self::TABLE . '`' );

		return $builder;
	}

	/**
	 * Exclude transfer transactions from current query.
	 *
	 * @since 1.0.2
	 * @return $this
	 */
	public function notTransfer() {
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
	public function expense_only() {
		$this->where( "{$this->table}.type", 'expense' );

		return $this;
	}

	/**
	 * Include only payments
	 *
	 * @since 1.0.2
	 * @return $this
	 */
	public function income_only() {

		$this->where( "{$this->table}.type", 'income' );

		return $this;
	}
}
