<?php
/**
 * Class for Account querying.
 *
 * @since    1.0.2
 * @package  EverAccounting
 */

namespace EverAccounting;

use EverAccounting\Traits\Query_Where;

defined( 'ABSPATH' ) || exit;

class Query_Account extends Query {
	use Query_Where;

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
	 * @since 1.0.2
	 * @var array
	 */
	protected $search_columns = [ 'name', 'number', 'bank_name', 'bank_phone', 'bank_address' ];

	/**
	 * Static constructor.
	 *
	 * @since 1.0.2
	 * @return Query_Account
	 */
	public static function init() {
		$builder = new self();
		$builder->from( self::TABLE . ' as `' . self::TABLE . '`' );

		return $builder;
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

	/**
	 * Return with balance of the account.
	 *
	 * @since 1.0.2
	 * @return $this
	 */
	public function withBalance() {
		$transaction = \EverAccounting\Transactions\Transaction::TABLE;
		$this->select( "{$this->table}.*" )
			 ->select( "SUM(CASE WHEN {$transaction}.type='income' then amount WHEN {$transaction}.type='expense' then - amount END ) + {$this->table}.opening_balance  as balance" )
			 ->left_join( "ea_transactions as {$transaction}", "{$transaction}.account_id", "{$this->table}.id" )
			 ->group_by( "{$this->table}.id" );

		return $this;
	}

}
