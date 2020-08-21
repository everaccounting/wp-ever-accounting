<?php
/**
 * Class for Category querying.
 *
 * @since    1.0.2
 * @package  EverAccounting
 */

namespace EverAccounting;

use EverAccounting\Traits\WP_Query;

defined( 'ABSPATH' ) || exit();

class Query_Transfer extends Query {
	/**
	 * Implement WP style query.
	 */
	use WP_Query;

	/**
	 * @since 1.0.2
	 * @var string
	 */
	protected $cache_group = 'transfers';

	/**
	 * Static constructor.
	 *
	 *
	 * @since 1.0.2
	 *
	 * @param string $id
	 *
	 * @return Query_Transfer
	 */
	public static function init( $id = 'transfers_query' ) {
		$builder     = new self();
		$builder->id = $id;
		$builder->from( 'ea_transfers' . ' transfers' );
		return $builder;
	}

	/**
	 * Searchable columns for the current table.
	 *
	 * @since 1.0.2
	 *
	 * @return array Table columns.
	 */
	protected function get_search_columns() {
		return array( 'name', 'email', 'phone', 'fax', 'address' );
	}

}
