<?php
/**
 * Class for Account querying.
 *
 * @since    1.0.2
 * @package  EverAccounting
 */

namespace EverAccounting;

use EverAccounting\Traits\WP_Query;

defined( 'ABSPATH' ) || exit;

class Query_Account extends Query {
	/**
	 * Implement WP style query.
	 */
	use WP_Query;

	/**
	 * @since 1.0.2
	 * @var string
	 */
	protected $cache_group = 'contacts';

	/**
	 * Table name.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	protected static $table = 'ea_accounts';

	/**
	 * Static constructor.
	 *
	 *
	 * @param string $id
	 *
	 * @since 1.0.2
	 * @return Query_Account
	 */
	public static function init( $id = 'account_query' ) {
		$builder     = new self();
		$builder->id = $id;
		$builder->from( self::$table . ' accounts' );

		return $builder;
	}

	/**
	 * Searchable columns for the current table.
	 *
	 * @since 1.0.2
	 *
	 * @return array Table columns.
	 */
	protected function get_search_columns() {
		return array( 'name', 'number', 'bank_name', 'bank_phone', 'bank_address' );
	}

	/**
	 * Return with balance of the account.
	 *
	 * @since 1.0.2
	 * @return $this
	 */
	public function withBalance() {
		$this->select( 'accounts.*' )
		     ->select( "SUM(CASE WHEN t.type='income' then amount WHEN t.type='expense' then - amount END ) + opening_balance  as balance" )
		     ->leftJoin( 'ea_transactions as t', 't.account_id', 'accounts.id' )
		     ->group_by( 'accounts.id' );

		return $this;
	}

}
