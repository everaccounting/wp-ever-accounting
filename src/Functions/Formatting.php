<?php

defined( 'ABSPATH' ) || exit;

/**
 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
 * Non-scalar values are ignored.
 *
 * @param string|array $var Data to sanitize.
 *
 * @since 1.1.6
 * @return string|array
 */
function ea_clean( $var ) {
	if ( is_array( $var ) ) {
		return array_map( 'ea_clean', $var );
	} else {
		return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
	}
}

/**
 * Sanitize a string destined to be a tooltip.
 *
 * @param string $var Data to sanitize.
 *
 * @since 1.1.6
 * @return string
 */
function ea_sanitize_tooltip( $var ) {
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
 * Parse a relative date option from the settings API into a standard format.
 *
 * @param mixed $raw_value Value stored in DB.
 *
 * @since 1.1.6
 * @return array Nicely formatted array with number and unit values.
 */
function ea_parse_relative_date_option( $raw_value ) {
	$periods = array(
		'day'   => __( 'day(s)', 'wp-ever-accounting' ),
		'week'  => __( 'week(s)', 'wp-ever-accounting' ),
		'month' => __( 'month(s)', 'wp-ever-accounting' ),
		'year'  => __( 'year(s)', 'wp-ever-accounting' ),
	);

	$value = wp_parse_args(
		(array) $raw_value,
		array(
			'number' => '',
			'unit'   => 'days',
		)
	);

	$value['number'] = ! empty( $value['number'] ) ? absint( $value['number'] ) : '';

	if ( ! in_array( $value['unit'], array_keys( $periods ), true ) ) {
		$value['unit'] = 'days';
	}

	return $value;
}
