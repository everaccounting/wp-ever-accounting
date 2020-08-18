<?php
/**
 * EverAccounting Contact Functions.
 *
 * Contact related functions.
 *
 * @since 1.0.2
 * @package EverAccounting
 */

use \EverAccounting\Contact;
use \EverAccounting\Query_Contact;
use \EverAccounting\Exception;

defined( 'ABSPATH' ) || exit();

/**
 * Get contact types.
 *
 * @since 1.0.2
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
 * @since 1.0.2
 *
 * @return \EverAccounting\Contact|null
 */
function eaccounting_get_contact( $contact ) {
	if ( empty( $contact ) ) {
		return null;
	}
	try {
		if ( $contact instanceof Contact ) {
			$_contact = $contact;
		} elseif ( is_object( $contact ) && ! empty( $contact->id ) ) {
			$_contact = new Contact( null );
			$_contact->populate( $contact );
		} else {
			$_contact = new Contact( absint( $contact ) );
		}

		if ( ! $_contact->exists() ) {
			throw new Exception( 'invalid_id', __( 'Invalid contact.', 'wp-ever-accounting' ) );
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
 * @since 1.0.2
 *
 * @return \EverAccounting\Contact|WP_Error
 */
function eaccounting_insert_contact( $args ) {
	try {
		$default_args = array(
			'id' => null,
		);
		$args         = (array) wp_parse_args( $args, $default_args );
		$contact      = new Contact( $args['id'] );
		$contact->set_props( $args );

		//validation
		if ( ! $contact->get_date_created() ) {
			$contact->set_date_created();
		}

		if ( ! $contact->get_company_id() ) {
			$contact->set_company_id();
		}

		if ( ! $contact->get_creator_id() ) {
			$contact->set_creator_id();
		}

		if ( ! $contact->get_currency_code() ) {
			$contact->set_currency_code( eaccounting()->settings->get( 'default_currency' ) );
		}

		if ( empty( $contact->get_name() ) ) {
			throw new Exception( 'missing_required', __( 'Name is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $contact->get_type() ) ) {
			throw new Exception( 'missing_required', __( 'Type is required', 'wp-ever-accounting' ) );
		}

		if ( $contact->get_user_id() != null && ! get_user_by( 'ID', $contact->get_user_id() ) ) {
			throw new Exception( 'missing_required', __( 'Invalid WP User ID', 'wp-ever-accounting' ) );
		}
		if ( ! empty( $contact->get_email() ) ) {
			$existing_id = Query_Contact::init()
			                            ->where( 'email', $contact->get_email() )
			                            ->where( 'type', $contact->get_type() )
			                            ->where( 'company_id', $contact->get_company_id() )
			                            ->value( 0 );
			if ( ! empty( $existing_id ) && $existing_id != $contact->get_id() ) {
				throw new Exception( 'duplicate_email', __( 'The email address is already in used.', 'wp-ever-accounting' ) );
			}
		}

		$contact->save();

	} catch ( Exception $e ) {
		return new WP_Error( $e->getErrorCode(), $e->getMessage() );
	}

	return $contact;
}

/**
 * Delete an contact.
 *
 * @param $contact_id
 *
 * @since 1.0.2
 *
 * @return bool
 */
function eaccounting_delete_contact( $contact_id ) {
	try {
		$contact = new Contact( $contact_id );
		if ( ! $contact->exists() ) {
			throw new Exception( 'invalid_id', __( 'Invalid contact.', 'wp-ever-accounting' ) );
		}

		$contact->delete();

		return empty( $contact->get_id() );

	} catch ( Exception $exception ) {
		return false;
	}
}
