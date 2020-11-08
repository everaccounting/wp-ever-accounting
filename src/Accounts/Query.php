<?php
/**
 * Class for Account querying.
 *
 * @since    1.1.0
 * @package  EverAccounting
 */

namespace EverAccounting\Accounts;

defined( 'ABSPATH' ) || exit();

/**
 * Class Query
 * @since   1.1.0
 *
 * @package EverAccounting\Account
 */
class Query extends \EverAccounting\Query {
	/**
	 * Table name in database (without prefix).
	 *
	 * @var string
	 */
	const TABLE = 'ea_accounts';

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
	protected $cache_group = 'accounts';

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
				'search_columns' => array( 'name', 'number', 'bank_name', 'bank_phone', 'bank_address' ),
			),
			parent::get_query_vars()
		);
	}



	public function include_balance(){
		$transaction = \EverAccounting\Transactions\Query::TABLE;
		$this->select( "{$this->table}.*" )
		     ->select( "SUM(CASE WHEN {$transaction}.type='income' then amount WHEN {$transaction}.type='expense' then - amount END ) + {$this->table}.opening_balance  as balance" )
		     ->left_join( "ea_transactions", "ea_transactions.account_id", "{$this->table}.id" )
		     ->group_by( "{$this->table}.id" );

		return $this;
	}
}
