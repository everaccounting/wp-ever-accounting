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

class Query_Contact extends Query {
	use Query_Where;

	/**
	 * Table name in database (without prefix).
	 *
	 * @var string
	 */
	const TABLE = 'ea_contacts';

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
	protected $cache_group = 'contacts';

	/**
	 * @since 1.0.2
	 * @var array
	 */
	protected $search_columns = [ 'name', 'email', 'phone', 'fax', 'address' ];

	/**
	 * Static constructor.
	 *
	 *
	 * @since 1.0.2
	 * @return Query_Contact
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
	public function typeCustomer() {

		$this->where( 'type', 'customer' );

		return $this;
	}

	/**
	 * Include only vendors
	 *
	 * @since 1.0.2
	 * @return $this
	 */
	public function typeVendor() {

		$this->where( 'type', 'vendor' );

		return $this;
	}
}
