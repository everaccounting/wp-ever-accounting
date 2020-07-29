<?php
/**
 * EverAccounting Contact Functions
 *
 * Contact related functions.
 *
 * @package EverAccounting\Functions
 * @version 1.0.2
 */

defined( 'ABSPATH' ) || exit();

/**
 * Get contact types.
 *
 * since 1.0.0
 * @return array
 */
function eaccounting_get_contact_types() {
	return apply_filters( 'eaccounting_contact_types', array(
		'customer' => __( 'Customer', 'wp-ever-accounting' ),
		'vendor'   => __( 'Vendor', 'wp-ever-accounting' ),
	) );
}


/**
 * Main function for returning contact.
 *
 * @param $contact
 *
 * @return EAccounting_Contact|false
 * @since 1.0.0
 *
 */
function eaccounting_get_contact( $contact ) {
	if ( empty( $contact ) ) {
		return false;
	}

	try {
		$contact = new EAccounting_Contact( $contact );
		if ( ! $contact->exists() ) {
			throw new Exception( __( 'Invalid contact.', 'wp-ever-accounting' ) );
		}

		return $contact;
	} catch ( Exception $exception ) {
		return false;
	}
}

/**
 *  Create new contact programmatically.
 *
 *  Returns a new contact object on success.
 *
 * @param array $args Contact arguments.
 *
 * @return EAccounting_Contact|WP_Error
 * @since 1.0.0
 *
 */
function eaccounting_insert_contact( $args ) {
	try {
		$default_args = array(
			'id' => null,
		);
		$args         = (array) wp_parse_args( $args, $default_args );
		$contact      = new EAccounting_Contact( $args['id'] );
		$contact->set_props( $args );
		$contact->save();

	} catch ( Exception $e ) {
		return new WP_Error( 'error', $e->getMessage() );
	}

	return $contact;
}

/**
 * Delete an contact.
 *
 * @param $contact_id
 *
 * @return bool
 * @since 1.0.0
 *
 */
function eaccounting_delete_contact( $contact_id ) {
	try {
		$contact = new EAccounting_Contact( $contact_id );
		if ( ! $contact->exists() ) {
			throw new Exception( __( 'Invalid contact.', 'wp-ever-accounting' ) );
		}

		$contact->delete();

		return empty( $contact->get_id() );

	} catch ( Exception $exception ) {
		return false;
	}
}
