<?php
/**
 * EverAccounting Contact Functions.
 *
 * Contact related functions.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

use EverAccounting\Contact;

defined( 'ABSPATH' ) || exit();

/**
 * Get contact types.
 *
 * @since 1.1.0
 * @return array
 */
function eaccounting_get_contact_types() {
	return apply_filters(
		'eaccounting_contact_types',
		array(
			'customer' => __( 'Customer', 'wp-ever-accounting' ),
			'vendor'   => __( 'Vendor', 'wp-ever-accounting' ),
		)
	);
}

/**
 * Retrieves contact data given a contact id or contact object.
 *
 * @param int|object|Contact $contact contact to retrieve
 * @param string $output The required return type. One of OBJECT, ARRAY_A, or ARRAY_N.Default OBJECT.
 * @param string $filter Type of filter to apply. Accepts 'raw', 'edit', 'db', or 'display'. Default 'raw'.
 *
 * @return Contact|array|null
 * @since 1.1.0
 */
function eaccounting_get_contact( $contact, $output = OBJECT, $filter = 'raw' ) {
	if ( empty( $contact ) ) {
		return null;
	}

	if ( $contact instanceof Contact ) {
		$_contact = $contact;
	} elseif ( is_object( $contact ) ) {
		$_contact = new Contact( $contact );
	} else {
		$_contact = Contact::get_data_by( $contact );
	}

	if ( ! $_contact ) {
		return null;
	}

	$_contact = $_contact->filter( $filter );

	if ( ARRAY_A === $output ) {
		return $_contact->to_array();
	}

	if ( ARRAY_N === $output ) {
		return array_values( $_contact->to_array() );
	}

	return $_contact->filter( $filter );
}

/**
 *  Create new contact programmatically.
 *
 * @param array|object|Contact $contact_data An array, object, or Contact object of data arguments.
 *
 * @return Contact|WP_Error The Contact object or WP_Error otherwise.
 * @global wpdb $wpdb WordPress database abstraction object.
 * @since 1.1.0
 */
function eaccounting_insert_contact( $contact_data ) {
	global $wpdb;
	$user_id = get_current_user_id();
	if ( $contact_data instanceof Contact ) {
		$contact_data = $contact_data->to_array();
	} elseif ( $contact_data instanceof stdClass ) {
		$contact_data = get_object_vars( $contact_data );
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
	$changes = $contact_data;
	if ( ! empty( $contact_data['id'] ) ) {
		$update = true;
		$id     = absint( $contact_data['id'] );
		$before = eaccounting_get_contact( $id );

		if ( is_null( $before ) ) {
			return new WP_Error( 'invalid_contact_id', __( 'Invalid item id to update.' ) );
		}
		// Store changes value.
		$changes = array_diff_assoc( $contact_data, $before->to_array() );

		// Merge old and new fields with new fields overwriting old ones.
		$contact_data = array_merge( $before->to_array(), $contact_data );
	}

	$data_arr = wp_parse_args( $contact_data, $defaults );
	$data_arr = eaccounting_sanitize_contact( $data_arr, 'db' );

	if ( empty( $data_arr['name'] ) ) {
		return new WP_Error( 'invalid_contact_name', esc_html__( 'Contact name is required', 'wp-ever-accounting' ) );
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
	 * Filters contact data before it is inserted into the database.
	 *
	 * @param array $data Contact data to be inserted.
	 * @param array $data_arr Sanitized account data.
	 * @param array $contact_data Contact data as originally passed to the function.
	 *
	 * @since 1.2.1
	 */
	$data = apply_filters( 'eaccounting_insert_contact_data', $data, $data_arr, $contact_data );

	$data  = wp_unslash( $data );
	$where = array( 'id' => $id );

	if ( $update ) {

		/**
		 * Fires immediately before an existing contact is updated in the database.
		 *
		 * @param int $id Contact id.
		 * @param array $data Contact data to be inserted.
		 * @param array $changes Contact data to be updated.
		 * @param array $data_arr Sanitized contact data.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_update_contact', $id, $data, $changes, $data_arr );
		if ( false === $wpdb->update( $wpdb->prefix . 'ea_contacts', $data, $where ) ) {
			new WP_Error( 'db_update_error', __( 'Could not update contact in the database.' ), $wpdb->last_error );
		}

		/**
		 * Fires immediately after an existing contact is updated in the database.
		 *
		 * @param int $id Contact id.
		 * @param array $data Contact data to be inserted.
		 * @param array $changes Contact data to be updated.
		 * @param array $data_arr Sanitized contact data.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_update_contact', $id, $data, $changes, $data_arr );
	} else {

		/**
		 * Fires immediately before an existing contact is inserted in the database.
		 *
		 * @param array $data Contact data to be inserted.
		 * @param string $data_arr Sanitized contact data.
		 * @param array $contact_data Contact data as originally passed to the function.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_insert_contact', $data, $data_arr, $contact_data );

		if ( false === $wpdb->insert( $wpdb->prefix . 'ea_contacts', $data ) ) {
			new WP_Error( 'db_insert_error', __( 'Could not insert contact into the database.' ), $wpdb->last_error );
		}

		$id = (int) $wpdb->insert_id;

		/**
		 * Fires immediately after an existing contact is inserted in the database.
		 *
		 * @param int $id Contact id.
		 * @param array $data Contact has been inserted.
		 * @param array $data_arr Sanitized contact data.
		 * @param array $contact_data Contact data as originally passed to the function.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_insert_contact', $id, $data, $data_arr, $contact_data );
	}

	// Clear cache.
	wp_cache_delete( $id, 'ea_contacts' );
	wp_cache_set( 'last_changed', microtime(), 'ea_contacts' );

	// Get new contact object.
	$contact = eaccounting_get_contact( $id );

	/**
	 * Fires once an contact has been saved.
	 *
	 * @param int $id Contact id.
	 * @param Contact $contact Contact object.
	 * @param bool $update Whether this is an existing contact being updated.
	 *
	 * @since 1.2.1
	 */
	do_action( 'eaccounting_saved_contact', $id, $contact, $update );

	return $contact;
}

/**
 * Delete an contact.
 *
 * @param int $contact_id Contact ID
 *
 * @return Contact |false|null Contact data on success, false or null on failure.
 * @since 1.1.0
 */
function eaccounting_delete_contact( $contact_id ) {
	global $wpdb;

	$contact = eaccounting_get_contact( $contact_id );
	if ( ! $contact || ! $contact->exists() ) {
		return false;
	}

	/**
	 * Filters whether an contact delete should take place.
	 *
	 * @param bool|null $delete Whether to go forward with deletion.
	 * @param Contact $contact contact object.
	 *
	 * @since 1.2.1
	 */
	$check = apply_filters( 'eaccounting_pre_delete_contact', null, $contact );
	if ( null !== $check ) {
		return $check;
	}

	/**
	 * Fires before an item is deleted.
	 *
	 * @param int $contact_id Contact id.
	 * @param Contact $contact Contact object.
	 *
	 * @since 1.2.1
	 *
	 * @see eaccounting_delete_contact()
	 */
	do_action( 'eaccounting_before_delete_contact', $contact_id, $contact );

	$result = $wpdb->delete( $wpdb->prefix . 'ea_contacts', array( 'id' => $contact_id ) );
	if ( ! $result ) {
		return false;
	}

	wp_cache_delete( $contact_id, 'ea_contacts' );
	wp_cache_set( 'last_changed', microtime(), 'ea_contacts' );

	/**
	 * Fires after an contact is deleted.
	 *
	 * @param int $contact_id contact id.
	 * @param Contact $contact contact object.
	 *
	 * @since 1.2.1
	 *
	 * @see eaccounting_delete_contact()
	 */
	do_action( 'eaccounting_delete_contact', $contact_id, $contact );

	return $contact;
}

/**
 * Sanitizes every contact field.
 *
 * If the context is 'raw', then the contact object or array will get minimal
 * sanitization of the integer fields.
 *
 * @param object|array $contact The contact object or array
 * @param string $context Optional. How to sanitize post fields. Accepts 'raw', 'edit', 'db', 'display'. Default 'display'.
 *
 * @return object|Contact|array The now sanitized contact object or array
 * @see eaccounting_sanitize_contact_field()
 *
 * @since 1.2.1
 */
function eaccounting_sanitize_contact( $contact, $context = 'raw' ) {
	if ( is_object( $contact ) ) {
		// Check if post already filtered for this context.
		if ( isset( $contact->filter ) && $context == $contact->filter ) {
			return $contact;
		}
		if ( ! isset( $contact->id ) ) {
			$contact->id = 0;
		}
		foreach ( array_keys( get_object_vars( $contact ) ) as $field ) {
			$contact->$field = eaccounting_sanitize_contact_field( $field, $contact->$field, $contact->id, $context );
		}
		$contact->filter = $context;
	} elseif ( is_array( $contact ) ) {
		// Check if post already filtered for this context.
		if ( isset( $contact['filter'] ) && $context == $contact['filter'] ) { //phpcs:ignore
			return $contact;
		}
		if ( ! isset( $contact['id'] ) ) {
			$contact['id'] = 0;
		}
		foreach ( array_keys( $contact ) as $field ) {
			$contact[ $field ] = eaccounting_sanitize_contact_field( $field, $contact[ $field ], $contact['id'], $context );
		}
		$contact['filter'] = $context;
	}

	return $contact;
}

/**
 * Sanitizes a contact field based on context.
 *
 * Possible context values are:  'raw', 'edit', 'db', 'display'.
 *
 * @param string $field The contact Object field name.
 * @param mixed $value The contact Object value.
 * @param int $contact_id contact id.
 * @param string $context Optional. How to sanitize the field. Possible values are 'raw', 'edit','db', 'display'. Default 'display'.
 *
 * @return mixed Sanitized value.
 * @since 1.2.1
 */
function eaccounting_sanitize_contact_field( $field, $value, $contact_id, $context ) {
	if ( false !== strpos( $field, '_id' ) || $field === 'id' ) { //phpcs:ignore
		$value = absint( $value );
	}

	$context = strtolower( $context );

	if ( 'raw' === $context ) {
		return $value;
	}

	if ( 'edit' === $context ) {

		/**
		 * Filters an contact field to edit before it is sanitized.
		 *
		 * @param mixed $value Value of the contact field.
		 * @param int $contact_id Contact id.
		 *
		 * @since 1.2.1
		 */
		$value = apply_filters( "eaccounting_edit_contact_{$field}", $value, $contact_id );

	} elseif ( 'db' === $context ) {

		/**
		 * Filters a contact field value before it is sanitized.
		 *
		 * @param mixed $value Value of the contact field.
		 * @param int $contact_id Contact id.
		 *
		 * @since 1.2.1
		 */
		$value = apply_filters( "eaccounting_pre_contact_{$field}", $value, $contact_id );

	} else {
		// Use display filters by default.

		/**
		 * Filters the contact field sanitized for display.
		 *
		 * The dynamic portion of the filter name, `$field`, refers to the contact field name.
		 *
		 * @param mixed $value Value of the item field.
		 * @param int $contact_id contact id.
		 * @param string $context Context to retrieve the contact field value.
		 *
		 * @since 1.2.1
		 */
		$value = apply_filters( "eaccounting_contact_{$field}", $value, $contact_id, $context );
	}

	return $value;
}

/**
 * Retrieves an array of the contacts matching the given criteria.
 *
 * @param array $args Arguments to retrieve contacts.
 *
 * @return Contact[]|int Array of contact objects or count.
 * @since 1.1.0
 *
 */
function eaccounting_get_contacts( $args = array() ) {
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
	$query       = new \EverAccounting\Contact_Query( $parsed_args );

	if ( true === $parsed_args['count_total'] ) {
		return $query->get_total();
	}


	return $query->get_results();
}
