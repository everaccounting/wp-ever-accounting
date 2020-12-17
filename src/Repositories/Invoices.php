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
use EverAccounting\Models\Note;

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
		'issue_date'     => '%s',
		'due_date'       => '%s',
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
	public function get_line_items( $invoice ) {
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
	 * @since 1.1.0
	 *
	 * @param Invoice $invoice
	 */
	public function delete_items( $invoice ) {
		global $wpdb;
		$wpdb->delete( $wpdb->prefix . LineItems::TABLE, array( 'parent_id' => $invoice->get_id() ) );
	}


	/**
	 * Get invoices collection.
	 *
	 * @since 1.1.0
	 *
	 * @param array $args
	 *
	 * @return array|false|int|mixed|object|null
	 */
	public function get_invoices( $args = array() ) {
		global $wpdb;
		// Prepare args.
		$args = wp_parse_args(
			$args,
			array(
				'type'             => '',
				'include'          => '',
				'search'           => '',
				'search_cols'      => array(),
				'orderby_cols'     => array(),
				'exclude_transfer' => true,
				'fields'           => '*',
				'orderby'          => 'id',
				'order'            => 'ASC',
				'number'           => 20,
				'offset'           => 0,
				'paged'            => 1,
				'return'           => 'objects',
				'count_total'      => false,
			)
		);

		$qv            = apply_filters( 'eaccounting_get_invoices_args', $args );
		$query_fields  = eaccounting_prepare_query_fields( $qv, $this->table );
		$query_from    = eaccounting_prepare_query_from( $this->table );
		$query_where   = 'WHERE 1=1';
		$query_where  .= eaccounting_prepare_query_where( $qv, $this->table );
		$query_orderby = eaccounting_prepare_query_orderby( $qv, $this->table );
		$query_limit   = eaccounting_prepare_query_limit( $qv );
		$count_total   = true === $qv['count_total'];
		$cache_key     = md5( serialize( $qv ) );
		$results       = wp_cache_get( $cache_key, 'ea_invoices' );
		$request       = "SELECT $query_fields $query_from $query_where $query_orderby $query_limit";

		if ( false === $results ) {
			if ( $count_total ) {
				$results = (int) $wpdb->get_var( $request );
				wp_cache_set( $cache_key, $results, 'ea_invoices' );
			} else {
				$results = $wpdb->get_results( $request );
				if ( in_array( $qv['fields'], array( 'all', '*' ), true ) ) {
					foreach ( $results as $key => $item ) {
						wp_cache_set( $item->id, $item, 'ea_invoices' );
						wp_cache_set( "key-{$item->key}", $item->id, 'ea_invoices' );
						wp_cache_set( "invoice_number-{$item->invoice_number}", $item->id, 'ea_invoices' );
					}
				}
				wp_cache_set( $cache_key, $results, 'ea_invoices' );
			}
		}

		if ( 'objects' === $qv['return'] && true !== $qv['count_total'] ) {
			$results = array_map( 'eaccounting_get_invoice', $results );
		}

		return $results;
	}


	/**
	 * Add invoice note.
	 *
	 * @since 1.1.0
	 *
	 * @param $invoice
	 * @param $note
	 *
	 * @return array
	 */
	public function add_note( $content, $invoice ) {
		$note = new Note();
		$note->set_props(
			array(
				'parent_id'   => $invoice->get_id(),
				'parent_type' => 'invoice',
				'content'     => $content,
			)
		);

		$note->save();

		return $note->get_data();
	}

}
