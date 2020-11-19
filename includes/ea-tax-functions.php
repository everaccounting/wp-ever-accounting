<?php
/**
 * EverAccounting Tax functions.
 *
 * Functions related to taxes.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

use EverAccounting\Exception;
use EverAccounting\Tax;

defined( 'ABSPATH' ) || exit;

/**
 * Get tax types.
 *
 * @return array
 * @since 1.1.0
 */
function eaccounting_get_tax_types() {
	$types = array(
		'fixed'     => __( 'Fixed', 'wp-ever-accounting' ),
		'normal'    => __( 'Normal', 'wp-ever-accounting' ),
		'inclusive' => __( 'Inclusive', 'wp-ever-accounting' ),
		'compound'  => __( 'Compound', 'wp-ever-accounting' ),
	);

	return apply_filters( 'eaccounting_tax_types', $types );
}

/**
 * Main function for returning tax.
 *
 * @param $tax
 *
 * @return \EverAccounting\Tax|null
 * @since 1.1.0
 *
 */
function eaccounting_get_tax( $tax ) {

}

/**
 *  Create new tax programmatically.
 *
 *  Returns a new tax object on success.
 *
 * @param array $args {
 *  An array of elements that make up an invoice to update or insert.
 *
 * @type int $id The tax ID. If equal to something other than 0,
 *                                         the tax with that id will be updated. Default 0.
 *
 * @type string $name The name of the tax.
 * @type double $rate The rate of the tax.
 * @type string $type The type for the tax.
 * @type int $enabled Status of the tax
 * }
 *
 * @return Tax|WP_Error
 * @since 1.1.0
 *
 */
function eaccounting_insert_tax( $args ) {

}

/**
 * Delete an tax.
 *
 * @param $tax_id
 *
 * @return bool
 * @since 1.1.0
 *
 */
function eaccounting_delete_tax( $tax_id ) {
}

/**
 * Get taxes.
 *
 * @param array $args {
 *
 * @type string $name The name of the tax.
 * @type double $rate The rate of the tax.
 * @type string $type The type for the tax.
 * @type int $enabled Status of the tax.
 * }
 *
 * @param bool $callback
 *
 * @return array|int
 * @since 1.1.0
 */
function eaccounting_get_taxes( $args = array(), $callback = true ) {

}
