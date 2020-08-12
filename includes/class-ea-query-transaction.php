<?php
/**
 * Class for Transaction querying.
 *
 * @package  EverAccounting
 * @since    1.0.2
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
	 * @var string
	 * @since 1.0.2
	 */
	protected $cache_group = 'transactions';

	/**
	 * Static constructor.
	 *
	 *
	 * @param string $id
	 *
	 * @return Query_Transaction
	 * @since 1.0.2
	 */
	public static function init( $id = 'transactions_query' ) {
		$builder     = new self();
		$builder->id = $id;
		$builder->from( 'ea_transactions' );
		return $builder;
	}

	/**
	 * Exclude transfer transactions from current query.
	 *
	 * @return $this
	 * @since 1.0.2
	 */
	public function isNotTransfer() {
		global $wpdb;
		$this->whereRaw( "category_id NOT IN(select id from {$wpdb->prefix}ea_categories where type='other')" );

		return $this;
	}

	/**
	 * Include only customers
	 *
	 * @return $this
	 * @since 1.0.2
	 */
	public function isPurchase() {

		$this->where('type', 'purchase');

		return $this;
	}

	/**
	 * Include only payments
	 *
	 * @return $this
	 * @since 1.0.2
	 */
	public function isPayment() {

		$this->where('type', 'payment');

		return $this;
	}

	/**
	 * Searchable columns for the current table.
	 *
	 * @return array Table columns.
	 * @since 1.0.2
	 *
	 */
	protected function get_search_columns() {
		return array( 'paid_at', 'amount', 'reference', 'description' );
	}

}
