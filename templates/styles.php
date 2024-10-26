<?php
/**
 * Styles
 *
 * HOWEVER, on occasion EverAccounting will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 * *
 *
 * @see     https://wpeveraccounting.com/docs/
 * @package EverAccounting\Templates
 * @version 1.0.0
 */

use EverAccounting\Utilities\ColorsUtil;

defined( 'ABSPATH' ) || exit;

// Load colors.
$text_color      = get_option( 'eac_print_text_color', '#3c3c3c' );
$text_lighter_20 = ColorsUtil::lighten( $text_color, 20 );
$text_lighter_40 = ColorsUtil::lighten( $text_color, 40 );
$link_color      = get_option( 'eac_print_link_color', '#0073aa' );
$text_align      = is_rtl() ? 'right' : 'left';
$align_left      = is_rtl() ? 'right' : 'left';
$align_right     = is_rtl() ? 'left' : 'right';
?>
<style type="text/css">
	.text {
		color: <?php echo esc_attr( $text_color ); ?>;
		font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
	}

	.link {
		color: <?php echo esc_attr( $link_color ); ?>;
	}

	.td {
		color: <?php echo esc_attr( $text_lighter_20 ); ?>;
		vertical-align: middle;
	}

	.address {
		padding: 12px;
		color: <?php echo esc_attr( $text_lighter_20 ); ?>;
	}

	.align-left {
		text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
	}

	.align-right {
		text-align: <?php echo is_rtl() ? 'left' : 'right'; ?>;
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
		margin- <?php echo is_rtl() ? 'left' : 'right'; ?>: 10px;
		max-width: 100%;
	}
</style>
