<?php
/**
 * EverAccounting Formatting
 *
 * Functions for formatting data.
 *
 * @package EverAccounting/Functions
 * @version 1.0.2
 */

defined( 'ABSPATH' ) || exit;

/**
 * Converts a string (e.g. 'yes' or 'no') to a bool.
 *
 * @param string $string String to convert.
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
function eaccounting__bool_to_string( $bool ) {
	if ( ! is_bool( $bool ) ) {
		$bool = wc_string_to_bool( $bool );
	}

	return true === $bool ? 'yes' : 'no';
}

/**
 * Explode a string into an array by $delimiter and remove empty values.
 *
 * @param string $string String to convert.
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
 */
function eaccounting_clean( $var ) {
	if ( is_array( $var ) ) {
		return array_map( 'wc_clean', $var );
	} else {
		return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
	}
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
	$attributes = array();
	foreach ( $raw_attributes as $name => $value ) {
		$attributes[] = esc_attr( $name ) . '="' . esc_attr( $value ) . '"';
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
