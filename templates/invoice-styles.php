<?php
/**
 * The Template for styling an invoice.
 *
 * This template can be overridden by copying it to yourtheme/eac/invoice-styles.php
 *
 * HOWEVER, on occasion EverAccounting will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://wpeveraccounting.com/docs/
 * @package EverAccounting\Templates
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

// Load colors.
$bg     = get_option( 'eac_invoice_background_color', '#ffffff' );
$border = get_option( 'eac_invoice_border_color', '#dddddd' );
$text   = get_option( 'eac_invoice_text_color', '#333333' );

// !important; is a gmail hack to prevent styles being stripped if it doesn't like something.
// body{padding: 0;} ensures proper scale/positioning of the email in the iOS native email app.
?>
<style type="text/css">
	.invoice {
		background-color: <?php echo esc_attr( $bg ); ?>;
		border: 1px solid <?php echo esc_attr( $border ); ?>;
	}
	.invoice p{
		margin: 0;
	}
	.invoice ul {
		margin-top: 0.5rem;
		margin-bottom: 0;
	}
	.invoice li {
		margin-bottom: 0;
	}
	.invoice hr {
		border: none;
		border-bottom: 1px solid <?php echo esc_attr( $border ); ?>;
		margin: 0;
		padding: 0;
	}
	.bkit-panel.invoice table th, .bkit-panel.invoice table td {
		padding: 0;
	}
	.bkit-panel.invoice .has-padding th, .bkit-panel.invoice .has-padding td {
		padding: 0.5em 1em;
	}
</style>
