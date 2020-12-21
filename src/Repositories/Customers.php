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

use EverAccounting\Abstracts\ResourceRepository;
use EverAccounting\Models\Customer;

defined( 'ABSPATH' ) || exit;

/**
 * Class Customers
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Repositories
 */
class Customers extends ResourceRepository {
	/**
	 * Name of the table.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	const TABLE = 'ea_contacts';

	/**
	 * @since 1.1.0
	 * @var string
	 */
	protected $table = self::TABLE;

	/**
	 * A map of database fields to data types.
	 *
	 * @since 1.1.0
	 * @var array
	 */
	protected $data_type = array(
		'id'            => '%d',
		'currency_code' => '%s',
		'user_id'       => '%d',
		'name'          => '%s',
		'email'         => '%s',
		'phone'         => '%s',
		'fax'           => '%s',
		'birth_date'    => '%s',
		'address'       => '%s',
		'country'       => '%s',
		'website'       => '%s',
		'tax_number'    => '%s',
		'type'          => '%s',
		'note'          => '%s',
		'enabled'       => '%d',
		'creator_id'    => '%d',
		'date_created'  => '%s',
	);

	/**
	 * Method to read a item from the database.
	 *
	 * @param Customer $item Item object.
	 *
	 */
	public function read( &$item ) {
		global $wpdb;
		$table = $wpdb->prefix . $this->table;

		$item->set_defaults();

		if ( ! $item->get_id() ) {
			$item->set_id( 0 );
			return;
		}

		// Maybe retrieve from the cache.
		$raw_item = wp_cache_get( $item->get_id(), $item->get_cache_group() );
		// If not found, retrieve from the db.
		if ( false === $raw_item ) {
			$raw_item = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT * FROM {$table} WHERE id = %d AND `type` = 'customer'",
					$item->get_id()
				)
			);

			// Update the cache with our data
			wp_cache_set( $item->get_id(), $raw_item, $item->get_cache_group() );
		}

		if ( ! $raw_item ) {
			$item->set_id( 0 );
			return;
		}

		foreach ( array_keys( $this->data_type ) as $key ) {
			$method = "set_$key";
			$item->$method( $raw_item->$key );
		}

		$item->set_object_read( true );
		do_action( 'eaccounting_read_' . $item->get_object_type(), $item );
	}
}
