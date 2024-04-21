<?php
/**
 * Updates functions.
 *
 * @since 1.0.0
 * @package EverAccounting\Functions
 */

defined( 'ABSPATH' ) || exit;

function eac_update_121_currency() {
	$options = get_option('eaccounting_currencies', array());
	foreach ($options as $option) {
		\EverAccounting\Models\Currency::insert($option);
	}
}
