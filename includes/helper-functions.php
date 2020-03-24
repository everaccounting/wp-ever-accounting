<?php
defined( 'ABSPATH' ) || exit();
/**
 * The purpose of this helper method is to receive an incoming format string in php date/time format
 * and spit out the js and moment.js equivalent formats.
 * Note, if no format string is given, then it is assumed the user wants what is set for WP.
 *
 * since 1.0.0
 *
 * @param null $date_format
 * @param null $time_format
 *
 * @return array
 */
function eaccounting_convert_php_to_moment_formats( $date_format = null, $time_format = null ) {
	if ( $date_format === null ) {
		$date_format = (string) get_option( 'date_format' );
	}
	if ( $time_format === null ) {
		$time_format = (string) get_option( 'time_format' );
	}
	$replacements = [
		'd' => 'DD',
		'D' => 'ddd',
		'j' => 'D',
		'l' => 'dddd',
		'N' => 'E',
		'S' => 'o',
		'w' => 'e',
		'z' => 'DDD',
		'W' => 'W',
		'F' => 'MMMM',
		'm' => 'MM',
		'M' => 'MMM',
		'n' => 'M',
		't' => '', // no equivalent
		'L' => '', // no equivalent
		'o' => 'YYYY',
		'Y' => 'YYYY',
		'y' => 'YY',
		'a' => 'a',
		'A' => 'A',
		'B' => '', // no equivalent
		'g' => 'h',
		'G' => 'H',
		'h' => 'hh',
		'H' => 'HH',
		'i' => 'mm',
		's' => 'ss',
		'u' => 'SSS',
		'e' => 'zz', // deprecated since version 1.6.0 of moment.js
		'I' => '', // no equivalent
		'O' => '', // no equivalent
		'P' => '', // no equivalent
		'T' => '', // no equivalent
		'Z' => '', // no equivalent
		'c' => '', // no equivalent
		'r' => '', // no equivalent
		'U' => 'X',
	];

	$date_format_string = strtr( $date_format, $replacements );
	$time_format_string = strtr( $time_format, $replacements );

	return array(
		'moment'       => $date_format_string . ' ' . $time_format_string,
		'moment_split' => array(
			'date' => $date_format_string,
			'time' => $time_format_string
		)
	);
}
