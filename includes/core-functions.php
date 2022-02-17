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

/**
 * Get financial Start
 *
 * @since 1.0.2
 * @return string
 */
function ever_accounting_get_financial_start( $year = null, $format = 'Y-m-d' ) {
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
function ever_accounting_get_financial_end( $year = null, $format = 'Y-m-d' ) {
	$dt = new \Ever_Accounting\DateTime( ever_accounting_get_financial_start( $year, 'Y-m-d' ) );
	//  if ( $dt->copy()->addYear( 1 )->subDay( 1 )->getTimestamp() > strtotime(date_i18n('Y-m-d H:i')) ) {
	//      $today = new \Ever_Accounting\DateTime( 'now' );
	//      return $today->date( $format );
	//  }
	return $dt->addYear( 1 )->subDay( 1 )->date( $format );
}
