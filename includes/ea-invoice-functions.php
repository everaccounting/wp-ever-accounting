<?php
/**
 * EverAccounting invoice Functions.
 *
 * All invoice related function of the plugin.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

use EverAccounting\Invoice;
use EverAccounting\Invoice_Item;

defined( 'ABSPATH' ) || exit;


/**
 * Retrieves invoice data given a invoice id or invoice object.
 *
 * @param int|object|Invoice $invoice invoice to retrieve
 * @param string $output The required return type. One of OBJECT, ARRAY_A, or ARRAY_N. Default OBJECT.
 *
 * @return Invoice|array|null
 * @since 1.1.0
 */
function eaccounting_get_invoice( $invoice, $output = OBJECT ) {
	if ( empty( $invoice ) ) {
		return null;
	}

	if ( $invoice instanceof Invoice ) {
		$_invoice = $invoice;
	} else {
		$_invoice = new Invoice( $invoice );
	}

	if ( !$_invoice->exists() ) {
		return null;
	}

	if ( ARRAY_A === $output ) {
		return $_invoice->to_array();
	}

	if ( ARRAY_N === $output ) {
		return array_values( $_invoice->to_array() );
	}

	return $_invoice;
}

/**
 *  Insert or update a invoice.
 *
 * @param array|object|Invoice $data An array, object, or invoice object of data arguments.
 *
 * @return Invoice|WP_Error The invoice object or WP_Error otherwise.
 * @global wpdb $wpdb WordPress database abstraction object.
 * @since 1.1.0
 */
function eaccounting_insert_invoice( $data ) {
	if ( $data instanceof Invoice ) {
		$data = $data->to_array();
	} elseif ( is_object( $data ) ) {
		$data = get_object_vars( $data );
	}

	if ( empty( $data ) || ! is_array( $data ) ) {
		return new WP_Error( 'invalid_invoice_data', __( 'Invoice could not be saved.', 'wp-ever-accounting' ) );
	}

	$data = wp_parse_args( $data, array( 'id' => null ) );
	$invoice = new Invoice( (int) $data['id'] );
	$invoice->set_props( $data );
	$is_error = $invoice->save();
	if ( is_wp_error( $is_error ) ) {
		return $is_error;
	}

	return $invoice;
}

/**
 * Delete an invoice.
 *
 * @param int $invoice_id Invoice ID
 *
 * @return array|false Invoice array data on success, false on failure.
 * @since 1.1.0
 */
function eaccounting_delete_invoice( $invoice_id ) {
	if ( $invoice_id instanceof Invoice ) {
		$invoice_id = $invoice_id->get_id();
	}

	if ( empty( $invoice_id ) ) {
		return false;
	}

	$invoice = new Invoice( (int) $invoice_id );
	if ( ! $invoice->exists() ) {
		return false;
	}

	return $invoice->delete();
}

/**
 * Retrieves an array of the invoices matching the given criteria.
 *
 * @param array $args Arguments to retrieve invoices.
 *
 * @return Invoice[]|int Array of invoice objects or count.
 * @since 1.1.0
 *
 */
function eaccounting_get_invoices( $args = array() ) {
	$defaults = array(
		'number'        => 20,
		'orderby'       => 'name',
		'order'         => 'DESC',
		'include'       => array(),
		'exclude'       => array(),
		'no_found_rows' => false,
		'count_total'   => false,
	);

	$parsed_args = wp_parse_args( $args, $defaults );
	$query       = new \EverAccounting\Invoice_Query( $parsed_args );
	if ( true === $parsed_args['count_total'] ) {
		return $query->get_total();
	}


	return $query->get_results();
}

/**
 * Get array of invoice items.
 *
 * @param int $invoice_id Invoice item.
 *
 * @return Invoice_Item[]
 */
function eaccounting_get_invoice_items( $invoice_id ) {
	global $wpdb;
	$invoice_id = (int) $invoice_id;
	if ( empty( $invoice_id ) ) {
		return [];
	}
	$last_changed = wp_cache_get_last_changed( 'ea_invoices' );
	$cache_key    = "ea_invoice_items:$invoice_id:$last_changed";
	$items        = wp_cache_get( $cache_key, 'ea_invoice_items_query' );
	if ( false === $items ) {
		$items = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_invoice_items WHERE invoice_id=%d", $invoice_id ) );
		wp_cache_set( $cache_key, $items, 'ea_invoice_items_query' );
	}

	foreach ( $items as $key => $row ) {
		wp_cache_set( $row->id, $row, 'ea_invoice_items' );
		$item = new Invoice_Item( 0 );
		$item->set_props( $row );
		$item->set_object_read( true );
		$items[ $key ] = $item;
	}

	return $items;
}

/**
 * Deletes the invoice items from database.
 *
 * @return bool true on success, false on failure.
 * @since 1.1.0
 */
function eaccounting_delete_invoice_items( $invoice_id ) {
	global $wpdb;
	$invoice_id = (int) $invoice_id;
	if ( empty( $invoice_id ) ) {
		return false;
	}

	/**
	 * Fires before invoice items is deleted.
	 *
	 * @param int $invoice_id Invoice id.
	 *
	 * @since 1.2.1
	 *
	 */
	do_action( 'eaccounting_before_delete_invoice_items', $invoice_id );

	$result = $wpdb->delete( $wpdb->prefix . 'ea_invoice_items', array( 'invoice_id' => $invoice_id ) );
	if ( ! $result ) {
		return false;
	}

	/**
	 * Fires after invoice items is deleted.
	 *
	 * @param int $invoice_id Invoice id.
	 *
	 * @since 1.2.1
	 */
	do_action( 'eaccounting_delete_invoice_items', $invoice_id );

	wp_cache_set( 'last_changed', microtime(), 'ea_invoice_items' );

	return true;
}


/**
 * Deletes the invoice notes from database.
 *
 * @return bool true on success, false on failure.
 * @since 1.1.0
 */
function eaccounting_delete_invoice_notes( $invoice_id ) {
	global $wpdb;
	$invoice_id = (int) $invoice_id;
	if ( empty( $invoice_id ) ) {
		return false;
	}

	/**
	 * Fires before invoice notes is deleted.
	 *
	 * @param int $invoice_id Invoice id.
	 *
	 * @since 1.2.1
	 *
	 */
	do_action( 'eaccounting_before_delete_invoice_notes', $invoice_id );

	$result = $wpdb->delete( $wpdb->prefix . 'ea_notes', array( 'parent_id' => $invoice_id, 'type' => 'invoice' ) );
	if ( ! $result ) {
		return false;
	}

	/**
	 * Fires after invoice notes is deleted.
	 *
	 * @param int $invoice_id Invoice id.
	 *
	 * @since 1.2.1
	 */
	do_action( 'eaccounting_delete_invoice_notes', $invoice_id );

	wp_cache_set( 'last_changed', microtime(), 'ea_invoice_notes' );

	return true;
}