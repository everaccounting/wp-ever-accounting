<?php
/**
 * ApiKey repository.
 *
 * Handle ApiKey insert, update, delete & retrieve from database.
 *
 * @version   1.1.0
 * @package   EverAccounting\Repositories
 */

namespace EverAccounting\Repositories;

use EverAccounting\Abstracts\ResourceRepository;

defined( 'ABSPATH' ) || exit;

/**
 * Class ApiKeys
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Repositories
 */
class ApiKeys extends ResourceRepository {
	/**
	 * Table name
	 *
	 * @var string
	 */
	const TABLE = 'ea_api_keys';

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
		'user_id'       => '%d',
		'description'   => '%s',
		'permission'    => '%s',
		'api_key'       => '%s',
		'api_secret'    => '%s',
		'nonces'        => '%s',
		'truncated_key' => '%s',
		'last_access'   => '%s',
	);

	/**
	 * Get api_key collection.
	 *
	 * @param array $args
	 *
	 * @return int|array|null
	 * @since 1.1.0
	 *
	 */
	public static function get_api_keys( $args = array() ) {
		global $wpdb;
		// Prepare args.
		$args = wp_parse_args(
			$args,
			array(
				'status'       => 'all',
				'type'         => '',
				'include'      => '',
				'search'       => '',
				'search_cols'  => array( 'user_id', 'permission' ),
				'orderby_cols' => array( 'user_id', 'permission' ),
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

		$qv    = apply_filters( 'eaccounting_get_api_keys_args', $args );
		$table = 'ea_api_keys';

		$query_fields  = eaccounting_prepare_query_fields( $qv, $table );
		$query_from    = eaccounting_prepare_query_from( $table );
		$query_where   = 'WHERE 1=1';
		$query_where   .= eaccounting_prepare_query_where( $qv, $table );
		$query_orderby = eaccounting_prepare_query_orderby( $qv, $table );
		$query_limit   = eaccounting_prepare_query_limit( $qv );
		$count_total   = true === $qv['count_total'];
		$cache_key     = md5( serialize( $qv ) );
		$results       = wp_cache_get( $cache_key, 'ea_api_keys' );
		$request       = "SELECT $query_fields $query_from $query_where $query_orderby $query_limit";

		if ( false === $results ) {
			if ( $count_total ) {
				$results = (int) $wpdb->get_var( $request );
				wp_cache_set( $cache_key, $results, 'ea_api_keys' );
			} else {
				$results = $wpdb->get_results( $request );
				if ( in_array( $qv['fields'], array( 'all', '*' ), true ) ) {
					foreach ( $results as $key => $item ) {
						wp_cache_set( $item->id, $item, 'ea_api_keys' );
						wp_cache_set( "key-{$item->user_id}", $item->id, 'ea_api_keys' );
						wp_cache_set( "key-{$item->permission}", $item->id, 'ea_api_keys' );
					}
				}
				wp_cache_set( $cache_key, $results, 'ea_api_keys' );
			}
		}

		if ( 'objects' === $qv['return'] && true !== $qv['count_total'] ) {
			$results = array_map( 'eaccounting_get_api_key', $results );
		}

		return $results;

	}

}
