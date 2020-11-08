<?php
/**
 * EverAccounting Customer Functions.
 *
 * Customer related functions.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

namespace EverAccounting\Customers;

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
	return \EverAccounting\Contacts\query( array_merge( $args, array( 'type' => 'customer' ) ) );
}

/**
 * Main function for returning contact.
 *
 * @since 1.1.0
 *
 * @param $customer
 *
 * @return \EverAccounting\Contacts\Contact|null
 */
function get( $customer ) {
	$customer = \EverAccounting\Contacts\get( $customer );

	return $customer && $customer->get_type() === 'customer' ? $customer : null;
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
	$args['type'] = 'customer';

	return \EverAccounting\Contacts\insert( $args );
}


/**
 * Delete an contact.
 *
 * @since 1.1.0
 *
 * @param $customer_id
 *
 * @return bool
 */
function delete( $customer_id ) {
	$customer = get( $customer_id );

	return $customer && \EverAccounting\Contacts\delete( $customer );
}
