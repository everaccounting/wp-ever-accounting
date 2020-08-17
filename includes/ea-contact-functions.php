<?php
/**
 * EverAccounting Contact Functions.
 *
 * Contact related functions.
 *
 * @package EverAccounting
 * @since 1.0.2
 */

defined( 'ABSPATH' ) || exit();

/**
 * Get contact types.
 *
 * @return array
 * @since 1.0.2
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
 * @return \EverAccounting\Contact|null
 * @since 1.0.2
 *
 */
function eaccounting_get_contact( $contact ) {
	if ( empty( $contact ) ) {
		return null;
	}
	try {
		if ( $contact instanceof \EverAccounting\Contact ) {
			$_contact = $contact;
		} elseif ( is_object( $contact ) && ! empty( $contact->id ) ) {
			$_contact = new \EverAccounting\Contact( null );
			$_contact->populate( $contact );
		} else {
			$_contact = new \EverAccounting\Contact( absint( $contact ) );
		}

		if ( ! $_contact->exists() ) {
			throw new Exception( __( 'Invalid contact.', 'wp-ever-accounting' ) );
		}

		return $_contact;
	} catch ( Exception $exception ) {
		return null;
	}
}

/**
 *  Create new contact programmatically.
 *
 *  Returns a new contact object on success.
 *
 * @param array $args Contact arguments.
 *
 * @return \EverAccounting\Contact|WP_Error
 * @since 1.0.2
 *
 */
function eaccounting_insert_contact( $args ) {
	try {
		$default_args = array(
			'id' => null,
		);
		$args         = (array) wp_parse_args( $args, $default_args );
		$contact      = new \EverAccounting\Contact( $args['id'] );
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
 * @since 1.0.2
 *
 */
function eaccounting_delete_contact( $contact_id ) {
	try {
		$contact = new \EverAccounting\Contact( $contact_id );
		if ( ! $contact->exists() ) {
			throw new Exception( __( 'Invalid contact.', 'wp-ever-accounting' ) );
		}

		$contact->delete();

		return empty( $contact->get_id() );

	} catch ( Exception $exception ) {
		return false;
	}
}
