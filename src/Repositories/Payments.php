<?php
/**
 * Payments repository.
 *
 * Handle payment insert, update, delete & retrieve from database.
 *
 * @version   1.1.0
 * @package   EverAccounting\Repositories
 */

namespace EverAccounting\Repositories;

use EverAccounting\Abstracts\ResourceRepository;
use EverAccounting\Models\Payment;

defined( 'ABSPATH' ) || exit;

/**
 * Class Revenues
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Repositories
 */
class Payments extends ResourceRepository {
	/**
	 * Name of the table.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	const TABLE = 'ea_transactions';

	/**
	 * Table name.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	protected $table = self::TABLE;

	/**
	 * A map of database fields to data types.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $data_type = array(
		'id'             => '%d',
		'type'           => '%s',
		'payment_date'   => '%s',
		'currency_code'  => '%s', // protected
		'currency_rate'  => '%f', // protected
		'amount'         => '%f',
		'account_id'     => '%d',
		'document_id'    => '%d',
		'contact_id'     => '%d',
		'category_id'    => '%d',
		'description'    => '%s',
		'payment_method' => '%s',
		'reference'      => '%s',
		'attachment_id'  => '%d',
		'parent_id'      => '%d',
		'reconciled'     => '%d',
		'creator_id'     => '%d',
		'date_created'   => '%s',
	);

	/**
	 * Method to read a item from the database.
	 *
	 * @param Payment $item Item object.
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
					"SELECT * FROM {$table} WHERE id = %d AND `type` = 'expense'",
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
