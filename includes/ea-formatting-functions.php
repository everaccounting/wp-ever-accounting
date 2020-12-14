<?php
/**
 * EverAccounting Formatting Functions for formatting data.
 *
 * @since   1.0.2
 * @package EverAccounting
 */

defined( 'ABSPATH' ) || exit;

/**
 * Converts a string (e.g. 'yes' or 'no') to a bool.
 *
 * @since 1.0.2
 *
 * @param string|boolean $string String to convert.
 *
 * @return bool
 */
function eaccounting_string_to_bool( $string ) {
	return is_bool( $string ) ? $string : ( 'yes' === strtolower( $string ) || 1 === $string || 'true' === strtolower( $string ) || '1' === $string );
}

/**
 * Converts a bool to a 'yes' or 'no'.
 *
 * @since 1.0.2
 *
 * @param bool $bool String to convert.
 *
 * @return string
 */
function eaccounting_bool_to_string( $bool ) {
	if ( ! is_bool( $bool ) ) {
		$bool = eaccounting_string_to_bool( $bool );
	}

	return true === $bool ? 'yes' : 'no';
}

/**
 * Converts a bool to a 1 or 0.
 *
 * @since 1.1.0
 *
 * @param $bool
 *
 * @return int
 */
function eaccounting_bool_to_number( $bool ) {
	if ( ! is_bool( $bool ) ) {
		$bool = eaccounting_string_to_bool( $bool );
	}

	return true === $bool ? 1 : 0;
}

/**
 * Explode a string into an array by $delimiter and remove empty values.
 *
 * @since 1.0.2
 *
 * @param string       $delimiter Delimiter, defaults to ','.
 *
 * @param string|array $string    String to convert.
 *
 * @return array
 */
function eaccounting_string_to_array( $string, $delimiter = ',' ) {
	return is_array( $string ) ? $string : array_filter( explode( $delimiter, $string ) );
}

/**
 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
 * Non-scalar values are ignored.
 *
 * @since 1.0.2
 *
 * @param string|array $var Data to sanitize.
 *
 * @return string|array
 */
function eaccounting_clean( $var ) {
	if ( is_array( $var ) ) {
		return array_map( 'eaccounting_clean', $var );
	}

	return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
}


/**
 * Run eaccounting_clean over posted textarea but maintain line breaks.
 *
 * @since  1.0.2
 *
 * @param string $var Data to sanitize.
 *
 * @return string
 */
function eaccounting_sanitize_textarea( $var ) {
	return implode( "\n", array_map( 'eaccounting_clean', explode( "\n", $var ) ) );
}


/**
 * Sanitize a string destined to be a tooltip.
 *
 * @since  1.0.2 Tooltips are encoded with htmlspecialchars to prevent XSS. Should not be used in conjunction with esc_attr()
 *
 * @param string $var Data to sanitize.
 *
 * @return string
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
 * @since 1.0.2
 * @return string
 */
function eaccounting_date_format() {
	return apply_filters( 'eaccounting_date_format', eaccounting()->settings->get( 'date_format', 'Y-m-d' ) );
}

/**
 * EAccounting Time Format - Allows to change time format for everything.
 *
 * @since 1.0.2
 * @return string
 */
function eaccounting_time_format() {
	return apply_filters( 'eaccounting_time_format', eaccounting()->settings->get( 'time_format', 'H:i' ) );
}

/**
 * Format a date for output.
 *
 * @since  1.0.2
 *
 * @param \EverAccounting\Core\DateTime|string $date   Instance of DateTime.
 * @param string                               $format Data format.
 *                                                     Defaults to the eaccounting_date_format function if not set.
 *
 * @return string
 */
function eaccounting_format_datetime( $date, $format = '' ) {

	if ( empty( $date ) || '0000-00-00 00:00:00' === $date || '0000-00-00' === $date ) {
		return '';
	}

	if ( ! $format ) {
		$format = eaccounting_date_format();
	}

	if ( ! is_numeric( $date ) ) {
		$date = strtotime( $date );
	}

	return date_i18n( $format, $date );
}

/**
 * Array merge and sum function.
 *
 * Source:  https://gist.github.com/Nickology/f700e319cbafab5eaedc
 *
 * @since 1.0.2
 * @return array
 */
function eaccounting_array_merge_recursive() {
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
					$final[ $key ] = eaccounting_array_merge_recursive( $value, $b[ $key ] );
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
 * @since 1.0.2
 *
 * @param array $raw_attributes Attribute name value pairs.
 *
 * @return string
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
 * @since 1.0.2
 *
 * @param bool   $html True if escaping for HTML text node, false for attributes. Determines how quotes are handled.
 *
 * @param string $json JSON to escape.
 *
 * @return string Escaped JSON.
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
 * @since 1.0.2
 *
 * @param bool $allow_decimal
 *
 * @param      $number
 *
 * @return int|float|null
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
 * @since 1.0.2
 *
 * @param $code
 *
 * @param $amount
 *
 * @return float|int
 */
function eaccounting_sanitize_price( $amount, $code = 'USD' ) {
	return eaccounting_get_money( $amount, $code, false )->getAmount();
}

/**
 * Format price with currency code & number format
 *
 * @since 1.0.2
 *
 * @param $code
 *
 * @param $amount
 *
 * @return string
 */
function eaccounting_format_price( $amount, $code = null ) {
	if ( $code === null ) {
		$code = eaccounting()->settings->get( 'default_currency', 'USD' );
	}

	$amount = eaccounting_get_money( $amount, $code, true );
	if ( is_wp_error( $amount ) ) {
		eaccounting_logger()->alert( sprintf( __( 'invalid currency code %s', 'wp-ever-accounting' ), $code ) );

		return '00.00';
	}

	return $amount->format();
}

/**
 * Convert a date string to a EAccounting_DateTime.
 *
 * @since  1.0.2
 *
 * @param string $time_string Time string.
 *
 * @throws Exception
 * @return \EverAccounting\Core\DateTime
 */
function eaccounting_string_to_datetime( $time_string ) {
	// Strings are defined in local WP timezone. Convert to UTC.
	if ( 1 === preg_match( '/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(Z|((-|\+)\d{2}:\d{2}))$/', $time_string, $date_bits ) ) {
		$offset    = ! empty( $date_bits[7] ) ? iso8601_timezone_to_offset( $date_bits[7] ) : ( (float) get_option( 'gmt_offset', 0 ) * HOUR_IN_SECONDS );
		$timestamp = gmmktime( $date_bits[4], $date_bits[5], $date_bits[6], $date_bits[2], $date_bits[3], $date_bits[1] ) - $offset;
	} elseif ( is_numeric( $time_string ) ) {
		$local_time = gmdate( 'Y-m-d H:i:s', $time_string );
		$timezone   = wp_timezone();
		$datetime   = date_create( $local_time, $timezone );
		$timestamp  = $datetime->getTimestamp();
	} else {
		$original_timezone = date_default_timezone_get();
		date_default_timezone_set( 'UTC' );
		$timestamp = strtotime( get_gmt_from_date( gmdate( 'Y-m-d H:i:s', strtotime( $time_string ) ) ) );
		date_default_timezone_set( $original_timezone );
	}
	$datetime = new \EverAccounting\Core\DateTime( "@{$timestamp}", new DateTimeZone( 'UTC' ) );

	return $datetime;
}

/**
 * Convert RGB to HEX.
 *
 * @since 1.1.0
 *
 * @param mixed $color Color.
 *
 * @return array
 */
function eaccounting_rgb_from_hex( $color ) {
	$color = str_replace( '#', '', $color );
	// Convert shorthand colors to full format, e.g. "FFF" -> "FFFFFF".
	$color = preg_replace( '~^(.)(.)(.)$~', '$1$1$2$2$3$3', $color );

	$rgb      = array();
	$rgb['R'] = hexdec( $color[0] . $color[1] );
	$rgb['G'] = hexdec( $color[2] . $color[3] );
	$rgb['B'] = hexdec( $color[4] . $color[5] );

	return $rgb;
}

/**
 * Make HEX color darker.
 *
 * @param mixed $color  Color.
 * @param int   $factor Darker factor.
 *                      Defaults to 30.
 * @return string
 */
function eaccounting_hex_darker( $color, $factor = 30 ) {
	$base  = eaccounting_rgb_from_hex( $color );
	$color = '#';

	foreach ( $base as $k => $v ) {
		$amount      = $v / 100;
		$amount      = eaccounting_round_number( $amount * $factor, 0 );
		$new_decimal = $v - $amount;

		$new_hex_component = dechex( $new_decimal );
		if ( strlen( $new_hex_component ) < 2 ) {
			$new_hex_component = '0' . $new_hex_component;
		}
		$color .= $new_hex_component;
	}

	return $color;
}

/**
 * Make HEX color lighter.
 *
 * @param mixed $color  Color.
 * @param int   $factor Lighter factor.
 *                      Defaults to 30.
 * @return string
 */
function eaccounting_hex_lighter( $color, $factor = 30 ) {
	$base  = eaccounting_rgb_from_hex( $color );
	$color = '#';

	foreach ( $base as $k => $v ) {
		$amount      = 255 - $v;
		$amount      = $amount / 100;
		$amount      = eaccounting_round_number( $amount * $factor, 0 );
		$new_decimal = $v + $amount;

		$new_hex_component = dechex( $new_decimal );
		if ( strlen( $new_hex_component ) < 2 ) {
			$new_hex_component = '0' . $new_hex_component;
		}
		$color .= $new_hex_component;
	}

	return $color;
}

/**
 * Determine whether a hex color is light.
 *
 * @param mixed $color Color.
 * @return bool  True if a light color.
 */
function eaccounting_hex_is_light( $color ) {
	$hex = str_replace( '#', '', $color );

	$c_r = hexdec( substr( $hex, 0, 2 ) );
	$c_g = hexdec( substr( $hex, 2, 2 ) );
	$c_b = hexdec( substr( $hex, 4, 2 ) );

	$brightness = ( ( $c_r * 299 ) + ( $c_g * 587 ) + ( $c_b * 114 ) ) / 1000;

	return $brightness > 155;
}

/**
 * Detect if we should use a light or dark color on a background color.
 *
 * @param mixed  $color Color.
 * @param string $dark  Darkest reference.
 *                      Defaults to '#000000'.
 * @param string $light Lightest reference.
 *                      Defaults to '#FFFFFF'.
 * @return string
 */
function eaccounting_light_or_dark( $color, $dark = '#000000', $light = '#FFFFFF' ) {
	return eaccounting_hex_is_light( $color ) ? $dark : $light;
}

/**
 * Format string as hex.
 *
 * @param string $hex HEX color.
 * @return string|null
 */
function eaccounting_format_hex( $hex ) {
	$hex = trim( str_replace( '#', '', $hex ) );

	if ( strlen( $hex ) === 3 ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}

	return $hex ? '#' . $hex : null;
}
