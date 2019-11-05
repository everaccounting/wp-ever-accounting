<?php

defined( 'ABSPATH' ) || exit();

/**
 * Open wrapper
 * since 1.0.0
 * @param string $class
 */
function eaccounting_page_wrapper_open($class = ' '){
	$classes = 'wrap ea-wrapper '. sanitize_html_class($class);
	echo '<div class="'.$classes.'">';
}

/**
 * Close wrapper
 *
 * since 1.0.0
 */
function eaccounting_page_wrapper_close(){
	echo '<div><!--.ea-wrapper-->';
}
