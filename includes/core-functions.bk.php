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
//require_once EVER_ACCOUNTING_ABSPATH . '/includes/ea-update-functions.php';
// require_once EACCOUNTING_ABSPATH . '/includes/ea-account-functions.php';
//require_once EACCOUNTING_ABSPATH . '/includes/ea-misc-functions.php';
//require_once EACCOUNTING_ABSPATH . '/includes/ea-formatting-functions.php';
//require_once EACCOUNTING_ABSPATH . '/includes/ea-rest-functions.php';
//require_once EACCOUNTING_ABSPATH . '/includes/ea-form-functions.php';
//require_once EACCOUNTING_ABSPATH . '/includes/ea-file-functions.php';
// require_once EACCOUNTING_ABSPATH . '/includes/ea-currency-functions.php';
//require_once EACCOUNTING_ABSPATH . '/includes/ea-transaction-functions.php';
// require_once EACCOUNTING_ABSPATH . '/includes/ea-category-functions.php';
// require_once EACCOUNTING_ABSPATH . '/includes/ea-contact-functions.php';
// require_once EACCOUNTING_ABSPATH . '/includes/ea-notes-functions.php';
//require_once EACCOUNTING_ABSPATH . '/includes/ea-deprecated-functions.php';
// require_once EACCOUNTING_ABSPATH . '/includes/ea-item-functions.php';
//require_once EACCOUNTING_ABSPATH . '/includes/ea-tax-functions.php';
//require_once EACCOUNTING_ABSPATH . '/includes/ea-document-functions.php';
//require_once EACCOUNTING_ABSPATH . '/includes/ea-template-functions.php';
//require_once EACCOUNTING_ABSPATH . '/includes/functions/deprecated.php';

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

/**
 * Update option.
 *
 * @param $key
 * @param $value
 *
 * @since 1.1.0
 */
function ever_accounting_update_option( $key, $value ) {
	return eaccounting()->settings->set( array( $key => $value ), true );
}

/**
 * Get financial Start
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

	$financial_year = new \Ever_Accounting\DateTime();
	$financial_year->setDate( $year, $month, $day );

	return $financial_year->format( $format );
}

/**
 * Get financial end date.
 *
 * @param string $format
 * @param null $year
 *
 * @since 1.0.2
 *
 * @throws \Exception
 * @return string
 */
function eaccounting_get_financial_end( $year = null, $format = 'Y-m-d' ) {
	$dt = new \Ever_Accounting\DateTime( eaccounting_get_financial_start( $year, 'Y-m-d' ) );
	//  if ( $dt->copy()->addYear( 1 )->subDay( 1 )->getTimestamp() > strtotime(date_i18n('Y-m-d H:i')) ) {
	//      $today = new \Ever_Accounting\DateTime( 'now' );
	//      return $today->date( $format );
	//  }
	return $dt->addYear( 1 )->subDay( 1 )->date( $format );
}

/**
 * Instance of money class.
 *
 * For formatting with currency code
 * eaccounting_money( 100000, 'USD', true )->format()
 * For inserting into database
 * eaccounting_money( "$100,000", "USD", false )->getAmount()
 *
 * @param string $code
 * @param bool $convert
 *
 * @param mixed $amount
 *
 * @since 1.0.2
 *
 * @return \Ever_Accounting\Money|WP_Error
 */
function eaccounting_money( $amount, $code = 'USD', $convert = false ) {
	try {
		return new \Ever_Accounting\Money( $amount, $code, $convert );
	} catch ( Exception $e ) {
		return new \WP_Error( 'invalid_action', $e->getMessage() );
	}
}

/**
 * Get default currency.
 *
 * @since 1.1.0
 * @return string
 */
function eaccounting_get_default_currency() {
	$currency = eaccounting()->settings->get( 'default_currency', 'USD' );

	return apply_filters( 'eaccounting_default_currency', $currency );
}


/**
 * Format price with currency code & number format
 *
 * @param string $amount
 *
 * @param string $code If not passed will be used default currency.
 *
 * @since 1.0.2
 *
 * @return string
 */
function eaccounting_format_price( $amount, $code = null ) {
	if ( is_null( $code ) ) {
		$code = eaccounting_get_default_currency();
	}

	$amount = eaccounting_money( $amount, $code, true );
	if ( is_wp_error( $amount ) ) {
		/* translators: %s currency code */
		eaccounting_logger()->log_alert( sprintf( __( 'invalid currency code %s', 'wp-ever-accounting' ), $code ) );

		return '00.00';
	}

	return $amount->format();
}

/**
 * Sanitize price for inserting into database
 *
 * @param string $amount
 *
 * @param string $code If not passed will be used default currency.
 *
 * @since 1.0.2
 *
 * @return float|int
 */
function eaccounting_sanitize_price( $amount, $code = null ) {
	$amount = eaccounting_money( $amount, $code, false );
	if ( is_wp_error( $amount ) ) {
		/* translators: %s currency code */
		eaccounting_logger()->log_alert( sprintf( __( 'invalid currency code %s', 'wp-ever-accounting' ), $code ) );

		return 0;
	}

	return $amount->getAmount();
}

/**
 * Wrapper for sanitize and formatting.
 * If needs formatting with symbol $get_value = false otherwise true.
 *
 * @param null $code
 * @param false $get_value
 * @param string $amount
 *
 * @since 1.1.0
 *
 * @return float|int|string
 */
function eaccounting_price( $amount, $code = null, $get_value = false ) {
	if ( $get_value ) {
		return eaccounting_sanitize_price( $amount, $code );
	}

	return eaccounting_format_price( $amount, $code );
}

/**
 * Convert price from default to any other currency.
 *
 * @param string $amount
 *
 * @param string $to
 * @param string $to_rate
 *
 * @since 1.0.2
 *
 * @return float|int|string
 */
function eaccounting_price_from_default( $amount, $to, $to_rate ) {
	$default = eaccounting_get_default_currency();
	$money   = eaccounting_money( $amount, $to );
	// No need to convert same currency
	if ( $default === $to ) {
		return $money->getAmount();
	}

	try {
		$money = $money->multiply( (float) $to_rate );
	} catch ( Exception $e ) {
		return 0;
	}

	return $money->getAmount();
}

/**
 * Convert price from other currency to default currency.
 *
 * @param      $amount
 *
 * @param      $from
 * @param      $from_rate
 *
 * @since 1.0.2
 *
 * @return float|int|string
 */
function eaccounting_price_to_default( $amount, $from, $from_rate ) {
	$default = eaccounting_get_default_currency();
	$money   = eaccounting_money( $amount, $from );
	// No need to convert same currency
	if ( $default === $from ) {
		return $money->getAmount();
	}

	try {
		$money = $money->divide( (float) $from_rate );
	} catch ( Exception $e ) {
		return 0;
	}

	return $money->getAmount();
}

/**
 * Convert price convert between currency.
 *
 * @param      $from
 * @param null $to
 * @param null $from_rate
 * @param null $to_rate
 * @param      $amount
 *
 * @since 1.1.0
 *
 * @return float|int|string
 */
function eaccounting_price_convert( $amount, $from, $to = null, $from_rate = null, $to_rate = null ) {
	$default = eaccounting_get_default_currency();
	if ( is_null( $to ) ) {
		$to = $default;
	}

	if ( is_null( $from_rate ) ) {
		$from      = eaccounting_get_currency( $from );
		$from_rate = $from->get_rate();
	}
	if ( is_null( $to_rate ) ) {
		$to      = eaccounting_get_currency( $to );
		$to_rate = $to->get_rate();
	}

	if ( $from !== $default ) {
		$amount = eaccounting_price_to_default( $amount, $from, $from_rate );
	}

	return eaccounting_price_from_default( $amount, $to, $to_rate );
}


/**
 * Get payment methods.
 *
 * @since 1.0.2
 * @return array
 */
function eaccounting_get_payment_methods() {
	return apply_filters(
		'eaccounting_payment_methods',
		array(
			'cash'          => __( 'Cash', 'wp-ever-accounting' ),
			'bank_transfer' => __( 'Bank Transfer', 'wp-ever-accounting' ),
			'check'         => __( 'Cheque', 'wp-ever-accounting' ),
		)
	);
}

/**
 * Get the logger of the plugin.
 *
 * @since 1.0.2
 * @return \Ever_Accounting\Logger
 */
function eaccounting_logger() {
	return eaccounting()->logger;
}

/**
 * Trigger logging cleanup using the logging class.
 *
 * @since 1.0.2
 */
function eaccounting_cleanup_logs() {
	$logger = new \Ever_Accounting\Logger();
	$logger->clear_expired_logs();
}

/**
 * Define a constant if it is not already defined.
 *
 * @param mixed $value Value.
 *
 * @param string $name Constant name.
 *
 * @since 1.0.2
 *
 */
function eaccounting_maybe_define_constant( $name, $value ) {
	if ( ! defined( $name ) ) {
		define( $name, $value );
	}
}

/**
 * Create a collection from the given value.
 *
 * @param mixed $items
 *
 * @since 1.0.2
 *
 * @return \Ever_Accounting\Collection
 */
function eaccounting_collect( $items ) {
	return new \Ever_Accounting\Collection( $items );
}


/**
 * Wrapper for _doing_it_wrong().
 *
 * @param string $function Function used.
 * @param string $message Message to log.
 * @param string $version Version the message was added in.
 *
 * @since  1.1.0
 *
 */
function eaccounting_doing_it_wrong( $function, $message, $version ) {

	$message .= ' Backtrace: ' . wp_debug_backtrace_summary();

	if ( wp_doing_ajax() || defined( 'REST_REQUEST' ) ) {
		do_action( 'doing_it_wrong_run', $function, $message, $version );
		error_log( "{$function} was called incorrectly. {$message}. This message was added in version {$version}." );
	} else {
		_doing_it_wrong( $function, $message, $version );
	}

}

/**
 * Fetches data stored on disk.
 *
 * @param string $key Type of data to fetch.
 *
 * @since 1.1.0
 *
 * @return mixed Fetched data.
 */
function eaccounting_get_data( $key ) {
	// Try fetching it from the cache.
	$data = wp_cache_get( "eaccounting-data-$key", 'eaccounting-data' );
	if ( $data ) {
		return $data;
	}

	$data = apply_filters( "eaccounting_get_data_$key", include EACCOUNTING_ABSPATH . "/includes/data/$key.php" );
	wp_cache_set( "eaccounting-data-$key", 'eaccounting-data' );

	return $data;
}

/**
 * Send HTML emails from Ever_Accounting.
 *
 * @param mixed $to Receiver.
 * @param mixed $subject Subject.
 * @param mixed $message Message.
 * @param string $attachments Attachments. (default: "").
 *
 * @return bool
 */
function eaccounting_mail( $to, $subject, $message, $attachments = '' ) {
	$mailer = eaccounting()->mailer();

	return $mailer->send( $to, $subject, $message, $attachments );
}


/**
 * Based on wp_list_pluck, this calls a method instead of returning a property.
 *
 * @param array $list List of objects or arrays.
 * @param int|string $callback_or_field Callback method from the object to place instead of the entire object.
 * @param int|string $index_key Optional. Field from the object to use as keys for the new array.
 *                                      Default null.
 *
 * @since 1.1.0
 *
 * @return array Array of values.
 */
function eaccounting_list_pluck( $list, $callback_or_field, $index_key = null ) {
	// Use wp_list_pluck if this isn't a callback.
	$first_el = current( $list );
	if ( ! is_object( $first_el ) || ! is_callable( array( $first_el, $callback_or_field ) ) ) {
		return wp_list_pluck( $list, $callback_or_field, $index_key );
	}
	if ( ! $index_key ) {
		/*
		 * This is simple. Could at some point wrap array_column()
		 * if we knew we had an array of arrays.
		 */
		foreach ( $list as $key => $value ) {
			$list[ $key ] = $value->{$callback_or_field}();
		}

		return $list;
	}

	/*
	 * When index_key is not set for a particular item, push the value
	 * to the end of the stack. This is how array_column() behaves.
	 */
	$newlist = array();
	foreach ( $list as $value ) {
		// Get index. @since 3.2.0 this supports a callback.
		if ( is_callable( array( $value, $index_key ) ) ) {
			$newlist[ $value->{$index_key}() ] = $value->{$callback_or_field}();
		} elseif ( isset( $value->$index_key ) ) {
			$newlist[ $value->$index_key ] = $value->{$callback_or_field}();
		} else {
			$newlist[] = $value->{$callback_or_field}();
		}
	}

	return $newlist;
}

/**
 * Sets the last changed time for cache group.
 *
 * @since 1.1.0
 * @return void
 */
function eaccounting_cache_set_last_changed( $group ) {
	wp_cache_set( 'last_changed', microtime(), $group );
}

/**
 * Get percentage of a full number.
 * what percentage of 3 of 10
 *
 * @param     $total
 * @param     $number
 * @param int $decimals
 *
 * @since 1.1.0
 *
 * @return float
 */
function eaccounting_get_percentage( $total, $number, $decimals = 2 ) {
	return round( ( $number / $total ) * 100, $decimals );
}


/**
 * Queue some JavaScript code to be output in the footer.
 *
 * @param $code
 *
 * @since 1.0.2
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

		$js = "<!-- Ever_Accounting JavaScript -->\n<script type=\"text/javascript\">\njQuery(function($) { $eaccounting_queued_js });\n</script>\n";

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
		if ( $user && in_array( 'administrator', $user->roles, true ) ) {
			$user_id = $user->ID;
		}
	}

	if ( empty( $user_id ) ) {
		$users   = get_users(
			array(
				'role'   => 'administrator',
				'fields' => 'ID',
			)
		);
		$user_id = reset( $users );
	}

	return $user_id;
}

/**
 * Get user full name.
 *
 * @param $user_id
 *
 * @since 1.1.0
 *
 * @return string|void
 */
function eaccounting_get_full_name( $user_id ) {
	$unknown = __( 'Unknown User', 'wp-ever-accounting' );
	if ( empty( $user_id ) ) {
		return $unknown;
	}
	$user = get_userdata( absint( $user_id ) );
	if ( empty( $user ) ) {
		return $unknown;
	}
	$name = array_filter( array( $user->first_name, $user->last_name ) );
	if ( empty( $name ) ) {
		return empty( $user->display_name ) ? $unknown : $user->display_name;
	}

	return implode( ' ', $name );
}

/**
 * @param $file
 * @param $item_name
 */
function eaccounting_init_license( $file, $item_name ) {
	if ( is_admin() && class_exists( '\Ever_Accounting\License' ) ) {
		$license = new \Ever_Accounting\License( $file, $item_name );
	}
}
