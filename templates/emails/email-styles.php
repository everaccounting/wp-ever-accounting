<?php
/**
 * Email Styles
 *
 * This template can be overridden by copying it to yourtheme/eaccounting/invoice/items.php.
 *
 * HOWEVER, on occasion WP Ever Accounting will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @package eaccounting\Templates
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

// Load colors.
$bg        = '#f7f7f7';
$body      = '#ffffff';
$base      = '#77B82E';
$base_text = eaccounting_light_or_dark( $base, '#202020', '#ffffff' );
$text      = '#3c3c3c';

// Pick a contrasting color for links.
$link_color = eaccounting_hex_is_light( $base ) ? $base : $base_text;

if ( eaccounting_hex_is_light( $body ) ) {
	$link_color = eaccounting_hex_is_light( $base ) ? $base_text : $base;
}

$bg_darker_10    = eaccounting_hex_darker( $bg, 10 );
$body_darker_10  = eaccounting_hex_darker( $body, 10 );
$base_lighter_20 = eaccounting_hex_lighter( $base, 20 );
$base_lighter_40 = eaccounting_hex_lighter( $base, 40 );
$text_lighter_20 = eaccounting_hex_lighter( $text, 20 );
$text_lighter_40 = eaccounting_hex_lighter( $text, 40 );

// !important; is a gmail hack to prevent styles being stripped if it doesn't like something.
// body{padding: 0;} ensures proper scale/positioning of the email in the iOS native email app.
?>
	body {
	padding: 0;
	}

	#wrapper {
	background-color: <?php echo esc_attr( $bg ); ?>;
	margin: 0;
	padding: 70px 0;
	-webkit-text-size-adjust: none !important;
	width: 100%;
	}

	#wrapper > p {
	height: 0;
	margin: 0;
	padding: 0;
	}

	#wrapper .wrapper-table {
	margin: auto;
	max-width: 900px;
	width: 100%;
	}

	#template_container {
	box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1) !important;
	background-color: <?php echo esc_attr( $body ); ?>;
	border: 1px solid <?php echo esc_attr( $bg_darker_10 ); ?>;
	border-radius: 3px !important;
	}

	#template_header {
	background-color: <?php echo esc_attr( $base ); ?>;
	border-radius: 3px 3px 0 0 !important;
	color: <?php echo esc_attr( $base_text ); ?>;
	border-bottom: 0;
	font-weight: bold;
	line-height: 100%;
	vertical-align: middle;
	font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
	}

	#template_header h1,
	#template_header h1 a {
	color: <?php echo esc_attr( $base_text ); ?>;
	background-color: inherit;
	}

	#template_header_image img {
	margin-left: 0;
	margin-right: 0;
	}

	#template_footer td {
	padding: 0;
	border-radius: 6px;
	}

	#template_footer #credit {
	border: 0;
	color: <?php echo esc_attr( $text_lighter_40 ); ?>;
	font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
	font-size: 12px;
	line-height: 150%;
	text-align: center;
	padding: 18px 36px 18px 36px;
	}

	#template_footer #credit p {
	margin: 10px 0;
	}

	#body_content {
	background-color: <?php echo esc_attr( $body ); ?>;
	}

	#body_content table td {
	padding: 27px;
	}

	#body_content table td td {
	padding: 10px;
	}

	#body_content table td th {
	padding: 10px;
	}

	#body_content p {
	margin: 0 0 16px;
	}

	#body_content_inner {
	color: <?php echo esc_attr( $text_lighter_20 ); ?>;
	font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
	font-size: 14px;
	line-height: 150%;
	text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
	}

	.td {
	color: <?php echo esc_attr( $text_lighter_20 ); ?>;
	border: 1px solid <?php echo esc_attr( $body_darker_10 ); ?>;
	vertical-align: middle;
	}

	.address {
	padding: 12px;
	color: <?php echo esc_attr( $text_lighter_20 ); ?>;
	border: 1px solid <?php echo esc_attr( $body_darker_10 ); ?>;
	}

	.text {
	color: <?php echo esc_attr( $text ); ?>;
	font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
	}

	.link {
	color: <?php echo esc_attr( $link_color ); ?>;
	}

	#header_wrapper {
	padding: 22px 24px;
	display: block;
	}

	h1 {
	color: <?php echo esc_attr( $base ); ?>;
	font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
	font-size: 30px;
	font-weight: 300;
	line-height: 150%;
	margin: 0;
	text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
	text-shadow: 0 1px 0 <?php echo esc_attr( $base_lighter_20 ); ?>;
	}

	h2 {
	color: <?php echo esc_attr( $base ); ?>;
	display: block;
	font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
	font-size: 18px;
	font-weight: bold;
	line-height: 130%;
	margin: 0 0 18px;
	text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
	}

	h3 {
	color: <?php echo esc_attr( $base ); ?>;
	display: block;
	font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
	font-size: 16px;
	font-weight: bold;
	line-height: 130%;
	margin: 16px 0 8px;
	text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
	}

	a {
	color: <?php echo esc_attr( $link_color ); ?>;
	font-weight: normal;
	text-decoration: underline;
	}

	img {
	border: none;
	display: inline-block;
	font-size: 14px;
	font-weight: bold;
	height: auto;
	outline: none;
	text-decoration: none;
	text-transform: capitalize;
	vertical-align: middle;
	margin-<?php echo is_rtl() ? 'left' : 'right'; ?>: 10px;
	max-width: 100%;
	height: auto;
	}
	.table-bordered {
	border: 1px solid <?php echo esc_attr( $body_darker_10 ); ?>;
	border-collapse: collapse;
	border-spacing: 0;
	width: 100%;
	}

	.table-bordered th,
	.table-bordered td {
	border: 1px solid <?php echo esc_attr( $body_darker_10 ); ?>;
	color: <?php echo esc_attr( $text_lighter_20 ); ?>;
	font-size: 14px;
	}
	.small {
	font-size: 85%;
	}
	.bold {
	font-weight: bold;
	}
	.normal {
	font-weight: normal;
	}
	.text-left {
	text-align: left;
	}
	.text-right {
	text-align: right !important;
	}
	.text-center {
	text-align: center !important;
	}
	.text-justify {
	text-align: justify !important;
	}
	.text-nowrap {
	white-space: nowrap !important;
	}
	.text-lowercase {
	text-transform: lowercase;
	}
	.text-uppercase {
	text-transform: uppercase;
	}
	.text-capitalize {
	text-transform: capitalize;
	}
<?php