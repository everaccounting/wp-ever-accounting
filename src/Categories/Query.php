<?php
/**
 * Class for Category querying.
 *
 * @since    1.1.0
 * @package  EverAccounting
 */
namespace EverAccounting\Categories;

defined( 'ABSPATH' ) || exit();

/**
 * Class Query
 * @since   1.1.0
 *
 * @package EverAccounting\Category
 */
class Query extends \EverAccounting\Query {
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
				'search_columns' => array( 'name', 'type' ),
			),
			parent::get_query_vars()
		);
	}

	/**
	 * Parse extra args.
	 *
	 * @param $vars
	 * @since 1.1.0
	 */
	protected function parse_extra( $vars ) {
		if ( ! empty( $vars['type'] ) && array_key_exists( $vars['type'], get_types() ) ) {
			$this->where( 'type', $vars['type'] );
		}
		parent::get_query_vars( $vars );
	}


	/**
	 * Include only customers
	 *
	 * @since 1.1.0
	 * @return $this
	 */
	public function expense_only() {
		$this->where( "{$this->table}.type", 'expense' );

		return $this;
	}

	/**
	 * Include only payments
	 *
	 * @since 1.1.0
	 * @return $this
	 */
	public function income_only() {

		$this->where( "{$this->table}.type", 'income' );

		return $this;
	}
}
