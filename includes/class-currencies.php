<?php
/**
 * Currencies class.
 *
 * Handle currency insert, update, delete & retrieve from database.
 *
 * @version   1.1.3
 * @package   Ever_Accounting
 */

namespace Ever_Accounting;

defined( 'ABSPATH' ) || exit;

/**
 * Currencies class
 */
class Currencies {
	/**
	 * Currencies construct.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct() {
		add_action( 'update_option_eaccounting_settings', array( __CLASS__, 'update_default_currency' ), 10, 2 );
		add_action( 'eaccounting_delete_currency', array( __CLASS__, 'delete_currency_reference' ), 10, 2 );
	}

	/**
	 * Update default currency.
	 *
	 * @param $id
	 *
	 * @since 1.1.0
	 * @return bool|void
	 *
	 */
	public static function update_default_currency( $value, $old_value ) {
		if ( ! array_key_exists( 'default_currency', $value ) || $value['default_currency'] === $old_value['default_currency'] ) {
			return;
		}

		if ( empty( new Currency( $old_value['default_currency'] ) ) ) {
			return;
		}

		do_action( 'eaccounting_pre_change_default_currency', $value['default_currency'], $old_value['default_currency'] );
		$new_currency          = new Currency( $old_value['default_currency'] );
		$new_currency_old_rate = $new_currency->get_rate();
		$conversion_rate       = (float) ( 1 / $new_currency_old_rate );
		$currencies            = eaccounting_collect( get_option( 'eaccounting_currencies', array() ) );
		$currencies            = $currencies->each(
			function ( $currency ) use ( $conversion_rate ) {
				$currency['rate'] = eaccounting_format_decimal( $currency['rate'] * $conversion_rate, 4 );

				return $currency;
			}
		)->all();
		update_option( 'eaccounting_currencies', $currencies );
	}

	/**
	 * Delete currency id from settings.
	 *
	 * @param $data
	 *
	 * @param $id
	 *
	 * @since 1.1.0
	 */
	public static function delete_currency_reference( $id, $data ) {
		$default_currency = eaccounting()->settings->get( 'default_currency' );
		if ( $default_currency === $data['code'] ) {
			eaccounting()->settings->set( array( array( 'default_currency' => '' ) ), true );
		}
	}

	/**
	 * Return all available currency codes.
	 *
	 * @since 1.1.0
	 * @return array
	 */
	public static function get_codes() {
		return require EVER_ACCOUNTING_DIR . '/i18n/currencies.php';
	}

	/**
	 * Check if currency code is a valid one.
	 *
	 * @param $code
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public static function sanitize_code( $code ) {
		$codes = self::get_codes();
		$code  = strtoupper( trim( $code ) );
		if ( empty( $code ) || ! array_key_exists( $code, $codes ) ) {
			return '';
		}

		return $code;
	}

	/**
	 * @param $currency
	 *
	 * @since 1.1.0
	 *
	 * @return float
	 */
	public static function get_rate( $currency ) {
		$exist = self::get( $currency->get_id() );
		if ( $exist ) {
			return $exist->get_rate();
		}

		return 1;
	}

	/**
	 * Get currency by code
	 *
	 * @param string $code Currency code
	 *
	 * @since 1.1.3
	 * @return Currency|null
	 */
	public static function get_by_code( $code ) {
		global $wpdb;
		$currency = wp_cache_get( $code, 'ea_currencies' );
		if ( $currency === false ) {
			$currency = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_currencies WHERE code = %s", self::sanitize_code( $code ) ) );
			wp_cache_set( $code, $currency, 'ea_currencies' );
		}

		return new Currency( $currency );
	}

	/**
	 * Get currency
	 *
	 * @param string $code Currency Code
	 * @param string $output The required return type. One of OBJECT, ARRAY_A, or ARRAY_N. Default OBJECT.
	 *
	 * @since 1.0.0
	 */
	public static function get( $code, $output = OBJECT ) {
		if ( empty( $code ) ) {
			return null;
		}

		if ( $code instanceof Currency ) {
			$currency = $code;
		} else if ( is_numeric( $code ) ) {
			$currency = new Currency( $code );
		} else {
			$currency = new Currency( self::get_by_code( $code ) );
		}

		if ( ! $currency->exists() ) {
			return null;
		}

		if ( ARRAY_A === $output ) {
			return $currency->get_data();
		}

		if ( ARRAY_N === $output ) {
			return array_values( $currency->get_data() );
		}

		return $currency;
	}

	/**
	 * Insert currency
	 *
	 * @param array|object $data Currency Data
	 *
	 * @since 1.1.0
	 * @return Currency|\WP_Error
	 */
	public static function insert( $data ) {
		if ( $data instanceof Currency ) {
			$data = $data->get_data();
		} elseif ( is_object( $data ) ) {
			$data = get_object_vars( $data );
		}

		if ( empty( $data ) || ! is_array( $data ) ) {
			return new \WP_Error( 'invalid_data', __( 'Currency could not be saved.', 'wp-ever-accounting' ) );
		}
		$codes    = self::get_codes();
		$data     = wp_parse_args( $data, array( 'id' => null, 'code' => '' ) );
		$defaults = array_key_exists( $data['code'], $codes ) ? $codes[ $data['code'] ] : array();
		$data     = wp_parse_args( $data, $defaults );
		$currency = new Currency( (int) $data['id'] );
		$currency->set_props( $data );
		$is_error = $currency->save();
		if ( is_wp_error( $is_error ) ) {
			return $is_error;
		}

		return $currency;
	}

	/**
	 * Delete currency
	 *
	 * @param int $id Currency ID
	 *
	 * @since 1.1.0
	 * @return array|false
	 */
	public static function delete( $id ) {
		if ( $id instanceof Currency ) {
			$id = $id->get_id();
		}

		if ( empty( $id ) ) {
			return false;
		}

		$currency = new Currency( (int) $id );
		if ( ! $currency->exists() ) {
			return false;
		}

		return $currency->delete();
	}

	/**
	 * Get all currencies
	 *
	 * @param array $args Search arguments
	 *
	 * @since 1.0.0
	 * @return int|Currencies[]
	 */
	public static function query( $args = array(), $count = false ) {
		global $wpdb;
		$results      = null;
		$total        = 0;
		$cache_group  = Currency::get_cache_group();
		$table        = $wpdb->prefix . Currency::get_table_name();
		$columns      = Currency::get_columns();
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
			'orderby'  => 'name',
			'order'    => 'ASC',
			'search'   => '',
			'balance'  => '',
			'offset'   => '',
			'per_page' => 20,
			'paged'    => 1,
			'no_count' => false,
			'fields'   => 'all',
			'return'   => 'objects',
		) );

		if ( false !== $cache ) {
			return $count ? $cache->total : $cache->results;
		}

		// Fields setup
		if ( is_array( $args['fields'] ) ) {
			$fields .= implode( ',', $args['fields'] );
		} elseif ( 'all' === $args['fields'] ) {
			$fields .= "$table.* ";
		} else {
			$fields .= "$fields.id";
		}

		if ( false === (bool) $args['no_count'] ) {
			$fields = 'SQL_CALC_FOUND_ROWS ' . $fields;
		}

		// Query from.
		$from = "FROM $table";

		//Parse query args
		if ( isset( $args['include'] ) ) {
			$args['id__in'] = $args['include'];
		}
		if ( isset( $args['exclude'] ) ) {
			$args['id__not_in'] = $args['exclude'];
		}

		// Parse arch params
		if ( ! empty ( $args['search'] ) ) {
			$allowed_fields = array( 'name', 'code', 'rate' );
			$search_fields  = ! empty( $args['search_field'] ) ? $args['search_field'] : $allowed_fields;
			$search_fields  = array_intersect( $search_fields, $allowed_fields );
			$searches       = array();
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

		if ( ! empty( $args['date_from'] ) ) {
			$date  = get_gmt_from_date( gmdate( 'Y-m-d H:i:s', strtotime( $args['date_from'] . ' 00:00:00' ) ) );
			$where .= $wpdb->prepare( " AND DATE($table.date_created) >= %s", $date );
		}

		if ( ! empty( $args['date_to'] ) ) {
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
		if ( ! empty( $args['status'] ) && ! in_array( $args['status'], array( 'all', 'any' ), true ) ) {
			$status = eaccounting_string_to_bool( $args['status'] );
			$status = eaccounting_bool_to_number( $status );
			$where  .= " AND $table.`enabled` = ('$status')";
		}

		// Parse creator id params
		if ( ! empty( $args['creator_id'] ) ) {
			$creator_id = implode( ',', wp_parse_id_list( $args['creator_id'] ) );
			$where      .= " AND $table.`creator_id` IN ($creator_id)";
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
					wp_cache_add( $row->code, $row, $cache_group );
					$item = new Currency();
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

		if ( 'objects' === $args['return'] && true !== $args['no_count'] ) {
			$results = array_map( array( __CLASS__, 'get' ), $results );
		}

		return $count ? $total : $results;
	}
}
