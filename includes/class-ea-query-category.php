<?php
/**
 * Class for Category querying.
 *
 * @package  EverAccounting
 * @since    1.0.2
 */
namespace EverAccounting;

use EverAccounting\Traits\WP_Query;
defined( 'ABSPATH' ) || exit();

class Query_Category extends Query {
	/**
	 * Implement WP style query.
	 */
	use WP_Query;

	/**
	 * @var string
	 * @since 1.0.2
	 */
	protected $cache_group = 'categories';

	/**
	 * Static constructor.
	 *
	 *
	 * @param string $id
	 *
	 * @return Query_Category
	 * @since 1.0.2
	 */
	public static function init( $id = 'categories_query' ) {
		$builder     = new self();
		$builder->id = $id;
		$builder->from( 'ea_categories' );
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
		return array( 'name');
	}
}
