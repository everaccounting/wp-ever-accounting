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

class Query_Contact extends Query {
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
	 * Static constructor.
	 *
	 *
	 * @param string $id
	 *
	 * @return Query_Contact
	 * @since 1.0.2
	 */
	public static function init( $id = 'contacts_query' ) {
		$builder     = new self();
		$builder->id = $id;
		$builder->from( 'ea_contacts' );

		return $builder;
	}

	/**
	 * Include only customers
	 *
	 * @return $this
	 * @since 1.0.2
	 */
	public function isCustomer() {

		$this->where( 'type', 'customer' );

		return $this;
	}

	/**
	 * Include only vendors
	 *
	 * @return $this
	 * @since 1.0.2
	 */
	public function isVendor() {

		$this->where( 'type', 'vendor' );

		return $this;
	}

	/**
	 * Searchable columns for the current table.
	 *
	 * @return array Table columns.
	 * @since 1.0.2
	 *
	 */
	protected function get_search_columns() {
		return array( 'name', 'email', 'phone', 'fax', 'address' );
	}

}
