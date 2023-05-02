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

	return '<span class="eac-help-tip ea-help-tip" title="' . wp_kses_post( $tip ) . '"></span>';
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
 * Get request variable.
 *
 * @param string $key Request variable key.
 * @param string $method Request method.
 * @param mixed  $default Default value.
 * @param string $sanitizer Sanitizer function.
 *
 * @since 1.1.6
 * @return mixed
 */
function eac_get_request_var( $key, $method = 'get', $default = null, $sanitizer = 'eac_clean' ) {
	$method = strtolower( $method );

	if ( 'get' === $method ) {
		$value = isset( $_GET[ $key ] ) ? eac_clean( wp_unslash( $_GET[ $key ] ) ) : $default; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	} elseif ( 'post' === $method ) {
		$value = isset( $_POST[ $key ] ) ? eac_clean( wp_unslash( $_POST[ $key ] ) ) : $default; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	} elseif ( 'request' === $method ) {
		$value = isset( $_REQUEST[ $key ] ) ? eac_clean( wp_unslash( $_REQUEST[ $key ] ) ) : $default; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	return $sanitizer ? call_user_func( $sanitizer, $value ) : $value;
}

/**
 * Get input variable.
 *
 * @param int    $type Input type.
 * @param string $var Input variable.
 * @param string $sanitizer Sanitizer function.
 * @param mixed  $default Default value.
 * @return mixed
 */
function eac_filter_input( $type, $var, $sanitizer = 'eac_clean', $default = null ) {
	$value = filter_input( $type, $var, FILTER_SANITIZE_FULL_SPECIAL_CHARS );

	$value = $sanitizer ? call_user_func( $sanitizer, $value ) : $value;

	return empty( $value ) ? $default : $value;
}
