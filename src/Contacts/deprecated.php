<?php
defined( 'ABSPATH' ) || exit();
/**
 * Get contact types.
 *
 * @since 1.0.2
 * @return array
 */
function eaccounting_get_contact_types() {
	return \EverAccounting\Contacts\get_types();
}

/**
 * Main function for returning contact.
 *
 * @since 1.0.2
 *
 * @param $contact
 *
 * @return \EverAccounting\Contacts\Contact|null
 */
function eaccounting_get_contact( $contact ) {
	return \EverAccounting\Contacts\get( $contact );
}

/**
 *  Create new contact programmatically.
 *
 *  Returns a new contact object on success.
 *
 * @since 1.0.2
 *
 * @param array $args Contact arguments.
 *
 * @return \EverAccounting\Contacts\Contact|WP_Error
 */
function eaccounting_insert_contact( $args ) {
	return \EverAccounting\Contacts\insert( $args );
}
