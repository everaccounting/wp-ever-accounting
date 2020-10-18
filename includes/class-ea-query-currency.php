<?php
/**
 * Class for Currency querying.
 *
 * @package  EverAccounting
 * @since    1.0.2
 */
namespace EverAccounting;

use EverAccounting\Traits\Query_Where;

defined( 'ABSPATH' ) || exit();

class Query_Currency extends Query {
	use Query_Where;

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
	 * @since 1.0.2
	 * @var array
	 */
	protected $search_columns = [ 'name', 'code', 'symbol', 'rate' ];

	/**
	 * Static constructor.
	 *
	 *
	 * @since 1.0.2
	 * @return Query_Currency
	 */
	public static function init() {
		$builder = new self();
		$builder->from( self::TABLE . ' as `' . self::TABLE . '`' );

		return $builder;
	}
}
