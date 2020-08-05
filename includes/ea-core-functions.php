<?php
/**
 * EverAccounting Core Functions
 *
 * General core functions available on both the front-end and admin.
 *
 * @package EverAccounting\Functions
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit();

function eaccounting_mail( $to, $subject, $message, $headers = "Content-Type: text/html\r\n", $attachments = '' ) {

}

/**
 * Queue some JavaScript code to be output in the footer.
 *
 * @param $code
 *
 * @return void
 * @since 1.0.2
 */
function eaccounting_enqueue_js( $code ) {
	global $eaccounting_queued_js;

	if ( empty( $eaccounting_queued_js ) ) {
		$eaccounting_queued_js = '';
	}

	$eaccounting_queued_js .= "\n" . $code . "\n";
}

/**
 * Output any queued javascript code in the footer.
 * @return void
 * @since 1.0.2
 */
function eaccounting_print_js() {
	global $eaccounting_queued_js;

	if ( ! empty( $eaccounting_queued_js ) ) {
		// Sanitize.
		$eaccounting_queued_js = wp_check_invalid_utf8( $eaccounting_queued_js );
		$eaccounting_queued_js = preg_replace( '/&#(x)?0*(?(1)27|39);?/i', "'", $eaccounting_queued_js );
		$eaccounting_queued_js = str_replace( "\r", '', $eaccounting_queued_js );

		$js = "<!-- EverAccounting JavaScript -->\n<script type=\"text/javascript\">\njQuery(function($) { $eaccounting_queued_js });\n</script>\n";

		echo apply_filters( 'eaccounting_queued_js', $js ); // WPCS: XSS ok.

		unset( $eaccounting_queued_js );
	}
}


/**
 * Get the current user ID.
 *
 * The function is being used for inserting
 * the creator id of object over the plugin.
 *
 * @return int|mixed
 * @since 1.0.2
 */
function eaccounting_get_current_user_id() {
	$user_id = get_current_user_id();
	if ( empty( $user_id ) ) {
		$user = get_user_by( 'email', get_option( 'admin_email' ) );
		if ( $user && in_array( 'administrator', $user->roles ) ) {
			$user_id = $user->ID;
		}
	}

	if ( empty( $user_id ) ) {
		$users   = get_users( [
			'role'   => 'administrator',
			'fields' => 'ID'
		] );
		$user_id = reset( $users );
	}

	return $user_id;
}


/**
 * Instance of money class.
 *
 * For formatting with currency code
 * eaccounting_get_money( 100000, 'USD', true )->format()
 * For inserting into database
 * eaccounting_get_money( "$100,000", "USD", false )->getAmount()
 *
 * @param mixed $amount
 * @param string $currency
 * @param bool $convert
 *
 * @return EAccounting_Money|WP_Error
 */
function eaccounting_get_money( $amount, $currency = 'USD', $convert = false ) {
	try {
		$currency_object = new EAccounting_Currency();
		$currency_object->set_code( $currency );

		return new EAccounting_Money( $amount, $currency_object, $convert );
	} catch ( Exception $e ) {
		return new WP_Error( 'invalid_action', $e->getMessage() );
	}
}


/**
 * Convert price from one currency to another currency.
 *
 * @param $method
 * @param $amount
 * @param $from
 * @param $to
 * @param $rate
 * @param bool $format
 *
 * @return float|int|string
 */
function __eaccounting_convert_price( $method, $amount, $from, $to, $rate, $format = false ) {
	$money = eaccounting_get_money( $amount, $to );
	// No need to convert same currency
	if ( $from == $to ) {
		return $format ? $money->format() : $money->getAmount();
	}

	try {
		$money = $money->$method( (double) $rate );
	} catch ( Exception $e ) {
		return 0;
	}

	return $format ? $money->format() : $money->getAmount();
}


/**
 * Convert price from default to any other currency.
 *
 * @param $amount
 * @param $to
 * @param $rate
 * @param bool $format
 *
 * @return float|int|string
 * @since 1.0.2
 */
function eaccounting_price_convert_from_default( $amount, $to, $rate, $format = false ) {
	$code = eaccounting_get_default_currency();

	return __eaccounting_convert_price( 'multiply', $amount, $code, $to, $rate, $format );
}

/**
 * Convert price from other currency to default currency.
 *
 * @param $amount
 * @param $from
 * @param $rate
 * @param bool $format
 *
 * @return float|int|string
 * @since 1.0.2
 *
 */
function eaccounting_price_convert_to_default( $amount, $from, $rate, $format = false ) {
	$code = eaccounting_get_currency_code();

	return __eaccounting_convert_price( 'divide', $amount, $from, $code, $rate, $format );
}


/**
 * Get the plugin default currency code.
 *
 * @return string
 * @since 1.0.0
 */
function eaccounting_get_currency_code() {
	return apply_filters( 'eaccounting_currency_code', 'USD' );
}

/**
 * Get payment methods.
 *
 * since 1.0.0
 * @return array
 */
function eaccounting_get_payment_methods() {
	return apply_filters( 'eaccounting_payment_methods', [
		'cash'          => __( 'Cash', 'wp-ever-accounting' ),
		'bank_transfer' => __( 'Bank Transfer', 'wp-ever-accounting' ),
		'check'         => __( 'Cheque', 'wp-ever-accounting' ),
	] );
}

/**
 * Get the logger of the plugin.
 *
 * @return EAccounting_Logger
 * @since 1.0.2
 */
function eaccounting_logger() {
	return new EAccounting_Logger();
}

/**
 * Trigger logging cleanup using the logging class.
 *
 * @since 1.0.2
 */
function wc_cleanup_logs() {
	$logger = new EAccounting_Logger();
	$logger->clear_expired_logs();
}

/**
 * Define a constant if it is not already defined.
 *
 * @since 1.0.2
 * @param string $name  Constant name.
 * @param mixed  $value Value.
 */
function eaccounting_maybe_define_constant( $name, $value ) {
	if ( ! defined( $name ) ) {
		define( $name, $value );
	}
}
