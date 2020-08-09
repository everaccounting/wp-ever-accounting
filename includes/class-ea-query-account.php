<?php
/**
 * Class for Account querying
 *
 * @package  EverAccounting/Classes
 * @since    1.0.2
 */
namespace EverAccounting;

use EverAccounting\Traits\WP_Query;

defined( 'ABSPATH' ) || exit;

class Query_Account extends Query {
	/**
	 * Implement WP style query.
	 */
	use WP_Query;

	/**
	 * @var string
	 * @since 1.0.2
	 */
	protected $cache_group = 'contacts';

	/**
	 * Table name.
	 *
	 * @var string
	 * @since 1.0.2
	 */
	protected static $table = 'ea_accounts';

	/**
	 * Static constructor.
	 *
	 *
	 * @param string $id
	 *
	 * @return Query_Account
	 * @since 1.0.0
	 */
	public static function init( $id = 'account_query' ) {
		$builder     = new self();
		$builder->id = $id;
		$builder->from( self::$table );
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
		return array( 'name', 'number', 'bank_name', 'bank_phone', 'bank_address' );
	}

}
