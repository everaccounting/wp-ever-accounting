<?php

defined( 'ABSPATH' ) || exit();

/**
 * Insert Tax
 *
 * @param $args
 *
 * @return int|WP_Error
 * @since 1.0.0
 */
function eaccounting_insert_tax( $args ) {
	global $wpdb;
	$update = false;
	$id     = null;
	$args   = (array) apply_filters( 'ever_accounting_create_tax', $args );

	if ( isset( $args['id'] ) && ! empty( trim( $args['id'] ) ) ) {
		$id          = (int) $args['id'];
		$update      = true;
		$item_before = (array) eaccounting_get_tax( $id );
		if ( is_null( $item_before ) ) {
			return new \WP_Error( 'invalid_action', __( 'Could not find the item to  update', 'wp-ever-accounting' ) );
		}

		$args = array_merge( $item_before, $args );
	}

	error_log(print_r($args, true ));
	$data = array(
		'id'              => empty( $args['id'] ) ? null : absint( $args['id'] ),
		'name'            => !isset( $args['name'] ) ? '' : sanitize_text_field( $args['id'] ),
		'number'          => !isset( $args['number'] ) ? '' : sanitize_text_field( $args['number'] ),
		'currency_code'   => !isset( $args['currency_code'] ) ? '' : sanitize_text_field( $args['currency_code'] ),
		'opening_balance' => !isset( $args['opening_balance'] ) ? '0.00' : (double) $args['opening_balance'],
		'bank_name'       => !isset( $args['bank_name'] ) ? '' : sanitize_text_field( $args['bank_name'] ),
		'bank_phone'      => !isset( $args['bank_phone'] ) ? '' : sanitize_text_field( $args['bank_phone'] ),
		'bank_address'    => !isset( $args['bank_address'] ) ? '' : sanitize_textarea_field( $args['bank_address'] ),
		'enabled'         => '1' == $args['enabled'] ? '1' : '0',
		'updated_at'      => empty( $args['updated_at'] ) ? date( 'Y-m-d H:i:s' ) : $args['updated_at'],
		'created_at'      => empty( $args['created_at'] ) ? date( 'Y-m-d H:i:s' ) : $args['created_at'],
	);


	if ( empty( $data['name'] ) ) {
		return new WP_Error( 'empty_content', __( 'Empty name is not permitted', 'wp-ever-accounting' ) );
	}

	if ( empty( $data['currency_code'] ) ) {
		return new WP_Error( 'empty_content', __( 'Currency code is required', 'wp-ever-accounting' ) );
	}

	$where = array( 'id' => $id );
	$data  = wp_unslash( $data );

	if ( $update ) {
		do_action( 'ever_crm_pre_account_update', $id, $data );
		if ( false === $wpdb->update( $wpdb->ea_accounts, $data, $where ) ) {
			return new WP_Error( 'db_update_error', __( 'Could not update note in the database', 'wp-ever-crm' ), $wpdb->last_error );
		}
		do_action( 'ever_crm_account_update', $id, $data, $item_before );
	} else {
		do_action( 'ever_crm_pre_note_insert', $id, $data );
		if ( false === $wpdb->insert( $wpdb->ea_accounts, $data ) ) {
			return new WP_Error( 'db_insert_error', __( 'Could not insert note into the database', 'wp-ever-crm' ), $wpdb->last_error );
		}
		$id = (int) $wpdb->insert_id;
		do_action( 'ever_crm_note_insert', $id, $data );
	}

	return $id;
}

/**
 * Get account
 *
 * @param $id
 *
 * @return object|null
 * @since 1.0.0
 */
function eaccounting_get_tax( $id ) {
	global $wpdb;

	return $wpdb->get_row( $wpdb->prepare( "select * from {$wpdb->ea_accounts} where id=%s", $id ) );
}

/**
 * Delete account
 *
 * @param $id
 *
 * @return bool
 * @since 1.0.0
 */
function eaccounting_delete_tax( $id ) {
	global $wpdb;
	$id = absint( $id );

	$account = eaccounting_get_account( $id );
	if ( is_null( $account ) ) {
		return false;
	}

	do_action( 'ever_crm_pre_account_delete', $id, $account );
	if ( false == $wpdb->delete( $wpdb->ea_accounts, array( 'id' => $id ), array( '%d' ) ) ) {
		return false;
	}
	do_action( 'ever_crm_account_delete', $id, $account );

	return true;
}

function eaccounting_get_accounts( $args = true, $counts = false ) {

}
