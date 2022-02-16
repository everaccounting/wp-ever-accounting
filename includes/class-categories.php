<?php
/**
 * Categories class.
 *
 * Handle category insert, update, delete & retrieve from database.
 *
 * @version   1.1.3
 * @package   Ever_Accounting
 */

defined( 'ABSPATH' ) || exit;

class Categories {

	/**
	 * Categories construct.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct() {
		// accounts
		add_action( 'eaccounting_pre_save_category', array( __CLASS__, 'validate_category_data' ), 10, 2 );
		add_action( 'eaccounting_delete_category', array( __CLASS__, 'delete_category_reference' ) );
	}

	/**
	 * Validate category data.
	 *
	 * @param null $id
	 * @param Category $category
	 *
	 * @param array $data
	 *
	 * @throws \Exception
	 *
	 * @since 1.1.0
	 */
	public static function validate_category_data( $data, $id ) {
		global $wpdb;
		$existing_category_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT id from {$wpdb->prefix}ea_categories WHERE type=%s AND name='%s'", eaccounting_clean( $data['type'] ), eaccounting_clean( $data['name'] ) ) );

		if ( ! empty( $existing_category_id ) && ( $id != $existing_category_id ) ) {
			throw new \Exception( __( 'Duplicate category.', 'wp-ever-accounting' ) );
		}
	}

	/**
	 * Delete category id from transactions.
	 *
	 * @param $id
	 *
	 * @since 1.1.0
	 */
	public static function delete_category_reference( $id ) {
		global $wpdb;
		$wpdb->update( $wpdb->prefix . 'ea_transactions', array( 'category_id' => null ), array( 'category_id' => absint( $id ) ) );
		$wpdb->update( $wpdb->prefix . 'ea_documents', array( 'category_id' => null ), array( 'category_id' => absint( $id ) ) );
		$wpdb->update( $wpdb->prefix . 'ea_items', array( 'category_id' => null ), array( 'category_id' => absint( $id ) ) );
	}

	/**
	 * Get all the available type of category the plugin support.
	 *
	 * @return array
	 * @since 1.1.0
	 */
	public static function get_category_types() {
		$types = array(
			'expense' => __( 'Expense', 'wp-ever-accounting' ),
			'income'  => __( 'Income', 'wp-ever-accounting' ),
			'other'   => __( 'Other', 'wp-ever-accounting' ),
			'item'    => __( 'Item', 'wp-ever-accounting' ),
		);

		return apply_filters( 'eaccounting_category_types', $types );
	}

	/**
	 * Get the category type label of a specific type.
	 *
	 * @param $type
	 *
	 * @return string
	 * @since 1.1.0
	 *
	 */
	public static function get_category_type( $type ) {
		$types = self::get_category_types();

		return array_key_exists( $type, $types ) ? $types[ $type ] : null;
	}

	/**
	 * Get category
	 *
	 * @param int $id Category ID
	 * @param string $output The required return type. One of OBJECT, ARRAY_A, or ARRAY_N. Default OBJECT.
	 *
	 * @since 1.0.0
	 */
	public static function get_category( $id, $output = OBJECT ) {
		if ( empty( $id ) ) {
			return null;
		}

		if ( $id instanceof Category ) {
			$category = $id;
		} else {
			$category = new Category( $id );
		}

		if ( ! $category->exists() ) {
			return null;
		}

		if ( ARRAY_A === $output ) {
			return $category->get_data();
		}

		if ( ARRAY_N === $output ) {
			return array_values( $category->get_data() );
		}

		return $category;
	}

	/**
	 * Get category by name.
	 *
	 * @param $name
	 * @param $type
	 *
	 * @return \Ever_Accounting\Category|null
	 * @since 1.1.0
	 *
	 */
	public static  function get_category_by_name( $name, $type ) {
		global $wpdb;
		$cache_key = "$name-$type";
		$category  = wp_cache_get( $cache_key, 'ea_categories' );
		if ( false === $category ) {
			$category = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_categories where `name`=%s AND `type`=%s", eaccounting_clean( $name ), eaccounting_clean( $type ) ) );
			wp_cache_set( $cache_key, $category, 'ea_categories' );
		}
		if ( $category ) {
			wp_cache_set( $category->id, $category, 'ea_categories' );

			return self::get_category( $category );
		}

		return null;
	}


	/**
	 * Insert category
	 *
	 * @param array|object $data Category Data
	 *
	 * @since 1.1.0
	 * @return object|\WP_Error
	 */
	public static function insert_category( $data ) {
		if ( $data instanceof Category ) {
			$data = $data->get_data();
		} elseif ( is_object( $data ) ) {
			$data = get_object_vars( $data );
		}

		if ( empty( $data ) || ! is_array( $data ) ) {
			return new \WP_Error( 'invalid_data', __( 'Category could not be saved.', 'wp-ever-accounting' ) );
		}

		$data     = wp_parse_args( $data, array( 'id' => null ) );
		$category = new Category( (int) $data['id'] );
		$category->set_props( $data );
		$is_error = $category->save();
		if ( is_wp_error( $is_error ) ) {
			return $is_error;
		}

		return $category;
	}

	/**
	 * Delete category
	 *
	 * @param int $id Category ID
	 *
	 * @since 1.1.0
	 * @return object|bool
	 */
	public static function delete_category( $id ) {
		if ( $id instanceof Category ) {
			$id = $id->get_id();
		}

		if ( empty( $id ) ) {
			return false;
		}

		$category = new Category( (int) $id );
		if ( ! $category->exists() ) {
			return false;
		}

		return $category->delete();
	}

	/**
	 * Get all categories
	 *
	 * @param array $args Search arguments
	 *
	 * @since 1.0.0
	 * @return int|object
	 */
	public static function get_categories( $args = array(), $count = false ) {
		global $wpdb;
		$results      = null;
		$total        = 0;
		$cache_group  = Category::get_cache_group();
		$table        = $wpdb->prefix . Category::get_table_name();
		$meta_table   = $wpdb->prefix . Category::get_meta_type();
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
			'type'       => ''
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
			$allowed_fields = array( 'name', 'type', 'color' );
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

		// Parse status params
		if ( ! empty( $args['status'] ) && ! in_array( $args['status'], array( 'all', 'any'), true ) ) {
			$status = eaccounting_string_to_bool( $args['status'] );
			$status = eaccounting_bool_to_number( $status );
			$where .= " AND $table.`enabled` = ('$status')";
		}

		// Parse type params
		if ( ! empty( $args['type'] ) ) {
			$types  = implode( "','", wp_parse_list( $args['type'] ) );
			$where .= " AND $table.`type` IN ('$types')";
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
					$item = new Category();
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
