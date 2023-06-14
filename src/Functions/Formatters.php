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
function eac_clean( $var ) {
	if ( is_array( $var ) ) {
		return array_map( 'eac_clean', $var );
	} else {
		return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
	}
}

/**
 * Display help tip.
 *
 * @param string $tip Help tip text.
 * @param bool   $allow_html Allow sanitized HTML if true or escape.
 *
 * @since 1.0.2
 * @since 1.1.6 Renamed from eaccounting_tooltip() to eac_tooltip().
 * @return string
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
 * @param string $var Data to sanitize.
 *
 * @since 1.1.6
 * @return string
 */
function eac_sanitize_tooltip( $var ) {
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
 * Get input variable.
 *
 * @param string $var Input variable.
 * @param string $default Default value.
 * @param string $method Request method. Possible values: get, post, request.
 * @param string $sanitizer Sanitizer function.
 *
 * @since 1.1.6
 * @return mixed
 */
function eac_get_input_var( $var, $default = null, $method = 'get', $sanitizer = 'eac_clean' ) {
	$method = strtolower( $method );

	if ( 'get' === $method ) {
		$value = isset( $_GET[ $var ] ) ? eac_clean( wp_unslash( $_GET[ $var ] ) ) : $default; // phpcs:ignore
	} elseif ( 'post' === $method ) {
		$value = isset( $_POST[ $var ] ) ? eac_clean( wp_unslash( $_POST[ $var ] ) ) : $default; // phpcs:ignore
	} elseif ( 'request' === $method ) {
		$value = isset( $_REQUEST[ $var ] ) ? eac_clean( wp_unslash( $_REQUEST[ $var ] ) ) : $default; // phpcs:ignore
	}

	return $sanitizer ? call_user_func( $sanitizer, $value ) : $value;
}

/**
 * Check if an input variable is set.
 *
 * @param string $var Input variable.
 * @param string $method Request method.
 *
 * @since 1.1.6
 * @return bool
 */
function eac_is_input_var_set( $var, $method = 'get' ) {
	$method = strtolower( $method );

	if ( 'get' === $method ) {
		return isset( $_GET[ $var ] ); // phpcs:ignore
	} elseif ( 'post' === $method ) {
		return isset( $_POST[ $var ] ); // phpcs:ignore
	} elseif ( 'request' === $method ) {
		return isset( $_REQUEST[ $var ] ); // phpcs:ignore
	}

	return false;
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
 * Check if the number is empty.
 *
 * @param string $number Number to check.
 *
 * @since 1.1.6
 * @return bool
 */
function eac_is_empty_number( $number ) {
	// convert to double to remove trailing zeros.
	return empty( (float) $number );
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
		'phone'     => '',
		'email'     => '',
	);
	$format       = apply_filters( 'ever_accounting_address_format', "<strong>{name}</strong>\n{company}\n{address_1}\n{address_2}\n{city} {state} {postcode}\n{country}\n{phone}\n{email}" );
	$args         = array_map( 'trim', wp_parse_args( $args, $default_args ) );
	$countries    = eac_get_countries();
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
			'{phone}'     => $args['phone'],
			'{email}'     => $args['email'],
		)
	);

	$formatted_address = str_replace( array_keys( $replace ), $replace, $format );
	// Clean up white space.
	$formatted_address = preg_replace( '/  +/', ' ', trim( $formatted_address ) );
	$formatted_address = preg_replace( '/\n\n+/', "\n", $formatted_address );
	// Break newlines apart and remove empty lines/trim commas and white space.
	$address_lines = array_map( 'trim', array_filter( explode( "\n", $formatted_address ) ) );
	// If Vat is set, add it to the last line.
	if ( ! empty( $args['vat'] ) ) {
		$address_lines[ count( $address_lines ) - 1 ] = sprintf( '%s %s', __( 'VAT:', 'wp-ever-accounting' ), $args['vat'] );
	}

	return implode( $separator, $address_lines );
}
