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
use EverAccounting\Models\Document;
use EverAccounting\Models\DocumentItem;

defined( 'ABSPATH' ) || exit;

/**
 * Class Accounts
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Repositories
 */
class Documents extends ResourceRepository {
	/**
	 * @var string
	 */
	const TABLE = 'ea_documents';

	/**
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
		'invoice_number' => '%s',
		'order_number'   => '%s',
		'status'         => '%s',
		'issue_date'     => '%s',
		'due_date'       => '%s',
		'payment_date'   => '%s',
		'category_id'    => '%d',
		'customer_id'    => '%d',
		'name'           => '%s',
		'phone'          => '%s',
		'email'          => '%s',
		'tax_number'     => '%s',
		'postcode'       => '%s',
		'address'        => '%s',
		'country'        => '%s',
		'currency_code'  => '%s',
		'currency_rate'  => '%f',
		'subtotal'       => '%f',
		'discount'       => '%f',
		'discount_type'  => '%s',
		'total_tax'      => '%f',
		'total'          => '%f',
		'tax_inclusive'  => '%d',
		'terms'          => '%s',
		'attachment_id'  => '%d',
		'key'            => '%s',
		'parent_id'      => '%d',
		'creator_id'     => '%d',
		'date_created'   => '%s',
	);


	/**
	 * Read order items of a specific type from the database for this order.
	 *
	 * @param Document $document Order object.
	 *
	 * @return array
	 */
	public function get_items( $document ) {
		global $wpdb;

		// Get from cache if available.
		$items = 0 < $document->get_id() ? wp_cache_get( 'document-item-' . $document->get_id(), 'ea-document-items' ) : false;

		if ( false === $items ) {
			$items = $wpdb->get_results(
				$wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_line_items WHERE document_id = %d ORDER BY id;", $document->get_id() )
			);
			foreach ( $items as $item ) {
				wp_cache_set( 'document-item-' . $item->id, $item, 'ea-document-items' );
			}
			if ( 0 < $document->get_id() ) {
				wp_cache_set( 'document-item' . $document->get_id(), $items, 'ea-document-items' );
			}
		}
		$results = array();
		foreach ( $items as $item ) {
			$results[ absint( $item->item_id ) ] = new DocumentItem( $item );
		}

		return $results;
	}

	/**
	 * Delete Invoice Items.
	 *
	 * @since 1.1.0
	 *
	 * @param Document $document
	 */
	public function delete_items( $document ) {
		global $wpdb;
		$wpdb->delete( $wpdb->prefix . DocumentItems::TABLE, array( 'document_id' => $document->get_id() ) );
	}


	/**
	 * Get documents collection.
	 *
	 * @since 1.1.0
	 *
	 * @param array $args
	 *
	 * @return array|false|int|mixed|object|null
	 */
	public function get_documents( $args = array() ) {
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

		$qv            = apply_filters( 'eaccounting_get_documents_args', $args );
		$query_fields  = eaccounting_prepare_query_fields( $qv, $this->table );
		$query_from    = eaccounting_prepare_query_from( $this->table );
		$query_where   = 'WHERE 1=1';
		$query_where  .= eaccounting_prepare_query_where( $qv, $this->table );
		$query_orderby = eaccounting_prepare_query_orderby( $qv, $this->table );
		$query_limit   = eaccounting_prepare_query_limit( $qv );
		$count_total   = true === $qv['count_total'];
		$cache_key     = md5( serialize( $qv ) );
		$results       = wp_cache_get( $cache_key, 'ea_documents' );
		$request       = "SELECT $query_fields $query_from $query_where $query_orderby $query_limit";

		if ( false === $results ) {
			if ( $count_total ) {
				$results = (int) $wpdb->get_var( $request );
				wp_cache_set( $cache_key, $results, 'ea_documents' );
			} else {
				$results = $wpdb->get_results( $request );
				if ( in_array( $qv['fields'], array( 'all', '*' ), true ) ) {
					foreach ( $results as $key => $item ) {
						wp_cache_set( $item->id, $item, 'ea_documents' );
						wp_cache_set( "key-{$item->key}", $item->id, 'ea_documents' );
						wp_cache_set( "document_number-{$item->invoice_number}", $item->id, 'ea_documents' );
					}
				}
				wp_cache_set( $cache_key, $results, 'ea_documents' );
			}
		}

		if ( 'objects' === $qv['return'] && true !== $qv['count_total'] ) {
			$results = array_map( 'eaccounting_get_document', $results );
		}

		return $results;
	}


}
