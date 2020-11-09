<?php
/**
 * Class for Currency querying.
 *
 * @package  EverAccounting
 * @since    1.0.2
 */

namespace EverAccounting\Currencies;
defined( 'ABSPATH' ) || exit();

/**
 * Class Query
 * @since   1.1.0
 *
 * @package EverAccounting\Currency
 */
class Query extends \EverAccounting\Query {
	/**
	 * Table name in database (without prefix).
	 *
	 * @var string
	 */
	const TABLE = 'ea_currencies';

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
	protected $cache_group = 'currencies';

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
				'search_columns' => array( 'name', 'code', 'symbol', 'rate' ),
			),
			parent::get_query_vars()
		);
	}
}

