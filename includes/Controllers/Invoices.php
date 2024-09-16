<?php

namespace EverAccounting\Controllers;

use EverAccounting\Models\Invoice;

defined( 'ABSPATH' ) || exit;

/**
 * Invoices controller.
 *
 * @since 1.0.0
 * @author  Sultan Nasir Uddin <manikdrmc@gmail.com>
 * @package EverAccounting
 * @subpackage Controllers
 */
class Invoices {

	/**
	 * Get an invoice from the database.
	 *
	 * @param mixed $invoice Invoice ID or object.
	 *
	 * @since 1.1.6
	 * @return Invoice|null Invoice object if found, otherwise null.
	 */
	public function get( $invoice ) {
		return Invoice::find( $invoice );
	}

	/**
	 * Insert a new invoice into the database.
	 *
	 * @param array $data Invoice data.
	 * @param bool  $wp_error Optional. Whether to return a WP_Error on failure. Default false.
	 *
	 * @since 1.1.0
	 * @return Invoice|false|\WP_Error Invoice object on success, false or WP_Error on failure.
	 */
	public function insert( $data, $wp_error = true ) {
		return Invoice::insert( $data, $wp_error );
	}

	/**
	 * Delete an invoice from the database.
	 *
	 * @param int $id Invoice ID.
	 *
	 * @since 1.1.0
	 * @return bool True on success, false on failure.
	 */
	public function delete( $id ) {
		$invoice = $this->get( $id );
		if ( ! $invoice ) {
			return false;
		}

		return $invoice->delete();
	}

	/**
	 * Get query results for invoices.
	 *
	 * @param array $args Query arguments.
	 * @param bool  $count Optional. Whether to return only the total found invoices for the query.
	 *
	 * @since 1.1.0
	 * @return array|int|Invoice[] Array of invoice objects, the total found invoices for the query, or the total found invoices for the query as int when `$count` is true.
	 */
	public function query( $args = array(), $count = false ) {
		if ( $count ) {
			return Invoice::count( $args );
		}

		return Invoice::results( $args );
	}
}
