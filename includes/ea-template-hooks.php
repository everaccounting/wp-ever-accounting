<?php
/**
 * EverAccounting Template Hooks
 *
 * Action/filter hooks used for EverAccounting functions/templates.
 *
 * @package EverAccounting\Templates
 */

defined( 'ABSPATH' ) || exit;

/**
 * Markup
 * @see eaccounting_output_page_start_content()
 * @see eaccounting_output_page_end_content()
 */
add_action( 'eaccounting_page_header', 'eaccounting_page_header' );
add_action( 'eaccounting_page_footer', 'eaccounting_page_footer' );

/**
 * Restricted content
 * @see eaccounting_output_restricted_page_content()
 */
add_action( 'eaccounting_page_content_unauthorized', 'eaccounting_page_content_unauthorized');

/**
 * Invoices
 */
add_action('eaccounting_page_content_invoices', 'eaccounting_page_content_invoices');
add_action('eaccounting_page_invoice_before_content', 'eaccounting_page_invoice_header');

/**
 * Checkout
 */
add_action('eaccounting_page_invoice_after_content', 'eaccounting_checkout_form');
add_action('eaccounting_checkout_form_bottom', 'eaccounting_checkout_button');
add_action('eaccounting_checkout_form_before_submit', 'eaccounting_checkout_total_amount');
