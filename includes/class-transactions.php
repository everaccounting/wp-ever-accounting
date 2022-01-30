<?php
/**
 * Transactions class.
 *
 * Handle transaction insert, update, delete & retrieve from database.
 *
 * @version   1.1.3
 * @package   EverAccounting
 */

namespace EverAccounting;


defined( 'ABSPATH' ) || exit;

class Transactions {

	/**
	 * Transactions construct.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct() {
		// payments
		add_action( 'eaccounting_validate_payment_data', array( __CLASS__, 'validate_payment_data' ), 10, 2 );

		// revenues
		add_action( 'eaccounting_validate_revenue_data', array( __CLASS__, 'validate_revenue_data' ), 10, 2 );
	}

	/**
	 * Validate payment data.
	 *
	 * @param null $id
	 * @param \WP_Error $errors
	 *
	 * @param array $data
	 *
	 * @throws \Exception
	 * @since 1.1.0
	 */
	public static function validate_payment_data( $data, $id = null ) {
		if ( empty( $data['payment_date'] ) ) {
			throw new \Exception( 'empty_prop', __( 'Payment date is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $data['payment_method'] ) ) {
			throw new \Exception( 'empty_prop', __( 'Payment method is required.', 'wp-ever-accounting' ) );
		}

		$category = Categories::get_category( $data['category_id'] );
		if ( empty( $category ) || ! in_array( $category->get_type(), array( 'expense', 'other' ), true ) ) {
			throw new \Exception( __( 'A valid payment category is required.', 'wp-ever-accounting' ) );
		}

		$vendor = Contacts::get_vendor( $data['contact_id'] );
		if ( ! empty( $data['contact_id'] ) && empty( $vendor ) ) {
			throw new \Exception( __( 'Vendor is not valid.', 'wp-ever-accounting' ) );
		}

		$account = Accounts::get_account( $data['account_id'] );
		if ( ! empty( $data['account_id'] ) && empty( $account ) ) {
			throw new \Exception( __( 'Account is not valid.', 'wp-ever-accounting' ) );
		}

		if ( empty( eaccounting_sanitize_number( $data['amount'] ) ) ) {
			throw new \Exception( 'empty_prop', __( 'Payment amount is required.', 'wp-ever-accounting' ) );
		}
	}

	/**
	 * Validate expense data.
	 *
	 * @param null $id
	 * @param \WP_Error $errors
	 *
	 * @param array $data
	 *
	 * @throws \Exception
	 * @since 1.1.0
	 */
	public static function validate_revenue_data( $data, $id = null ) {
		if ( empty( $data['payment_date'] ) ) {
			throw new \Exception( 'empty_prop', __( 'Revenue date is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $data['payment_method'] ) ) {
			throw new \Exception( 'empty_prop', __( 'Payment method is required.', 'wp-ever-accounting' ) );
		}

		$category = Categories::get_category( $data['category_id'] );
		if ( empty( $category ) || ! in_array( $category->get_type(), array( 'income', 'other' ), true ) ) {
			throw new \Exception( 'empty_prop', __( 'A valid income category is required.', 'wp-ever-accounting' ) );
		}

		$account = Accounts::get_account( $data['account_id'] );
		if ( empty( $account ) ) {
			throw new \Exception( 'empty_prop', __( 'Account is required.', 'wp-ever-accounting' ) );
		}

		$customer = Contacts::get_customer( $data['contact_id'] );
		if ( ! empty( $data['contact_id'] ) && empty( $customer ) ) {
			throw new \Exception( 'empty_prop', __( 'Customer is not valid.', 'wp-ever-accounting' ) );
		}

		if ( empty( eaccounting_sanitize_number( $data['amount'] ) ) ) {
			throw new \Exception( 'empty_prop', __( 'Revenue amount is required.', 'wp-ever-accounting' ) );
		}
	}

	/**
	 * Get contact types.
	 *
	 * @since 1.1.3
	 * @return array
	 */
	public static function get_transaction_types() {
		return apply_filters(
			'eaccounting_transaction_types',
			array(
				'income'  => __( 'Income', 'wp-ever-accounting' ),
				'expense' => __( 'Expense', 'wp-ever-accounting' ),
			)
		);
	}

	/**
	 * Get a single payment
	 *
	 * @param array|object $payment Payment data
	 *
	 * @return Payment|array|null
	 * @since 1.1.0
	*/
	public static function get_payment( $id, $output = false ) {
		if( empty( $id ) ) {
			return null;
		}

		if( $id instanceof Payment ) {
			$payment = $id;
		} else {
			$payment = new Payment( $id );
		}

		if ( ! $payment->exists() ) {
			return null;
		}

		if ( ARRAY_A === $output ) {
			return $payment->get_data();
		}

		if ( ARRAY_N === $output ) {
			return array_values( $payment->get_data() );
		}

		return $payment;


	}

	/**
	 * Insert payment
	 *
	 * @param array|object $args Payment data
	 *
	 * @return object|\WP_Error|bool
	 * @since 1.1.0
	*/
	public static function insert_payment( $data ) {
		if ( $data instanceof Payment ) {
			$data = $data->get_data();
		} elseif ( is_object( $data ) ) {
			$data = get_object_vars( $data );
		}

		if ( empty( $data ) || ! is_array( $data ) ) {
			return new \WP_Error( 'invalid_data', __( 'Payment could not be saved.', 'wp-ever-accounting' ) );
		}

		$data    = wp_parse_args( $data, array( 'id' => null ) );
		$payment = new Payment( (int) $data['id'] );
		$payment->set_props( $data );
		$is_error = $payment->save();
		if ( is_wp_error( $is_error ) ) {
			return $is_error;
		}

		return $payment;
	}

	/**
	 * Delete payment
	 *
	 * @param int $id Payment id
	 *
	 * @return object|bool
	 * @since 1.1.0
	*/
	public static function delete_payment( $id ) {
		if ( $id instanceof Payment ) {
			$id = $id->get_id();
		}

		if ( empty( $id ) ) {
			return false;
		}

		$payment = new Payment( (int) $id );
		if ( ! $payment->exists() ) {
			return false;
		}

		return $payment->delete();
	}

	/**
	 * Get all payments
	 *
	 * @return array|int
	 * @since 1.1.0
	*/
	public static function get_payments( $args = array(), $count = false ) {
		return self::get_transactions( array_merge( $args, array( 'type' => 'expense' ) ), $count );
	}

	/**
	 * Get a single revenue
	 *
	 * @param int $id  Revenue ID
	 * @param string $output The required return type. One of OBJECT, ARRAY_A, or ARRAY_N. Default OBJECT.
	 *
	 * @return Revenue|array|null
	 * @since 1.1.0
	*/
	public static function get_revenue( $id, $output = OBJECT ) {
		if ( empty( $id ) ) {
			return null;
		}

		if ( $id instanceof Revenue ) {
			$revenue = $id;
		} else {
			$revenue = new Revenue( $id );
		}

		if ( ! $revenue->exists() ) {
			return null;
		}

		if ( ARRAY_A === $output ) {
			return $revenue->get_data();
		}

		if ( ARRAY_N === $output ) {
			return array_values( $revenue->get_data() );
		}

		return $revenue;
	}

	/**
	 * Insert revenue
	 *
	 * @param array|object $args Revenue data
	 *
	 * @return object|\WP_Error|bool
	 * @since 1.1.0
	 */
	public static function insert_revenue( $data ) {
		if ( $data instanceof Revenue ) {
			$data = $data->get_data();
		} elseif ( is_object( $data ) ) {
			$data = get_object_vars( $data );
		}

		if ( empty( $data ) || ! is_array( $data ) ) {
			return new \WP_Error( 'invalid_data', __( 'Revenue could not be saved.', 'wp-ever-accounting' ) );
		}

		$data    = wp_parse_args( $data, array( 'id' => null ) );
		$revenue = new Revenue( (int) $data['id'] );
		$revenue->set_props( $data );
		$is_error = $revenue->save();
		if ( is_wp_error( $is_error ) ) {
			return $is_error;
		}

		return $revenue;
	}

	/**
	 * Delete a revenue
	 *
	 * @param int $id Revenue id
	 *
	 * @return bool
	 * @since 1.1.0
	*/
	public static function delete_revenue( $id ) {
		if ( $id instanceof Revenue ) {
			$id = $id->get_id();
		}

		if ( empty( $id ) ) {
			return false;
		}

		$revenue = new Revenue( (int) $id );
		if ( ! $revenue->exists() ) {
			return false;
		}

		return $revenue->delete();
	}

	/**
	 * Get all revenues
	 *
	 *
	 * @return array|int
	 * @since 1.1.0
	 */
	public static function get_revenues( $args = array(), $count = false ) {
		return self::get_transactions( array_merge( $args, array( 'type' => 'income' ) ), $count );
	}


	/**
	 * Get transaction
	 *
	 * @param int $id Transaction ID
	 * @param string $output The required return type. One of OBJECT, ARRAY_A, or ARRAY_N. Default OBJECT.
	 *
	 * @since 1.0.0
	 */
	public static function get_transaction( $id, $output = OBJECT ) {
		if ( empty( $id ) ) {
			return null;
		}

		if ( $id instanceof Transaction ) {
			$transaction = $id;
		} else {
			$transaction = new Transaction( $id );
		}

		if ( ! $transaction->exists() ) {
			return null;
		}

		if ( ARRAY_A === $output ) {
			return $transaction->get_data();
		}

		if ( ARRAY_N === $output ) {
			return array_values( $transaction->get_data() );
		}

		return $transaction;
	}

	/**
	 * Get all transactions
	 *
	 * @param array $args Search arguments
	 *
	 * @since 1.0.0
	 * @return int|object
	 */
	public static function get_transactions( $args = array(), $count = false ) {
		global $wpdb;
		$results      = null;
		$total        = 0;
		$cache_group  = Transaction::get_cache_group();
		$table        = $wpdb->prefix . Transaction::get_table_name();
		$meta_table   = $wpdb->prefix . Transaction::get_meta_type();
		$columns      = Transaction::get_columns();
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
			'type'       => '',
			'orderby'    => 'date_created',
			'order'      => 'ASC',
			'search'     => '',
			'offset'     => '',
			'per_page'   => 20,
			'paged'      => 1,
			'meta_key'   => '',
			'meta_value' => '',
			'no_count'   => false,
			'fields'     => 'all',
			'return'     => 'objects',
			'transfer'   => true,
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

		// Parse search params
		if ( ! empty ( $args['search'] ) ) {
			$allowed_fields = array( 'description', 'reference' );
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

		// Parse payment_method params
		if ( ! empty( $args['payment_method'] ) ) {
			$payment_method = implode( "','", wp_parse_list( $args['payment_method'] ) );
			$where          .= " AND $table.`payment_method` IN ('$payment_method')";
		}

		// Parse account_id params
		if ( ! empty( $args['account_id'] ) ) {
			$account_id = implode( ',', wp_parse_id_list( $args['account_id'] ) );
			$where      .= " AND $table.`account_id` IN ($account_id)";
		}

		// Parse document_id params
		if ( ! empty( $args['document_id'] ) ) {
			$document_id = implode( ',', wp_parse_id_list( $args['document_id'] ) );
			$where       .= " AND $table.`document_id` IN ($document_id)";
		}

		// Parse category_id params
		if ( ! empty( $args['category_id'] ) ) {
			$category_id = implode( ',', wp_parse_id_list( $args['category_id'] ) );
			$where       .= " AND $table.`category_id` IN ($category_id)";
		}

		// Parse contact_id params
		if ( ! empty( $args['contact_id'] ) ) {
			$contact_id = implode( ',', wp_parse_id_list( $args['contact_id'] ) );
			$where       .= " AND $table.`contact_id` IN ($contact_id)";
		}

		// Parse parent_id params
		if ( ! empty( $args['parent_id'] ) ) {
			$parent_id = implode( ',', wp_parse_id_list( $args['parent_id'] ) );
			$where       .= " AND $table.`parent_id` IN ($parent_id)";
		}
		 // Parse payment_date between start and end date
		if( !empty( $args['payment_date']) && is_array( $args['payment_date'])) {
			$before = $args['payment_date']['before'];
			$after = $args['payment_date']['after'];
			$where .= " AND $table.`payment_date` BETWEEN '$before' AND '$after'";
		}

		// Parse creator id params
		if ( ! empty( $args['creator_id'] ) ) {
			$creator_id = implode( ',', wp_parse_id_list( $args['creator_id'] ) );
			$where      .=  " AND $table.`creator_id__in` IN ($creator_id)";
		}

		// Parse transfer params
		if( true === $args['transfer']) {
			$where .= " AND $table.`category_id` NOT IN (SELECT id from {$wpdb->prefix}ea_categories where type='other' )";
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
		$meta_query = new \WP_Meta_Query();
		$meta_query->parse_query_vars( $args );
		if ( ! empty( $meta_query->queries ) ) {
			$meta_clauses = $meta_query->get_sql( str_replace( $wpdb->prefix, '', $meta_table ), $table, 'id' );
			$from         .= $meta_clauses['join'];
			$where        .= $meta_clauses['where'];

			if ( $meta_query->has_or_relation() ) {
				$fields = 'DISTINCT ' . $fields;
			}
		}

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
					$item = new Transaction();
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
