<?php
/**
 * The Template for displaying a payment voucher.
 *
 * This template can be overridden by copying it to yourtheme/eac/payment.php
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

get_header( 'eac' );

/**
 * ever_accounting_before_main_content hook.
 *
 * @since 1.1.6
 */
do_action( 'ever_accounting_before_main_content' );

/**
 * ever_accounting_payment_content hook.
 *
 * @since 1.1.6
 */
eac_get_template_part( 'content', 'payment' );

/**
 * ever_accounting_after_main_content hook.
 *
 * @since 1.1.6
 */
do_action( 'ever_accounting_after_main_content' );

get_footer( 'eac' );
