<?php
/**
 * EverAccounting Item Functions.
 *
 * All item related function of the plugin.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

use EverAccounting\Item;

defined( 'ABSPATH' ) || exit;


// Sanitization and escaping filters
add_filter( 'eaccounting_pre_item_enabled', 'eaccounting_bool_to_number', 10, 1 );
add_filter( 'eaccounting_edit_item_enabled', 'eaccounting_string_to_bool', 10, 1 );

/**
 * Main function for returning item.
 *
 * @param int|array|object|Item $item item to retrieve
 * @param string                $filter Optional. Type of filter to apply. Accepts 'raw', 'edit', 'db', or 'display'. Default 'raw'.
 *
 * @return array|Item|null
 * @since 1.1.0
 */
function eaccounting_get_item( $item, $filter = 'raw' ) {
	if ( empty( $item ) ) {
		return null;
	}

	$item = new Item( $item );
	if ( ! $item->exists() ) {
		return null;
	}

	return $item->filter( $filter );
}


/**
 * Get item by sku.
 *
 * @param string $sku Item sku
 *
 * @return array|Item|null
 * @since 1.1.0
 */
function eaccounting_get_item_by_sku( $sku ) {
	global $wpdb;
	$sku = eaccounting_clean( $sku );
	if ( empty( $sku ) ) {
		return null;
	}
	$cache_key = "item-sku-$sku";
	$item      = wp_cache_get( $cache_key, 'ea_items' );
	if ( false === $item ) {
		$item = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_items where `sku`=%s", eaccounting_clean( $sku ) ) );
		wp_cache_set( $cache_key, $item, 'ea_items' );
	}
	if ( $item ) {
		wp_cache_set( $item->id, $item, 'ea_items' );

		return eaccounting_get_item( $item, 'raw' );
	}

	return null;
}

/**
 *  Create new item programmatically.
 *
 * @param array|object|Item $item_data An array, object, or Item object of data arguments.
 *
 * @return Item|WP_Error The Item object or WP_Error otherwise.
 * @global wpdb $wpdb WordPress database abstraction object.
 * @since 1.1.0
 */
function eaccounting_insert_item( $item_data ) {
	global $wpdb;
	$user_id = get_current_user_id();
	if ( $item_data instanceof Item ) {
		$item_data = $item_data->to_array();
	} elseif ( $item_data instanceof stdClass ) {
		$item_data = get_object_vars( $item_data );
	}

	$defaults = array(
		'name'           => '',
		'sku'            => '',
		'thumbnail_id'   => '',
		'description'    => '',
		'sale_price'     => 0.0000,
		'purchase_price' => 0.0000,
		'quantity'       => 1,
		'category_id'    => '',
		'sales_tax'      => '',
		'purchase_tax'   => '',
		'enabled'        => true,
		'creator_id'     => $user_id,
		'date_created'   => '',
	);

	// Are we updating or creating?
	$id      = null;
	$update  = false;
	$changes = $item_data;
	if ( ! empty( $item_data['id'] ) ) {
		$update = true;
		$id     = absint( $item_data['id'] );
		$before = eaccounting_get_item( $id );

		if ( is_null( $before ) ) {
			return new WP_Error( 'invalid_item_id', __( 'Invalid item id to update.' ) );
		}
		// Store changes value.
		$changes = array_diff_assoc( $item_data, $before->to_array() );

		// Merge old and new fields with new fields overwriting old ones.
		$item_data = array_merge( $before->to_array(), $item_data );
	}

	$data_arr = wp_parse_args( $item_data, $defaults );
	$data_arr = eaccounting_sanitize_item( $data_arr, 'db' );

	if ( empty( $data_arr['name'] ) ) {
		return new WP_Error( 'invalid_item_name', esc_html__( 'Item name is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['quantity'] ) ) {
		return new WP_Error( 'invalid_item_quantity', esc_html__( 'Item quantity is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['purchase_price'] ) ) {
		return new WP_Error( 'invalid_item_purchase_price', esc_html__( 'Item purchase price is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['sale_price'] ) ) {
		return new WP_Error( 'invalid_item_sale_price', esc_html__( 'Item sale price is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['date_created'] ) || '0000-00-00 00:00:00' === $data_arr['date_created'] ) {
		$data_arr['date_created'] = current_time( 'mysql' );
	}

	// Compute fields.
	$name           = $data_arr['name'];
	$sku            = $data_arr['sku'];
	$description    = $data_arr['description'];
	$sale_price     = $data_arr['sale_price'];
	$purchase_price = $data_arr['purchase_price'];
	$quantity       = $data_arr['quantity'];
	$category_id    = $data_arr['category_id'];
	$sales_tax      = $data_arr['sales_tax'];
	$purchase_tax   = $data_arr['purchase_tax'];
	$thumbnail_id   = (int) $data_arr['thumbnail_id'];
	$enabled        = (int) $data_arr['enabled'];
	$creator_id     = (int) $data_arr['creator_id'];
	$date_created   = $data_arr['date_created'];
	$data           = compact( 'name', 'sku', 'description', 'sale_price', 'purchase_price', 'quantity', 'category_id', 'sales_tax', 'purchase_tax', 'thumbnail_id', 'enabled', 'creator_id', 'date_created' );

	/**
	 * Filters item data before it is inserted into the database.
	 *
	 * @param array $data Item data to be inserted.
	 * @param array $data_arr Sanitized account data.
	 * @param array $item_data Item data as originally passed to the function.
	 *
	 * @since 1.2.1
	 */
	$data = apply_filters( 'eaccounting_insert_item_data', $data, $data_arr, $item_data );

	$data  = wp_unslash( $data );
	$where = array( 'id' => $id );

	if ( $update ) {

		/**
		 * Fires immediately before an existing item is updated in the database.
		 *
		 * @param int $id Item id.
		 * @param array $data Item data to be inserted.
		 * @param array $changes Item data to be updated.
		 * @param array $data_arr Sanitized item data.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_update_item', $id, $data, $changes, $data_arr );
		if ( false === $wpdb->update( $wpdb->prefix . 'ea_items', $data, $where ) ) {
			new WP_Error( 'db_update_error', __( 'Could not update item in the database.' ), $wpdb->last_error );
		}

		/**
		 * Fires immediately after an existing item is updated in the database.
		 *
		 * @param int $id Item id.
		 * @param array $data Item data to be inserted.
		 * @param array $changes Item data to be updated.
		 * @param array $data_arr Sanitized item data.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_update_item', $id, $data, $changes, $data_arr );
	} else {

		/**
		 * Fires immediately before an existing item is inserted in the database.
		 *
		 * @param array $data Item data to be inserted.
		 * @param string $data_arr Sanitized item data.
		 * @param array $item_data Item data as originally passed to the function.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_insert_item', $data, $data_arr, $item_data );

		if ( false === $wpdb->insert( $wpdb->prefix . 'ea_items', $data ) ) {
			new WP_Error( 'db_insert_error', __( 'Could not insert item into the database.' ), $wpdb->last_error );
		}

		$id = (int) $wpdb->insert_id;

		/**
		 * Fires immediately after an existing item is inserted in the database.
		 *
		 * @param int $id Item id.
		 * @param array $data Item has been inserted.
		 * @param array $data_arr Sanitized item data.
		 * @param array $item_data Item data as originally passed to the function.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_insert_item', $id, $data, $data_arr, $item_data );
	}

	// Clear cache.
	eaccounting_delete_cache( 'ea_items', $id );

	// Get new item object.
	$item = eaccounting_get_item( $id );

	/**
	 * Fires once an item has been saved.
	 *
	 * @param int $id Item id.
	 * @param Item $item Item object.
	 * @param bool $update Whether this is an existing item being updated.
	 *
	 * @since 1.2.1
	 */
	do_action( 'eaccounting_saved_item', $id, $item, $update );

	return $item;
}

/**
 * Delete an item.
 *
 * @param int $item_id Item ID
 *
 * @return @return Item |false|null Note data on success, false or null on failure.
 * @since 1.1.0
 */
function eaccounting_delete_item( $item_id ) {
	global $wpdb;

	$item = eaccounting_get_item( $item_id );
	if ( ! $item->exists() ) {
		return false;
	}

	/**
	 * Filters whether an item delete should take place.
	 *
	 * @param bool|null $delete Whether to go forward with deletion.
	 * @param Item $item account object.
	 *
	 * @since 1.2.1
	 */
	$check = apply_filters( 'eaccounting_pre_delete_item', null, $item );
	if ( null !== $check ) {
		return $check;
	}

	/**
	 * Fires before an item is deleted.
	 *
	 * @param int $item_id Item id.
	 * @param Item $item Item object.
	 *
	 * @since 1.2.1
	 *
	 * @see eaccounting_delete_item()
	 */
	do_action( 'eaccounting_before_delete_item', $item_id, $item );

	$result = $wpdb->delete( $wpdb->prefix . 'ea_items', array( 'id' => $item_id ) );
	if ( ! $result ) {
		return false;
	}

	eaccounting_delete_cache( 'ea_items', $item_id );

	/**
	 * Fires after an item is deleted.
	 *
	 * @param int $item_id item id.
	 * @param Item $item item object.
	 *
	 * @since 1.2.1
	 *
	 * @see eaccounting_delete_item()
	 */
	do_action( 'eaccounting_delete_item', $item_id, $item );

	return $item;
}

/**
 * Sanitizes every item field.
 *
 * If the context is 'raw', then the item object or array will get minimal
 * sanitization of the integer fields.
 *
 * @param object|array $item The item object or array
 * @param string       $context Optional. How to sanitize post fields. Accepts 'raw', 'edit', 'db', 'display'. Default 'display'.
 *
 * @return object|Item|array The now sanitized item object or array
 * @see eaccounting_sanitize_item_field()
 *
 * @since 1.2.1
 */
function eaccounting_sanitize_item( $item, $context = 'raw' ) {
	if ( is_object( $item ) ) {
		// Check if post already filtered for this context.
		if ( isset( $item->filter ) && $context == $item->filter ) {
			return $item;
		}
		if ( ! isset( $item->id ) ) {
			$item->id = 0;
		}
		foreach ( array_keys( get_object_vars( $item ) ) as $field ) {
			$item->$field = eaccounting_sanitize_item_field( $field, $item->$field, $item->id, $context );
		}
		$item->filter = $context;
	} elseif ( is_array( $item ) ) {
		// Check if post already filtered for this context.
		if ( isset( $item['filter'] ) && $context == $item['filter'] ) { //phpcs:ignore
			return $item;
		}
		if ( ! isset( $item['id'] ) ) {
			$item['id'] = 0;
		}
		foreach ( array_keys( $item ) as $field ) {
			$item[ $field ] = eaccounting_sanitize_item_field( $field, $item[ $field ], $item['id'], $context );
		}
		$item['filter'] = $context;
	}

	return $item;
}

/**
 * Sanitizes a item field based on context.
 *
 * Possible context values are:  'raw', 'edit', 'db', 'display'.
 *
 * @param string $field The item Object field name.
 * @param mixed  $value The item Object value.
 * @param int    $item_id Item id.
 * @param string $context Optional. How to sanitize the field. Possible values are 'raw', 'edit','db', 'display'. Default 'display'.
 *
 * @return mixed Sanitized value.
 * @since 1.2.1
 */
function eaccounting_sanitize_item_field( $field, $value, $item_id, $context ) {
	if ( false !== strpos( $field, '_id' ) || $field === 'id' ) { //phpcs:ignore
		$value = absint( $value );
	}

	$context = strtolower( $context );

	if ( 'raw' === $context ) {
		return $value;
	}

	if ( 'edit' === $context ) {

		/**
		 * Filters an item field to edit before it is sanitized.
		 *
		 * @param mixed $value Value of the item field.
		 * @param int $item_id Item id.
		 *
		 * @since 1.2.1
		 */
		$value = apply_filters( "eaccounting_edit_item_{$field}", $value, $item_id );

	} elseif ( 'db' === $context ) {

		/**
		 * Filters a item field value before it is sanitized.
		 *
		 * @param mixed $value Value of the item field.
		 * @param int $item_id Item id.
		 *
		 * @since 1.2.1
		 */
		$value = apply_filters( "eaccounting_pre_item_{$field}", $value, $item_id );

	} else {
		// Use display filters by default.

		/**
		 * Filters the item field sanitized for display.
		 *
		 * The dynamic portion of the filter name, `$field`, refers to the item field name.
		 *
		 * @param mixed $value Value of the item field.
		 * @param int $item_id item id.
		 * @param string $context Context to retrieve the item field value.
		 *
		 * @since 1.2.1
		 */
		$value = apply_filters( "eaccounting_item_{$field}", $value, $item_id, $context );
	}

	return $value;
}

/**
 * Get items.
 *
 * @param array $args {
 *
 * @type string $name The name of the item.
 * @type string $sku The sku of the item.
 * @type int $image_id The image_id for the item.
 * @type string $description The description of the item.
 * @type double $sale_price The sale_price of the item.
 * @type double $purchase_price The purchase_price for the item.
 * @type int $quantity The quantity of the item.
 * @type int $category_id The category_id of the item.
 * @type int $tax_id The tax_id of the item.
 * @type int $enabled The enabled of the item.
 * }
 *
 * @return array|int
 * @since 1.1.0
 */
function eaccounting_get_items( $args = array() ) {
	// Prepare args.
	$args = wp_parse_args(
		$args,
		array(
			'status'      => 'all',
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
	global $wpdb;
	$qv           = apply_filters( 'eaccounting_get_items_args', $args );
	$table        = \EverAccounting\Repositories\Items::TABLE;
	$columns      = \EverAccounting\Repositories\Items::get_columns();
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
		$where  .= " AND $table.`id` IN ($include)";
	} elseif ( ! empty( $qv['exclude'] ) ) {
		$exclude = implode( ',', wp_parse_id_list( $qv['exclude'] ) );
		$where  .= " AND $table.`id` NOT IN ($exclude)";
	}

	// search
	$search_cols = array( 'name', 'sku', 'description' );
	if ( ! empty( $qv['search'] ) ) {
		$searches = array();
		$where   .= ' AND (';
		foreach ( $search_cols as $col ) {
			$searches[] = $wpdb->prepare( $col . ' LIKE %s', '%' . $wpdb->esc_like( $qv['search'] ) . '%' );
		}
		$where .= implode( ' OR ', $searches );
		$where .= ')';
	}

	if ( ! empty( $qv['status'] ) && ! in_array( $qv['status'], array( 'all', 'any' ), true ) ) {
		$status = eaccounting_string_to_bool( $qv['status'] );
		$status = eaccounting_bool_to_number( $status );
		$where .= " AND $table.`enabled` = ('$status')";
	}

	if ( ! empty( $qv['date_created'] ) && is_array( $qv['date_created'] ) ) {
		$date_created_query = new \WP_Date_Query( $qv['date_created'], "{$table}.date_created" );
		$where             .= $date_created_query->get_sql();
	}

	if ( ! empty( $qv['creator_id'] ) ) {
		$creator_id = implode( ',', wp_parse_id_list( $qv['creator_id'] ) );
		$where     .= " AND $table.`creator_id` IN ($creator_id)";
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
	$cache_key   = 'query:' . md5( serialize( $qv ) ) . ':' . wp_cache_get_last_changed( 'ea_items' );
	$results     = wp_cache_get( $cache_key, 'ea_items' );
	$clauses     = compact( 'select', 'from', 'where', 'orderby', 'limit' );
	if ( false === $results ) {
		if ( $count_total ) {
			$results = (int) $wpdb->get_var( "SELECT COUNT(id) $from $where" );
			wp_cache_set( $cache_key, $results, 'ea_items' );
		} else {
			$results = $wpdb->get_results( implode( ' ', $clauses ) );
			if ( in_array( $fields, array( 'all', '*' ), true ) ) {
				foreach ( $results as $key => $item ) {
					if ( ! empty( $item->sku ) ) {
						wp_cache_set( 'item-sku-' . $item->sku, $item, 'ea_items' );
					}
					wp_cache_set( $item->id, $item, 'ea_items' );
				}
			}
			wp_cache_set( $cache_key, $results, 'ea_items' );
		}
	}

	if ( 'objects' === $qv['return'] && true !== $qv['count_total'] ) {
		$results = array_map( 'eaccounting_get_item', $results );
	}

	return $results;
}
