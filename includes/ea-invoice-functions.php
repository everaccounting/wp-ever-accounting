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
 * @param string $output The required return type. One of OBJECT, ARRAY_A, or ARRAY_N.Default OBJECT.
 * @param string $filter Type of filter to apply. Accepts 'raw', 'edit', 'db', or 'display'. Default 'raw'.
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

function eaccounting_insert_invoice( $invoice_data ) {
	global $wpdb;

	$user_id = get_current_user_id();
	if ( $invoice_data instanceof Invoice ) {
		$invoice_data = $invoice_data->to_array();
	} elseif ( $invoice_data instanceof stdClass ) {
		$invoice_data = get_object_vars( $invoice_data );
	}

	$defaults = array(
		'document_number' => '',
		'type'            => '',
		'order_number'    => '',
		'status'          => 'draft',
		'issue_date'      => '',
		'due_date'        => '',
		'payment_date'    => '',
		'category_id'     => '',
		'contact_id'      => '',
		'address'         => array(),
		'currency_code'   => '',
		'currency_rate'   => '',
		'discount'        => 0.00,
		'discount_type'   => '',
		'subtotal'        => 0.00,
		'total_tax'       => 0.00,
		'total_discount'  => 0.00,
		'total_fees'      => 0.00,
		'total_shipping'  => 0.00,
		'total'           => 0.00,
		'tax_inclusive'   => '',
		'note'            => '',
		'terms'           => '',
		'attachment_id'   => '',
		'key'             => '',
		'parent_id'       => 0,
		'creator_id'      => $user_id,
		'date_created'    => '',
	);

	// Are we updating or creating?
	$id      = null;
	$update  = false;
	if ( ! empty( $invoice_data['id'] ) ) {
		$update = true;
		$id     = absint( $invoice_data['id'] );
		$before = eaccounting_get_invoice( $id );

		if ( is_null( $before ) ) {
			return new WP_Error( 'invalid_invoice_id', __( 'Invalid invoice id to update.' ) );
		}

		$before = $before->to_array();

		// Store changes value.
		$changes = array_diff_assoc( wp_array_slice_assoc( $invoice_data, array_keys( $defaults ) ), $before );

		// Handle changes. When currency, customer get changed need to update the data.
		if ( ! empty( $changes['currency_code'] ) ) {
			$currency = eaccounting_get_currency( $changes['currency_code'] );
			if ( ! $currency || empty( $currency->rate ) ) {
				return new WP_Error( 'invalid_transaction_account_currency', esc_html__( 'Transaction associated account currency does not exist', 'wp-ever-accounting' ) );
			}
			$invoice_data['currency_rate'] = $currency->rate;
		}
		var_dump($changes['contact_id']);
		if ( ! empty( $changes['contact_id'] ) ) {
			$contact = eaccounting_get_contact( (int) $changes['contact_id'] );
			var_dump($contact);
		}


		// Merge old and new fields with new fields overwriting old ones.
		$invoice_data = array_merge( $before, $invoice_data );
	}

	$data_arr = wp_parse_args( $invoice_data, $defaults );
	$data_arr = eaccounting_sanitize_account( $data_arr, 'db' );


	var_dump( $data_arr );


//	if ( empty( $data_arr['currency_rate'] ) ) {
//		return new WP_Error( 'invalid_invoice_currency_rate', esc_html__( 'Currency rate is required', 'wp-ever-accounting' ) );
//	}
//
//	if ( empty( $data_arr['currency_code'] ) ) {
//		return new WP_Error( 'invalid_invoice_currency_code', esc_html__( 'Currency code is required', 'wp-ever-accounting' ) );
//	}
//
//	if ( empty( $data_arr['date_created'] ) || '0000-00-00 00:00:00' === $data_arr['date_created'] ) {
//		$data_arr['date_created'] = current_time( 'mysql' );
//	}

	// validate required items.

	// validate invoice number.

	// check if key is generated.

	// calculate totals.

	// save items.

	// status transition.
}

/**
 * Sanitizes every account field.
 *
 * If the context is 'raw', then the account object or array will get minimal
 * sanitization of the integer fields.
 *
 * @param object|array $invoice The account object or array
 * @param string $context Optional. How to sanitize post fields. Accepts 'raw', 'edit', 'db', 'display'. Default 'display'.
 *
 * @return object|Invoice|array The now sanitized account object or array
 * @see eaccounting_sanitize_invoice_field()
 *
 * @since 1.2.1
 *
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
 * @param mixed $value The account Object value.
 * @param int $invoice_id Account id.
 * @param string $context Optional. How to sanitize the field. Possible values are 'raw', 'edit','db', 'display'. Default 'display'.
 *
 * @return mixed Sanitized value.
 * @since 1.2.1
 *
 */
function eaccounting_sanitize_invoice_field( $field, $value, $invoice_id, $context ) {
	if ( false !== strpos( $field, '_id' ) || $field === 'id' ) {
		$value = absint( $value );
	}

	$context = strtolower( $context );

	if ( 'raw' === $context ) {
		if ( $field === 'address' ) {
			$value = maybe_unserialize( $value );
		}

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
		 *
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
		 *
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
		 *
		 */
		$value = apply_filters( "eaccounting_invoice_{$field}", $value, $invoice_id, $context );
	}

	return $value;
}

function eaccounting_insert_invoice_item( $item_data ) {
	global $wpdb;
	$user_id = get_current_user_id();
	if ( $item_data instanceof Invoice_Item ) {
		$item_data = $item_data->to_array();
	} elseif ( $item_data instanceof stdClass ) {
		$item_data = get_object_vars( $item_data );
	}

	$defaults = [
		'invoice_id'    => null,
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
	];

	// Are we updating or creating?
	$id          = null;
	$update      = false;
	$data_before = array();
	if ( ! empty( $item_data['id'] ) ) {
		$update      = true;
		$id          = absint( $item_data['id'] );
		$data_before = eaccounting_get_invoice_item( $id );

		if ( is_null( $data_before ) ) {
			return new WP_Error( 'invalid_invoice_item_id', __( 'Invalid invoice item id to update.' ) );
		}

		// Merge old and new fields with new fields overwriting old ones.
		$item_data   = array_merge( $data_before->to_array(), $item_data );
		$data_before = $data_before->to_array();
	}
	$item_data = wp_parse_args( $item_data, $defaults );
	$data_arr  = eaccounting_sanitize_invoice_item( $item_data, 'db' );

	if ( empty( $data_arr['invoice_id'] ) ) {
		return new WP_Error( 'invalid_invoice_item_invoice_id', esc_html__( 'Invoice id is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['item_id'] ) ) {
		return new WP_Error( 'invalid_invoice_item_item_id', esc_html__( 'Invoice item id is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['currency_code'] ) ) {
		return new WP_Error( 'invalid_invoice_item_item_name', esc_html__( 'Item currency is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['item_name'] ) ) {
		return new WP_Error( 'invalid_invoice_item_item_name', esc_html__( 'Item name is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['date_created'] ) || '0000-00-00 00:00:00' === $data_arr['date_created'] ) {
		$data_arr['date_created'] = current_time( 'mysql' );
	}

	$subtotal         = (float) $data_arr['price'] * (float) $data_arr['quantity'];
	$discount         = (float) $data_arr['discount'];
	$subtotal_for_tax = $subtotal - $discount;
	$tax_rate         = ( (float) $data_arr['tax_rate'] / 100 );
	$total_tax        = eaccounting_calculate_tax( $subtotal_for_tax, $tax_rate );

	if ( 'tax_subtotal_rounding' !== eaccounting()->settings->get( 'tax_subtotal_rounding', 'tax_subtotal_rounding' ) ) {
		$total_tax = eaccounting_format_decimal( $total_tax, 2 );
	}

	if ( eaccounting_prices_include_tax() ) {
		$subtotal -= $total_tax;
	}

	$total = $subtotal - $discount + $total_tax;
	if ( $total < 0 ) {
		$total = 0;
	}
	$data_arr['subtotal'] = $subtotal;
	$data_arr['tax']      = $total_tax;
	$data_arr['total']    = $total;

	$fields = array_keys( $defaults );
	$data   = wp_array_slice_assoc( $data_arr, $fields );

	/**
	 * Filters invoice item data before it is inserted into the database.
	 *
	 * @param array $data Data to be inserted.
	 * @param array $data_arr Sanitized data.
	 *
	 * @since 1.2.1
	 */
	$data = apply_filters( 'eaccounting_insert_item_data', $data, $data_arr );

	$data  = wp_unslash( $data );
	$where = array( 'id' => $id );


	if ( $update ) {

		/**
		 * Fires immediately before an existing invoice item is updated in the database.
		 *
		 * @param int $id Invoice item id.
		 * @param array $data Invoice item data to be inserted.
		 * @param array $changes Invoice item data to be updated.
		 * @param array $data_arr Sanitized invoice item data.
		 * @param array $data_before Invoice item previous data.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_update_invoice_item', $id, $data, $data_arr, $data_before );
		if ( false === $wpdb->update( $wpdb->prefix . 'ea_invoice_items', $data, $where, $data_before ) ) {
			new WP_Error( 'db_update_error', __( 'Could not update invoice item in the database.' ), $wpdb->last_error );
		}

		/**
		 * Fires immediately after an existing Invoice item is updated in the database.
		 *
		 * @param int $id Invoice item id.
		 * @param array $data Invoice item data to be inserted.
		 * @param array $changes Invoice item data to be updated.
		 * @param array $data_arr Sanitized Invoice item data.
		 * @param array $data_before Invoice item previous data.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_update_invoice_item', $id, $data, $data_arr, $data_before );
	} else {

		/**
		 * Fires immediately before an existing Invoice item is inserted in the database.
		 *
		 * @param array $data Invoice item data to be inserted.
		 * @param string $data_arr Sanitized Invoice item data.
		 * @param array $item_data Invoice item data as originally passed to the function.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_insert_invoice_item', $data, $data_arr, $item_data );

		if ( false === $wpdb->insert( $wpdb->prefix . 'ea_invoice_items', $data ) ) {
			new WP_Error( 'db_insert_error', __( 'Could not insert invoice item into the database.' ), $wpdb->last_error );
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
		do_action( 'eaccounting_insert_invoice_item', $id, $data, $data_arr, $item_data );
	}

	// Clear cache.
	wp_cache_delete( $id, 'ea_invoice_items' );
	wp_cache_set( 'last_changed', microtime(), 'ea_invoice_items' );

	// Get new item object.
	$item = eaccounting_get_invoice_item( $id );

	/**
	 * Fires once an item has been saved.
	 *
	 * @param int $id Item id.
	 * @param Invoice_Item $item Item object.
	 * @param bool $update Whether this is an existing item being updated.
	 *
	 * @since 1.2.1
	 */
	do_action( 'eaccounting_saved_invoice_item', $id, $item, $update, $data_arr, $data_before );

	return $item;
}


/**
 * Retrieves invoice item data given a invoice id or invoice object.
 *
 * @param int|object|Invoice_Item $invoice_item invoice item to retrieve
 * @param string $output The required return type. One of OBJECT, ARRAY_A, or ARRAY_N.Default OBJECT.
 * @param string $filter Type of filter to apply. Accepts 'raw', 'edit', 'db', or 'display'. Default 'raw'.
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

	return $_invoice_item;
}

/**
 * Sanitizes every invoice item field.
 *
 * If the context is 'raw', then the account object or array will get minimal
 * sanitization of the integer fields.
 *
 * @param object|array $invoice_item The invoice item object or array
 * @param string $context Optional. How to sanitize post fields. Accepts 'raw', 'edit', 'db', 'display'. Default 'display'.
 *
 * @return object|Invoice_Item|array The now sanitized account object or array
 * @see eaccounting_sanitize_invoice_field()
 *
 * @since 1.2.1
 *
 */
function eaccounting_sanitize_invoice_item( $invoice_item, $context = 'raw' ) {
	if ( is_object( $invoice_item ) ) {
		// Check if post already filtered for this context.
		if ( isset( $invoice_item->filter ) && $context == $invoice_item->filter ) {
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
		if ( isset( $invoice_item['filter'] ) && $context == $invoice_item['filter'] ) {
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
 * Sanitizes an invoice item field based on context.
 *
 * Possible context values are:  'raw', 'edit', 'db', 'display'.
 *
 * @param string $field The invoice item Object field name.
 * @param mixed $value The invoice item Object value.
 * @param int $invoice_item_id invoice item id.
 * @param string $context Optional. How to sanitize the field. Possible values are 'raw', 'edit','db', 'display'. Default 'display'.
 *
 * @return mixed Sanitized value.
 * @since 1.2.1
 *
 */
function eaccounting_sanitize_invoice_item_field( $field, $value, $invoice_item_id, $context ) {
	if ( false !== strpos( $field, '_id' ) || $field === 'id' ) {
		$value = absint( $value );
	}

	$context = strtolower( $context );

	if ( 'raw' === $context ) {
		if ( $field === 'extra' ) {
			$value = maybe_unserialize( $value );
		}

		return $value;
	}

	if ( 'edit' === $context ) {

		/**
		 * Filters an account field to edit before it is sanitized.
		 *
		 * @param mixed $value Value of the account field.
		 * @param int $invoice_item_id Account id.
		 *
		 * @since 1.2.1
		 *
		 */
		$value = apply_filters( "eaccounting_edit_invoice_item_{$field}", $value, $invoice_item_id );

	} elseif ( 'db' === $context ) {

		/**
		 * Filters a account field value before it is sanitized.
		 *
		 * @param mixed $value Value of the account field.
		 * @param int $invoice_item_id Account id.
		 *
		 * @since 1.2.1
		 *
		 */
		$value = apply_filters( "eaccounting_pre_invoice_item_{$field}", $value, $invoice_item_id );
	} else {
		// Use display filters by default.

		/**
		 * Filters the account field sanitized for display.
		 *
		 * The dynamic portion of the filter name, `$field`, refers to the account field name.
		 *
		 * @param mixed $value Value of the account field.
		 * @param int $invoice_item_id account id.
		 * @param string $context Context to retrieve the account field value.
		 *
		 * @since 1.2.1
		 *
		 */
		$value = apply_filters( "eaccounting_invoice_item_{$field}", $value, $invoice_item_id, $context );
	}

	return $value;
}
