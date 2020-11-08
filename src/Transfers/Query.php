<?php
/**
 * Class for Category querying.
 *
 * @since    1.0.2
 * @package  EverAccounting
 */

namespace EverAccounting\Transfers;


defined( 'ABSPATH' ) || exit();

class Query extends \EverAccounting\Query {
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

				'search_columns' => array(),
			),
			parent::get_query_vars()
		);
	}

	/**
	 * Return with balance of the account.
	 *
	 * @since 1.0.2
	 * @return $this
	 */
	public function include_transactions() {
		$this->left_join( "ea_transactions", "ea_transactions.id", "{$this->table}.income_id" )
		     ->group_by( "{$this->table}.id" );
		return $this;
	}
}
