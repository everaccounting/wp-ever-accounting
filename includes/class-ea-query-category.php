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

class Query_Category extends Query {
	use Query_Where;

	/**
	 * Table name in database (without prefix).
	 *
	 * @var string
	 */
	const TABLE = 'ea_categories';

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
	protected $cache_group = 'categories';

	/**
	 * @since 1.0.2
	 * @var array
	 */
	protected $search_columns = [ 'name' ];

	/**
	 * Static constructor.
	 *
	 *
	 * @since 1.0.2
	 * @return Query_Category
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
	public function typeExpense() {
		$this->where( "{$this->table}.type", 'expense' );

		return $this;
	}

	/**
	 * Include only payments
	 *
	 * @since 1.0.2
	 * @return $this
	 */
	public function typeIncome() {

		$this->where( "{$this->table}.type", 'income' );

		return $this;
	}

}
