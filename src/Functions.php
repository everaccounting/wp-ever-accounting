<?php

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/Functions/Formatting.php';
require_once __DIR__ . '/Functions/Template.php';

/**
 * Display a tooltip.
 *
 * @param string $tip The tip.
 * @param bool $allow_html Allow sanitized HTML if true or escape.
 *
 * @return string
 */
function ea_tooltip( $tip, $allow_html = false ) {
	if ( $allow_html ) {
		$tip = ea_sanitize_tooltip( $tip );
	} else {
		$tip = esc_attr( $tip );
	}

	return '<span class="ea-help-tip" data-tip="' . esc_attr( $tip ) . '"></span>';
}
