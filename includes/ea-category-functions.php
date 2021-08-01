<?php
/**
 * EverAccounting category Functions.
 *
 * All category related function of the plugin.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

use EverAccounting\Category;

defined( 'ABSPATH' ) || exit();

// Sanitization and escaping filters
add_filter( 'eaccounting_pre_category_enabled', 'eaccounting_bool_to_number', 10, 1 );
add_filter( 'eaccounting_edit_category_enabled', 'eaccounting_string_to_bool', 10, 1 );

/**
 * Get all the available type of category the plugin support.
 *
 * @return array
 * @since 1.1.0
 */
function eaccounting_get_category_types() {
	$types = array(
		'expense' => __( 'Expense', 'wp-ever-accounting' ),
		'income'  => __( 'Income', 'wp-ever-accounting' ),
		'other'   => __( 'Other', 'wp-ever-accounting' ),
		'item'    => __( 'Item', 'wp-ever-accounting' ),
	);

	return apply_filters( 'eaccounting_category_types', $types );
}

/**
 * Retrieves category data given a category id or category object.
 *
 * @param int|object|Category $category category to retrieve
 * @param string $output The required return type. One of OBJECT, ARRAY_A, or ARRAY_N.Default OBJECT.
 * @param string $filter Type of filter to apply. Accepts 'raw', 'edit', 'db', or 'display'. Default 'raw'.
 *
 * @return Category|array|null
 * @since 1.1.0
 */
function eaccounting_get_category( $category, $output = OBJECT, $filter = 'raw' ) {
	if ( empty( $category ) ) {
		return null;
	}

	if ( $category instanceof Category ) {
		$_category = $category;
	} elseif ( is_object( $category ) ) {
		$_category = new Category( $category );
	} else {
		$_category = Category::get_instance( $category );
	}

	if ( ! $_category ) {
		return null;
	}

	$_category = $_category->filter( $filter );

	if ( ARRAY_A === $output ) {
		return $_category->to_array();
	}

	if ( ARRAY_N === $output ) {
		return array_values( $_category->to_array() );
	}

	return $_category->filter( $filter );
}

/**
 * Get category by name.
 *
 * @param string $name Category Name
 * @param string $type Category type
 *
 * @return Category|null
 * @since 1.1.0
 */
function eaccounting_get_category_by_name( $name, $type ) {
	global $wpdb;
	$cache_key = "$name-$type";
	$category  = wp_cache_get( $cache_key, 'ea_categories' );
	if ( false === $category ) {
		$category = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_categories where `name`=%s AND `type`=%s", eaccounting_clean( $name ), eaccounting_clean( $type ) ) );
		wp_cache_set( $cache_key, $category, 'ea_categories' );
	}
	if ( $category ) {
		wp_cache_set( $category->id, $category, 'ea_categories' );

		return eaccounting_get_category( $category, 'raw' );
	}

	return null;
}

/**
 *  Insert or update a category.
 *
 * @param array|object|Category $category_arr An array, object, or category object of data arguments.
 *
 * @return Category|WP_Error The category object or WP_Error otherwise.
 * @global wpdb $wpdb WordPress database abstraction object.
 * @since 1.1.0
 */
function eaccounting_insert_category( $category_arr ) {
	global $wpdb;
	if ( $category_arr instanceof Category ) {
		$category_arr = $category_arr->to_array();
	} elseif ( $category_arr instanceof stdClass ) {
		$category_arr = get_object_vars( $category_arr );
	}

	$defaults = array(
		'name'         => '',
		'type'         => '',
		'color'        => '',
		'enabled'      => true,
		'date_created' => '',
	);

	// Are we updating or creating?
	$id          = null;
	$update      = false;
	$data_before = array();
	if ( ! empty( $item_data['id'] ) ) {
		$update      = true;
		$id          = absint( $item_data['id'] );
		$data_before = eaccounting_get_category( $id, ARRAY_A );

		if ( is_null( $data_before ) ) {
			return new WP_Error( 'invalid_category_id', __( 'Invalid category id to update.', 'wp-ever-accounting' ) );
		}

		// Merge old and new fields with new fields overwriting old ones.
		$category_arr = array_merge( $data_before, $category_arr );
		$data_before  = $data_before->to_array();
	}

	$item_data = wp_parse_args( $category_arr, $defaults );
	$data_arr  = eaccounting_sanitize_category( $category_arr, 'db' );

	// Check required
	if ( empty( $data_arr['name'] ) ) {
		return new WP_Error( 'invalid_category_name', esc_html__( 'Category name is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['type'] ) ) {
		return new WP_Error( 'invalid_category_type', esc_html__( 'Category type id is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['date_created'] ) || '0000-00-00 00:00:00' === $data_arr['date_created'] ) {
		$data_arr['date_created'] = current_time( 'mysql' );
	}

	$fields = array_keys( $defaults );
	$data   = wp_array_slice_assoc( $data_arr, $fields );

	/**
	 * Filters category data before it is inserted into the database.
	 *
	 * @param array $data Data to be inserted.
	 * @param array $data_arr Sanitized data.
	 *
	 * @since 1.2.1
	 */
	$data = apply_filters( 'eaccounting_insert_category', $data, $data_arr );

	$data  = wp_unslash( $data );
	$where = array( 'id' => $id );

	if ( $update ) {

		/**
		 * Fires immediately before an existing category item is updated in the database.
		 *
		 * @param int $id Category id.
		 * @param array $data Category data to be inserted.
		 * @param array $changes Category data to be updated.
		 * @param array $data_arr Sanitized category item data.
		 * @param array $data_before Category previous data.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_update_category', $id, $data, $data_arr, $data_before );
		if ( false === $wpdb->update( $wpdb->prefix . 'ea_categories', $data, $where, $data_before ) ) {
			new WP_Error( 'db_update_error', __( 'Could not update category in the database.', 'wp-ever-accounting' ), $wpdb->last_error );
		}

		/**
		 * Fires immediately after an existing category is updated in the database.
		 *
		 * @param int $id Category id.
		 * @param array $data Category data to be inserted.
		 * @param array $changes Category data to be updated.
		 * @param array $data_arr Sanitized Category data.
		 * @param array $data_before Category previous data.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_update_category', $id, $data, $data_arr, $data_before );
	} else {

		/**
		 * Fires immediately before an existing category is inserted in the database.
		 *
		 * @param array $data Category data to be inserted.
		 * @param string $data_arr Sanitized category item data.
		 * @param array $item_data Category data as originally passed to the function.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_insert_category', $data, $data_arr, $item_data );

		if ( false === $wpdb->insert( $wpdb->prefix . 'ea_categories', $data ) ) {
			new WP_Error( 'db_insert_error', __( 'Could not insert category into the database.', 'wp-ever-accounting' ), $wpdb->last_error );
		}

		$id = (int) $wpdb->insert_id;

		/**
		 * Fires immediately after an existing category is inserted in the database.
		 *
		 * @param int $id Category id.
		 * @param array $data Category has been inserted.
		 * @param array $data_arr Sanitized category data.
		 * @param array $item_data Category data as originally passed to the function.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_insert_category', $id, $data, $data_arr, $item_data );
	}

	// Clear cache.
	wp_cache_delete( $id, 'ea_categories' );
	wp_cache_set( 'last_changed', microtime(), 'ea_categories' );

	// Get new category object.
	$category = eaccounting_get_category( $id );

	/**
	 * Fires once a category has been saved.
	 *
	 * @param int $id Category id.
	 * @param Category $category Category object.
	 * @param bool $update Whether this is an existing category being updated.
	 *
	 * @since 1.2.1
	 */
	do_action( 'eaccounting_saved_category', $id, $category, $update, $data_arr, $data_before );

	return $category;
}

/**
 * Delete a category.
 *
 * @param int $category_id Category id.
 *
 * @return Category |false|null Category data on success, false or null on failure.
 * @since 1.1.0
 */
function eaccounting_delete_category( $category_id ) {
	global $wpdb;

	$category = eaccounting_get_category( $category_id );
	if ( ! $category || ! $category->exists() ) {
		return false;
	}

	/**
	 * Filters whether a category delete should take place.
	 *
	 * @param bool|null $delete Whether to go forward with deletion.
	 * @param Category $category contact object.
	 *
	 * @since 1.2.1
	 */
	$check = apply_filters( 'eaccounting_pre_delete_category', null, $category );
	if ( null !== $check ) {
		return $check;
	}

	/**
	 * Fires before a category is deleted.
	 *
	 * @param int $category_id Category id.
	 * @param Category $category category object.
	 *
	 * @since 1.2.1
	 *
	 * @see eaccounting_delete_category()
	 */
	do_action( 'eaccounting_before_delete_category', $category_id, $category );

	$result = $wpdb->delete( $wpdb->prefix . 'ea_categories', array( 'id' => $category_id ) );
	if ( ! $result ) {
		return false;
	}

	wp_cache_delete( $category_id, 'ea_categories' );
	wp_cache_set( 'last_changed', microtime(), 'ea_categories' );

	/**
	 * Fires after a category is deleted.
	 *
	 * @param int $category_id contact id.
	 * @param Category $category contact object.
	 *
	 * @since 1.2.1
	 *
	 * @see eaccounting_delete_category()
	 */
	do_action( 'eaccounting_delete_category', $category_id, $category );

	return $category;
}

/**
 * Sanitizes every category field.
 *
 * If the context is 'raw', then the category object or array will get minimal
 * sanitization of the integer fields.
 *
 * @param object|array $category The category object or array
 * @param string $context Optional. How to sanitize post fields. Accepts 'raw', 'edit', 'db', 'display'. Default 'display'.
 *
 * @return object|Category|array The now sanitized category object or array
 * @see eaccounting_sanitize_category_field()
 *
 * @since 1.2.1
 */
function eaccounting_sanitize_category( $category, $context = 'raw' ) {
	if ( is_object( $category ) ) {
		// Check if post already filtered for this context.
		if ( isset( $category->filter ) && $context == $category->filter ) {
			return $category;
		}
		if ( ! isset( $category->id ) ) {
			$category->id = 0;
		}
		foreach ( array_keys( get_object_vars( $category ) ) as $field ) {
			$category->$field = eaccounting_sanitize_category_field( $field, $category->$field, $category->id, $context );
		}
		$category->filter = $context;
	} elseif ( is_array( $category ) ) {
		// Check if post already filtered for this context.
		if ( isset( $category['filter'] ) && $context == $category['filter'] ) { //phpcs:ignore
			return $category;
		}
		if ( ! isset( $category['id'] ) ) {
			$category['id'] = 0;
		}
		foreach ( array_keys( $category ) as $field ) {
			$category[ $field ] = eaccounting_sanitize_category_field( $field, $category[ $field ], $category['id'], $context );
		}
		$category['filter'] = $context;
	}

	return $category;
}

/**
 * Sanitizes a category field based on context.
 *
 * Possible context values are:  'raw', 'edit', 'db', 'display'.
 *
 * @param string $field The category Object field name.
 * @param mixed $value The category Object value.
 * @param int $category_id Category id.
 * @param string $context Optional. How to sanitize the field. Possible values are 'raw', 'edit','db', 'display'. Default 'display'.
 *
 * @return mixed Sanitized value.
 * @since 1.2.1
 */
function eaccounting_sanitize_category_field( $field, $value, $category_id, $context ) {
	if ( false !== strpos( $field, '_id' ) || $field === 'id' ) { //phpcs:ignore
		$value = absint( $value );
	}

	$context = strtolower( $context );

	if ( 'raw' === $context ) {
		return $value;
	}

	if ( 'edit' === $context ) {

		/**
		 * Filters an category field to edit before it is sanitized.
		 *
		 * @param mixed $value Value of the category field.
		 * @param int $category_id Category id.
		 *
		 * @since 1.2.1
		 */
		$value = apply_filters( "eaccounting_edit_category_{$field}", $value, $category_id );

	} elseif ( 'db' === $context ) {

		/**
		 * Filters a category field value before it is sanitized.
		 *
		 * @param mixed $value Value of the category field.
		 * @param int $category_id Category id.
		 *
		 * @since 1.2.1
		 */
		$value = apply_filters( "eaccounting_pre_category_{$field}", $value, $category_id );

	} else {
		// Use display filters by default.

		/**
		 * Filters the category field sanitized for display.
		 *
		 * The dynamic portion of the filter name, `$field`, refers to the category field name.
		 *
		 * @param mixed $value Value of the category field.
		 * @param int $category_id category id.
		 * @param string $context Context to retrieve the category field value.
		 *
		 * @since 1.2.1
		 */
		$value = apply_filters( "eaccounting_category_{$field}", $value, $category_id, $context );
	}

	return $value;
}


/**
 * Get category items.
 *
 * @param array $args
 *
 * @return int|array|null
 * @since 1.1.0
 */
function eaccounting_get_categories( $args = array() ) {
	global $wpdb;
	// Prepare args.
	$args = wp_parse_args(
		$args,
		array(
			'status'      => 'all',
			'type'        => '',
			'include'     => '',
			'search'      => '',
			'fields'      => '*',
			'orderby'     => 'id',
			'order'       => 'ASC',
			'number'      => 20,
			'offset'      => 0,
			'paged'       => 1,
			'return'      => 'objects',
			'count_total' => false,
		)
	);

	$qv           = apply_filters( 'eaccounting_get_categories_args', $args );
	$table        = $wpdb->prefix . 'ea_categories';
	$columns      = [ 'name', 'type', 'color', 'enabled', 'date_created'];
	$qv['fields'] = wp_parse_list( $qv['fields'] );
	$qv['fields'] = wp_parse_list( $qv['fields'] );
	foreach ( $qv['fields'] as $index => $field ) {
		if ( ! in_array( $field, $columns, true ) ) {
			unset( $qv['fields'][ $index ] );
		}
	}
	$fields = is_array( $qv['fields'] ) && ! empty( $qv['fields'] ) ? implode( ',', $qv['fields'] ) : '*';
	$where  = 'WHERE 1=1';

	if ( ! empty( $qv['include'] ) ) {
		$include = implode( ',', wp_parse_id_list( $qv['include'] ) );
		$where   .= " AND $table.`id` IN ($include)";
	} elseif ( ! empty( $qv['exclude'] ) ) {
		$exclude = implode( ',', wp_parse_id_list( $qv['exclude'] ) );
		$where   .= " AND $table.`id` NOT IN ($exclude)";
	}

	if ( ! empty( $qv['type'] ) ) {
		$types = implode( "','", wp_parse_list( $qv['type'] ) );
		$where .= " AND $table.`type` IN ('$types')";
	}

	if ( ! empty( $qv['status'] ) && ! in_array( $qv['status'], array( 'all', 'any' ), true ) ) {
		$status = eaccounting_string_to_bool( $qv['status'] );
		$status = eaccounting_bool_to_number( $status );
		$where  .= " AND $table.`enabled` = ('$status')";
	}

	if ( ! empty( $qv['date_created'] ) && is_array( $qv['date_created'] ) ) {
		$date_created_query = new \WP_Date_Query( $qv['date_created'], "{$table}.date_created" );
		$where              .= $date_created_query->get_sql();
	}

	$search_cols = array( 'name', 'type' );
	if ( ! empty( $qv['search'] ) ) {
		$searches = array();
		$where    .= ' AND (';
		foreach ( $search_cols as $col ) {
			$searches[] = $wpdb->prepare( $col . ' LIKE %s', '%' . $wpdb->esc_like( $qv['search'] ) . '%' );
		}
		$where .= implode( ' OR ', $searches );
		$where .= ')';
	}

	$order   = isset( $qv['order'] ) ? strtoupper( $qv['order'] ) : 'ASC';
	$orderby = isset( $qv['orderby'] ) && in_array( $qv['orderby'], $columns, true ) ? eaccounting_clean( $qv['orderby'] ) : "{$table}.id";

	$limit = '';
	if ( isset( $qv['number'] ) && $qv['number'] > 0 ) {
		if ( $qv['offset'] ) {
			$limit = $wpdb->prepare( 'LIMIT %d, %d', $qv['offset'], $qv['number'] );
		} else {
			$limit = $wpdb->prepare( 'LIMIT %d, %d', $qv['number'] * ( $qv['paged'] - 1 ), $qv['number'] );
		}
	}

	$select      = "SELECT {$fields}";
	$from        = "FROM {$wpdb->prefix}$table $table";
	$orderby     = "ORDER BY {$orderby} {$order}";
	$count_total = true === $qv['count_total'];
	$clauses     = compact( 'select', 'from', 'where', 'orderby', 'limit' );
	$cache_key   = 'query:' . md5( serialize( $qv ) ) . ':' . wp_cache_get_last_changed( 'ea_categories' );
	$results     = wp_cache_get( $cache_key, 'ea_categories' );
	if ( false === $results ) {
		if ( $count_total ) {
			$results = (int) $wpdb->get_var( "SELECT COUNT(id) $from $where" );
			wp_cache_set( $cache_key, $results, 'ea_categories' );
		} else {
			$results = $wpdb->get_results( implode( ' ', $clauses ) );
			if ( in_array( $fields, array( 'all', '*' ), true ) ) {
				foreach ( $results as $key => $item ) {
					wp_cache_set( $item->id, $item, 'ea_categories' );
					wp_cache_set( $item->name . '-' . $item->type, $item, 'ea_categories' );
				}
			}
			wp_cache_set( $cache_key, $results, 'ea_categories' );
		}
	}

	if ( 'objects' === $qv['return'] && true !== $qv['count_total'] ) {
		$results = array_map( 'eaccounting_get_category', $results );
	}

	return $results;
}
