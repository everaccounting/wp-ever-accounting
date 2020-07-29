<?php
/**
 * EverAccounting Admin Functions
 *
 * @package  EverAccounting/Admin/Functions
 * @version  1.0.2
 */

defined( 'ABSPATH' ) || exit();


/**
 * Get all EverAccounting screen ids.
 *
 * @return array
 */
function eaccounting_get_screen_ids() {
	$eaccounting_screen_id = sanitize_title( __( 'EAccounting', 'wp-ever-accounting' ) );
	$screen_ids   = array(
		'toplevel_page_' . $eaccounting_screen_id,
		$eaccounting_screen_id . '_page_wc-reports',
		$eaccounting_screen_id . '_page_wc-shipping',
		$eaccounting_screen_id . '_page_wc-settings',
		$eaccounting_screen_id . '_page_wc-status',
		$eaccounting_screen_id . '_page_wc-addons',

	);

	return apply_filters( 'eaccounting_screen_ids', $screen_ids );
}
