<?php

defined( 'ABSPATH' ) || exit();

/**
 * Open wrapper
 * since 1.0.0
 * @param string $class
 */
function eaccounting_page_wrapper_open($class = ' '){
	$classes = 'wrap ea-main-wrapper '. sanitize_html_class($class);
	echo '<div class="'.$classes.'">';
	do_action('eaccounting_page_top');
	echo '<div class="ea-page-wrapper">';
}

/**
 * Close wrapper
 *
 * since 1.0.0
 */
function eaccounting_page_wrapper_close(){
	echo '<div><!--.ea-page-wrapper-->';
	echo '<div><!--.ea-wrapper-->';
}

function eaccounting_add_page_header(){
	ob_start();
	include ever_accounting()->plugin_path() . '/templates/header.php';
	$html = ob_get_contents();
	ob_get_clean();
	echo $html;
}
add_action('eaccounting_page_top', 'eaccounting_add_page_header');
