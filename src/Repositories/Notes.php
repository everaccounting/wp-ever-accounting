<?php
/**
 * Notes repository.
 *
 * Handle Notes insert, update, delete & retrieve from database.
 *
 * @version   1.1.0
 * @package   EverAccounting\Repositories
 */

namespace EverAccounting\Repositories;

use EverAccounting\Abstracts\ResourceRepository;
use EverAccounting\Models\Note;

defined( 'ABSPATH' ) || exit;

/**
 * Class InvoiceHistories
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Repositories
 */
class Notes extends ResourceRepository {
	/**
	 * @var string
	 */
	const TABLE = 'ea_notes';

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
		'id'           => '%d',
		'parent_id'    => '%d',
		'parent_type'  => '%s',
		'note'         => '%s',
		'highlight'    => '%d',
		'author'       => '%s',
		'date_created' => '%s',
	);


	/**
	 * Get notes
	 * 
	 * @since 1.1.0
	 *
	 * @param array $args
	 * 
	 * @return Note[]|array|int
	 */
	public function get_notes( $args = array() ) {
		global $wpdb;
		// Prepare args.
		$args = wp_parse_args(
			$args,
			array(
				'include'      => '',
				'parent_id'    => '',
				'parent_type'  => '',
				'search'       => '',
				'search_cols'  => array( 'note', 'date_created' ),
				'orderby_cols' => array( 'note' ),
				'fields'       => '*',
				'orderby'      => 'id',
				'order'        => 'ASC',
				'number'       => 20,
				'offset'       => 0,
				'paged'        => 1,
				'return'       => 'objects',
				'count_total'  => false,
			)
		);

		$qv            = apply_filters( 'eaccounting_get_notes_args', $args );
		$query_fields  = eaccounting_prepare_query_fields( $qv, $this->table );
		$query_from    = eaccounting_prepare_query_from( $this->table );
		$query_where   = 'WHERE 1=1';
		$query_where  .= eaccounting_prepare_query_where( $qv, $this->table );
		$query_orderby = eaccounting_prepare_query_orderby( $qv, $this->table );
		$query_limit   = eaccounting_prepare_query_limit( $qv );
		$count_total   = true === $qv['count_total'];

		if ( ! empty( $qv['parent_id'] ) ) {
			$parent_id    = implode( ',', wp_parse_id_list( $qv['parent_id'] ) );
			$query_where .= " AND $this->table.`parent_id` IN ($parent_id)";
		}
		if ( ! empty( $qv['parent_type'] ) ) {
			$parent_types = implode( "','", wp_parse_list( $qv['parent_type'] ) );
			$query_where .= " AND $this->table.`parent_type` IN ('$parent_types')";
		}

		$cache_key = md5( serialize( $qv ) );
		$results   = wp_cache_get( $cache_key, 'ea_notes' );
		$request   = "SELECT $query_fields $query_from $query_where $query_orderby $query_limit";

		if ( false === $results ) {
			if ( $count_total ) {
				$results = (int) $wpdb->get_var( $request );
				wp_cache_set( $cache_key, $results, 'ea_notes' );
			} else {
				$results = $wpdb->get_results( $request );
				if ( in_array( $qv['fields'], array( 'all', '*' ), true ) ) {
					foreach ( $results as $key => $item ) {
						wp_cache_set( $item->id, $item, 'ea_notes' );
					}
				}
				wp_cache_set( $cache_key, $results, 'ea_notes' );
			}
		}

		if ( 'objects' === $qv['return'] && true !== $qv['count_total'] ) {
			$results = array_map( 'eaccounting_get_note', $results );
		}

		return $results;
	}



}
