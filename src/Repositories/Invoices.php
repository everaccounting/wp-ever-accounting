<?php
/**
 * Invoice repository.
 *
 * Handle invoice insert, update, delete & retrieve from database.
 *
 * @version   1.1.0
 * @package   EverAccounting\Repositories
 */

namespace EverAccounting\Repositories;

use EverAccounting\Abstracts\ResourceRepository;
use EverAccounting\Models\Invoice;
use EverAccounting\Models\LineItem;

defined( 'ABSPATH' ) || exit;

/**
 * Class Accounts
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Repositories
 */
class Invoices extends ResourceRepository {
	/**
	 * @var string
	 */
	const TABLE = 'ea_invoices';

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
		'id'             => '%d',
		'invoice_number' => '%s',
		'order_number'   => '%s',
		'status'         => '%s',
		'issued_at'      => '%s',
		'due_at'         => '%s',
		'category_id'    => '%d',
		'customer_id'    => '%d',
		'name'           => '%s',
		'phone'          => '%s',
		'email'          => '%s',
		'tax_number'     => '%s',
		'postcode'       => '%s',
		'address'        => '%s',
		'country'        => '%s',
		'subtotal'       => '%f',
		'total_discount' => '%f',
		'total_tax'      => '%f',
		'total_vat'      => '%f',
		'total_shipping' => '%f',
		'total'          => '%f',
		'note'           => '%s',
		'footer'         => '%s',
		'attachment_id'  => '%d',
		'currency_code'  => '%s',
		'currency_rate'  => '%s',
		'key'            => '%s',
		'parent_id'      => '%d',
		'creator_id'     => '%d',
		'date_created'   => '%s',
	);


	/**
	 * Read order items of a specific type from the database for this order.
	 *
	 * @param Invoice $invoice Order object.
	 *
	 * @return array
	 */
	public function read_items( $invoice ) {
		global $wpdb;

		// Get from cache if available.
		$items = 0 < $invoice->get_id() ? wp_cache_get( 'line-item-' . $invoice->get_id(), 'ea-line-items' ) : false;

		if ( false === $items ) {
			$items = $wpdb->get_results(
				$wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_line_items WHERE parent_id = %d AND parent_type = %s ORDER BY id;", $invoice->get_id(), 'invoice' )
			);
			foreach ( $items as $item ) {
				wp_cache_set( 'line-item-' . $item->id, $item, 'ea-line-items' );
			}
			if ( 0 < $invoice->get_id() ) {
				wp_cache_set( 'line-item' . $invoice->get_id(), $items, 'ea-line-items' );
			}
		}
		$results = array();
		foreach ( $items as $item ) {
			$results[ absint( $item->item_id ) ] = new LineItem( $item );
		}
		return $results;
	}

	/**
	 * Delete Invoice Items.
	 *
	 * @param Invoice $invoice
	 * @since 1.1.0
	 */
	public function delete_items( $invoice ) {
		global $wpdb;
		$wpdb->delete( $wpdb->prefix . LineItems::TABLE, array( 'parent_id' => $invoice->get_id() ) );
	}

}
