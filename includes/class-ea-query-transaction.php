<?php
/**
 * Class for Transaction querying.
 *
 * @since    1.0.2
 * @package  EverAccounting
 */

namespace EverAccounting;

use EverAccounting\Traits\WP_Query;

defined( 'ABSPATH' ) || exit();

class Query_Transaction extends Query {
	/**
	 * Implement WP style query.
	 * @since    1.0.2
	 */
	use WP_Query;

	/**
	 * @since 1.0.2
	 * @var string
	 */
	protected $cache_group = 'transactions';

	/**
	 * Static constructor.
	 *
	 *
	 * @param string $id
	 *
	 * @since 1.0.2
	 * @return Query_Transaction
	 */
	public static function init( $id = 'transactions_query' ) {
		$builder     = new self();
		$builder->id = $id;
		$builder->from( 'ea_transactions' . ' transactions' );

		return $builder;
	}

	/**
	 * Exclude transfer transactions from current query.
	 *
	 * @since 1.0.2
	 * @return $this
	 */
	public function isNotTransfer() {
		global $wpdb;
		$this->whereRaw( "category_id NOT IN(select id from {$wpdb->prefix}ea_categories where type='other')" );

		return $this;
	}

	/**
	 * Include only customers
	 *
	 * @since 1.0.2
	 * @return $this
	 */
	public function isExpense() {
		$this->where( 'transactions.type', 'expense' );

		return $this;
	}

	/**
	 * Include only payments
	 *
	 * @since 1.0.2
	 * @return $this
	 */
	public function isIncome() {

		$this->where( 'transactions.type', 'income' );

		return $this;
	}

	public function withCategory(){
		$this->leftJoin('ea_categories categories', 'categories.id', 'transactions.category_id');
		return $this;
	}

	/**
	 * Searchable columns for the current table.
	 *
	 * @since 1.0.2
	 *
	 * @return array Table columns.
	 */
	protected function get_search_columns() {
		return array( 'paid_at', 'amount', 'reference', 'description' );
	}

}
