<?php
/**
 * Accounts class.
 *
 * Handle account insert, update, delete & retrieve from database.
 *
 * @version   1.1.3
 * @package   EverAccounting
 */

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;

class Accounts {

	/**
	 * Accounts construct.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct() {
		// accounts
		add_action( 'eaccounting_pre_save_account', array( __CLASS__, 'validate_account_data' ), 10, 2 );
		add_action( 'eaccounting_delete_account', array( __CLASS__, 'delete_account_reference' ) );
	}

	/**
	 * Validate account data.
	 *
	 * @param int $id
	 * @param Account $account
	 *
	 * @param array $data
	 *
	 * @throws \Exception
	 * @since 1.1.0
	 */
	public static function validate_account_data( $data, $id ) {
		global $wpdb;
		if ( $id != (int) $wpdb->get_var( $wpdb->prepare( "SELECT id from {$wpdb->prefix}ea_accounts WHERE number='%s'", eaccounting_clean( $data['number'] ) ) ) ) { // @codingStandardsIgnoreLine
			throw new \Exception( __( 'Duplicate account.', 'wp-ever-accounting' ) );
		}

	}

	/**
	 * When an account is deleted check if
	 * default account need to be updated or not.
	 *
	 * @param $account_id
	 *
	 * @since 1.1.0
	 */
	public static function delete_account_reference( $account_id ) {
		global $wpdb;
		// $wpdb->update( "{$wpdb->prefix}ea_documents", array( 'account_id' => null ), array( 'account_id' => $account_id ) );
		$wpdb->update( "{$wpdb->prefix}ea_transactions", array( 'account_id' => null ), array( 'account_id' => $account_id ) );

		// delete default account
		$default_account = eaccounting()->settings->get( 'default_account' );
		if ( intval( $default_account ) === intval( $account_id ) ) {
			eaccounting()->settings->set( array( array( 'default_account' => '' ) ), true );
		}
	}

	/**
	 * Get account
	 *
	 * @param int $id Account ID
	 * @param string $output The required return type. One of OBJECT, ARRAY_A, or ARRAY_N. Default OBJECT.
	 *
	 * @since 1.0.0
	 */
	public static function get_account( $id, $output = OBJECT ) {
		if ( empty( $id ) ) {
			return null;
		}

		if ( $id instanceof Account ) {
			$account = $id;
		} else {
			$account = new Account( $id );
		}

		if ( ! $account->exists() ) {
			return null;
		}

		if ( ARRAY_A === $output ) {
			return $account->get_data();
		}

		if ( ARRAY_N === $output ) {
			return array_values( $account->get_data() );
		}

		return $account;
	}

	/**
	 * Get account currency code
	 *
	 * @param $account
	 *
	 * @since 1.1.0
	 * @return mixed|null
	 */
	public static function get_account_currency_code( $account ) {
		$exist = self::get_account( $account );
		if ( $exist ) {
			return $exist->get_prop( 'currency_code' );
		}

		return null;
	}

	/**
	 * Insert account
	 *
	 * @param array|object $data Account Data
	 *
	 * @since 1.1.0
	 * @return object|\WP_Error
	 */
	public static function insert_account( $data ) {
		if ( $data instanceof Account ) {
			$data = $data->get_data();
		} elseif ( is_object( $data ) ) {
			$data = get_object_vars( $data );
		}

		if ( empty( $data ) || ! is_array( $data ) ) {
			return new \WP_Error( 'invalid_data', __( 'Account could not be saved.', 'wp-ever-accounting' ) );
		}

		$data     = wp_parse_args( $data, array( 'id' => null ) );
		$account = new Account( (int) $data['id'] );
		$account->set_props( $data );
		$is_error = $account->save();
		if ( is_wp_error( $is_error ) ) {
			return $is_error;
		}

		return $account;
	}

	/**
	 * Delete account
	 *
	 * @param int $id Account ID
	 *
	 * @since 1.1.0
	 * @return object|bool
	 */
	public static function delete_account( $id ) {
		if ( $id instanceof Account ) {
			$id = $id->get_id();
		}

		if ( empty( $id ) ) {
			return false;
		}

		$account = new Account( (int) $id );
		if ( ! $account->exists() ) {
			return false;
		}

		return $account->delete();
	}

	/**
	 * Get all accounts
	 *
	 * @param array $args Search arguments
	 *
	 * @since 1.0.0
	 * @return int|object
	 */
	public static function get_accounts( $args = array(), $count = false ) {
		global $wpdb;
		$results      = null;
		$total        = 0;
		$cache_group  = Account::get_cache_group();
		$table        = $wpdb->prefix . Account::get_table_name();
		$meta_table   = $wpdb->prefix . Account::get_meta_type();
		$columns      = Account::get_columns();
		$key          = md5( serialize( $args ) );
		$last_changed = wp_cache_get_last_changed( $cache_group );
		$cache_key    = "$cache_group:$key:$last_changed";
		$cache        = wp_cache_get( $cache_key, $cache_group );
		$fields       = '';
		$join         = '';
		$where        = '';
		$groupby      = '';
		$having       = '';
		$limit        = '';

		$args = (array) wp_parse_args( $args, array(
			'orderby'    => 'date_created',
			'order'      => 'ASC',
			'search'     => '',
			'balance'    => '',
			'offset'     => '',
			'per_page'   => 20,
			'paged'      => 1,
			'meta_key'   => '',
			'meta_value' => '',
			'no_count'   => false,
			'fields'     => 'all',
			'return'     => 'objects',

		) );

		if ( false !== $cache ) {
			return $count ? $cache->total : $cache->results;
		}

		// Fields setup
		if ( is_array( $args['fields'] ) ) {
			$fields .= implode( ',', $args['fields'] );
		} elseif( 'all' === $args['fields'] ) {
			$fields .= "$table.* ";
		} else {
			$fields .= "$fields.id";
		}

		if ( false === (bool) $args['no_count'] ) {
			$fields = 'SQL_CALC_FOUND_ROWS ' . $fields;
		}

		// Query from.
		$from = "FROM $table";

		// Parse arch params
		if ( ! empty ( $args['search'] ) ) {
			$allowed_fields = array( 'name', 'number', 'currency_code', 'bank_name', 'bank_phone', 'bank_address' );
			$search_fields = ! empty( $args['search_field'] ) ? $args['search_field'] : $allowed_fields;
			$search_fields = array_intersect( $search_fields, $allowed_fields );
			$searches = array();
			foreach ( $search_fields as $field ) {
				$searches[] = $wpdb->prepare( $field . ' LIKE %s', '%' . $wpdb->esc_like( $args['search'] ) . '%' );
			}

			$where .= ' AND (' . implode( ' OR ', $searches ) . ')';
		}

		// Parse date params
		if ( ! empty ( $args['date'] ) ) {
			$args['date_from'] = $args['date'];
			$args['date_to']   = $args['date'];
		}

		if ( !empty( $args['date_from'])) {
			$date = get_gmt_from_date( gmdate( 'Y-m-d H:i:s', strtotime( $args['date_from'] . ' 00:00:00' ) ) );
			$where .= $wpdb->prepare( " AND DATE($table.date_created) >= %s", $date );
		}

		if ( !empty( $args['date_to'])) {
			$date  = get_gmt_from_date( gmdate( 'Y-m-d H:i:s', strtotime( $args['date_to'] . ' 23:59:59' ) ) );
			$where .= $wpdb->prepare( " AND DATE($table.date_created) <= %s", $date );
		}

		if ( ! empty( $args['date_after'] ) ) {
			$date  = get_gmt_from_date( gmdate( 'Y-m-d H:i:s', strtotime( $args['date_after'] ) ) );
			$where .= $wpdb->prepare( " AND DATE($table.date_created) > %s", $date );
		}

		if ( ! empty( $args['date_before'] ) ) {
			$date  = get_gmt_from_date( gmdate( 'Y-m-d H:i:s', strtotime( $args['date_before'] ) ) );
			$where .= $wpdb->prepare( " AND DATE($table.date_created) < %s", $date );
		}

		// Parse balance params
		$join = '';
		if ( true === $args['balance'] && ! $args['no_count'] ) {
			$sub_query = "
		SELECT account_id, SUM(CASE WHEN ea_transactions.type='income' then amount WHEN ea_transactions.type='expense' then - amount END) as total from
		{$wpdb->prefix}ea_transactions as ea_transactions LEFT JOIN $table ea_accounts ON ea_accounts.id=ea_transactions.account_id GROUP BY account_id";
			$join     .= " LEFT JOIN ($sub_query) as calculated ON calculated.account_id = {$table}.id";
			$fields   .= " , ( {$table}.opening_balance + IFNULL( calculated.total, 0) ) as balance ";
		}

		// Parse __in params
		$ins = array();
		foreach ( $args as $arg => $value ) {
			if ( '__in' === substr( $arg, - 4 ) ) {
				$ins[ $arg ] = wp_parse_list( $value );
			}
		}
		if ( ! empty( $ins ) ) {
			foreach ( $ins as $key => $value ) {
				if ( empty( $value ) || ! is_array( $value ) ) {
					continue;
				}

				$field = str_replace( array( 'record_', '__in' ), '', $key );
				$field = empty( $field ) ? 'id' : $field;
				$type  = is_numeric( reset( $value ) ) ? '%d' : '%s';

				if ( ! empty( $value ) ) {
					$format = '(' . implode( ',', array_fill( 0, count( $value ), $type ) ) . ')';

					$where .= $wpdb->prepare( " AND $table.$field IN {$format}", $value ); // @codingStandardsIgnoreLine prepare okay
				}
			}
		}

		// Parse not__in params.
		$not_ins = array();
		foreach ( $args as $arg => $value ) {
			if ( '__not_in' === substr( $arg, - 8 ) ) {
				$not_ins[ $arg ] = $value;
			}
		}
		if ( ! empty( $not_ins ) ) {
			foreach ( $not_ins as $key => $value ) {
				if ( empty( $value ) || ! is_array( $value ) ) {
					continue;
				}

				$field = str_replace( array( 'record_', '__not_in' ), '', $key );
				$field = empty( $field ) ? 'id' : $field;
				$type  = is_numeric( reset( $value ) ) ? '%d' : '%s';

				if ( ! empty( $value ) ) {
					$format = '(' . implode( ',', array_fill( 0, count( $value ), $type ) ) . ')';
					$where  .= $wpdb->prepare( " AND $table.$field NOT IN {$format}", $value ); // @codingStandardsIgnoreLine prepare okay
				}
			}
		}

		// Parse status params
		if ( ! empty( $args['status'] ) && ! in_array( $args['status'], array( 'all', 'any'), true ) ) {
			$status = eaccounting_string_to_bool( $args['status'] );
			$status = eaccounting_bool_to_number( $status );
			$where .= " AND $table.`enabled` = ('$status')";
		}

		// Parse creator id params
		if ( ! empty( $args['creator_id'] ) ) {
			$creator_id = implode( ',', wp_parse_id_list( $args['creator_id'] ) );
			$where      .=  " AND $table.`creator_id__in` IN ($creator_id)";
		}

		// Parse type params
		if ( ! empty( $args['type'] ) ) {
			$types  = implode( "','", wp_parse_list( $args['type'] ) );
			$where .= " AND $table.`type` IN ('$types')";
		}

		// Parse currency code params
		if ( ! empty( $args['currency_code'] ) ) {
			$currency_code = implode( "','", wp_parse_list( $args['currency_code'] ) );
			$where        .= " AND $table.`currency_code` IN ('$currency_code')";
		}

		//Parse pagination
		$page     = absint( $args['paged'] );
		$per_page = absint( $args['per_page'] );
		if ( $per_page >= 0 ) {
			$offset = absint( ( $page - 1 ) * $per_page );
			$limit  = " LIMIT {$offset}, {$per_page}";
		}

		//Parse order.
		$orderby = "$table.id";
		if ( in_array( $args['orderby'], $columns, true ) ) {
			$orderby = sprintf( '%s.%s', $table, $args['orderby'] );
		} elseif ( 'meta_value_num' === $args['orderby'] && ! empty( $args['meta_key'] ) ) {
			$orderby = "CAST($meta_table.meta_value AS SIGNED)";
		} elseif ( 'meta_value' === $args['orderby'] && ! empty( $args['meta_key'] ) ) {
			$orderby = "$meta_table.meta_value";
		}
		// Show the recent records first by default.
		$order = 'DESC';
		if ( 'ASC' === strtoupper( $args['order'] ) ) {
			$order = 'ASC';
		}

		$orderby = sprintf( 'ORDER BY %s %s', $orderby, $order );

		//Parse meta param.
		if ( null === $results ) {
			$request = "SELECT {$fields} {$from} {$join} WHERE 1=1 {$where} {$groupby} {$having} {$orderby} {$limit}";

			if ( is_array( $args['fields'] ) || 'all' === $args['fields'] ) {
				$results = $wpdb->get_results( $request );
			} else {
				$results = $wpdb->get_col( $request );
			}

			if ( ! $args['no_count'] ) {
				$total = (int) $wpdb->get_var( "SELECT FOUND_ROWS()" );
			}

			if ( 'all' === $args['fields'] ) {
				foreach ( $results as $key => $row ) {
					wp_cache_add( $row->id, $row, $cache_group );
					$item = new Account();
					$item->set_props( $row );
					$item->set_object_read( true );
					$results[ $key ] = $item;
				}
			}

			$cache          = new \StdClass;
			$cache->results = $results;
			$cache->total   = $total;

			wp_cache_add( $cache_key, $cache, $cache_group );
		}

		return $count ? $total : $results;
	}
}
