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
add_filter( 'eaccounting_pre_item_enabled', 'eaccounting_bool_to_number' );
add_filter( 'eaccounting_pre_item_quantity', 'intval' );
add_filter( 'eaccounting_pre_item_sale_price', 'floatval' );
add_filter( 'eaccounting_pre_item_purchase_price', 'floatval' );
add_filter( 'eaccounting_pre_item_sales_tax', 'floatval' );
add_filter( 'eaccounting_pre_item_purchase_tax', 'floatval' );
add_filter( 'eaccounting_pre_item_sku', 'sanitize_key' );
add_filter( 'eaccounting_pre_item_title', 'sanitize_title' );
add_filter( 'eaccounting_pre_item_description', 'sanitize_textarea_field' );
add_filter( 'eaccounting_edit_item_enabled', 'eaccounting_string_to_bool' );

/**
 * Retrieves item data given a item id or item object.
 *
 * @param int|object|Item $item item to retrieve
 * @param string $output The required return type. One of OBJECT, ARRAY_A, or ARRAY_N. Default OBJECT.
 * @param string $filter Type of filter to apply. Accepts 'raw', 'edit', 'db', or 'display'. Default 'raw'.
 *
 * @return Item|array|null
 * @since 1.1.0
 */
function eaccounting_get_item( $item, $output = OBJECT, $filter = 'raw' ) {
	if ( empty( $item ) ) {
		return null;
	}

	if ( $item instanceof Item ) {
		$_item = $item;
	} elseif ( is_object( $item ) ) {
		$_item = new Item( $item );
	} else {
		$_item = Item::get_instance( $item );
	}

	if ( ! $_item ) {
		return null;
	}

	$_item = $_item->filter( $filter );

	if ( ARRAY_A === $output ) {
		return $_item->to_array();
	}

	if ( ARRAY_N === $output ) {
		return array_values( $_item->to_array() );
	}

	return $_item->filter( $filter );
}
/**
 *  Insert or update an item.
 *
 * @param array|object|Item $item_arr An array, object, or item object of data arguments.
 *
 * @return Item|WP_Error The item object or WP_Error otherwise.
 * @global wpdb $wpdb WordPress database abstraction object.
 * @since 1.1.0
 */
function eaccounting_insert_item( $item_arr ) {
	global $wpdb;
	$user_id = get_current_user_id();
	if ( $item_arr instanceof Item ) {
		$item_arr = $item_arr->to_array();
	} elseif ( $item_arr instanceof stdClass ) {
		$item_arr = get_object_vars( $item_arr );
	}

	$defaults = array(
		'name'           => '',
		'sku'            => '',
		'thumbnail_id'   => null,
		'description'    => '',
		'sale_price'     => 0.0000,
		'purchase_price' => 0.0000,
		'quantity'       => 1,
		'category_id'    => null,
		'sales_tax'      => '',
		'purchase_tax'   => '',
		'enabled'        => true,
		'creator_id'     => $user_id,
		'date_created'   => null,
	);

	// Are we updating or creating?
	$id          = null;
	$update      = false;
	$data_before = array();
	if ( ! empty( $item_data['id'] ) ) {
		$update      = true;
		$id          = absint( $item_data['id'] );
		$data_before = eaccounting_get_item( $id, ARRAY_A );

		if ( is_null( $data_before ) ) {
			return new WP_Error( 'invalid_item_id', __( 'Invalid item id to update.', 'wp-ever-accounting' ) );
		}

		// Merge old and new fields with new fields overwriting old ones.
		$item_arr    = array_merge( $data_before, $item_arr );
		$data_before = $data_before->to_array();
	}

	$item_data = wp_parse_args( $item_arr, $defaults );
	$data_arr  = eaccounting_sanitize_item( $item_arr, 'db' );

	// Check required
	if ( empty( $data_arr['name'] ) ) {
		return new WP_Error( 'invalid_item_name', esc_html__( 'Item name is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['quantity'] ) ) {
		return new WP_Error( 'invalid_item_quantity', esc_html__( 'Item quantity id is required', 'wp-ever-accounting' ) );
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

	$fields = array_keys( $defaults );
	$data   = wp_array_slice_assoc( $data_arr, $fields );

	/**
	 * Filters item data before it is inserted into the database.
	 *
	 * @param array $data Data to be inserted.
	 * @param array $data_arr Sanitized data.
	 *
	 * @since 1.2.1
	 */
	$data = apply_filters( 'eaccounting_insert_item', $data, $data_arr );

	$data  = wp_unslash( $data );
	$where = array( 'id' => $id );

	if ( $update ) {

		/**
		 * Fires immediately before an existing item is updated in the database.
		 *
		 * @param int $id Item id.
		 * @param array $data Item data to be inserted.
		 * @param array $changes Item data to be updated.
		 * @param array $data_arr Sanitized item item data.
		 * @param array $data_before Item previous data.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_update_item', $id, $data, $data_arr, $data_before );
		if ( false === $wpdb->update( $wpdb->prefix . 'ea_items', $data, $where, $data_before ) ) {
			new WP_Error( 'db_update_error', __( 'Could not update item in the database.','wp-ever-accounting' ), $wpdb->last_error );
		}

		/**
		 * Fires immediately after an existing item is updated in the database.
		 *
		 * @param int $id Item id.
		 * @param array $data Item data to be inserted.
		 * @param array $changes Item data to be updated.
		 * @param array $data_arr Sanitized Item data.
		 * @param array $data_before Item previous data.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_update_item', $id, $data, $data_arr, $data_before );
	} else {

		/**
		 * Fires immediately before an existing item is inserted in the database.
		 *
		 * @param array $data Item data to be inserted.
		 * @param string $data_arr Sanitized item item data.
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
	wp_cache_delete( $id, 'ea_items' );
	wp_cache_set( 'last_changed', microtime(), 'ea_items' );

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
	do_action( 'eaccounting_saved_item', $id, $item, $update, $data_arr, $data_before );

	return $item;
}

/**
 * Delete an item.
 *
 * @param int $item_id Item ID
 *
 * @return Item |false|null Item data on success, false or null on failure.
 * @since 1.1.0
 */
function eaccounting_delete_item( $item_id ) {
	global $wpdb;

	$item = eaccounting_get_item( $item_id );
	if ( ! $item || ! $item->exists() ) {
		return false;
	}

	/**
	 * Filters whether an item delete should take place.
	 *
	 * @param bool|null $delete Whether to go forward with deletion.
	 * @param Item $item item object.
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

	wp_cache_delete( $item_id, 'ea_items' );
	wp_cache_set( 'last_changed', microtime(), 'ea_items' );

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
 * @param string $context Optional. How to sanitize post fields. Accepts 'raw', 'edit', 'db', 'display'. Default 'display'.
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
 * @param mixed $value The item Object value.
 * @param int $item_id Item id.
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
 * Retrieves an array of the items matching the given criteria.
 *
 * @param array $args Arguments to retrieve items.
 *
 * @return Item[]|int Array of item objects or count.
 * @since 1.1.0
 *
 */
function eaccounting_get_items( $args = array() ) {
	$defaults = array(
		'number'        => 20,
		'orderby'       => 'name',
		'order'         => 'DESC',
		'include'       => array(),
		'exclude'       => array(),
		'no_found_rows' => false,
		'count_total'   => false,
	);

	$parsed_args = wp_parse_args( $args, $defaults );
	$query       = new \EverAccounting\Item_Query( $parsed_args );
	if ( true === $parsed_args['count_total'] ) {
		return $query->get_total();
	}


	return $query->get_results();
}
