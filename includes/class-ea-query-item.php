<?php
/**
 * Class for Item querying.
 *
 * @since    1.0.2
 * @package  EverAccounting
 */

namespace EverAccounting;

use EverAccounting\Traits\Query_Where;

defined( 'ABSPATH' ) || exit;

class Query_Item extends Query {
	use Query_Where;

	/**
	 * Table name in database (without prefix).
	 *
	 * @var string
	 */
	const TABLE = 'ea_items';

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
	protected $cache_group = 'items';

	/**
	 * @since 1.0.2
	 * @var array
	 */
	protected $search_columns = [ 'name', 'sku', 'description', 'sale_price', 'purchase_price' ];

	/**
	 * Static constructor.
	 *
	 *
	 * @since 1.0.2
	 * @return Query_Item
	 */
	public static function init() {
		$builder = new self();
		$builder->from( self::TABLE . ' as `' . self::TABLE . '`' );

		return $builder;
	}
}
