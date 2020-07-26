<?php
/**
 * EverAccounting Core Functions
 *
 * General core functions available on both the front-end and admin.
 *
 * @package EverAccounting\Functions
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit();

function eaccounting_mail( $to, $subject, $message, $headers = "Content-Type: text/html\r\n", $attachments = '' ) {

}


/**
 * Queue some JavaScript code to be output in the footer.
 *
 * @param $code
 *
 * @return void
 * @since 1.0.2
 */
function eaccounting_enqueue_js( $code ) {
	global $eaccounting_queued_js;

	if ( empty( $eaccounting_queued_js ) ) {
		$eaccounting_queued_js = '';
	}

	$eaccounting_queued_js .= "\n" . $code . "\n";
}

/**
 * Output any queued javascript code in the footer.
 * @return void
 * @since 1.0.2
 */
function eaccounting_print_js() {
	global $eaccounting_queued_js;

	if ( ! empty( $eaccounting_queued_js ) ) {
		// Sanitize.
		$eaccounting_queued_js = wp_check_invalid_utf8( $eaccounting_queued_js );
		$eaccounting_queued_js = preg_replace( '/&#(x)?0*(?(1)27|39);?/i', "'", $eaccounting_queued_js );
		$eaccounting_queued_js = str_replace( "\r", '', $eaccounting_queued_js );

		$js = "<!-- EverAccounting JavaScript -->\n<script type=\"text/javascript\">\njQuery(function($) { $eaccounting_queued_js });\n</script>\n";

		echo apply_filters( 'eaccounting_queued_js', $js ); // WPCS: XSS ok.

		unset( $eaccounting_queued_js );
	}
}
