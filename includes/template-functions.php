<?php

defined( 'ABSPATH' ) || exit();
/**
 * Gets and includes template files.
 *
 * @since 1.0.0
 * @param mixed  $template_name
 * @param array  $args (default: array()).
 * @param string $template_path (default: '').
 * @param string $default_path (default: '').
 */
function eaccounting_get_template( $template_name, $args = [], $template_path = 'eaccounting', $default_path = '' ) {
	if ( $args && is_array( $args ) ) {
		extract( $args );
	}
	include eaccounting_locate_template( $template_name, $template_path, $default_path );
}


/**
 * Locates a template and return the path for inclusion.
 *
 * This is the load order:
 *
 *      yourtheme       /   $template_path  /   $template_name
 *      yourtheme       /   $template_name
 *      $default_path   /   $template_name
 *
 * @since 1.0.0
 * @param string      $template_name
 * @param string      $template_path (default: 'eaccounting').
 * @param string|bool $default_path (default: '') False to not load a default.
 * @return string
 */
function eaccounting_locate_template( $template_name, $template_path = 'eaccounting', $default_path = '' ) {
	// Look within passed path within the theme - this is priority.
	$template = locate_template(
		[
			trailingslashit( $template_path ) . $template_name,
			$template_name,
		]
	);

	// Get default template.
	if ( ! $template && false !== $default_path ) {
		$default_path = $default_path ? $default_path : EACCOUNTING_ABSPATH . '/templates/';
		if ( file_exists( trailingslashit( $default_path ) . $template_name ) ) {
			$template = trailingslashit( $default_path ) . $template_name;
		}
	}

	// Return what we found.
	return apply_filters( 'eaccounting_locate_template', $template, $template_name, $template_path );
}

/**
 * Gets template part (for templates in loops).
 *
 * @since 1.0.0
 * @param string      $slug
 * @param string      $name (default: '').
 * @param string      $template_path (default: 'job_manager').
 * @param string|bool $default_path (default: '') False to not load a default.
 */
function eaccounting_get_template_part( $slug, $name = '', $template_path = 'job_manager', $default_path = '' ) {
	$template = '';

	if ( $name ) {
		$template = eaccounting_locate_template( "{$slug}-{$name}.php", $template_path, $default_path );
	}

	// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/job_manager/slug.php.
	if ( ! $template ) {
		$template = eaccounting_locate_template( "{$slug}.php", $template_path, $default_path );
	}

	if ( $template ) {
		load_template( $template, false );
	}
}

/**
 * Open wrapper
 * since 1.0.0
 *
 * @param string $class
 */
function eaccounting_page_wrapper_open( $class = ' ' ) {
	$classes = 'ea-main-wrapper ' . sanitize_html_class( $class );
	//echo '<div class="wrap">';
	echo '<div class="wrap ' . $classes . '">';
	//do_action( 'eaccounting_page_top' );
	echo '<div class="ea-page-wrapper">';
	echo '<hr class="wp-header-end">';
}

/**
 * Close wrapper
 *
 * since 1.0.0
 */
function eaccounting_page_wrapper_close() {
	echo '<div><!--.ea-page-wrapper-->';
	//echo '<div><!--.ea-main-wrapper-->';
	echo '<div><!--.wrap-->';
}

function eaccounting_add_page_header() {
	ob_start();
	include eaccounting()->plugin_path() . '/templates/header.php';
	$html = ob_get_contents();
	ob_get_clean();
	echo $html;
}

add_action( 'eaccounting_page_top', 'eaccounting_add_page_header' );

