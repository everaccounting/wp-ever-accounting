<?php
defined( 'ABSPATH' ) || exit();

function eaccounting_insert_reconciliations( $args ) {
	global $wpdb;
	$update = false;
	$id     = null;
	$args   = (array) apply_filters( 'eaccounting_create_reconciliation', $args );

	if ( isset( $args['id'] ) && ! empty( trim( $args['id'] ) ) ) {
		$id          = absint( $args['id'] );
		$update      = true;
		$item_before = (array) eaccounting_get_reconciliation( $id );
		if ( is_null( $item_before ) ) {
			return new \WP_Error( 'invalid_action', __( 'Could not find the item to  update', 'wp-ever-accounting' ) );
		}

		$args = array_merge( $item_before, $args );
	}




}

function eaccounting_get_reconciliation( $args ) {
}

function eaccounting_delete_reconciliation( $args ) {
}

function eaccounting_get_reconciliations( $args ) {
}
