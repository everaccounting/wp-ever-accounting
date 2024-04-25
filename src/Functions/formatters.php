<?php

use EverAccounting\Utilities\I18n;

defined( 'ABSPATH' ) || exit;

/**
 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
 * Non-scalar values are ignored.
 *
 * @param string|array $value Data to sanitize.
 *
 * @return string|array
 * @since 1.1.6
 */
function eac_clean( $value ) {
	if ( is_array( $value ) ) {
		return array_map( 'eac_clean', $value );
	} else {
		return is_scalar( $value ) ? sanitize_text_field( $value ) : $value;
	}
}

/**
 * Display help tip.
 *
 * @param string $tip Help tip text.
 * @param bool   $allow_html Allow sanitized HTML if true or escape.
 *
 * @return string
 * @since 1.1.6 Renamed from eaccounting_tooltip() to eac_tooltip().
 * @since 1.0.2
 */
function eac_tooltip( $tip, $allow_html = false ) {
	if ( $allow_html ) {
		$tip = eac_sanitize_tooltip( $tip );
	} else {
		$tip = esc_attr( $tip );
	}

	return '<span class="eac-tooltip" title="' . wp_kses_post( $tip ) . '"></span>';
}


/**
 * Sanitize a string destined to be a tooltip.
 *
 * @param string $text Data to sanitize.
 *
 * @return string
 * @since 1.1.6
 */
function eac_sanitize_tooltip( $text ) {
	return htmlspecialchars(
		wp_kses(
			html_entity_decode( $text ),
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
 * Get the ajax action url.
 *
 * @param array|string $args Array of arguments.
 * @param bool         $ajax Whether to use ajax or not.
 * @param bool         $nonce Whether to use nonce or not.
 *
 * @since 1.0.0
 */
function eac_action_url( $args = array(), $ajax = true, $nonce = true ) {
	$args = wp_parse_args( $args );
	if ( isset( $args['action'] ) && 0 !== strpos( $args['action'], 'eac_' ) ) {
		$args['action'] = 'eac_' . $args['action'];
	}
	$url = add_query_arg( $args, admin_url( $ajax ? 'admin-ajax.php' : 'admin-post.php' ) );
	if ( $nonce && isset( $args['action'] ) ) {
		$url = wp_nonce_url( $url, $args['action'] );
	}

	return $url;
}

/**
 * Get country address format.
 *
 * @param array  $args Arguments.
 * @param string $separator How to separate address lines.
 *
 * @return string
 */
function eac_get_formatted_address( $args = array(), $separator = '<br/>' ) {
	$default_args = array(
		'name'      => '',
		'company'   => '',
		'address_1' => '',
		'address_2' => '',
		'city'      => '',
		'state'     => '',
		'postcode'  => '',
		'country'   => '',
	);
	$format       = apply_filters( 'ever_accounting_address_format', "{name}\n{company}\n{address_1}\n{address_2}\n{city} {state} {postcode}\n{country}" );
	$args         = array_map( 'trim', wp_parse_args( $args, $default_args ) );
	$countries    = I18n::get_countries();
	$country      = isset( $countries[ $args['country'] ] ) ? $countries[ $args['country'] ] : $args['country'];
	$replace      = array_map(
		'esc_html',
		array(
			'{name}'      => $args['name'],
			'{company}'   => $args['company'],
			'{address_1}' => $args['address_1'],
			'{address_2}' => $args['address_2'],
			'{city}'      => $args['city'],
			'{state}'     => $args['state'],
			'{postcode}'  => $args['postcode'],
			'{country}'   => $country,
		)
	);

	$formatted_address = str_replace( array_keys( $replace ), $replace, $format );
	// Clean up white space.
	$formatted_address = preg_replace( '/  +/', ' ', trim( $formatted_address ) );
	$formatted_address = preg_replace( '/\n\n+/', "\n", $formatted_address );
	// Break newlines apart and remove empty lines/trim commas and white space.
	$address_lines = array_map( 'trim', array_filter( explode( "\n", $formatted_address ) ) );
	$address_lines = array_filter( $address_lines );
	// If phone is set, add it to the last line.
	if ( ! empty( $args['phone'] ) ) {
		// it should click to call on mobile.
		$address_lines[] = eac_make_phone_clickable( $args['phone'] );
	}
	// If email is set, add it to the last line.
	if ( ! empty( $args['email'] ) ) {
		$address_lines[] = sprintf( '<a href="mailto:%s">%s</a>', $args['email'], $args['email'] );
	}
	// If Vat is set, add it to the last line.
	if ( ! empty( $args['vat'] ) ) {
		$address_lines[ count( $address_lines ) - 1 ] = sprintf( '%s %s', __( 'VAT:', 'wp-ever-accounting' ), $args['vat'] );
	}
	if ( ! empty( $args['tax'] ) ) {
		$address_lines[ count( $address_lines ) - 1 ] = sprintf( '%s %s', __( 'Tax ID:', 'wp-ever-accounting' ), $args['tax_id'] );
	}

	return implode( $separator, $address_lines );
}

/**
 * Convert plaintext phone number to clickable phone number.
 *
 * Remove formatting and allow "+".
 * Example and specs: https://developer.mozilla.org/en/docs/Web/HTML/Element/a#Creating_a_phone_link
 *
 * @param string $phone Content to convert phone number.
 *
 * @since 1.1.6
 *
 * @return string Content with converted phone number.
 */
function eac_make_phone_clickable( $phone ) {
	$number = trim( preg_replace( '/[^\d|\+]/', '', $phone ) );

	return $number ? '<a href="tel:' . esc_attr( $number ) . '">' . esc_html( $phone ) . '</a>' : '';
}
