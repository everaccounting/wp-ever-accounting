<?php
/**
 * Template Name: Invoice
 *
 * This template can be overridden by copying it to yourtheme/eac/invoice.php.
 *
 * @version 1.0.0
 * @package EverAccounting
 * @var string $uuid_key The UUID key of the invoice.
 */

defined( 'ABSPATH' ) || exit;
// $document = \EverAccounting\Models\Invoice::get(
// array(
// 'type'     => 'invoice',
// 'uuid_key' => $uuid_key,
// )
// );

eac_get_header();

/**
 * ever_accounting_before_main_content hook.
 *
 * @since 1.6.1
 */
do_action( 'ever_accounting_before_main_content' );

eac_get_template_part( 'content', 'invoice' );

/**
 * ever_accounting_after_main_content hook.
 *
 * @since 1.6.1
 */
do_action( 'ever_accounting_after_main_content' );

eac_get_footer();

