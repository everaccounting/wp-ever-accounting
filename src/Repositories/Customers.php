<?php
/**
 * Customer repository.
 *
 * Handle customer insert, update, delete & retrieve from database.
 *
 * @version   1.1.0
 * @package   EverAccounting\Repositories
 */

namespace EverAccounting\Repositories;

use EverAccounting\Abstracts\ContactsRepository;
use EverAccounting\Models\Customer;

defined( 'ABSPATH' ) || exit;

/**
 * Class Customers
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Repositories
 */
class Customers extends ContactsRepository {

	/**
	 * Customers constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->object_type = 'customer';
	}

	/**
	 * @param array $args
	 * @param bool  $callback
	 * @since 1.1.0
	 *
	 * @return array|int
	 */
	public function get_items( $args = array(), $callback = false ) {
		$args            = wp_parse_args( $args, array( 'where' => array() ) );
		$args['where'][] = array(
			'joint'     => 'AND',
			'condition' => "{$this->table_name}.type = 'customer' ",
		);

		return parent::get_items( $args, $callback = false ); // TODO: Change the autogenerated stub
	}

	/**
	 * Retrieves column defaults.
	 *
	 * Sub-classes can define default for any/all of columns defined in the get_columns() method.
	 *
	 * @since 1.1.0
	 * @return array All defined column defaults.
	 */
	public static function get_defaults() {
		return array_merge( parent::get_defaults(), array( 'type' => 'customer' ) );
	}
}