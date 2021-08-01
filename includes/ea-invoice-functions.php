<?php
/**
 * EverAccounting invoice Functions.
 *
 * All invoice related function of the plugin.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

use EverAccounting\Invoice;
use EverAccounting\Invoice_Item;

defined( 'ABSPATH' ) || exit;

// Sanitization and escaping filters
add_filter( 'eaccounting_pre_invoice_item_item_name', 'sanitize_text_field' );
add_filter( 'eaccounting_pre_invoice_item_quantity', 'floatval' );
add_filter( 'eaccounting_pre_invoice_item_price', 'floatval' );
add_filter( 'eaccounting_pre_invoice_item_subtotal', 'floatval' );
add_filter( 'eaccounting_pre_invoice_item_tax_rate', 'floatval' );
add_filter( 'eaccounting_pre_invoice_item_discount', 'floatval' );
add_filter( 'eaccounting_pre_invoice_item_tax', 'floatval' );
add_filter( 'eaccounting_pre_invoice_item_total', 'floatval' );
add_filter( 'eaccounting_pre_invoice_item_currency_code', 'eaccounting_sanitize_currency_code' );
add_filter( 'eaccounting_pre_invoice_item_extra', 'maybe_serialize' );

/**
 * Retrieves invoice data given a invoice id or invoice object.
 *
 * @param int|object|Invoice $invoice invoice to retrieve
 * @param string             $output The required return type. One of OBJECT, ARRAY_A, or ARRAY_N.Default OBJECT.
 * @param string             $filter Type of filter to apply. Accepts 'raw', 'edit', 'db', or 'display'. Default 'raw'.
 *
 * @return Invoice|array|null
 * @since 1.1.0
 */
function eaccounting_get_invoice( $invoice, $output = OBJECT, $filter = 'raw' ) {
	if ( empty( $invoice ) ) {
		return null;
	}

	if ( $invoice instanceof Invoice ) {
		$_invoice = $invoice;
	} elseif ( is_object( $invoice ) ) {
		$_invoice = new Invoice( $invoice );
	} else {
		$_invoice = Invoice::get_instance( $invoice );
	}

	if ( ! $_invoice ) {
		return null;
	}

	$_invoice = $_invoice->filter( $filter );

	if ( ARRAY_A === $output ) {
		return $_invoice->to_array();
	}

	if ( ARRAY_N === $output ) {
		return array_values( $_invoice->to_array() );
	}

	return $_invoice->filter( $filter );
}

/**
 *  Insert or update an invoice.
 *
 * @param array|object|Invoice $invoice_arr An array, object, or invoice object of data arguments.
 *
 * @return Invoice|WP_Error The invoice object or WP_Error otherwise.
 * @global wpdb $wpdb WordPress database abstraction object.
 * @since 1.1.0
 */
function eaccounting_insert_invoice( $invoice_arr ) {
	global $wpdb;
	$user_id = get_current_user_id();
	if ( $invoice_arr instanceof Invoice ) {
		$invoice_arr = $invoice_arr->to_array();
	} elseif ( $invoice_arr instanceof stdClass ) {
		$invoice_arr = get_object_vars( $invoice_arr );
	}

	$defaults = array(
		'document_number' => '',
		'type'            => '',
		'order_number'    => '',
		'status'          => 'draft',
		'issue_date'      => null,
		'due_date'        => null,
		'payment_date'    => null,
		'category_id'     => null,
		'contact_id'      => null,
		'address'         => array(
			'name'       => '',
			'company'    => '',
			'street'     => '',
			'city'       => '',
			'state'      => '',
			'postcode'   => '',
			'country'    => '',
			'email'      => '',
			'phone'      => '',
			'vat_number' => '',
		),
		'discount'        => 0.00,
		'discount_type'   => 'percentage',
		'subtotal'        => 0.00,
		'total_tax'       => 0.00,
		'total_discount'  => 0.00,
		'total_fees'      => 0.00,
		'total_shipping'  => 0.00,
		'total'           => 0.00,
		'tax_inclusive'   => 1,
		'note'            => '',
		'terms'           => '',
		'attachment_id'   => null,
		'currency_code'   => null,
		'currency_rate'   => 1,
		'key'             => null,
		'parent_id'       => null,
		'creator_id'      => $user_id,
		'date_created'    => null,
	);

	// Are we updating or creating?
	$id          = null;
	$update      = false;
	$data_before = array();
	if ( ! empty( $item_data['id'] ) ) {
		$update      = true;
		$id          = absint( $item_data['id'] );
		$data_before = eaccounting_get_invoice( $id, ARRAY_A );

		if ( is_null( $data_before ) ) {
			return new WP_Error( 'invalid_invoice_id', __( 'Invalid invoice id to update.', 'wp-ever-accounting' ) );
		}

		// Merge old and new fields with new fields overwriting old ones.
		$invoice_arr = array_merge( $data_before, $invoice_arr );
		$data_before = $data_before->to_array();
	}

	$item_data = wp_parse_args( $invoice_arr, $defaults );
	$data_arr  = eaccounting_sanitize_invoice( $invoice_arr, 'db' );

	// Check required
	if ( empty( $data_arr['currency_code'] ) ) {
		return new WP_Error( 'invalid_invoice_currency_code', esc_html__( 'Invoice currency code is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['category_id'] ) ) {
		return new WP_Error( 'invalid_invoice_category_id', esc_html__( 'Invoice category id is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['contact_id'] ) ) {
		return new WP_Error( 'invalid_invoice_contact_id', esc_html__( 'Invoice contact id is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['issue_date'] ) ) {
		return new WP_Error( 'invalid_invoice_issue_date', esc_html__( 'Invoice issue date id is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['due_date'] ) ) {
		return new WP_Error( 'invalid_invoice_due_date', esc_html__( 'Invoice due date id is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['date_created'] ) || '0000-00-00 00:00:00' === $data_arr['date_created'] ) {
		$data_arr['date_created'] = current_time( 'mysql' );
	}

	$fields = array_keys( $defaults );
	$data   = wp_array_slice_assoc( $data_arr, $fields );

	/**
	 * Filters invoice data before it is inserted into the database.
	 *
	 * @param array $data Data to be inserted.
	 * @param array $data_arr Sanitized data.
	 *
	 * @since 1.2.1
	 */
	$data = apply_filters( 'eaccounting_insert_invoice', $data, $data_arr );

	$data  = wp_unslash( $data );
	$where = array( 'id' => $id );

	if ( $update ) {

		/**
		 * Fires immediately before an existing invoice item is updated in the database.
		 *
		 * @param int $id Invoice id.
		 * @param array $data Invoice data to be inserted.
		 * @param array $changes Invoice data to be updated.
		 * @param array $data_arr Sanitized invoice data.
		 * @param array $data_before Invoice previous data.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_update_invoice', $id, $data, $data_arr, $data_before );
		if ( false === $wpdb->update( $wpdb->prefix . 'ea_invoices', $data, $where, $data_before ) ) {
			new WP_Error( 'db_update_error', __( 'Could not update invoice in the database.', 'wp-ever-accounting' ), $wpdb->last_error );
		}

		/**
		 * Fires immediately after an existing invoice is updated in the database.
		 *
		 * @param int $id Invoice id.
		 * @param array $data Invoice data to be inserted.
		 * @param array $changes Invoice data to be updated.
		 * @param array $data_arr Sanitized Invoice data.
		 * @param array $data_before Invoice previous data.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_update_invoice', $id, $data, $data_arr, $data_before );
	} else {

		/**
		 * Fires immediately before an existing invoice is inserted in the database.
		 *
		 * @param array $data Invoice data to be inserted.
		 * @param string $data_arr Sanitized invoice item data.
		 * @param array $item_data Invoice data as originally passed to the function.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_insert_invoice', $data, $data_arr, $item_data );

		if ( false === $wpdb->insert( $wpdb->prefix . 'ea_invoices', $data ) ) {
			new WP_Error( 'db_insert_error', __( 'Could not insert invoice into the database.', 'wp-ever-accounting' ), $wpdb->last_error );
		}

		$id = (int) $wpdb->insert_id;

		/**
		 * Fires immediately after an existing invoice is inserted in the database.
		 *
		 * @param int $id Invoice id.
		 * @param array $data Invoice has been inserted.
		 * @param array $data_arr Sanitized invoice data.
		 * @param array $item_data Invoice data as originally passed to the function.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_insert_invoice', $id, $data, $data_arr, $item_data );
	}

	// Clear cache.
	wp_cache_delete( $id, 'ea_invoices' );
	wp_cache_set( 'last_changed', microtime(), 'ea_invoices' );

	// Get new item object.
	$invoice = eaccounting_get_invoice( $id );

	/**
	 * Fires once an invoice has been saved.
	 *
	 * @param int $id Invoice id.
	 * @param Invoice $invoice Invoice object.
	 * @param bool $update Whether this is an existing invoice being updated.
	 *
	 * @since 1.2.1
	 */
	do_action( 'eaccounting_saved_invoice', $id, $invoice, $update, $data_arr, $data_before );

	return $invoice;
}

/**
 * Sanitizes every account field.
 *
 * If the context is 'raw', then the account object or array will get minimal
 * sanitization of the integer fields.
 *
 * @param object|array $invoice The account object or array
 * @param string       $context Optional. How to sanitize post fields. Accepts 'raw', 'edit', 'db', 'display'. Default 'display'.
 *
 * @return object|Invoice|array The now sanitized account object or array
 * @see eaccounting_sanitize_invoice_field()
 *
 * @since 1.2.1
 */
function eaccounting_sanitize_invoice( $invoice, $context = 'raw' ) {
	if ( is_object( $invoice ) ) {
		// Check if post already filtered for this context.
		if ( isset( $invoice->filter ) && $context == $invoice->filter ) {
			return $invoice;
		}
		if ( ! isset( $invoice->id ) ) {
			$invoice->id = 0;
		}
		foreach ( array_keys( get_object_vars( $invoice ) ) as $field ) {
			$invoice->$field = eaccounting_sanitize_invoice_field( $field, $invoice->$field, $invoice->id, $context );
		}
		$invoice->filter = $context;
	} elseif ( is_array( $invoice ) ) {
		// Check if post already filtered for this context.
		if ( isset( $invoice['filter'] ) && $context == $invoice['filter'] ) {
			return $invoice;
		}
		if ( ! isset( $invoice['id'] ) ) {
			$invoice['id'] = 0;
		}
		foreach ( array_keys( $invoice ) as $field ) {
			$invoice[ $field ] = eaccounting_sanitize_invoice_field( $field, $invoice[ $field ], $invoice['id'], $context );
		}
		$invoice['filter'] = $context;
	}

	return $invoice;
}

/**
 * Sanitizes a account field based on context.
 *
 * Possible context values are:  'raw', 'edit', 'db', 'display'.
 *
 * @param string $field The account Object field name.
 * @param mixed  $value The account Object value.
 * @param int    $invoice_id Account id.
 * @param string $context Optional. How to sanitize the field. Possible values are 'raw', 'edit','db', 'display'. Default 'display'.
 *
 * @return mixed Sanitized value.
 * @since 1.2.1
 */
function eaccounting_sanitize_invoice_field( $field, $value, $invoice_id, $context ) {
	if ( false !== strpos( $field, '_id' ) || $field === 'id' ) {
		$value = absint( $value );
	}

	$context = strtolower( $context );

	if ( 'raw' === $context ) {
		return $value;
	}

	if ( 'edit' === $context ) {

		/**
		 * Filters an account field to edit before it is sanitized.
		 *
		 * @param mixed $value Value of the account field.
		 * @param int $invoice_id Account id.
		 *
		 * @since 1.2.1
		 */
		$value = apply_filters( "eaccounting_edit_invoice_{$field}", $value, $invoice_id );

	} elseif ( 'db' === $context ) {

		/**
		 * Filters a account field value before it is sanitized.
		 *
		 * @param mixed $value Value of the account field.
		 * @param int $invoice_id Account id.
		 *
		 * @since 1.2.1
		 */

		$value = apply_filters( "eaccounting_pre_invoice_{$field}", $value, $invoice_id );

	} else {
		// Use display filters by default.

		/**
		 * Filters the account field sanitized for display.
		 *
		 * The dynamic portion of the filter name, `$field`, refers to the account field name.
		 *
		 * @param mixed $value Value of the account field.
		 * @param int $invoice_id account id.
		 * @param string $context Context to retrieve the account field value.
		 *
		 * @since 1.2.1
		 */
		$value = apply_filters( "eaccounting_invoice_{$field}", $value, $invoice_id, $context );
	}

	return $value;
}

/**
 *  Insert or update a invoice_item.
 *
 * @param array|object|Invoice_Item $invoice_item_arr An array, object, or invoice_item object of data arguments.
 *
 * @return Invoice_Item|WP_Error The invoice_item object or WP_Error otherwise.
 * @global wpdb $wpdb WordPress database abstraction object.
 * @since 1.1.0
 */
function eaccounting_insert_invoice_item( $invoice_item_arr ) {
	global $wpdb;
	if ( $invoice_item_arr instanceof Invoice_Item ) {
		$invoice_item_arr = $invoice_item_arr->to_array();
	} elseif ( $invoice_item_arr instanceof stdClass ) {
		$invoice_item_arr = get_object_vars( $invoice_item_arr );
	}

	$defaults = array(
		'document_id'   => null,
		'item_id'       => null,
		'item_name'     => '',
		'price'         => 0.00,
		'quantity'      => 1,
		'subtotal'      => 0.00,
		'tax_rate'      => 0.00,
		'discount'      => 0.00,
		'tax'           => 0.00,
		'total'         => 0.00,
		'currency_code' => '',
		'extra'         => array(
			'shipping'     => 0.00,
			'shipping_tax' => 0.00,
			'fees'         => 0.00,
			'fees_tax'     => 0.00,
		),
		'date_created'  => null,
	);

	// Are we updating or creating?
	$id          = null;
	$update      = false;
	$data_before = array();
	if ( ! empty( $item_data['id'] ) ) {
		$update      = true;
		$id          = absint( $item_data['id'] );
		$data_before = eaccounting_get_invoice_item( $id, ARRAY_A );

		if ( is_null( $data_before ) ) {
			return new WP_Error( 'invalid_invoice_item_id', __( 'Invalid invoice item id to update.', 'wp-ever-accounting' ) );
		}

		// Merge old and new fields with new fields overwriting old ones.
		$invoice_item_arr = array_merge( $data_before, $invoice_item_arr );
		$data_before      = $data_before->to_array();
	}

	$item_data = wp_parse_args( $invoice_item_arr, $defaults );
	$data_arr  = eaccounting_sanitize_invoice_item( $invoice_item_arr, 'db' );

	// Check required
	if ( empty( $data_arr['item_id'] ) ) {
		return new WP_Error( 'invalid_invoice_item_item_id', esc_html__( 'Invoice Item item id is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['item_name'] ) ) {
		return new WP_Error( 'invalid_invoice_item_item_name', esc_html__( 'Invoice Item item name is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['document_id'] ) ) {
		return new WP_Error( 'invalid_invoice_item_document_id', esc_html__( 'Invoice Item document id is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['currency_code'] ) ) {
		return new WP_Error( 'invalid_invoice_item_currency_code', esc_html__( 'Invoice Item currency code is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['date_created'] ) || '0000-00-00 00:00:00' === $data_arr['date_created'] ) {
		$data_arr['date_created'] = current_time( 'mysql' );
	}

	$fields = array_keys( $defaults );
	$data   = wp_array_slice_assoc( $data_arr, $fields );

	/**
	 * Filters invoice_item data before it is inserted into the database.
	 *
	 * @param array $data Data to be inserted.
	 * @param array $data_arr Sanitized data.
	 *
	 * @since 1.2.1
	 */
	$data = apply_filters( 'eaccounting_insert_invoice_item', $data, $data_arr );

	$data  = wp_unslash( $data );
	$where = array( 'id' => $id );

	if ( $update ) {

		/**
		 * Fires immediately before an existing invoice_item item is updated in the database.
		 *
		 * @param int $id Invoice item id.
		 * @param array $data Invoice item data to be inserted.
		 * @param array $changes Invoice item data to be updated.
		 * @param array $data_arr Sanitized invoice_item item data.
		 * @param array $data_before Invoice item previous data.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_update_invoice_item', $id, $data, $data_arr, $data_before );
		if ( false === $wpdb->update( $wpdb->prefix . 'ea_invoice_items', $data, $where, $data_before ) ) {
			new WP_Error( 'db_update_error', __( 'Could not update invoice_item in the database.', 'wp-ever-accounting' ), $wpdb->last_error );
		}

		/**
		 * Fires immediately after an existing invoice_item is updated in the database.
		 *
		 * @param int $id Invoice item id.
		 * @param array $data Invoice item data to be inserted.
		 * @param array $changes Invoice item data to be updated.
		 * @param array $data_arr Sanitized invoice item data.
		 * @param array $data_before Invoice item previous data.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_update_invoice_item', $id, $data, $data_arr, $data_before );
	} else {

		/**
		 * Fires immediately before an existing invoice_item is inserted in the database.
		 *
		 * @param array $data Invoice item data to be inserted.
		 * @param string $data_arr Sanitized invoice item data.
		 * @param array $item_data Invoice item data as originally passed to the function.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_insert_invoice_item', $data, $data_arr, $item_data );

		if ( false === $wpdb->insert( $wpdb->prefix . 'ea_invoice_items', $data ) ) {
			new WP_Error( 'db_insert_error', __( 'Could not insert invoice_item into the database.', 'wp-ever-accounting' ), $wpdb->last_error );
		}

		$id = (int) $wpdb->insert_id;

		/**
		 * Fires immediately after an existing invoice_item is inserted in the database.
		 *
		 * @param int $id Invoice item id.
		 * @param array $data Invoice item has been inserted.
		 * @param array $data_arr Sanitized invoice item data.
		 * @param array $item_data Invoice item data as originally passed to the function.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_insert_invoice_item', $id, $data, $data_arr, $item_data );
	}

	// Clear cache.
	wp_cache_delete( $id, 'ea_invoice_items' );
	wp_cache_set( 'last_changed', microtime(), 'ea_invoice_items' );

	// Get new item object.
	$invoice_item = eaccounting_get_invoice_item( $id );

	/**
	 * Fires once an invoice_item has been saved.
	 *
	 * @param int $id Invoice_Item id.
	 * @param Invoice_Item $invoice_item Invoice_Item object.
	 * @param bool $update Whether this is an existing invoice item being updated.
	 *
	 * @since 1.2.1
	 */
	do_action( 'eaccounting_saved_invoice_item', $id, $invoice_item, $update, $data_arr, $data_before );

	return $invoice_item;
}

/**
 * Retrieves invoice item data given a invoice id or invoice object.
 *
 * @param int|object|Invoice_Item $invoice_item invoice item to retrieve
 * @param string                  $output The required return type. One of OBJECT, ARRAY_A, or ARRAY_N.Default OBJECT.
 * @param string                  $filter Type of filter to apply. Accepts 'raw', 'edit', 'db', or 'display'. Default 'raw'.
 *
 * @return Invoice_Item|array|null
 * @since 1.1.0
 */
function eaccounting_get_invoice_item( $invoice_item, $output = OBJECT, $filter = 'raw' ) {
	if ( empty( $invoice_item ) ) {
		return null;
	}

	if ( $invoice_item instanceof Invoice_Item ) {
		$_invoice_item = $invoice_item;
	} elseif ( is_object( $invoice_item ) ) {
		$_invoice_item = new Invoice_Item( $invoice_item );
	} else {
		$_invoice_item = Invoice_Item::get_instance( $invoice_item );
	}

	if ( ! $_invoice_item ) {
		return null;
	}

	$_invoice_item = $_invoice_item->filter( $filter );

	if ( ARRAY_A === $output ) {
		return $_invoice_item->to_array();
	}

	if ( ARRAY_N === $output ) {
		return array_values( $_invoice_item->to_array() );
	}

	return $_invoice_item->filter( $filter );
}

/**
 * Delete an invoice_item.
 *
 * @param int $invoice_item_id Invoice_Item id.
 *
 * @return Invoice_Item |false|null Invoice_Item data on success, false or null on failure.
 * @since 1.1.0
 */
function eaccounting_delete_invoice_item( $invoice_item_id ) {
	global $wpdb;

	$invoice_item = eaccounting_get_invoice_item( $invoice_item_id );
	if ( ! $invoice_item || ! $invoice_item->exists() ) {
		return false;
	}

	/**
	 * Filters whether an invoice_item delete should take place.
	 *
	 * @param bool|null $delete Whether to go forward with deletion.
	 * @param Invoice_Item $invoice_item invoice item object.
	 *
	 * @since 1.2.1
	 */
	$check = apply_filters( 'eaccounting_pre_delete_invoice_item', null, $invoice_item );
	if ( null !== $check ) {
		return $check;
	}

	/**
	 * Fires before an invoice item is deleted.
	 *
	 * @param int $invoice_item_id Contact id.
	 * @param Invoice_Item $invoice_item invoice_item object.
	 *
	 * @since 1.2.1
	 *
	 * @see eaccounting_delete_invoice_item()
	 */
	do_action( 'eaccounting_before_delete_invoice_item', $invoice_item_id, $invoice_item );

	$result = $wpdb->delete( $wpdb->prefix . 'ea_invoice_items', array( 'id' => $invoice_item_id ) );
	if ( ! $result ) {
		return false;
	}

	wp_cache_delete( $invoice_item_id, 'ea_invoice_items' );
	wp_cache_set( 'last_changed', microtime(), 'ea_invoice_items' );

	/**
	 * Fires after an invoice item is deleted.
	 *
	 * @param int $invoice_item_id invoice item id.
	 * @param Invoice_Item $invoice_item invoice item object.
	 *
	 * @since 1.2.1
	 *
	 * @see eaccounting_delete_invoice_item()
	 */
	do_action( 'eaccounting_delete_invoice_item', $invoice_item_id, $invoice_item );

	return $invoice_item;
}

/**
 * Sanitizes every invoice item field.
 *
 * If the context is 'raw', then the invoice item object or array will get minimal
 * sanitization of the integer fields.
 *
 * @param object|array $invoice_item The invoice item object or array
 * @param string       $context Optional. How to sanitize post fields. Accepts 'raw', 'edit', 'db', 'display'. Default 'display'.
 *
 * @return object|Invoice_Item|array The now sanitized invoice_item object or array
 * @see eaccounting_sanitize_invoice_item_field()
 *
 * @since 1.2.1
 */
function eaccounting_sanitize_invoice_item( $invoice_item, $context = 'raw' ) {
	if ( is_object( $invoice_item ) ) {
		// Check if post already filtered for this context.
		if ( isset( $invoice_item->filter ) && $context === $invoice_item->filter ) {
			return $invoice_item;
		}
		if ( ! isset( $invoice_item->id ) ) {
			$invoice_item->id = 0;
		}

		foreach ( array_keys( get_object_vars( $invoice_item ) ) as $field ) {
			$invoice_item->$field = eaccounting_sanitize_invoice_item_field( $field, $invoice_item->$field, $invoice_item->id, $context );
		}
		$invoice_item->filter = $context;
	} elseif ( is_array( $invoice_item ) ) {
		// Check if post already filtered for this context.
		if ( isset( $invoice_item['filter'] ) && $context === $invoice_item['filter'] ) {
			return $invoice_item;
		}
		if ( ! isset( $invoice_item['id'] ) ) {
			$invoice_item['id'] = 0;
		}
		foreach ( array_keys( $invoice_item ) as $field ) {
			$invoice_item[ $field ] = eaccounting_sanitize_invoice_item_field( $field, $invoice_item[ $field ], $invoice_item['id'], $context );
		}
		$invoice_item['filter'] = $context;
	}

	return $invoice_item;
}

/**
 * Sanitizes invoice_item field based on context.
 *
 * Possible context values are:  'raw', 'edit', 'db', 'display'.
 *
 * @param string $field The invoice_item Object field name.
 * @param mixed  $value The invoice_item Object value.
 * @param int    $invoice_item_id invoice_item id.
 * @param string $context Optional. How to sanitize the field. Possible values are 'raw', 'edit','db', 'display'. Default 'display'.
 *
 * @return mixed Sanitized value.
 * @since 1.2.1
 */
function eaccounting_sanitize_invoice_item_field( $field, $value, $invoice_item_id, $context ) {
	if ( false !== strpos( $field, '_id' ) || $field === 'id' ) { // phpcs:ignore
		$value = absint( $value );
	}

	$context = strtolower( $context );

	if ( 'raw' === $context ) {
		if ( $field === 'extra' ) { // phpcs:ignore
			$value = maybe_unserialize( $value );
		}

		return $value;
	}

	if ( 'edit' === $context ) {

		/**
		 * Filters invoice item field to edit before it is sanitized.
		 *
		 * @param mixed $value Value of the invoice item field.
		 * @param int $invoice_item_id Invoice Item id.
		 *
		 * @since 1.2.1
		 */
		$value = apply_filters( "eaccounting_edit_invoice_item_{$field}", $value, $invoice_item_id );

	} elseif ( 'db' === $context ) {

		/**
		 * Filters invoice item field value before it is sanitized.
		 *
		 * @param mixed $value Value of the invoice item field.
		 * @param int $invoice_item_id Invoice Item id.
		 *
		 * @since 1.2.1
		 */
		$value = apply_filters( "eaccounting_pre_invoice_item_{$field}", $value, $invoice_item_id );
	} else {
		// Use display filters by default.

		/**
		 * Filters the invoice_item field sanitized for display.
		 *
		 * @param mixed $value Value of the invoice item field.
		 * @param int $invoice_item_id Invoice Item id.
		 * @param string $context Context to retrieve the invoice item field value.
		 *
		 * @since 1.2.1
		 */
		$value = apply_filters( "eaccounting_invoice_item_{$field}", $value, $invoice_item_id, $context );
	}

	return $value;
}

