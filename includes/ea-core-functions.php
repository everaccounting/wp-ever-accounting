<?php
/**
 * EverAccounting Core Functions.
 *
 * General core functions available on both the front-end and admin.
 *
 * @since   1.0.0
 * @package EverAccounting
 */

defined( 'ABSPATH' ) || exit();

function eaccounting_mail( $to, $subject, $message, $headers = "Content-Type: text/html\r\n", $attachments = '' ) {

}

/**
 * Get financial Start
 *
 *
 * @since 1.0.2
 * @return string
 */
function eaccounting_get_financial_start( $year = null, $format = 'Y-m-d' ) {
	$financial_start = eaccounting()->settings->get( 'financial_year_start', '01-01' );
	$setting         = explode( '-', $financial_start );
	$day             = ! empty( $setting[0] ) ? $setting[0] : '01';
	$month           = ! empty( $setting[1] ) ? $setting[1] : '01';
	$year            = empty( $year ) ? date( 'Y' ) : $year;

	$financial_year = new DateTime();
	$financial_year->setDate( $year, $month, $day );

	return $financial_year->format( $format );
}

/**
 * Get financial end date.
 *
 * @since 1.0.2
 *
 * @param string $format
 * @param null   $year
 *
 * @throws \Exception
 * @return string
 */
function eaccounting_get_financial_end( $year = null, $format = 'Y-m-d' ) {
	$dt = new \EverAccounting\DateTime( eaccounting_get_financial_start( $year, 'Y-m-d' ) );

	return $dt->addYear( 1 )->subDay( 1 )->date( $format );
}

/**
 * Queue some JavaScript code to be output in the footer.
 *
 * @since 1.0.2
 *
 * @param $code
 *
 * @return void
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
 *
 * @since 1.0.2
 * @return void
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
 * @since 1.0.2
 * @return int|mixed
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
 * @since 1.0.2
 *
 * @param string $code
 * @param bool   $convert
 *
 * @param mixed  $amount
 *
 * @return \EverAccounting\Money|WP_Error
 */
function eaccounting_get_money( $amount, $code = 'USD', $convert = false ) {
	try {
		return new \EverAccounting\Money( $amount, $code, $convert );
	} catch ( Exception $e ) {
		return new \WP_Error( 'invalid_action', $e->getMessage() );
	}
}


/**
 * Convert price from one currency to another currency.
 *
 * @since 1.0.2
 *
 * @param      $amount
 * @param      $from
 * @param      $to
 * @param      $rate
 * @param bool $format
 *
 * @param      $method
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
 * @since 1.0.2
 *
 * @param      $to
 * @param      $rate
 * @param bool $format
 *
 * @param      $amount
 *
 * @return float|int|string
 */
function eaccounting_price_convert_from_default( $amount, $to, $rate, $format = false, $default = null ) {
	$default = $default === null ? eaccounting()->settings->get( 'default_currency', 'USD' ) : $default;

	return __eaccounting_convert_price( 'multiply', $amount, $default, $to, $rate, $format );
}

/**
 * Convert price from other currency to default currency.
 *
 * @since 1.0.2
 *
 * @param      $from
 * @param      $rate
 * @param bool $format
 *
 * @param      $amount
 *
 * @return float|int|string
 */
function eaccounting_price_convert_to_default( $amount, $from, $rate, $format = false, $default = null ) {
	$default = $default === null ? eaccounting()->settings->get( 'default_currency', 'USD' ) : $default;

	return __eaccounting_convert_price( 'divide', $amount, $from, $default, $rate, $format );
}

/**
 * Convert amount from one currency to another.
 *
 * @since 1.0.2
 *
 * @param string $from_code
 * @param double $from_rate
 * @param string $to_code
 * @param double $to_rate
 * @param double $amount
 *
 * @return float|int|string
 */
function eaccounting_price_convert_between( $amount, $from_code, $from_rate, $to_code, $to_rate ) {
	$default_amount = $amount;

	if ( $from_code != eaccounting()->settings->get( 'default_currency', 'USD' ) ) {
		$default_amount = eaccounting_price_convert_to_default( $amount, $from_code, $from_rate );
	}

	$converted_amount = eaccounting_price_convert_from_default( $default_amount, $to_code, $to_rate, false, $from_code );

	return $converted_amount;
}

/**
 * Get payment methods.
 *
 *
 * @since 1.0.2
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
 * @since 1.0.2
 * @return \EverAccounting\Logger
 */
function eaccounting_logger() {
	return new \EverAccounting\Logger();
}

/**
 * Trigger logging cleanup using the logging class.
 *
 * @since 1.0.2
 */
function eaccounting_cleanup_logs() {
	$logger = new \EverAccounting\Logger();
	$logger->clear_expired_logs();
}

/**
 * Define a constant if it is not already defined.
 *
 * @since 1.0.2
 *
 * @param mixed  $value Value.
 *
 * @param string $name  Constant name.
 */
function eaccounting_maybe_define_constant( $name, $value ) {
	if ( ! defined( $name ) ) {
		define( $name, $value );
	}
}


/**
 * Sanitize values to an absolute number, rounded to the required decimal place
 *
 * Allows zero values, but ignores truly empty values.
 *
 * The correct type will be used automatically, depending on its value:
 *
 * - Whole numbers (including numbers with a 0 value decimal) will be return as int
 * - Decimal numbers will be returned as floats
 * - Decimal numbers ending with 0 will be returned as strings
 *
 * 1     => (int) 1
 * 1.0   => (int) 1
 * 0.00  => (int) 0
 * 1.01  => (float) 1.01
 * 1.019 => (float) 1.02
 * 1.1   => (string) 1.10
 * 1.10  => (string) 1.10
 * 1.199 => (string) 1.20
 *
 * @since 1.0.2
 *
 * @param int   $precision Number of required decimal places (optional)
 *
 * @param mixed $val
 *
 * @return mixed              Returns an int, float or string on success, null when empty
 */
function eaccounting_round_number( $val, $precision = 2 ) {

	// 0 is a valid value so we check only for other empty values
	if ( is_null( $val ) || '' === $val || false === $val ) {
		return;
	}

	$period_decimal_sep         = preg_match( '/\.\d{1,2}$/', $val );
	$comma_decimal_sep          = preg_match( '/\,\d{1,2}$/', $val );
	$period_space_thousands_sep = preg_match( '/\d{1,3}(?:[.|\s]\d{3})+/', $val );
	$comma_thousands_sep        = preg_match( '/\d{1,3}(?:,\d{3})+/', $val );

	// Convert period and space thousand separators.
	if ( $period_space_thousands_sep && 0 === preg_match( '/\d{4,}$/', $val ) ) {
		$val = str_replace( ' ', '', $val );

		if ( ! $comma_decimal_sep ) {
			if ( ! $period_decimal_sep ) {
				$val = str_replace( '.', '', $val );
			}
		} else {
			$val = str_replace( '.', ':', $val );
		}
	}

	// Convert comma decimal separators.
	if ( $comma_decimal_sep ) {
		$val = str_replace( ',', '.', $val );
	}

	// Clean up temporary replacements.
	if ( ( $period_space_thousands_sep && $comma_decimal_sep ) || $comma_thousands_sep ) {
		$val = str_replace( array( ':', ',' ), '', $val );
	}

	// Value cannot be negative
	$val = abs( floatval( $val ) );

	// Decimal precision must be a absolute integer
	$precision = absint( $precision );

	// Enforce the number of decimal places required (precision)
	$val = sprintf( ( round( $val, $precision ) == intval( $val ) ) ? '%d' : "%.{$precision}f", $val );

	// Convert number to the proper type (int, float, or string) depending on its value
	if ( false === strpos( $val, '.' ) ) {

		$val = absint( $val );

	}

	return $val;

}

/**
 * Makes internal API request for usages within PHP
 *
 *
 *
 * @since 1.0.2
 *
 * @param array  $args
 * @param string $method
 *
 * @param        $endpoint
 *
 * @return array
 */
function eaccounting_rest_request( $endpoint, $args = array(), $method = 'GET' ) {
	$request = new \WP_REST_Request( $method, $endpoint );
	$request->set_query_params( $args );
	$response = rest_do_request( $request );
	$server   = rest_get_server();
	$result   = $server->response_to_data( $response, false );

	return $result;
}

/**
 * Create a collection from the given value.
 *
 * @since 1.0.2
 *
 * @param mixed $items
 *
 * @return \EAccounting\Collection
 */
function eaccounting_collect( $items ) {
	return new \EAccounting\Collection( $items );
}
