<?php
/**
 * EverAccounting Formatting Functions for formatting data.
 *
 * @package EverAccounting
 * @since   1.0.2
 */

defined( 'ABSPATH' ) || exit;

/**
 * Converts a string (e.g. 'yes' or 'no') to a bool.
 *
 * @param string|boolean $string String to convert.
 *
 * @return bool
 * @since 1.0.2
 */
function eaccounting_string_to_bool( $string ) {
	return is_bool( $string ) ? $string : ( 'yes' === strtolower( $string ) || 1 === $string || 'true' === strtolower( $string ) || '1' === $string );
}

/**
 * Converts a bool to a 'yes' or 'no'.
 *
 * @param bool $bool String to convert.
 *
 * @return string
 * @since 1.0.2
 */
function eaccounting_bool_to_string( $bool ) {
	if ( ! is_bool( $bool ) ) {
		$bool = eaccounting_string_to_bool( $bool );
	}

	return true === $bool ? 'yes' : 'no';
}

/**
 * Explode a string into an array by $delimiter and remove empty values.
 *
 * @param string|array $string String to convert.
 * @param string $delimiter Delimiter, defaults to ','.
 *
 * @return array
 * @since 1.0.2
 */
function eaccounting_string_to_array( $string, $delimiter = ',' ) {
	return is_array( $string ) ? $string : array_filter( explode( $delimiter, $string ) );
}

/**
 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
 * Non-scalar values are ignored.
 *
 * @param string|array $var Data to sanitize.
 *
 * @return string|array
 * @since 1.0.2
 */
function eaccounting_clean( $var ) {
	if ( is_array( $var ) ) {
		return array_map( 'eaccounting_clean', $var );
	}

	return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
}


/**
 * Run wc_clean over posted textarea but maintain line breaks.
 *
 * @param string $var Data to sanitize.
 *
 * @return string
 * @since  1.0.2
 */
function eaccounting_sanitize_textarea( $var ) {
	return implode( "\n", array_map( 'eaccounting_clean', explode( "\n", $var ) ) );
}


/**
 * Sanitize a string destined to be a tooltip.
 *
 * @param string $var Data to sanitize.
 *
 * @return string
 * @since  1.0.2 Tooltips are encoded with htmlspecialchars to prevent XSS. Should not be used in conjunction with esc_attr()
 */
function eaccounting_sanitize_tooltip( $var ) {
	return htmlspecialchars(
		wp_kses(
			html_entity_decode( $var ),
			array(
				'br'     => array(),
				'em'     => array(),
				'strong' => array(),
				'small'  => array(),
				'span'   => array(),
				'ul'     => array(),
				'li'     => array(),
				'ol'     => array(),
				'p'      => array(),
			)
		)
	);
}


/**
 * EverAccounting date format - Allows to change date format for everything.
 *
 * @return string
 * @since 1.0.2
 */
function eaccounting_date_format() {
	return apply_filters( 'eaccounting_date_format', get_option( 'date_format' ) );
}

/**
 * EAccounting Time Format - Allows to change time format for everything.
 *
 * @return string
 * @since 1.0.2
 */
function eaccounting_time_format() {
	return apply_filters( 'eaccounting_time_format', get_option( 'time_format' ) );
}

/**
 * Convert mysql datetime to PHP timestamp, forcing UTC. Wrapper for strtotime.
 *
 * @param string $time_string Time string.
 * @param int|null $from_timestamp Timestamp to convert from.
 *
 * @return int
 * @since 1.0.2
 *
 */
function eaccounting_string_to_timestamp( $time_string, $from_timestamp = null ) {
	$original_timezone = date_default_timezone_get();

	date_default_timezone_set( 'UTC' );

	if ( null === $from_timestamp ) {
		$next_timestamp = strtotime( $time_string );
	} else {
		$next_timestamp = strtotime( $time_string, $from_timestamp );
	}

	date_default_timezone_set( $original_timezone );

	return $next_timestamp;
}

/**
 * Convert a date string to a EAccounting_DateTime.
 *
 * @param string $time_string Time string.
 *
 * @return \EAccounting\DateTime
 * @throws Exception
 * @since  1.0.2
 */
function eaccounting_string_to_datetime( $time_string ) {
	// Strings are defined in local WP timezone. Convert to UTC.
	if ( 1 === preg_match( '/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(Z|((-|\+)\d{2}:\d{2}))$/', $time_string, $date_bits ) ) {
		$offset    = ! empty( $date_bits[7] ) ? iso8601_timezone_to_offset( $date_bits[7] ) : eaccounting_timezone_offset();
		$timestamp = gmmktime( $date_bits[4], $date_bits[5], $date_bits[6], $date_bits[2], $date_bits[3], $date_bits[1] ) - $offset;
	} else {
		$timestamp = eaccounting_string_to_timestamp( get_gmt_from_date( gmdate( 'Y-m-d H:i:s', eaccounting_string_to_timestamp( $time_string ) ) ) );
	}
	$datetime = new \EAccounting\DateTime( "@{$timestamp}", new DateTimeZone( 'UTC' ) );

	// Set local timezone or offset.
	if ( get_option( 'timezone_string' ) ) {
		$datetime->setTimezone( new DateTimeZone( eaccounting_timezone_string() ) );
	} else {
		$datetime->set_utc_offset( eaccounting_timezone_offset() );
	}

	return $datetime;
}

/**
 * EverAccounting Timezone - helper to retrieve the timezone string for a site until.
 * a WP core method exists (see https://core.trac.wordpress.org/ticket/24730).
 *
 * Adapted from https://secure.php.net/manual/en/function.timezone-name-from-abbr.php#89155.
 *
 * @return string PHP timezone string for the site
 * @since 1.0.2
 */
function eaccounting_timezone_string() {
	if ( function_exists( 'wp_timezone_string' ) ) {
		return wp_timezone_string();
	}

	// If site timezone string exists, return it.
	$timezone = get_option( 'timezone_string' );
	if ( $timezone ) {
		return $timezone;
	}

	// Get UTC offset, if it isn't set then return UTC.
	$utc_offset = (float) get_option( 'gmt_offset', 0 );
	if ( ! is_numeric( $utc_offset ) || 0.0 === $utc_offset ) {
		return 'UTC';
	}

	// Adjust UTC offset from hours to seconds.
	$utc_offset = (int) ( $utc_offset * 3600 );

	// Attempt to guess the timezone string from the UTC offset.
	$timezone = timezone_name_from_abbr( '', $utc_offset );
	if ( $timezone ) {
		return $timezone;
	}

	// Last try, guess timezone string manually.
	foreach ( timezone_abbreviations_list() as $abbr ) {
		foreach ( $abbr as $city ) {
			// WordPress restrict the use of date(), since it's affected by timezone settings, but in this case is just what we need to guess the correct timezone.
			if ( (bool) date( 'I' ) === (bool) $city['dst'] && $city['timezone_id'] && (int) $city['offset'] === $utc_offset ) { // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
				return $city['timezone_id'];
			}
		}
	}

	// Fallback to UTC.
	return 'UTC';
}

/**
 * Get timezone offset in seconds.
 *
 * @return float
 * @since  1.0.2
 */
function eaccounting_timezone_offset() {
	$timezone = get_option( 'timezone_string' );

	if ( $timezone ) {
		$timezone_object = new DateTimeZone( $timezone );

		return $timezone_object->getOffset( new DateTime( 'now' ) );
	}

	return (float) get_option( 'gmt_offset', 0 ) * HOUR_IN_SECONDS;
}


/**
 * Array merge and sum function.
 *
 * Source:  https://gist.github.com/Nickology/f700e319cbafab5eaedc
 *
 * @return array
 * @since 1.0.2
 */
function eaccounting_array_merge_recursive_numeric() {
	$arrays = func_get_args();

	// If there's only one array, it's already merged.
	if ( 1 === count( $arrays ) ) {
		return $arrays[0];
	}

	// Remove any items in $arrays that are NOT arrays.
	foreach ( $arrays as $key => $array ) {
		if ( ! is_array( $array ) ) {
			unset( $arrays[ $key ] );
		}
	}

	// We start by setting the first array as our final array.
	// We will merge all other arrays with this one.
	$final = array_shift( $arrays );

	foreach ( $arrays as $b ) {
		foreach ( $final as $key => $value ) {
			// If $key does not exist in $b, then it is unique and can be safely merged.
			if ( ! isset( $b[ $key ] ) ) {
				$final[ $key ] = $value;
			} else {
				// If $key is present in $b, then we need to merge and sum numeric values in both.
				if ( is_numeric( $value ) && is_numeric( $b[ $key ] ) ) {
					// If both values for these keys are numeric, we sum them.
					$final[ $key ] = $value + $b[ $key ];
				} elseif ( is_array( $value ) && is_array( $b[ $key ] ) ) {
					// If both values are arrays, we recursively call ourself.
					$final[ $key ] = eaccounting_array_merge_recursive_numeric( $value, $b[ $key ] );
				} else {
					// If both keys exist but differ in type, then we cannot merge them.
					// In this scenario, we will $b's value for $key is used.
					$final[ $key ] = $b[ $key ];
				}
			}
		}

		// Finally, we need to merge any keys that exist only in $b.
		foreach ( $b as $key => $value ) {
			if ( ! isset( $final[ $key ] ) ) {
				$final[ $key ] = $value;
			}
		}
	}

	return $final;
}

/**
 * Implode and escape HTML attributes for output.
 *
 * @param array $raw_attributes Attribute name value pairs.
 *
 * @return string
 * @since 1.0.2
 */
function eaccounting_implode_html_attributes( $raw_attributes ) {
	$attributes     = array();
	$raw_attributes = array_filter( $raw_attributes );
	foreach ( $raw_attributes as $name => $value ) {
		$attributes[] = esc_attr( $name ) . '="' . esc_attr( trim( $value ) ) . '"';
	}

	return implode( ' ', $attributes );
}

/**
 * Escape JSON for use on HTML or attribute text nodes.
 *
 * @param string $json JSON to escape.
 * @param bool $html True if escaping for HTML text node, false for attributes. Determines how quotes are handled.
 *
 * @return string Escaped JSON.
 * @since 1.0.2
 */
function eaccounting_esc_json( $json, $html = false ) {
	return _wp_specialchars(
		$json,
		$html ? ENT_NOQUOTES : ENT_QUOTES, // Escape quotes in attribute nodes only.
		'UTF-8',  // json_encode() outputs UTF-8 (really just ASCII), not the blog's charset.
		true  // Double escape entities: `&amp;` -> `&amp;amp;`.
	);
}

/**
 * Get only numbers from the string.
 *
 * @param      $number
 * @param bool $allow_decimal
 *
 * @return int|float|null
 * @since 1.0.2
 *
 */
function eaccounting_sanitize_number( $number, $allow_decimal = false ) {
	// Convert multiple dots to just one.
	$number = preg_replace( '/\.(?![^.]+$)|[^0-9.-]/', '', eaccounting_clean( $number ) );

	if ( $allow_decimal ) {
		return preg_replace( '/[^0-9.-]/', '', $number );
	}

	return preg_replace( '/[^0-9]/', '', $number );
}

/**
 * Sanitize price for inserting into database
 * since 1.0.0
 *
 * @param $amount
 * @param $code
 *
 * @return float|int
 * @since 1.0.2
 */
function eaccounting_sanitize_price( $amount, $code ) {
	return eaccounting_get_money( $amount, $code, false )->getAmount();
}

/**
 * Format price with currency code & number format
 *
 *
 * @param $amount
 * @param $code
 *
 * @return string
 * @since 1.0.2
 */
function eaccounting_format_price( $amount, $code = null ) {
	if ( $code == null ) {
		$code = eaccounting()->settings->get( 'default_currency', 'USD' );
	}
	$amount = eaccounting_get_money( $amount, $code, true );
	if ( is_wp_error( $amount ) ) {
		eaccounting_logger()->alert( sprintf( __( 'invalid currency code %s', 'wp-ever-account' ), $code ) );

		return '00.00';
	}

	return $amount->format();
}
