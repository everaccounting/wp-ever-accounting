<?php
defined( 'ABSPATH' ) || exit();

/**
 * Get contact types
 * since 1.0.0
 * @return array
 */
function eaccounting_get_contact_types() {
	return apply_filters( 'eaccounting_contact_types', array(
		'customer' => __( 'Customer', 'wp-eaccounting' ),
		'vendor'   => __( 'Vendor', 'wp-eaccounting' ),
	) );
}


/**
 * Insert Account
 *
 * @param $args
 *
 * @return int|WP_Error
 * @since 1.0.0
 */
function eaccounting_insert_contact( $args ) {
	global $wpdb;
	$update = false;
	$id     = null;
	$args   = (array) apply_filters( 'eaccounting_create_contact', $args );

	if ( isset( $args['id'] ) && ! empty( trim( $args['id'] ) ) ) {
		$id          = (int) $args['id'];
		$update      = true;
		$item_before = (array) eaccounting_get_contact( $id );
		if ( is_null( $item_before ) ) {
			return new \WP_Error( 'invalid_action', __( 'Could not find the item to  update', 'wp-ever-accounting' ) );
		}

		$args = array_merge( $item_before, $args );
	}


	$data = array(
		'id'         => $id,
		'user_id'    => empty( $args['user_id'] ) ? '' : absint( $args['user_id'] ),
		'first_name' => empty( $args['first_name'] ) ? '' : sanitize_text_field( $args['first_name'] ),
		'last_name'  => empty( $args['last_name'] ) ? '' : sanitize_text_field( $args['last_name'] ),
		'tax_number' => empty( $args['tax_number'] ) ? '' : sanitize_text_field( $args['tax_number'] ),
		'email'      => empty( $args['email'] ) ? '' : sanitize_email( $args['email'] ),
		'phone'      => empty( $args['phone'] ) ? '' : sanitize_text_field( $args['phone'] ),
		'address'    => empty( $args['address'] ) ? '' : sanitize_text_field( $args['address'] ),
		'city'       => empty( $args['city'] ) ? '' : sanitize_text_field( $args['city'] ),
		'state'      => empty( $args['state'] ) ? '' : sanitize_text_field( $args['state'] ),
		'postcode'   => empty( $args['postcode'] ) ? '' : sanitize_text_field( $args['postcode'] ),
		'country'    => empty( $args['country'] ) ? '' : sanitize_text_field( $args['country'] ),
		'website'    => empty( $args['website'] ) ? '' : esc_url_raw( $args['website'] ),
		'status'     => empty( $args['status'] ) ? 'inactive' : sanitize_key( $args['status'] ),
		'note'       => empty( $args['note'] ) ? '' : sanitize_textarea_field( $args['note'] ),
		'types'      => empty( $args['types'] ) ? array( 'customer' ) : $args['types'],
		'created_at' => empty( $args['created_at'] ) ? current_time( 'mysql' ) : sanitize_text_field( $args['created_at'] ),
		'updated_at' => current_time( 'mysql' ),
	);

	if ( ! empty( $user_id ) && ! get_user_by( 'ID', $user_id ) ) {
		return new WP_Error( 'invalid_wp_user_id', __( 'Invalid WP User ID', 'wp-eaccounting' ) );
	}

	//check if email duplicate
	if ( ! empty( $data['email'] ) ) {
		$email_duplicate = eaccounting_get_contact_by_email( $data['email'] );
		if ( $email_duplicate && $email_duplicate->id != $id ) {
			return new WP_Error( 'duplicate_email', __( 'The email address is already in used', 'wp-eaccounting' ) );
		}
	}

	if ( empty( $data['first_name'] ) || empty( $data['last_name'] ) ) {
		return new WP_Error( 'empty_name', __( 'First Name & Last Name is required', 'wp-eaccounting' ) );
	}

	//prepare types

	$types = [];
	if(is_array($data['types'])){
		foreach ( $data['types'] as $role ) {
			if ( array_key_exists( $role, eaccounting_get_contact_types() ) ) {
				$types[] = $role;
			}
		}
	}
	if ( empty( $types ) ) {
		$types = [ 'customer' ];
	}
	$data['types'] = maybe_serialize( $types );

	//user id
	if ( empty( $data['user_id'] ) ) {
		$data['user_id'] = '';
	}


	$where = array( 'id' => $id );
	$data  = wp_unslash( $data );

	if ( $update ) {
		do_action( 'eaccounting_pre_contact_update', $id, $data );
		if ( false === $wpdb->update( $wpdb->ea_contacts, $data, $where ) ) {
			return new WP_Error( 'db_update_error', __( 'Could not update note in the database', 'wp-ever-accounting' ), $wpdb->last_error );
		}
		do_action( 'eaccounting_contact_update', $id, $data, $item_before );
	} else {
		do_action( 'eaccounting_pre_contact_insert', $id, $data );
		if ( false === $wpdb->insert( $wpdb->ea_contacts, $data ) ) {

			return new WP_Error( 'db_insert_error', __( 'Could not insert account into the database', 'wp-ever-accounting' ), $wpdb->last_error );
		}
		$id = (int) $wpdb->insert_id;
		do_action( 'eaccounting_contact_insert', $id, $data );
	}

	return $id;
}


/**
 * @param $id
 * @param string $by
 *
 * @return array|object|void|null
 * @since 1.0.0
 */
function eaccounting_get_contact( $id, $by = 'id' ) {
	global $wpdb;
	switch ( $by ) {
		case 'user_id':
			$user_id = absint( $id );
			$sql     = "SELECT * FROM $wpdb->ea_contacts WHERE user_id = {$user_id} ";
			break;
		case 'email':
			$email = (string) $id;
			$sql   = "SELECT * FROM $wpdb->ea_contacts WHERE email = '{$email}'";
			break;
		case 'phone':
			$phone = (string) $id;
			$sql   = "SELECT * FROM $wpdb->ea_contacts WHERE phone = '{$phone}'";
			break;
		case 'id':
		default:
			$id  = absint( $id );
			$sql = "SELECT * FROM $wpdb->ea_contacts WHERE id = {$id} ";
			break;
	}

	return $wpdb->get_row( $sql );
}

/**
 * Get Contact by email
 *
 * since 1.0.0
 *
 * @param $email
 *
 * @return array|bool|object|void|null
 */
function eaccounting_get_contact_by_email( $email ) {
	global $wpdb;
	if ( ! $email = sanitize_email( $email ) ) {
		return false;
	}

	return $wpdb->get_row( $wpdb->prepare( "select * from {$wpdb->ea_contacts} where email=%s", $email ) );
}
