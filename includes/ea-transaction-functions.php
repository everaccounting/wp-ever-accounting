<?php
/**
 * EverAccounting Transaction functions
 *
 * Functions for all kind of transaction of the plugin.
 *
 * @package EverAccounting/Functions
 * @version 1.0.2
 */

defined( 'ABSPATH' ) || exit;


/**
 * @return array
 * @since 1.0.2
 */
function eaccounting_get_transaction_types() {
	$types = array(
		'income'   => __( 'Income', 'wp-ever-accounting' ),
		'expense'  => __( 'Expense', 'wp-ever-accounting' ),
		'transfer' => __( 'Transfer', 'wp-ever-accounting' ),
	);

	return $types;
}
