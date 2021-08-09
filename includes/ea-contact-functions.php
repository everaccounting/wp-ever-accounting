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
 * @param string             $output The required return type. One of OBJECT, ARRAY_A, or ARRAY_N. Default OBJECT.
 *
 * @return Contact|array|null
 * @since 1.1.0
 */
function eaccounting_get_contact( $contact, $output = OBJECT ) {
	if ( empty( $contact ) ) {
		return null;
	}

	if ( $contact instanceof Contact ) {
		$_contact = $contact;
	} else {
		$_contact = new Contact( $contact );
	}

	if ( ! $_contact->exists() ) {
		return null;
	}

	if ( ARRAY_A === $output ) {
		return $_contact->to_array();
	}

	if ( ARRAY_N === $output ) {
		return array_values( $_contact->to_array() );
	}

	return $_contact;
}

/**
 *  Insert or update a contact.
 *
 * @param array|object|Contact $data An array, object, or contact object of data arguments.
 *
 * @return Contact|WP_Error The contact object or WP_Error otherwise.
 * @global wpdb $wpdb WordPress database abstraction object.
 * @since 1.1.0
 */
function eaccounting_insert_contact( $data ) {
	if ( $data instanceof Contact ) {
		$data = $data->to_array();
	} elseif ( is_object( $data ) ) {
		$data = get_object_vars( $data );
	}

	if ( empty( $data ) || ! is_array( $data ) ) {
		return new WP_Error( 'invalid_contact_data', __( 'Contact could not be saved.', 'wp-ever-accounting' ) );
	}

	$data    = wp_parse_args( $data, array( 'id' => null ) );
	$contact = new Contact( (int) $data['id'] );
	$contact->set_props( $data );
	$is_error = $contact->save();
	if ( is_wp_error( $is_error ) ) {
		return $is_error;
	}

	return $contact;
}

/**
 * Delete an contact.
 *
 * @param int $contact_id Contact ID
 *
 * @return array|false Contact array data on success, false on failure.
 * @since 1.1.0
 */
function eaccounting_delete_contact( $contact_id ) {
	if ( $contact_id instanceof Contact ) {
		$contact_id = $contact_id->get_id();
	}

	if ( empty( $contact_id ) ) {
		return false;
	}

	$contact = new Contact( (int) $contact_id );
	if ( ! $contact->exists() ) {
		return false;
	}

	return $contact->delete();
}

/**
 * Retrieves an array of the contacts matching the given criteria.
 *
 * @param array $args Arguments to retrieve contacts.
 *
 * @return Contact[]|int Array of contact objects or count.
 * @since 1.1.0
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
