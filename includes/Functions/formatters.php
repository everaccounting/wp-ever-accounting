<?php

use EverAccounting\Utilities\I18n;

defined( 'ABSPATH' ) || exit;

/**
 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
 * Non-scalar values are ignored.
 *
 * @param string|array $value Data to sanitize.
 *
 * @since 1.1.6
 * @return string|array
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
 * @since 1.1.6 Renamed from eaccounting_tooltip() to eac_tooltip().
 * @since 1.0.2
 * @return string
 */
function eac_tooltip( $tip, $allow_html = false ) {
	if ( $allow_html ) {
		$tip = eac_sanitize_tooltip( $tip );
	} else {
		$tip = esc_attr( $tip );
	}

	return '<span class="eac-tooltip" title="' . wp_kses_post( $tip ) . '">[?]</span>';
}


/**
 * Sanitize a string destined to be a tooltip.
 *
 * @param string $text Data to sanitize.
 *
 * @since 1.1.6
 * @return string
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
 * Get country address format.
 *
 * @param array  $fields Address fields.
 * @param string $separator How to separate address lines.
 *
 * @return string
 */
function eac_get_formatted_address( $fields = array(), $separator = '<br/>' ) {
	$defaults          = array(
		'name'      => '',
		'company'   => '',
		'address_1' => '',
		'address_2' => '',
		'city'      => '',
		'state'     => '',
		'postcode'  => '',
		'country'   => '',
	);
	$format            = apply_filters( 'ever_accounting_address_format', "<strong>{name}</strong>\n{company}\n{address_1}\n{address_2}\n{city} {state} {postcode}\n{country}" );
	$fields            = array_map( 'trim', wp_parse_args( $fields, $defaults ) );
	$countries         = I18n::get_countries();
	$fields['country'] = isset( $countries[ $fields['country'] ] ) ? $countries[ $fields['country'] ] : $fields['country'];
	$replacers         = array_map(
		'esc_html',
		array(
			'{name}'      => $fields['name'],
			'{company}'   => $fields['company'],
			'{address_1}' => $fields['address_1'],
			'{address_2}' => $fields['address_2'],
			'{city}'      => $fields['city'],
			'{state}'     => $fields['state'],
			'{postcode}'  => $fields['postcode'],
			'{country}'   => $fields['country'],
		)
	);
	$formatted_address = str_replace( array_keys( $replacers ), $replacers, $format );
	// Clean up white space.
	$formatted_address = preg_replace( '/  +/', ' ', trim( $formatted_address ) );
	$formatted_address = preg_replace( '/\n\n+/', "\n", $formatted_address );
	// Break newlines apart and remove empty lines/trim commas and white space.
	$address_lines = array_map( 'trim', array_filter( explode( "\n", $formatted_address ) ) );
	// Now check if there any aditional fields.
	$extra = array_diff_key( $fields, $defaults );
	foreach ( $extra as $key => $value ) {
		if ( ! empty( $value ) ) {
			switch ( $key ) {
				case 'phone':
					$address_lines[] = eac_make_phone_clickable( $value );
					break;
				case 'email':
					$address_lines[] = '<a href="mailto:' . esc_attr( $value ) . '">' . esc_html( $value ) . '</a>';
					break;
				case 'website':
					$address_lines[] = '<a href="' . esc_url( $value ) . '">' . esc_html( $value ) . '</a>';
					break;
				case 'vat':
					$address_lines[] = __( 'VAT:', 'wp-ever-accounting' ) . ' ' . $value;
					break;
				default:
					$address_lines[] = $value;
			}
		}
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