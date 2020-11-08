<?php
/**
 * EverAccounting Vendor Functions.
 *
 * Vendor related functions.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

namespace EverAccounting\Vendors;

/**
 * Main function for querying contacts.
 *
 * @since 1.1.0
 *
 * @param array $args
 *
 * @return \EverAccounting\Query
 */
function query( $args = array() ) {
	return \EverAccounting\Contacts\query( array_merge( $args, array( 'type' => 'vendor' ) ) );
}

/**
 * Main function for returning contact.
 *
 * @since 1.1.0
 *
 * @param $vendor
 *
 * @return \EverAccounting\Contacts\Contact|null
 */
function get( $vendor ) {
	$vendor = \EverAccounting\Contacts\get( $vendor );

	return $vendor && $vendor->get_type() === 'vendor' ? $vendor : null;
}

/**
 *  Create new contact programmatically.
 *
 *  Returns a new contact object on success.
 *
 * @since 1.1.0
 *
 * @param array $args Contact arguments.
 *
 * @return \EverAccounting\Contacts\Contact|\WP_Error
 */
function insert( $args ) {
	$args['type'] = 'vendor';

	return \EverAccounting\Contacts\insert( $args );
}


/**
 * Delete an contact.
 *
 * @since 1.1.0
 *
 * @param $vendor_id
 *
 * @return bool
 */
function delete( $vendor_id ) {
	$vendor = get( $vendor_id );

	return $vendor && \EverAccounting\Contacts\delete( $vendor );
}
