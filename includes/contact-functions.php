<?php
defined( 'ABSPATH' ) || exit();

/**
 * Get contact types
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
		'id'            => $id,
		'user_id'       => empty( $args['user_id'] ) ? '' : absint( $args['user_id'] ),
		'name'          => empty( $args['name'] ) ? '' : sanitize_text_field( $args['name'] ),
		'email'         => empty( $args['email'] ) ? '' : sanitize_email( $args['email'] ),
		'phone'         => empty( $args['phone'] ) ? '' : sanitize_text_field( $args['phone'] ),
		'fax_number'    => empty( $args['fax_number'] ) ? '' : sanitize_text_field( $args['fax_number'] ),
		'birth_date'    => empty( $args['birth_date'] ) ? '' : sanitize_text_field( $args['birth_date'] ),
		'address'       => empty( $args['address'] ) ? '' : sanitize_textarea_field( $args['address'] ),
		'country'       => empty( $args['country'] ) ? '' : sanitize_text_field( $args['country'] ),
		'website'       => empty( $args['website'] ) ? '' : esc_url_raw( $args['website'] ),
		'note'          => empty( $args['note'] ) ? '' : sanitize_textarea_field( $args['note'] ),
		'tax_number'    => empty( $args['tax_number'] ) ? '' : sanitize_text_field( $args['tax_number'] ),
		'currency_code' => empty( $args['currency_code'] ) ? '' : sanitize_text_field( $args['currency_code'] ),
		'type'          => empty( $args['type'] ) ? 'customer' : sanitize_text_field( $args['type'] ),
		'file_id'       => empty( $args['file_id'] ) ? '' : intval( $args['file_id'] ),
		'enabled'       => isset( $args['enabled'] ) ? intval( $args['enabled'] ) : 1,
		'creator_id'    => empty( $args['creator_id'] ) ? get_current_user_id() : $args['creator_id'],
		'created_at'    => empty( $args['created_at'] ) ? date( 'Y-m-d H:i:s' ) : sanitize_text_field( $args['created_at'] ),
	);

	if ( ! empty( $user_id ) && ! get_user_by( 'ID', $user_id ) ) {
		return new WP_Error( 'invalid_wp_user_id', __( 'Invalid WP User ID', 'wp-ever-accounting' ) );
	}

	//check if email duplicate
	if ( ! empty( $data['email'] ) ) {
		$email_duplicate = eaccounting_get_contact( $data['email'], 'email' );
		if ( $email_duplicate && $email_duplicate->id != $id && $email_duplicate->type == $args['type'] ) {
			return new WP_Error( 'duplicate_email', __( 'The email address is already in used', 'wp-ever-accounting' ) );
		}
	}

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
function eaccounting_get_contact( $id, $by = 'id', $type = null ) {
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
 * Delete Contact
 *
 * @param $id
 *
 * @return bool|WP_Error
 * @since 1.0.0
 */
function eaccounting_delete_contact( $id ) {
	global $wpdb;
	$id = absint( $id );

	$account = eaccounting_get_contact( $id );
	if ( is_null( $account ) ) {
		return false;
	}

	$tables = [
		$wpdb->ea_payments => 'contact_id',
		$wpdb->ea_revenues => 'contact_id',
		$wpdb->ea_invoices => 'contact_id'
	];
	foreach ( $tables as $table => $column ) {
		if ( $wpdb->get_var( $wpdb->prepare( "SELECT count(id) FROM $table WHERE $column = %d", $id ) ) ) {
			return new WP_Error( 'not-permitted', __( 'Major dependencies are associated with contacts, you are not permitted to delete them.', 'wp-ever-accounting' ) );
		}

	}

	do_action( 'eaccounting_pre_contact_delete', $id, $account );
	if ( false == $wpdb->delete( $wpdb->ea_contacts, array( 'id' => $id ), array( '%d' ) ) ) {
		return false;
	}
	do_action( 'eaccounting_contact_delete', $id, $account );

	return true;
}


/**
 * Get contacts
 * since 1.0.0
 *
 * @param array $args
 * @param bool $count
 *
 * @return array|object|string|null
 */
function eaccounting_get_contacts( $args = array(), $count = false ) {
	global $wpdb;
	$query_fields  = '';
	$query_from    = '';
	$query_where   = '';
	$query_orderby = '';
	$query_limit   = '';

	$default = array(
		'include'        => array(),
		'exclude'        => array(),
		'search'         => '',
		'type'           => '',
		'orderby'        => 'name',
		'order'          => 'ASC',
		'fields'         => 'all',
		'search_columns' => array( 'name', 'email', 'phone', 'address', 'note' ),
		'per_page'       => 20,
		'page'           => 1,
		'offset'         => 0,
	);

	$args        = wp_parse_args( $args, $default );
	$query_from  = "FROM $wpdb->ea_contacts";
	$query_where = 'WHERE 1=1';


	//type
	if ( ! empty( $args['type'] ) && array_key_exists( $args['type'], eaccounting_get_contact_types() ) ) {
		$type        = '%' . $wpdb->esc_like( $args['type'] ) . '%';
		$query_where .= $wpdb->prepare( " AND $wpdb->ea_contacts.type LIKE %s ", $type );
	}


	//opening_balance
	if ( ! empty( $args['date_created'] ) ) {
		$date_created_check = trim( $args['date_created'] );
		$date_created       = preg_replace( '#[^0-9\-]#', '', $date_created_check );

		if ( strpos( $date_created_check, '>' ) !== false ) {
			$query_where .= $wpdb->prepare( " AND $wpdb->ea_contacts.created_at > %f ", $date_created );
		} elseif ( strpos( $date_created_check, '<' ) !== false ) {
			$query_where .= $wpdb->prepare( " AND $wpdb->ea_contacts.created_at < %f ", $date_created );
		} else {
			$query_where .= $wpdb->prepare( " AND $wpdb->ea_contacts.created_at = %f ", $date_created );
		}
	}

	//fields
	if ( is_array( $args['fields'] ) ) {
		$args['fields'] = array_unique( $args['fields'] );

		$query_fields = array();
		foreach ( $args['fields'] as $field ) {
			$field          = 'id' === $field ? 'id' : sanitize_key( $field );
			$query_fields[] = "$wpdb->ea_contacts.$field";
		}
		$query_fields = implode( ',', $query_fields );
	} elseif ( 'all' == $args['fields'] ) {
		$query_fields = "$wpdb->ea_contacts.*";
	} else {
		$query_fields = "$wpdb->ea_contacts.id";
	}

	//include
	$include = false;
	if ( ! empty( $args['include'] ) ) {
		$include = wp_parse_id_list( $args['include'] );
	}

	if ( ! empty( $include ) ) {
		// Sanitized earlier.
		$ids         = implode( ',', $include );
		$query_where .= " AND $wpdb->ea_contacts.id IN ($ids)";
	} elseif ( ! empty( $args['exclude'] ) ) {
		$ids         = implode( ',', wp_parse_id_list( $args['exclude'] ) );
		$query_where .= " AND $wpdb->ea_contacts.id NOT IN ($ids)";
	}

	//search
	$search = '';
	if ( isset( $args['search'] ) ) {
		$search = trim( $args['search'] );
	}
	if ( $search ) {
		$searches = array();
		$cols     = array_map( 'sanitize_key', $args['search_columns'] );
		$like     = '%' . $wpdb->esc_like( $search ) . '%';
		foreach ( $cols as $col ) {
			$searches[] = $wpdb->prepare( "$col LIKE %s", $like );
		}

		$query_where .= ' AND (' . implode( ' OR ', $searches ) . ')';
	}


	//ordering
	$order         = isset( $args['order'] ) ? esc_sql( strtoupper( $args['order'] ) ) : 'ASC';
	$order_by      = esc_sql( $args['orderby'] );
	$query_orderby = sprintf( " ORDER BY %s %s ", $order_by, $order );

	// limit
	if ( isset( $args['per_page'] ) && $args['per_page'] > 0 ) {
		if ( $args['offset'] ) {
			$query_limit = $wpdb->prepare( 'LIMIT %d, %d', $args['offset'], $args['per_page'] );
		} else {
			$query_limit = $wpdb->prepare( 'LIMIT %d, %d', $args['per_page'] * ( $args['page'] - 1 ), $args['per_page'] );
		}
	}

	if ( $count ) {
		return $wpdb->get_var( "SELECT count($wpdb->ea_contacts.id) $query_from $query_where" );
	}

	$request = "SELECT $query_fields $query_from $query_where $query_orderby $query_limit";

	if ( is_array( $args['fields'] ) || 'all' == $args['fields'] ) {
		return $wpdb->get_results( $request );
	}

	return $wpdb->get_col( $request );
}

