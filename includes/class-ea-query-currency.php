<?php
/**
 * Class for Currency querying.
 *
 * @package  EverAccounting
 * @since    1.0.2
 */
namespace EverAccounting;

use EverAccounting\Traits\WP_Query;

defined( 'ABSPATH' ) || exit();

class Query_Currency extends Query {
	/**
	 * Implement WP style query.
	 */
	use WP_Query;

	/**
	 * @var string
	 * @since 1.0.2
	 */
	protected $cache_group = 'currencies';

	/**
	 * Static constructor.
	 *
	 *
	 * @param string $id
	 *
	 * @return Query_Currency
	 * @since 1.0.2
	 */
	public static function init( $id = 'currencies_query' ) {
		$builder     = new self();
		$builder->id = $id;
		$builder->from( 'ea_currencies' . ' currencies' );

		return $builder;
	}


	/**
	 * Searchable columns for the current table.
	 *
	 * @return array Table columns.
	 * @since 1.0.2
	 *
	 */
	protected function get_search_columns() {
		return array( 'name', 'code', 'symbol', 'rate' );
	}

	/**
	 * Select as dropdown item
	 * @since 1.0.2
	 */
	public function selectAsOption() {
		$this->select( 'code as id, CONCAT (name,"(", symbol, ")") as value' );

		return $this;
	}
}
