<?php
/**
 * Template Name: Home
 *
 * This template can be overridden by copying it to yourtheme/eac/home.php.
 *
 * @version 1.0.0
 * @package EverAccounting
 * @var string $endpoint The endpoint being displayed.
 * @var string $content The content to display.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Hook for outputting the head of the current endpoint.
 *
 * @since 1.0.0
 */
do_action( 'ever_accounting_endpoint_header' );

/**
 * Hook for outputting the content of the current endpoint.
 *
 * @since 1.1.6
 */
do_action( 'ever_accounting_endpoint_content' );

/**
 * Hook for outputting the footer of the current endpoint.
 *
 * @since 1.0.0
 */
do_action( 'ever_accounting_endpoint_footer' );
