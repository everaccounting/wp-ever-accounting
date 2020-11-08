<?php
/**
 * Class for Category querying.
 *
 * @since    1.0.2
 * @package  EverAccounting
 */

namespace EverAccounting;

use EverAccounting\Traits\Query_Where;

defined( 'ABSPATH' ) || exit();

class Query_Transfer extends Query {
	use Query_Where;

	/**
	 * Table name in database (without prefix).
	 *
	 * @var string
	 */
	const TABLE = 'ea_transfers';

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
	protected $cache_group = 'transfers';

	/**
	 * @since 1.0.2
	 * @var array
	 */
	protected $search_columns = [ 'name', 'email', 'phone', 'fax', 'address' ];

	/**
	 * Static constructor.
	 *
	 *
	 * @since 1.0.2
	 * @return Query_Transfer
	 */
	public static function init() {
		$builder = new self();
		$builder->from( self::TABLE . ' as `' . self::TABLE . '`' );

		return $builder;
	}

	/**
	 * Return with balance of the account.
	 *
	 * @since 1.0.2
	 * @return $this
	 */
	public function withTransactions() {
		$transaction = Query_Transaction::TABLE;
		$this->left_join( "ea_transactions as {$transaction}", "{$transaction}.id", "{$this->table}.income_id" )
		     ->group_by( "{$this->table}.id" );

		return $this;
	}
}
