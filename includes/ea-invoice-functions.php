<?php

use EverAccounting\Invoice;

/**
 * Retrieves account data given a account id or account object.
 *
 * @param int|array|object|Invoice $invoice account to retrieve
 * @param string $filter Optional. Type of filter to apply. Accepts 'raw', 'edit', 'db', or 'display'. Default 'raw'.
 *
 * @return array|Invoice|null
 * @since 1.1.0
 */
function eaccounting_get_invoice( $invoice, $filter = 'raw' ) {
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
		'status'          => '',
		'issue_date'      => '',
		'due_date'        => '',
		'payment_date'    => '',
		'category_id'     => '',
		'contact_id'      => '',
		'address'         => '',
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
	$changes = $invoice_data;
	if ( ! empty( $invoice_data['id'] ) ) {
		$update = true;
		$id     = absint( $invoice_data['id'] );
		$before = eaccounting_get_invoice( $id );

		if ( is_null( $before ) ) {
			return new WP_Error( 'invalid_invoice_id', __( 'Invalid invoice id to update.' ) );
		}

		// Store changes value.
		$changes = array_diff_assoc( $invoice_data, $before->to_array() );

		// Merge old and new fields with new fields overwriting old ones.
		$invoice_data = array_merge( $before->to_array(), $invoice_data );
	}

	$data_arr = wp_parse_args( $invoice_data, $defaults );
	$data_arr = eaccounting_sanitize_account( $data_arr, 'db' );

//	var_dump( $data_arr );


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
