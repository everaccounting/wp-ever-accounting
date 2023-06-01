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
