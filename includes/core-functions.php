<?php
/**
 * Ever_Accounting Core Functions.
 *
 * General core functions available on both the front-end and admin.
 *
 * @since   1.0.0
 * @package Ever_Accounting
 */

defined( 'ABSPATH' ) || exit();

// Functions.
require_once EVER_ACCOUNTING_DIR . '/includes/deprecated/deprecated-functions.php';

/**
 * Get an option
 *
 * Looks to see if the specified setting exists, returns default if not
 *
 * @param string $key
 * @param bool $default
 *
 * @since 1.1.0
 *
 * @return mixed
 */
function ever_accounting_get_option( $key = '', $default = false ) {
	$option = get_option( 'ever_accounting_settings', array() );
	$value  = isset( $option[ $key ] ) ? $option[ $key ] : $default;
	$value  = apply_filters( 'ever_accounting_get_option', $value, $key, $default );

	return apply_filters( 'ever_accounting_get_option_' . $key, $value, $key, $default );
}
