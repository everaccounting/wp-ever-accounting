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
require_once EACCOUNTING_PATH . '/includes/deprecated/deprecated-functions.php';

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
 * Get financial Start
 *
 * @since 1.0.2
 * @return string
 */
function ever_accounting_get_financial_start( $year = null, $format = 'Y-m-d' ) {
	$financial_start = ever_accounting_get_option( 'financial_year_start', '01-01' );
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
function ever_accounting_get_financial_end( $year = null, $format = 'Y-m-d' ) {
	$dt = new \Ever_Accounting\DateTime( ever_accounting_get_financial_start( $year, 'Y-m-d' ) );
	//  if ( $dt->copy()->addYear( 1 )->subDay( 1 )->getTimestamp() > strtotime(date_i18n('Y-m-d H:i')) ) {
	//      $today = new \Ever_Accounting\DateTime( 'now' );
	//      return $today->date( $format );
	//  }
	return $dt->addYear( 1 )->subDay( 1 )->date( $format );
}

/**
 * Create a collection from the given value.
 *
 * @param mixed $items
 *
 * @return \Ever_Accounting\Collection
 * @since 1.0.2
 */
function ever_accounting_collect( $items ) {
	return new \Ever_Accounting\Collection( $items );
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
function ever_accounting_enqueue_js( $code ) {
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
function ever_accounting_print_js() {
	global $eaccounting_queued_js;

	if ( ! empty( $eaccounting_queued_js ) ) {
		// Sanitize.
		$eaccounting_queued_js = wp_check_invalid_utf8( $eaccounting_queued_js );
		$eaccounting_queued_js = preg_replace( '/&#(x)?0*(?(1)27|39);?/i', "'", $eaccounting_queued_js );
		$eaccounting_queued_js = str_replace( "\r", '', $eaccounting_queued_js );

		$js = "<!-- Ever_Accounting JavaScript -->\n<script type=\"text/javascript\">\njQuery(function($) { $eaccounting_queued_js });\n</script>\n";

		echo apply_filters( 'ever_accounting_queued_js', $js ); // WPCS: XSS ok.

		unset( $eaccounting_queued_js );
	}
}

/**
 * Get Ajax URL.
 *
 * @since 1.0.2
 * @return string
 */
function ever_accounting_ajax_url() {
	return admin_url( 'admin-ajax.php', 'relative' );
}

/**
 * Get the template path.
 *
 * @since 1.1.4
 * @return string
 */
function ever_accounting_template_path() {
	return apply_filters( 'ever_accounting_template_path', 'eaccounting/' );
}

/**
 * Plugin URL getter.
 *
 * @param string $path
 *
 * @return string
 * @since 1.1.4
 */
function ever_accounting_plugin_url( $path = '' ) {
	$url = untrailingslashit( plugins_url( '/', EVER_ACCOUNTING_FILE ) );
	if ( $path && is_string( $path ) ) {
		$url = trailingslashit( $url );
		$url .= ltrim( $path, '/' );
	}

	return $url;
}

/**
 * Plugin path getter.
 *
 * @param string $path
 *
 * @return string
 * @since 1.1.4
 */
function ever_accounting_plugin_path( $path = '' ) {
	$plugin_path = untrailingslashit( plugin_dir_path( EVER_ACCOUNTING_FILE ) );
	if ( $path && is_string( $path ) ) {
		$plugin_path = trailingslashit( $plugin_path );
		$plugin_path .= ltrim( $path, '/' );
	}

	return $plugin_path;
}

/**
 * Wrapper for _doing_it_wrong().
 *
 * @param string $function Function used.
 * @param string $message Message to log.
 * @param string $version Version the message was added in.
 *
 * @since  1.1.0
 */
function ever_accounting_doing_it_wrong( $function, $message, $version ) {

	$message .= ' Backtrace: ' . wp_debug_backtrace_summary();

	if ( wp_doing_ajax() || defined( 'REST_REQUEST' ) ) {
		do_action( 'doing_it_wrong_run', $function, $message, $version );
		error_log( "{$function} was called incorrectly. {$message}. This message was added in version {$version}." );
	} else {
		_doing_it_wrong( $function, $message, $version );
	}

}
